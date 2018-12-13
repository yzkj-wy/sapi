<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Logic;

use Admin\Model\GoodsModel;
use Common\Model\BaseModel;
use Common\Model\GoodsDetailModel;
use Common\Tool\Tool;
use Common\Tool\Extend\CombineArray;
use PlugInUnit\Validate\Children\Number;
use PlugInUnit\Validate\Children\SpecialCharFilter;
use Think\Cache;

class GoodsLogic extends AbstractGetDataLogic
{
    protected $idArray = array();
    
    /**
     * 批量数据（规格）
     * @var array
     */
    protected $arrayData = [];
    
    /**
     * 商品数据
     * @var array
     */
    private $goodsData = [];
    
    /**
     * 未审核
     * @var 16进制
     */
    const NOT_AUDITED = 0x00; 
    /**
     * 已审核
     * @var 16进制
     */
    const AUDITED     = 0x01;
    
    /**
     * 拒绝
     * @var
     */
    const REFUSE = 0x02;
    
    private $symbol = ' + ';
    
    /**
     * 批量更新
     * @var unknown
     */
    private $bitchArray = [];
    
    /**
     * @var bool
     */
    private $isOpen = false;
    
    /**
     * @return the $idArray
     */
    public function getIdArray()
    {
        return $this->idArray;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol === false ? ' - ' : ' + ';
    }

    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;
         
        $this->modelObj = new GoodsModel();
        
        $this->splitKey = $split;
        
        $this->covertKey = GoodsModel::$title_d;

    }
    
    /**
     * 获取店铺地址数据
     */
    public function getResult()
    {
        
    }

    
    /**
     * 获取已审核列表
     */
    public function getAleardyDataList()
    {
        return parent::getDataList();
    }

    /**
     * @description
     * @return array
     */
    protected  function searchTemporary()
    {
        return [
            GoodsModel::$approvalStatus_d => GoodsModel::AUDITED,
            GoodsModel::$pId_d            => 0
        ];
    }

    /**
     * 获取模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return GoodsModel::class;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    protected function hideenComment()
    {
        return [
            GoodsModel::$selling_d,
            GoodsModel::$code_d,
            GoodsModel::$top_d,
            GoodsModel::$seasonHot_d,
            GoodsModel::$description_d,
            GoodsModel::$goodsType_d,
            GoodsModel::$latestPromotion_d,
            GoodsModel::$pId_d,
            GoodsModel::$commentMember_d,
            GoodsModel::$salesSum_d,
            GoodsModel::$advanceDate_d,
            GoodsModel::$extend_d,
            GoodsModel::$createTime_d,
            GoodsModel::$weight_d,
            GoodsModel::$type_d,
            GoodsModel::$status_d,
            GoodsModel::$attrType_d,
            GoodsModel::$priceMarket_d,
            GoodsModel::$priceMember_d,
            GoodsModel::$stock_d
        ];
    }
    
    /**
     * 商品子类查看注释
     */
    public function getCommentByGoodsDetail()
    {
        $comment = $this->hideenComment();
        
        unset($comment[18], $comment[19], $comment[20]);
        
        return $this->modelObj->getComment($comment);
    }
    
    /**
     * 获取商品信息 组合关联数据
     */
    public function getUnioData()
    {
        if (empty($this->data)) {
            return [];
        }
        
        $goodsId = $this->data['goods_id'];
        
        if (($goodsId = intval($goodsId)) === 0) {
            return [];
        }
    
        $field = GoodsModel::$id_d . ',' . GoodsModel::$stock_d . ',' . GoodsModel::$priceMarket_d . ',' . GoodsModel::$priceMember_d . ',' . GoodsModel::$weight_d;
    
        $data = $this->modelObj->where(array(
            GoodsModel::$pId_d => $goodsId
        ))->getField($field);
    
        if (empty($data)) {
            return [];
        }
    
        foreach ($data as $key => $value) {
            unset($data[$key][GoodsModel::$id_d]);
        }
    
        return $data;
    }
    
    /**
     * 修改 商品及其子类 的上架状态
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::saveData()
     */
    public function saveData() :bool
    {
        if (empty($this->data)) {
            return false;
        }
        $post = $this->data;
        $goodsId = (int) $post[GoodsModel::$id_d];
        
        unset($post[GoodsModel::$id_d]);
        
        $status = $this->modelObj->where(GoodsModel::$id_d . '=%d or ' . GoodsModel::$pId_d . '=%d', [
            $goodsId,
            $goodsId
        ])->save($post);
        
        return $status !== false;
    }
    
    /**
     * 该商品通过审核
     */
    public function approvalGoodsOk()
    {
        $goodsId = (int) $this->data[GoodsModel::$id_d];
        
        $status = $this->editApprovalStatus(GoodsModel::AUDITED);
        
        return $status;
    }
    
    /**
     * 修改状态
     * @param int $status
     * @return boolean
     */
    private function editApprovalStatus ($status)
    {
        $goodsId = (int) $this->data[GoodsModel::$id_d];
        
        $status = $this->modelObj->where(GoodsModel::$id_d . '=%d or ' . GoodsModel::$pId_d . '=%d', [
            $goodsId,
            $goodsId
        ])->save([
            GoodsModel::$approvalStatus_d => $status
        ]);
        
        return $status;
    }
    
    /**
     * 拒绝审核
     * @return int
     */
    public function approvalGoodsOFF()
    {
        $goodsId = (int) $this->data[GoodsModel::$id_d];
    
        $this->modelObj->startTrans();
        
        $status = $this->editApprovalStatus(GoodsModel::REFUSE);
    
        if (!$this->modelObj->traceStation($status)) {
            return GoodsModel::ADD_ERROR;
        }
        return $status;
    }
    
    /**
     * 修改单个商品
     */
    public function singleCommodity()
    {
        if (empty($this->data)) {
            return false;
        }
        
        return $this->modelObj->save();
    }
    
    /**
     * 是否审核通过
     * @return bool
     */
    public function isAproval()
    {
       $data = $_SESSION['temp_com_data'][$this->data[GoodsModel::$id_d]];
       if (empty($data)) {
           return false;
       }
       
       return (int)$data[GoodsModel::$approvalStatus_d] === GoodsModel::AUDITED;
    }
    
    /**
     * 获取父类产品
     * @param BaseModel $model基类 模型
     * @return array【只许一次】
     */
    public function getGoodsDataByParentId(BaseModel $model)
    {
        if (($id = intval($this->data['id'])) === 0 || ! ($model instanceof BaseModel)) {
            return array();
        }
    
        $field = [
            GoodsModel::$id_d,
            GoodsModel::$title_d,
            GoodsModel::$code_d,
            GoodsModel::$classId_d,
            GoodsModel::$priceMarket_d,
            GoodsModel::$priceMember_d,
            GoodsModel::$stock_d,
            GoodsModel::$shelves_d,
            GoodsModel::$recommend_d,
            GoodsModel::$updateTime_d,
            GoodsModel::$sort_d,
            GoodsModel::$storeId_d,
            GoodsModel::$approvalStatus_d
        ];
    
        $data = $this->modelObj->getAttribute(array(
            'field' => $field,
            'where' => array(
                GoodsModel::$pId_d => $id
            ),
            'order' => GoodsModel::$createTime_d . GoodsModel::DESC . ',' . GoodsModel::$updateTime_d . GoodsModel::DESC
        ));
    
        if (empty($data)) {
            return array();
        }
        
        $classField = array(
            $model::$id_d,
            $model::$className_d
        );
        
        $data = $model->getDataByOtherModel($data, GoodsModel::$classId_d, $classField, $model::$id_d);
        return $data;
    }
    
    /**
     * 返回仓库分割键
     * @return string
     */
    public function getStoreSplitKey ()
    {
        return GoodsModel::$storeId_d;
    }
    
    /**
     * 根据订单信息 查询商品数据
     */
    public function getOrderInfo()
    {
        $id = Tool::characterJoin($this->data);

        if (empty($id)) {
            return array();
        }
    
        $field = [
            GoodsModel::$id_d,
            GoodsModel::$title_d,
        	GoodsModel::$pId_d
        ];
    
        $dataArray = $this->modelObj->field($field)
        	->where('id in (' . $id . ')')
        	->select();
        if (empty($dataArray)) {
            return $this->data;
        }

        $obj = new CombineArray($dataArray, GoodsModel::$id_d);
    
        $data = $obj->parseCombineList($this->data, 'goods_id');

        return $data;
    }
    
    /**
     * 获取商品数据
     */
    public function getGoodsData()
    {
    	$cacheKey = md5(json_encode($this->data));
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$data = $cache->get($cacheKey);
    	
    	if (!empty($data)) {
    		
    		return $data;
    	}
    	
    	$field = [
    		GoodsModel::$title_d,
    		GoodsModel::$id_d,
    		GoodsModel::$pId_d
    	];
    	
    	$data = $this->getDataByOtherModel($field, GoodsModel::$id_d);
    	
    	if (empty($data)) {
    		return array();
    	}
    	
    	$cache->set($cacheKey, $data);
    	
    	return $data;
    }
    
    /**
     * 获取商品标题
     * @return mixed|NULL|unknown|string[]|unknown[]|object
     */
    public function getGoodsTitle()
    {
        return $this->modelObj->where(GoodsModel::$id_d.' = %d', $this->data[$this->splitKey])->getField(GoodsModel::$title_d);
    }
    
    /**
     * 根据退货信息获取商品标题并组合数据
     */
    public function getGoodsDataByRefund()
    {
    	$title = $this->getGoodsTitle();
    	
    	if (empty($title)) {
    		return $this->data;
    	}
    	
    	$result = $this->data;
    	
    	$result[GoodsModel::$title_d] = $title;
    	
    	return $result;
    }
    
    
    /**
     * 合并数据
     */
    public function geGoodsTitleMergeData()
    {
        $title = $this->getGoodsTitle();
    
        if (empty($title)) {
            return $this->data;
        }
    
        $data = $this->data;
    
        $data[GoodsModel::$title_d] = $title;
        return $data;
    }
    
    /**
     * 回归库存
     */
    public function returnInventory()
    {
    	$args = $this->data['args'];
    	
    	if ($args['status'] == 0) {
    		$this->modelObj->commit();
    		return true;
    	}
    	
    	$data = $this->data['data'];
    	
        $status = $this->modelObj->where(GoodsModel::$id_d.'=:id')
        	->bind([':id' => $data['goods_id']])
        	->setInc(GoodsModel::$stock_d, $data['number']);
        
        if (!$this->traceStation($status)) {
            $this->errorMessage = '回归库存失败';
            return false;
        }
        $this->modelObj->commit();
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
        return [
            GoodsModel::$title_d
        ];
    }

    /**
     * 返回主键
     * @return string
     */
    public function getIdSplitKey()
    {
        return GoodsModel::$id_d;
    }

    /**
     * 验证提示消息
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $message = [
            GoodsModel::$id_d => [
                'number' => 'id必须是数字',
            ],
            GoodsModel::$title_d => [
                'required' => '请输入商品标题',
                'specialCharFilter' => '商品标题不能输入特殊字符'
            ],
            GoodsModel::$description_d => [
                'required' => '请输入商品简介',
                'specialCharFilter' => '商品简介不能输入特殊字符'
            ],
            GoodsModel::$classId_d => [
                'required' => '请选择商品分类',
                'number' => '商品分类编号必须为数字'
            ],
            GoodsModel::$brandId_d => [
                'required' => '请选择商品品牌',
                'number' => '商品品牌编号必须为数字'
            ],
            GoodsModel::$priceMarket_d => [
                'required' => '请输入商品市场价',
                'number' => '价格必须为数字'
            ],
            GoodsModel::$priceMember_d => [
                'required' => '请输入商品会员价',
                'number' => '价格必须为数字'
            ],
            GoodsModel::$stock_d => [
                'required' => '请输入商品库存',
                'number' => '库存数量必须为数字'
            ],
            GoodsModel::$advanceDate_d => [
                'required' => '请输入预售天数',
                'number' => '预售天数必须为数字'
            ],
            
            GoodsModel::$recommend_d => [
                'required' => '请选择是否推荐',
                'number' => '是否推荐 必须是数字 且介于${0-1}'
            ],
            GoodsModel::$weight_d => [
                'required' => '请输入重量',
                'number' => '重量必须是数字'
            ],
            
            GoodsModel::$classTwo_d => [
                'required' => '请选择二级分类',
                'number' => '二级分类必须是数字'
            ],
            
            GoodsModel::$classThree_d => [
                'required' => '请选择三级分类',
                'number' => '三级分类必须是数字'
            ],
            
            'detail' => [
                'required' => '请输入商品详情',
            ]
        ];

        return $message;
    }

    /**
     * 保存商品信息(通用信息)
     */
    public function addGoodsInfo()
    {
        //保存数据库
       
        $this->modelObj->startTrans();
        $id = $this->addData();
      
        if( $id === false){
            $this->modelObj->rollback();
            return false;
        }
        $detailModel = GoodsDetailModel::getInitnation();
        if($detailModel -> add(['goods_id' => $id , 'detail' => $this->data['detail']]) === false){
            $this->modelObj->rollback();
            $this->errorMessage = '发布商品详情失败';
            return false;
        }
        $goods_info['id'] = $id;
        $goods_info['title'] = $this->data['title'];
        $goods_info['description'] = $this->data['description'];
        $goods_info['class_id'] = $this->data['class_id'];
        $goods_info['brand_id'] = $this->data['brand_id'];
        $goods_info['advance_date'] = $this->data['advance_date'];
        $goods_info['recommend'] = $this->data['recommend'];
        $goods_info['shelves'] = $this->data['shelves'];
        $goods_info[GoodsModel::$pId_d] = 0;
        $goods_info[GoodsModel::$classTwo_d] = $this->data['class_two'];
        $goods_info[GoodsModel::$classThree_d] = $this->data['class_three'];
        $goods_info[GoodsModel::$expressId_d] = $this->data[GoodsModel::$expressId_d];
        $_SESSION['insertId'] = $id;
        
        $_SESSION['goods_info'] = [];
        
        $_SESSION['goods_info'] = $goods_info;
        return $this->modelObj->commit();
    }

    /**
     * 添加时处理参数
     * @return array
     */
    protected function getParseResultByAdd() :array
    {
        $data = [];
        
        $data =  $this->data;
       
        $data[GoodsModel::$pId_d] = 0;
       
        $data[GoodsModel::$storeId_d] = $_SESSION['store_id'];
        
        return $data;
    }
   

   
    //根据状态获取商品数量
    public function getGoodsNumBystatus(){
        //出售中的商品
        $sale['store_id'] = $_SESSION['store_id'];
        $sale['shelves'] = 1;
        $inTheSale = $this->modelObj->getGoodsNumByWhere($sale);
        //仓库中的商品
        $house['store_id'] = $_SESSION['store_id'];
        $inTheWarehouse= $this->modelObj->getGoodsNumByWhere($house);

        //等待审核的商品
        $audit['store_id'] = $_SESSION['store_id'];
        $audit['approval_status'] = 0;
        $waitForAudit= $this->modelObj->getGoodsNumByWhere($audit);
        //违规下架的商品
        $irreg['store_id'] = $_SESSION['store_id'];
        $irreg['shelves'] = 0;
        $irregularities= $this->modelObj->getGoodsNumByWhere($irreg);
        $data = array(
            "inTheSale"=>$inTheSale,
            "inTheWarehouse"=>$inTheWarehouse,
            "waitForAudit"=>$waitForAudit,
            "irregularities"=>$irregularities,
        );
        return $data;

    }

    /**
     * 获取顶级商品列表
     */
    public function getTopGoodsList()
    {
        $this->searchTemporary = [
            GoodsModel::$storeId_d =>  $_SESSION['store_id'],
            GoodsModel::$pId_d     => 0,
        ];

        return $this->getDataList();
    }

    /**
     * 获取数据表字段
     */
    protected function getTableColum() :array
    {
        return [
            GoodsModel::$id_d,
            GoodsModel::$title_d,
            GoodsModel::$createTime_d,
            GoodsModel::$approvalStatus_d,
            GoodsModel::$priceMarket_d,
            GoodsModel::$priceMember_d,
            GoodsModel::$shelves_d,
            GoodsModel::$classId_d,
            GoodsModel::$description_d,
            GoodsModel::$brandId_d,
            GoodsModel::$advanceDate_d,
            GoodsModel::$weight_d,
            GoodsModel::$stock_d,
            GoodsModel::$classTwo_d,
            GoodsModel::$classThree_d,
        	GoodsModel::$expressId_d,
            GoodsModel::$recommend_d
        ];
    }

   
    /**
     * 设置筛选条件
     */
    private function setSelectCondition()
    {

        $condition=  & $this->data;

        $condition[GoodsModel::$storeId_d] = session('store_id');
        $condition[GoodsModel::$pId_d] = 0;
        Tool::isSetDefaultValue($condition, array(
            GoodsModel::$classId_d,
            GoodsModel::$brandId_d,
            GoodsModel::$title_d,
            GoodsModel::$shelves_d,
        ), '');
        return $this->modelObj->buildSearch($condition, true, array(GoodsModel::$title_d));

    }

    /**
     * 删除顶级商品
     */
    public function delTopGoods()
    {
        $goodsId = $this->data['id'];
        $this->modelObj->startTrans();
        //获取顶级商品及所属商品
        $result = $this->modelObj
            ->where('id = :id or p_id = :p_id')
            ->bind([':id' => $goodsId, ':p_id' => $goodsId])
            ->delete();

        //删除商品
        if(!$this->traceStation($result)) {
            return false;
        }
        return $result;
    }

    /**
     * 删除子类商品
     * @return boolean
     */
    public function deleteGoodById()
    {
        $this->modelObj->startTrans();
        
        $status = $this->modelObj->where(GoodsModel::$id_d . '= %d', $this->data['id'])->delete();
        
        return $this->traceStation($status);
    }
    
    /**
     * 验证上下架
     */
    public function getMessageByShelves()
    {
        return [
          GoodsModel::$id_d => [
              'number' => '商品编号必须是数字'
          ],
          GoodsModel::$shelves_d => [
              'number' => '上下架必须是数字，且介于${0-1}'
          ]
        ];
    }
    
    /**
     * 修改上下架状态
     */
    public function changeShelve()
    {
        if($this->data['shelves'] != 0 && $this->data['shelves'] != 1){
            $this->errorMessage = '请选择正确的状态';
            return [];
        }
        if($this->modelObj->where(['id' => $this->data['id']]) -> save($this->data) === false){
            $this->errorMessage = '切换状态失败';
            return false;
        }

        return true;
    }
    
    /**
     * 获取商品子类
     */
    public function getChildGoodsList()
    {
        //获取上级商品ID
        $p_id = $this->data['id'];

        $goods = $this->modelObj->field('id,title,class_id,price_market,price_member,stock,shelves,recommend')->where('p_id = %s and store_id = ' . session('store_id') , $p_id)->select();

        return $goods;
    }
    
    /**
     * 根据规格生成对应产品
     *
     * @param array $data
     *            商品全数据
     * @return array 主键数组
     */
    public function addSpecDataByGoods()
    {
        if (empty($_SESSION['goods_info'])) {//保证返回数据类型一致性
            return [];
        }
        
        $item = empty($this->goodsData) ? $this->data : $this->goodsData;
        // 处理价格
        $build = array();
        foreach ($item as $key => & $value) {
    
            $value[GoodsModel::$updateTime_d] = time();
    
            $value[GoodsModel::$createTime_d] = time();
    
            $value[GoodsModel::$classId_d] = $_SESSION['goods_info']['class_id'];
    		
            $value[GoodsModel::$classTwo_d] = $_SESSION['goods_info']['class_two'];
            
            $value[GoodsModel::$classThree_d] = $_SESSION['goods_info']['class_three'];
            
            $value[GoodsModel::$brandId_d] = $_SESSION['goods_info']['brand_id'];
            
            // 扩展分类已摒弃
            // $value[GoodsModel::$extend_d] = empty($data[GoodsModel::$extend_d]) ? 0 : $data[GoodsModel::$extend_d];
    
            $value[GoodsModel::$recommend_d] = empty($_SESSION['goods_info']['recommend']) ? 0 : $_SESSION['goods_info']['recommend'];
    
            $value[GoodsModel::$shelves_d]   = empty($_SESSION['goods_info']['shelves']) ? 1 : $_SESSION['goods_info']['shelves'];
    
            $value[GoodsModel::$description_d] = $_SESSION['goods_info']['description'];
    
            $value[GoodsModel::$pId_d] = $_SESSION['goods_info']['id'];
    
            $value[GoodsModel::$advanceDate_d]   = $_SESSION['goods_info']['advance_date'];
    
            $value[GoodsModel::$priceMarket_d]   = $value[GoodsModel::$priceMarket_d];
    
            $value[GoodsModel::$priceMember_d]   = $value[GoodsModel::$priceMember_d];
    
            $value[GoodsModel::$storeId_d] = $_SESSION['store_id'];
    		
            $value[GoodsModel::$expressId_d] = $_SESSION['goods_info']['express_id'];
            
            $build[] = $this->modelObj->create($value);
        }
        if (empty($build)) {
            return [];
        }
        $this->modelObj->startTrans();
    	
        $this->isOpen = true;
        
        $insertId = 0;
       
        try {
        	$insertId = $this->modelObj->addAll($build);
        	
        } catch (\Exception $e) {
        	$this->modelObj->rollback();
        	$this->errorMessage = $e->getMessage();
        	return [];
        }
        
      
    
        if (! $this->traceStation($insertId)) {
            return [];
        }
//         商品规格现在与商品分类关联
//         $status = $this->where(GoodsModel::$pId_d . '= %d or ' . GoodsModel::$id_d . '= %d', [
//             $data[$model::$goodsId_d],
//             $data[$model::$goodsId_d]
//         ])->save([
//             GoodsModel::$goodsType_d => $data[GoodsModel::$goodsType_d]
//         ]);
    
        $count = count($build);
        $number = array();
        for ($i = 0; $i < $count; $i ++) {
            $number[$i] = $i + $insertId;
        }
      
       	unset($_SESSION['goods_info']);
        return $number;
    }


    /**
     * save
     * /**
     * UPDATE
     * db_goods as goods,
     * db_goods as product
     * SET goods.title = CASE goods.id
     * WHEN 1092 THEN
     * 'ooo'
     * WHEN 1093 THEN 'qqq'
     * END,
     * //product.title = 'eee'
     * WHERE goods.p_id = product.id and goods.p_id=1091 and product.id=1091;
     */
    public function saveGoods()
    {
        if (empty( $this->data)) {
            return false;
        }
        // $flag = GoodsModel::flag($data, GoodsModel::$classId_d);
    
        // $data[GoodsModel::$classId_d] = $flag;
        
        $tableName = $this->modelObj->getTableName();
        
        $this->modelObj->startTrans();
        
        $time = time();
        
        $sql = <<<aaa
            update {$tableName} as a, {$tableName} as b set
            a.title = '{$this->data['title']}',
            a.class_id = {$this->data['class_id']},
			a.class_two={$this->data['class_two']},
			a.class_three={$this->data['class_three']},
            a.brand_id = {$this->data['brand_id']},
            a.description = '{$this->data['description']}',
            a.shelves = {$this->data['shelves']},
            a.recommend = {$this->data['recommend']},
            a.weight = {$this->data['weight']},
            b.title  = REPLACE(b.title,a.title, '{$this->data['title']}'),
            b.class_id = {$this->data['class_id']},
            b.brand_id = {$this->data['brand_id']},
            b.description = '{$this->data['description']}',
            b.shelves = {$this->data['shelves']},
            b.recommend = {$this->data['recommend']},
            b.weight = {$this->data['weight']},
			b.class_two={$this->data['class_two']},
			b.class_three={$this->data['class_three']},
            a.express_id={$this->data['express_id']},
			b.express_id={$this->data['express_id']},
			a.update_time={$time},
			b.update_time={$time}
            where a.id = b.p_id and a.id = {$this->data['id']}
aaa;
        $status = $this->modelObj->execute($sql);
        if (!$this->traceStation($status)) {
            $this->errorMessage = '更新失败';
            return false;
        }
        
        $status = $this->modelObj->where(GoodsModel::$id_d.'=:id')
        	->bind([':id' => $this->data['id']])
        	->save([
        		GoodsModel::$priceMarket_d => $this->data['price_market'],
        		GoodsModel::$priceMember_d => $this->data['price_member']
        	]);
        
        if (!$this->traceStation($status)) {
        	$this->errorMessage = '更新失败';
        	return false;
        }
        
        $this->modelObj->commit();
        
        return $status;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getDataToBeUpdated():array
     */
    protected function getDataToBeUpdated():array
    {
        $parseData = $this->arrayData;
        $arr = array();
        foreach ($parseData as $key => $value) {
            $arr[$value[GoodsModel::$id_d]][] = $value[GoodsModel::$priceMarket_d];
            $arr[$value[GoodsModel::$id_d]][] = $value[GoodsModel::$priceMember_d];
            $arr[$value[GoodsModel::$id_d]][] = $value[GoodsModel::$stock_d];
            $arr[$value[GoodsModel::$id_d]][] = $value[GoodsModel::$weight_d];
        }
        
        return $arr;
    }

    /**
     * 要更新的字段
     * @return array
     */
    protected function getColumToBeUpdated():array
    {
        return [
            GoodsModel::$priceMarket_d,
            GoodsModel::$priceMember_d,
            GoodsModel::$stock_d,
            GoodsModel::$weight_d,
        ];
    }
    
    /**
     * 更新数据
     *
     * @param array $data form数据
     * @param BaseModel $model 模型对象
     * @param string $item 要截取的 键
     *            <pre>
     *            /**
     *            update db_goods set price =
     *            CASE id
     *            WHEN 1029 THEN 4.00
     *            WHEN 1028 THEN 7.00
     *            END,
     *            stock = CASE id
     *            WHEN 1029 THEN 5
     *            WHEN 1028 THEN 8
     *            END
     *            where id in(1029,1028);
     *            </pre>
     */
    public function updateData()
    {
        $data = $this->data;
       
        if (empty($data)) {
            return $data;
        }
    
        $specData = & $data;
    
        $parseData = array();
    
        foreach ($specData as $key => & $value) {
            if ( empty($value['goods_id'])) {
                continue;
            }
            
            $value[GoodsModel::$updateTime_d] = time();
            
            $parseData[$key] = $this->modelObj->create($value);
           
            $parseData[$key][GoodsModel::$id_d] = $value['goods_id'];
           
            unset($specData[$key]);
        }
        
        
        if (! empty($data)) {
    		
        	$this->goodsData = $specData;
        	
            $this->idArray = $this->addSpecDataByGoods();
           
            if (empty($this->idArray)) {
                $this->modelObj->rollback();
            }
        }
        if (empty($parseData)) {
            return $this->idArray;
        }
        
        $this->isOpen === true ? : $this->modelObj->startTrans();
        
        $this->arrayData = $parseData;
       	try {
	        $sql = $this->buildUpdateSql();
	        
	        $status = $this->modelObj->execute($sql);
       	} catch (\Exception $e) {
       		$this->errorMessage = $sql.'----'.$e->getMessage();
       		$this->modelObj->rollback();
       		return false;
       	}
        if (! $this->traceStation($status)) {
            return false;
        }
        return $status;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getFindOne()
     */
    public function getFindOne()
    {
        $data = parent::getFindOne();
        
        $_SESSION['goods_image_p_id']    = $data[GoodsModel::$id_d];
        $_SESSION['goods_info'] = $data;
        
        $_SESSION['insertId'] = $data[GoodsModel::$id_d];
        
        return $data;
    }
    
    /**
     * 验证规格消息
     * @return bool
     */
    public function getMessageBySpec()
    {
        if (empty($this->data['item'])) {
            return false;
        }
        
        $number = null;
        
        $spec = null;
        
        foreach ($this->data['item'] as $key => $value) {
            $number = new Number($value[GoodsModel::$priceMarket_d]);
            
            if (!$number->check()) {
                $this->errorMessage = '市场价必须是数字';
                return false;
            }
            
            $number = new Number($value[GoodsModel::$priceMember_d]);
            
            if (!$number->check()) {
                $this->errorMessage = '会员价必须是数字';
                return false;
            }
            
            $number = new Number($value[GoodsModel::$stock_d]);
            
            if (!$number->check()) {
                $this->errorMessage = '库存必须是数字';
                return false;
            }
            
            $number = new Number($value[GoodsModel::$weight_d]);
            
            if (!$number->check()) {
                $this->errorMessage = '重量必须是数字';
                return false;
            }
            $spec = new SpecialCharFilter($value['sku']);
            
            if (!$spec->check()) {
                $this->errorMessage = 'sku不能有特殊字符';
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 拼接数据
     */
    public function innerJoin()
    {
        $id = $this->data['goods_id'];
        
        $children = $this->modelObj->field([
                GoodsModel::$id_d
             ])->where(GoodsModel::$pId_d.' = :p_id')
             ->bind([':p_id' => $id])
             ->select();
        return Tool::characterJoin($children, GoodsModel::$id_d);
    }
    
    /**
     * 返回父级key
     */
    public function getSplitkeyByPId()
    {
    	return GoodsModel::$pId_d;
    }



    public function getDataByGoodsId()
    {
        $data = $this->data;
        if (!empty($data)) {

            $field = [
                GoodsModel::$id_d,
                GoodsModel::$title_d,
            ];
            $goods = $this->getDataByOtherModel($field, GoodsModel::$id_d);
            return $goods;
        } else {
            return [];
        }

    }
}