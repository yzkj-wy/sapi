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
namespace Admin\Logic;

use Common\Logic\AbstractGetDataLogic;
use Common\Model\StoreJoinCompanyModel;
use Common\TraitClass\GetStoreDataTrait;

/**
 * 会员开店申请逻辑处理
 * @author 王强
 * @version 1.0.0
 */
class StoreJoinCompanyLogic extends AbstractGetDataLogic
{
    use GetStoreDataTrait;
    /**
     * 初始化赋值属性
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
        
        $this->modelObj = StoreJoinCompanyModel::getInitnation();
    }
    
    /**
     * 获取详情页表注释
     */
    public function detailComment ()
    {
        
        $field = [StoreJoinCompanyModel::$createTime_d, StoreJoinCompanyModel::$updateTime_d];
        
        return $this->modelObj->getComment($field);
        
    }
    
    /**
     * 添加 数据；
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getFindOne()
     */
    public function getFindOne()
    {
        $data = parent::getFindOne();
        
        if (empty($data)) {
            return [];
        }
        
        $model = $this->modelObj;
        
        $_SESSION['approval_status'] = $data[$model::$status_d];
        
        return $data;
    }
    
    
    /**
     * 要隐藏的字段
     * @return []
     */
    protected function hideenComment()
    {
        return  [
            StoreJoinCompanyModel::$registeredCapital_d,
            StoreJoinCompanyModel::$validityStart_d,
            StoreJoinCompanyModel::$validityEnd_d,
            StoreJoinCompanyModel::$electronicVersion_d,
            StoreJoinCompanyModel::$organizationElectronic_d,
            StoreJoinCompanyModel::$organizationCode_d,
            StoreJoinCompanyModel::$taxpayerCertificate_d,
            StoreJoinCompanyModel::$remark_d,
            StoreJoinCompanyModel::$storeAddress_d,
        ];
    }
    
    /**
     * 返回用户分割键
     * @return string
     */
    public function getUserSplitKey ()
    {
        return StoreJoinCompanyModel::$userId_d;
    }
    
    /**
     * 返回模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return StoreJoinCompanyModel::class;
    }
}