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

use Common\Logic\AbstractGetDataLogic;
use Common\Model\StoreMemberModel;

/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class StoreMemberLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = '')
    {
       $this->data = $data;
       
       $this->modelObj = StoreMemberModel::getInitnation();
    }
    
    /**
     * 获取店铺地址数据
     */
    public function getResult()
    {
      
    }
    
    public function getModelClassName()
    {
        return StoreAddressModel::class;
    }
    //获取店铺会员等级
    public function getStoreUserLevel(){
        $where['store_id'] = $_SESSION['store_id'];
        $level = M('storeMemberLevel')->field('id,level_id,money_big,money_small,condition_num')->where($where)->select();
        foreach ($level as $key => $value) {
            $level[$key]['level_name'] = M('store_level_by_platform')->where(['id'=>$value['level_id']])->getField('level_name');
        }
        return $level;
    }
    //获取店铺会员数量
    public function userRankAnalysis(){
        $level = $this->getStoreUserLevel();
        $userRank = array();
        foreach ($level as $key => $value) {
            $where['store_id'] = $_SESSION['store_id'];
            $where['total_transaction']  = array('between',array($value['money_small'],$value['money_big']));
            $count = M('storeMember')->where($where)->count();
            $userRank[$key]['count'] = $count;
            $userRank[$key]['level_name'] = $value['level_name']; 
        }
        return $userRank;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum() :array
     */
    protected function getTableColum() :array
    {
        return [
            StoreMemberModel::$id_d,
            StoreMemberModel::$memberId_d,
            StoreMemberModel::$totalTransaction_d,
            StoreMemberModel::$transactionNumber_d,
            StoreMemberModel::$totalTransaction_d,
            StoreMemberModel::$moneyBig_d,
            StoreMemberModel::$moneySmall_d,
            StoreMemberModel::$lastTime_d,
        ];
    }
    
    /**
     * 获取用户相关字段
     * @return string
     */
    public function getSplitKeyByUserId()
    {
        return StoreMemberModel::$memberId_d;
    }
}