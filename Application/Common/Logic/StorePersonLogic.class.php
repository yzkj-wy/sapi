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

use Common\Model\StorePersonModel;
use Common\TraitClass\GetStoreDataTrait;

/**
 * 个人入驻申请逻辑处理
 * @author 王强
 * @version 1.0
 */
class StorePersonLogic extends AbstractGetDataLogic
{
    use GetStoreDataTrait;
   
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct(array $args = [], $split = '')
    {
        $this->data = $args;
        
        $this->modelObj = new StorePersonModel();
    }
    
    /**
     * 获取详情页表注释
     */
    public function detailComment ()
    {
    
        $field = [StorePersonModel::$createTime_d, StorePersonModel::$updateTime_d];
    
        return $this->modelObj->getComment($field);
    
    }
    
    /**
     * 返回用户分割键
     * @return string
     */
    public function getUserSplitKey ()
    {
        return StorePersonModel::$userId_d;
    }
    
    /**
     * 返回模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return StorePersonModel::class;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment()
    {
        return [
            StorePersonModel::$idcardPositive_d,
            StorePersonModel::$otherSide_d,
            StorePersonModel::$storeAddress_d
        ];
    }
    //添加数据
    public function addData($address){
        
        $post = $this->data; 
        $post['store_address']= $address;
        $res =  $this->modelObj->addPerson($post);
        if ($res['status'] == 0) { 
            M()->rollback();
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        M()->commit();
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>array('store_id'=>$res['data']));
    }
    //修改银行结算信息
    public function saveBank($address){
        $post = $this->data; 
        $where['id']= $post['store_id'];
        $res =  $this->modelObj->saveBank($where,$post);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>array('store_id'=>$res['data']));
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            StorePersonModel::$userId_d => [
                'required' => '请输入'.$comment[StorePersonModel::$userId_d],
                'number' => $comment[StorePersonModel::$userId_d].'必须是数字'
            ],
            StorePersonModel::$storeName_d => [
                'required' => '请输入'.$comment[StorePersonModel::$storeName_d],
            ],
            StorePersonModel::$personName_d => [
                'required' => '请输入'.$comment[StorePersonModel::$personName_d],
            ],
            StorePersonModel::$idCard_d => [
                'required' => '请输入'.$comment[StorePersonModel::$idCard_d],
            ],
            StorePersonModel::$idcardPositive_d => [
                'required' => '请输入'.$comment[StorePersonModel::$idcardPositive_d],
            ],
            
            StorePersonModel::$otherSide_d => [
                'required' => '请输入'.$comment[StorePersonModel::$otherSide_d],
            ],
            StorePersonModel::$mobile_d => [
                'required' => '请输入'.$comment[StorePersonModel::$mobile_d],
                'number' => $comment[StorePersonModel::$userId_d].'必须是数字'
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
            StorePersonModel::$userId_d => [
                'required' => true,
                'number' => true
            ],
            StorePersonModel::$storeName_d => [
                'required' => true,
            ],
            StorePersonModel::$personName_d => [
                'required' => true,
            ],
            StorePersonModel::$idCard_d => [
                'required' => true,
            ],
            StorePersonModel::$idcardPositive_d => [
                'required' => true,
            ],    
            StorePersonModel::$otherSide_d => [
                'required' => true,
            ],
            StorePersonModel::$mobile_d => [
                'required' => true,
                'number' => true
            ],            
        ];
        return $validate;
    }


}
