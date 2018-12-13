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

use Common\Model\StoreManagementCategoryModel;

/**
 * 店铺经营类目逻辑处理
 * @author 王强
 * @version 1.0
 */
class StoreManagementCategoryLogic extends AbstractGetDataLogic
{
    /**
     * 入驻类型
     * @var int 0 公司入驻 1 个人入驻
     */
    private $type = 0;
    
    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * 架构函数
     * @param unknown $data
     */
    public function __construct(array $data, $splitKey = '')
    {
        $this->data = $data;
        $this->splitKey = $splitKey;
        $this->modelObj = StoreManagementCategoryModel::getInitnation();
    }
    
    /**
     * 获取数据
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
       $data = $this->getShopCatgeoryClass();
       
       if (empty($data)) {
           return [];
       }
       
       $strIds = '';
       
       foreach ($data as $key => $value) {
        
           $strIds .= ','.$value[StoreManagementCategoryModel::$oneClass_d].','.$value[StoreManagementCategoryModel::$twoClass_d].','.$value[StoreManagementCategoryModel::$threeClass_d]; 
       }
       
       return substr($strIds, 1);
       
    }
    
    /**
     * 返回要隐藏的注释，
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    protected function hideenComment()
    {
        return [
            StoreManagementCategoryModel::$id_d,
            StoreManagementCategoryModel::$storeId_d,
            StoreManagementCategoryModel::$status_d
        ];
    }
    
    /**
     * 获取店铺经营分类
     * @return array
     */
    public function getShopCatgeoryClass()
    {
         
        if (!isset($this->data[$this->splitKey])) {
            return [];
        }
       
        $data = $this->modelObj->where(StoreManagementCategoryModel::$storeId_d.'=%d', $this->data[$this->splitKey])->select();
        
        return $data;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        // TODO Auto-generated method stub
        return StoreManagementCategoryModel::class;
    }
    //添加数据
    public function addData(){
        $data = $this->data;
        $res =  $this->modelObj->addManagement($data);
        
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */

    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            StoreManagementCategoryModel::$storeId_d => [
                'required' => '请输入'.$comment[StoreManagementCategoryModel::$storeId_d],
                'number' => $comment[StoreManagementCategoryModel::$storeId_d].'必须是数字'
            ],
            StoreManagementCategoryModel::$oneClass_d => [
                'required' => '请输入'.$comment[StoreManagementCategoryModel::$oneClass_d],
                'number' => $comment[StoreManagementCategoryModel::$oneClass_d].'必须是数字'   
            ],
            StoreManagementCategoryModel::$twoClass_d => [
                'required' => '请输入'.$comment[StoreManagementCategoryModel::$twoClass_d],
                'number' => $comment[StoreManagementCategoryModel::$twoClass_d].'必须是数字'
            ],
            StoreManagementCategoryModel::$threeClass_d => [
                'required' => '请输入'.$comment[StoreManagementCategoryModel::$threeClass_d],
                'number' => $comment[StoreManagementCategoryModel::$threeClass_d].'必须是数字'
            ],
            StoreManagementCategoryModel::$status_d => [
                'required' => '请输入'.$comment[StoreManagementCategoryModel::$status_d],
                'number' => $comment[StoreManagementCategoryModel::$status_d].'必须是数字'
            ],
            
        ];
        
        return $message;
    }
    
    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate()
    {
        $validate = [
            StoreManagementCategoryModel::$storeId_d => [
                'required' => true,
                'number' => true
            ],
            StoreManagementCategoryModel::$oneClass_d => [
                'required' => true,
                'number' => true  
            ],
            StoreManagementCategoryModel::$twoClass_d => [
                'required' => true,
                'number' => true
            ],
            StoreManagementCategoryModel::$threeClass_d => [
                'required' => true,
                'number' => true
            ],
            StoreManagementCategoryModel::$status_d => [
                'required' => true,
                'number' => true
            ],
        
        ];
        return $validate;
    }
}