<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Common\Controller;
use Think\Controller;
use Admin\Model\GoodsClassModel;
use Admin\Model\AuthGroupModel;
use Admin\Model\AuthRuleModel;
use Common\Model\BaseModel;
use Common\Tool\Tool;
use Common\TraitClass\NoticeTrait;
use Common\Model\PromotionTypeModel;
use Admin\Model\ProDiscountModel;
use Admin\Model\OrderModel;
use Common\Model\ExpressModel;
use Common\TraitClass\GETConfigTrait;
use Think\Hook;
use Common\Behavior\WangJinTing;
use Common\TraitClass\IsLoginTrait;

//权限认证
class AuthController extends Controller {
    
    use NoticeTrait;
    use GETConfigTrait;
    use IsLoginTrait;
    protected $addressModel = null;
    
    protected $cackeKey = 'EXPRESS_CACHE_DATA';
    
    
   
    
    protected $title = '后台管理系统';
    
	protected function _initialize(){
	    
	    Hook::add('reade', WangJinTing::class);
		
	    // $this->assign('title', $this->title);
	     
	}
	
	
	/**
	 * 获取分类 
	 */
	protected  function getClass()
	{
	    if (!S('classData'))
	    { 
	        //获取商品分类
	        
            $classData =  BaseModel::getInstance(GoodsClassModel::class)->getChildren(array(
                GoodsClassModel::$hideStatus_d => 1,
                GoodsClassModel::$fid_d        => 0,
                GoodsClassModel::$type_d       => 1,
            ), array( GoodsClassModel::$id_d,  GoodsClassModel::$className_d, GoodsClassModel::$fid_d));
        
	        S('classData', $classData, 10);
	    }
	    return  S('classData');
	}
    /**
     * 获取登录的人的权限 
     */
    protected function getPromisson()
    {
        $group = AuthGroupModel::getInitnation()->getAuthGroupById('rules', array('id' => session('group_id')), 'find');
        
        if (empty($group))
        {
            $this->error('抱歉，您没有任何权限');
        }
        
        //获取权限菜单
        $rule = AuthRuleModel::getInitnation()->getAuthGroupById('id,name,title', 'id in('.$group['rules'].')');
    } 
    
    /**
     * @param BaseModel $model
     * @return array
     */
    protected  function getGoodsClass(BaseModel $model)
    {
        if (!($model instanceof BaseModel))
        {
            return array();
        }
    
        $goodsClass = $model->getAttribute(array(
            'field' => array($model::$id_d, $model::$className_d, $model::$fid_d),
            'where' => array($model::$fid_d => 0, $model::$hideStatus_d => 1),
            'order' => array($model::$sortNum_d.' DESC')
        ), false, 'getAllClassId');
    
        //获取二级分类
        Tool::connect('parseString');
    
        $ids = Tool::characterJoin($goodsClass, 'id');
        if (!empty($ids))
        {
            $ids = str_replace('"', null, $ids);
            $children = $model->getAttribute(array(
                'field' => array($model::$id_d, $model::$className_d, $model::$fid_d),
                'where' => array($model::$fid_d => array('in', $ids), $model::$hideStatus_d => 1),
                'order' => array($model::$sortNum_d.' DESC')
            ), false, 'getAllClassId');
    
            $goodsClass = array_merge($goodsClass, (array)$children);
            Tool::connect("Tree", $goodsClass);
            $goodsClass = Tool::makeTree(array('parent_key' => $model::$fid_d));
        }
        return $goodsClass;
    }
    
    //获取分类
    
    public  function getChildren($id = 'goods_class_id')
    {
        Tool::checkPost($_POST, array('is_numeric' => array($id)),true, array($id)) ? true : $this->ajaxReturnData(null, 0, '参数错误');
    
        $model = BaseModel::getInstance(GoodsClassModel::class);
        $goodsClass = $model->getAttribute(array(
            'field' => array($model::$id_d, $model::$className_d, $model::$fid_d),
            'where' => array($model::$fid_d => $_POST[$id], $model::$hideStatus_d => 1),
            'order' => array($model::$sortNum_d.' DESC')
        ), false, 'getAllClassId');
        $this->ajaxReturnData($goodsClass);
    }
    
    /**
     * 促销类型
     */
    protected  function getProType()
    {
        //获取促销类型配置
        $promotionTypeModel = BaseModel::getInstance(PromotionTypeModel::class);
    
        $promotionData      = $promotionTypeModel->getField(
            $promotionTypeModel::$id_d.','.$promotionTypeModel::$promationName_d.','.$promotionTypeModel::$status_d
            );
    
        $this->promotionTypeModel = PromotionTypeModel::class;
    
        $this->classData  = $promotionData;
    }
    
   
    
    
    public function getAllClass()
    {
        $model = BaseModel::getInstance(GoodsClassModel::class);
        
        $result = $model->getOneAndSecondClass();
        
        $this->updateClient($result);
    }
    
    
    
    
    
    /**
     * 促销值
     */
    public function getDataTypeValue()
    {
        Tool::checkPost($_POST, array('is_numeric' => array('id')), true, array('id')) ? true : $this->ajaxReturnData(null, 0, '操作失败');
    
        $model = BaseModel::getInstance(ProDiscountModel::class);
    
        $data  = $model->getAttribute(array(
            'field' => array($model::$proId_d, $model::$proDiscount_d),
            'where' => array($model::$proId_d => $_POST['id'])
        ));
        $this->ajaxReturnData($data);
    }
    

}