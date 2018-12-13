<?php
namespace Admin\Logic;
use Admin\Model\GoodsImagesModel;
use Common\Logic\AbstractGetDataLogic;
use Common\Tool\Event;
use Common\Tool\Extend\ArrayChildren;
use Common\TraitClass\ThumbNailTrait;
use Common\TypeParse\AbstractParse;
use Common\TraitClass\GETConfigTrait;
use Common\Tool\Tool;
use Common\Tool\Extend\UnlinkPicture;
use Common\Tool\Extend\CURL;
use Think\Cache;

class GoodsImagesLogic extends AbstractGetDataLogic
{
    use ThumbNailTrait;
    
    use GETConfigTrait;
    /**
     * 宽度
     */
    private $imageWidth = 400;
  
    /**
     * 高度
     */
    private $imageHeight = 400;

     //缩略图前缀
    
     private  $thumbPerfix = 'thumb_';
    
     const OriginalImageNoThumb = 0; // 数据表原来的图片【不是缩略图】
     
     const OriginalImageThumb   = 1; // 数据表原来的图片【缩略图】

    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = null)
    {
        $this->data = $data;

        $this->splitKey = $split;

        $this->modelObj = new GoodsImagesModel();
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult(){}
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return GoodsImagesModel::class;
    }

    /**
     * @return mixed|void
     */
    protected function getSlaveColumnByWhere() :string
    {
        return GoodsImagesModel::$goodsId_d;
    }

    /**
     * @return array
     */
    public function getSlaveField() :array
    {
       return [
           GoodsImagesModel::$goodsId_d,
           GoodsImagesModel::$picUrl_d,
       ];
    }
    

    /**
     * 数据处理组合
     * @param array $slaveData
     * @param string $slaveColumnWhere
     * @return array
     */
    protected function parseSlaveData(array $slaveData, $slaveColumnWhere) :array
    {
        $data = $this->data;
        foreach( $slaveData as $key => &$value ){
            if( empty( $data[ $value[$slaveColumnWhere] ] ) ){
                continue;
            }
            $data[$value[$slaveColumnWhere]] = array_merge( $value, $data[ $value[$slaveColumnWhere] ]);
        }
        return $data;
    }
    
    /**
     * 回调方法s
     * @param $where
     */
    public function parseSlaveWhereAgain( $where) :string
    {
        return $where .' and '.GoodsImagesModel::$isThumb_d.' = 1';
    }

    /**
     * 获取商品图片(商品管理)
     */
    public function getGoodsImages()
    {
        $data = $this->data;
        //获取图片数组
        $images = $this->modelObj->field('id,pic_url')->where(['goods_id' => $data['goods_id']])->select();
       
        return $images;
    }

    /**
     * 检查参数
     */
    public function checkValidateByGoodsId()
    {
        return [
            'goods_id' => [
                'number' => 'goods_id必须是数字' 
            ],
        ];
    }
    
    /**
     * 添加图片
     * @return boolean
     */
    public function addPicture()
    {
        if (!is_array($this->data['pic_url'])) {
            $this->errorMessage = '数据错误';
            return false;
        }
        
        $thumbImageArray = (new CURL($this->data['pic_url'], C('create_thumb_file')))->sendImageToCreateThumb();
        
        $thumbImageArray = json_decode($thumbImageArray, true);
       
        
        if ($thumbImageArray['status'] == 0) {
            return false;
        }
        
        $pic = [];
        
        $pic[GoodsImagesModel::$picUrl_d]  = array_merge($this->data['pic_url'], $thumbImageArray['data']);
        
        $pic[GoodsImagesModel::$goodsId_d] = $_SESSION['insertId'];
       
        $status = $this->modelObj->addAll($pic);
        
        return $status;
    }
    
     /**
     * 修改图片 
     * @param array $data post 数据
     * @param string $key 商品编号键
     * @return bool
     */
    public function editPicture()
    {
        $pic = $this->data[GoodsImagesModel::$picUrl_d];
        
        if (empty($pic))
        {
            return false;
        }
        
        $data = $this->data;
        
        
        $id = $_SESSION['goods_image_p_id'];
        
        $isExitsThumb = $this->modelObj->where(GoodsImagesModel::$goodsId_d.' = %d', $id)->select();
       
        //分拣
        $arrayObj = new ArrayChildren($isExitsThumb);
        
        $pic = $arrayObj->inTheSameState(GoodsImagesModel::$isThumb_d);
       
        $temp = empty($pic[self::OriginalImageNoThumb]) ? [] : $pic[self::OriginalImageNoThumb];
       
        $thumbImageArray = [];
        
        if (empty($pic[self::OriginalImageThumb])) {//没有缩略图时生成缩略图

            $thumbImageArray = (new CURL($this->data['pic_url'], C('create_thumb_file')))->sendImageToCreateThumb();
            
            $thumbImageArray = json_decode($thumbImageArray, true);
            
            if ($thumbImageArray['status'] == 0) {
                return false;
            }
          
            $data[GoodsImagesModel::$picUrl_d] = array_merge($data[GoodsImagesModel::$picUrl_d], $thumbImageArray['data']);
        } 
        //比较是否添加
        $receive= [];
        
        $arrayObj->setData($temp);
        
        $receive = $arrayObj->parseArrayByArbitrarily(GoodsImagesModel::$picUrl_d);
        //比较是否有要添加的
        $arrayObj->setData($data[GoodsImagesModel::$picUrl_d]);
        
        $receive = $arrayObj->compareDataByArray($receive);
        
        if (empty($receive)) { // 没有 要添加的
            return true;
        }
        
        $data[GoodsImagesModel::$picUrl_d] =  $receive; //要添加的图片
        $data[GoodsImagesModel::$goodsId_d] = $id;
        return $this->modelObj->addAll($data);
    }

    /**
     * @return the $thumbPerfix
     */
    public function getThumbPerfix()
    {
        return $this->thumbPerfix;
    }
    
    /**
     * @param string $thumbPerfix
     */
    public function setThumbPerfix($thumbPerfix)
    {
        $this->thumbPerfix = $thumbPerfix;
    }
    

    /**
     * @return the $imageWidth
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }
    
    /**
     * @return the $imageHeight
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }
    
    /**
     * @param number $imageWidth
     */
    public function setImageWidth($imageWidth)
    {
        $this->imageWidth = $imageWidth;
    }
    
    /**
     * @param number $imageHeight
     */
    public function setImageHeight($imageHeight)
    {
        $this->imageHeight = $imageHeight;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice()
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        return [
            GoodsImagesModel::$picUrl_d => [
                'required' => $comment[GoodsImagesModel::$picUrl_d].'必须存在'
            ]
        ];
    }
    
    /**
     * 删除图片消息
     */
    public function getMessageByDelImage()
    {
        return [
            'id' => [
                'number' => '商品图片异常'
            ]
        ];
    }
    
    /**
     * 根据其他表数据获取商品图片数据
     */
    public function getImageByResource()
    {
    	
    	$cacheKey = md5(json_encode($this->data));
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$data = $cache->get($cacheKey);
    	
    	if (!empty($data)) {
    		
    		return $data;
    	}
    	
    	
    	$field = [
    		GoodsImagesModel::$goodsId_d,
    		GoodsImagesModel::$picUrl_d,
    	];
    	
    	$data = $this->getDataByOtherModel($field, GoodsImagesModel::$goodsId_d);
    	if (empty($data)) {
    		return array();
    	}
    	
    	$cache->set($cacheKey, $data);
    	
    	return $data;
    }
    
    /**
     * getDataByOtherModel附属方法
     */
    protected function parseReflectionData(array $getData) :array
    {
		
    	$data = $this->data;
    	
    	$result = (new ArrayChildren($getData))->convertIdByData(GoodsImagesModel::$goodsId_d);
    	
    	foreach ($data as $key => & $value) {
    		
    		if (!isset($result[$value[$this->splitKey]])) {
    			$value[GoodsImagesModel::$picUrl_d] = '';
    		} else {
    			$value[GoodsImagesModel::$picUrl_d] = $result[$value[$this->splitKey]][GoodsImagesModel::$picUrl_d];
    		}
    	}
    	
    	return $data;
    }
    
    /**
     * getDataByOtherModel附属方法
     */
    protected function setKeyByRelation()
    {
    	return $this->splitKey;
    }
    
    /**
     * getDataByOtherModel 附属方法
     */
    protected function parseWhere($where)
    {
    	return $where.' and '.GoodsImagesModel::$isThumb_d.' = 1';
    }
    
    /**
     * 删除图片
     * @return 
     */
    public function deleteManyPicture()
    {
        $image = $this->getFindOne();
        
        $fileName = $image[GoodsImagesModel::$picUrl_d];
        
        if (empty($fileName)) {
            return false;
        }
    
        $imageName = substr(strrchr($fileName, '/'), 1);
    
        $thumbFileName = str_replace($imageName, '', $fileName).$this->thumbPerfix.$imageName;
    
        $status = $this->modelObj->where(GoodsImagesModel::$picUrl_d.' in ("'.$fileName.'", "'.$thumbFileName.'")')->delete();
        //删除服务器商品图片
        (new CURL([$fileName], C('unlink_image')))->asynchronousExecution();
        
        return $status;
    }
    
    /**
     * 获取消息（用于获取商品图片列表）
     * @return array
     */
    public function getMessageByGoods()
    {
        return [
            GoodsImagesModel::$goodsId_d => [
                'number' => '商品非法'
            ]
        ];
    }
    
    /**
     * 获取商品图片列表
     */
    public function getImageListByGoods()
    {
        $this->searchTemporary = [
            GoodsImagesModel::$goodsId_d => $this->data[GoodsImagesModel::$goodsId_d],
            GoodsImagesModel::$isThumb_d => 0
        ];
        
        return $this->getNoPageList();
    }
    
    /**
     * 获取缓存key
     * @return string
     */
    protected function getCacheKey() :string
    {
        if (empty($_SESSION['store_id'])) {
            throw new \Exception('系统异常');
        }
    
        $key = 'image_sr_'.$_SESSION['store_id'].'4_data';
    
        return $key;
    }
    
    /**
     * 根据商品编号删除图片
     */
    public function deleteImagesByGoods()
    {
        //获取图片路径
        $picsPath = $this->modelObj
            ->where(GoodsImagesModel::$goodsId_d.' = :g_id')
            ->bind([':g_id' => $this->data['id']])
            ->getField('id, pic_url');
        
        if (empty($picsPath)) {
            $this->modelObj->commit();
            return true;
        }
        
        //删除图片
        
        $temp = [];
        
        $temp['fileName'] = array_values($picsPath);
        
        //删除服务器商品图片
        (new CURL($temp, C('unlink_image')))->asynchronousExecution();
        
        //删除数据库数据
        $result = $this->modelObj->where('goods_id ='. (int)$this->data['id']) -> delete();
       
        if(!$this->traceStation($result)){
            $this->errorMessage = '删除商品图片失败';
            return false;
        }
        
        return $result;
    }
    /**
     * 通过订单数据 获取商品图片
     */
    public function getPicByOrderData()
    {
    	$data = $this->getSlaveData();
    	
    	if (empty($data)) {
    		return $this->data;
    	}
    	
    	$data = (new ArrayChildren($data))->convertIdByData(GoodsImagesModel::$goodsId_d);
    	
    	$goodsData = $this->data;
    	
    	foreach ($goodsData as $key => & $value) {
    		
    		if (!isset($data[$value[$this->splitKey]])) {
    			continue;
    		}
    		
    		$value[GoodsImagesModel::$picUrl_d] = $data[$value[$this->splitKey]][GoodsImagesModel::$picUrl_d];
    	}
    	
    	return $goodsData;
    }
}