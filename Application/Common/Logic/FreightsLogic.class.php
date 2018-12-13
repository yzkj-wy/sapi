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

use Admin\Model\FreightsModel;
use Common\TraitClass\TemplateTrait;
use Common\Tool\Extend\PinYin;
use Think\Cache;

/**
 * 运费模板逻辑处理
 * 
 * @author Administrator
 */
class FreightsLogic extends AbstractGetDataLogic
{
    use TemplateTrait;
    /**
     * 构造方法
     * @param array $data            
     * @param string $split            
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = new FreightsModel();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
    	//获取
    	$cache = Cache::getInstance('', ['expire' => 100]);
    	
    	$key = 'wds'.$_SESSION['store_id'].'dds';
    	
    	$data = $cache->get($key);
    	
    	if (empty($data)) {
    		$data = $this->modelObj
    			->where(FreightsModel::$storeId_d.'=:id')
    			->bind([':id' => $_SESSION['store_id']])
    			->getField(FreightsModel::$id_d.','.FreightsModel::$expressTitle_d);
    	} else {
    		return $data;
    	}
    	
    	if (empty($data)) {
    		return [];
    	}
    	
    	$cache->set($key, $data);
    	return $data;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return FreightsModel::class;
    }

    /**
     * 获取仓库关联字段
     */
    public function getStockSplitKey()
    {
        return FreightsModel::$stockId_d;
    }

    /**
     * 搜索时 条件验证数据
     * 
     * @return string[][]
     */
    public function getSearchMessageNotice()
    {
        $comment = $this->getComment();
        
        $message = [
            FreightsModel::$expressTitle_d => [
                'specialCharFilter' => $comment[FreightsModel::$expressTitle_d] . '不能输入特殊字符'
            ]
        ];
        return $message;
    }

    /**
     * 获取验证规则
     * @return string
     */
    public function getCheckValidate()
    {
        $validate = [
            FreightsModel::$expressTitle_d => [
                'specialCharFilter' => true
            ]
        ];
        return ($validate);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            FreightsModel::$expressTitle_d => [
                'required' => '请输入' . $comment[FreightsModel::$expressTitle_d],
                'specialCharFilter' => $comment[FreightsModel::$expressTitle_d] . '不能输入特殊字符'
            ],
            FreightsModel::$sendTime_d => [
                'required' => '请输入' . $comment[FreightsModel::$sendTime_d],
                'number' => $comment[FreightsModel::$sendTime_d] . '必须是数字'
            ],
            FreightsModel::$isFree_shipping_d => [
                'required' => '请输入' . $comment[FreightsModel::$isFree_shipping_d],
                'number' => $comment[FreightsModel::$isFree_shipping_d] . '必须是数字'
            ],
            
            FreightsModel::$valuationMethod_d => [
                'required' => '请输入' . $comment[FreightsModel::$valuationMethod_d],
                'number' => $comment[FreightsModel::$valuationMethod_d] . '必须是数字'
            ],
            
            FreightsModel::$isSelect_condition_d => [
                'required' => '请输入' . $comment[FreightsModel::$isSelect_condition_d],
                'number' => $comment[FreightsModel::$isSelect_condition_d] . '必须是数字'
            ],
            
            FreightsModel::$stockId_d => [
                'required' => '请输入' . $comment[FreightsModel::$stockId_d],
                'number' => $comment[FreightsModel::$stockId_d] . '必须是数字'
            ]
        ];
        return $message;
    }

    /**
     * @return boolean[][]
     */
    public function getCheckAddOrUpdateValidate()
    {
        $message = [
            FreightsModel::$expressTitle_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            FreightsModel::$sendTime_d => [
                'required' => true,
                'number' => true
            ],
            FreightsModel::$isFree_shipping_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightsModel::$valuationMethod_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightsModel::$isSelect_condition_d => [
                'required' => true,
                'number' => true
            ],
            
            FreightsModel::$stockId_d => [
                'required' => true,
                'number' => true
            ]
        ];
        return $message;
    }

    /**
     * 获取模板
     */
    public function getTemplate()
    {
        $data = $this->modelObj->getField(FreightsModel::$id_d . ',' . FreightsModel::$expressTitle_d);
        
        if (empty($data)) {
            return array();
        }
        
        $pinObj = new PinYin();
        
        foreach ($data as $key => & $value) {
            $pinObj->setStr($value);
            $value = $pinObj->getFirstEnglish() . ' ' . $value;
        }
        return $data;
    }
    
    /**
     * @return string
     */
    protected function getSelectField()
    {
        return FreightsModel::$id_d.','.FreightsModel::$expressTitle_d;
    }
    
    /**
     * 商户关联字段
     */
    public function getStoreSplitKey()
    {
        return FreightsModel::$storeId_d;
    }
    //运费模板列表
    public function getFreightList(){
        $where['store_id'] = $_SESSION['store_id'];
        $field = "id,express_title,send_time,is_select_condition,valuation_method,is_free_shipping,stock_id";
        $freights = $this->modelObj->getFreightListByWhere($where,$field);
        if ($freights['status'] == 1) {
            //获取仓库信息
            $list = D('SendAddress')->getSendAddressByData($freights['data']);
            return $list;
        }else{
            return $freights;
        }
        
    }
    //运费模板详情
    public function getFreightDetail(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $field = "id,express_title,send_time,is_select_condition,valuation_method,is_free_shipping,stock_id";
        $freights = $this->modelObj->getFreightListById($where,$field);
        //获取仓库信息
        if ($freights['status'] == 1) {
            //获取仓库信息
            $list = D('SendAddress')->getSendAddressByOne($freights['data']);
            return $list;
        }else{
            return $freights;
        }
    }
    //添加运费模板
    public function freightAdd(){
        $post = $this->data;
        $post['create_time'] = time();
        $post['store_id'] = $_SESSION['store_id'];
        $res = $this->modelObj->addFreight($post);
        return $res;
    }
    //编辑运费模板
    public function freightSave(){
        $post = $this->data;
        $post['update_time'] = time();
        $where['id'] = $post['id'];
        $res = $this->modelObj->saveFreight($where,$post);
        return $res;
    }
    //删除运费模板
    public function freightDel(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $res = $this->modelObj->delFreight($where);
        return $res;
    }
    //搜索运费模板
    public function freightSearch(){
        $express_title = $this->data['express_title'];
        $where['store_id'] = $_SESSION['store_id'];
        $where['express_title'] = array("like","%".$express_title."%");
        $field = "id,express_title,send_time,is_select_condition,valuation_method,is_free_shipping,stock_id";
        $freights = $this->modelObj->getFreightListByWhere($where,$field);
        if ($freights['status'] == 1) {
            //获取仓库信息
            $list = D('SendAddress')->getSendAddressByData($freights['data']);
            return $list;
        }else{
            return $freights;
        }
    }
}                  