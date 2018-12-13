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
namespace Common\Behavior;
use Common\Logic\StoreLogic;
use Common\Logic\StoreInformationLogic;
use Common\Model\BaseModel;
use Common\Model\StoreJoinCompanyModel;
use Common\Logic\StoreManagementCategoryLogic;
use Common\Logic\StoreBindClassLogic;
use Common\Logic\StoreSellerLogic;
use Common\Model\StoreInformationModel;
use Common\Logic\StoreAlbumClassLogic;

/**
 * 开店成功行为
 * @author 王强
 * @version 1.0.0
 */
class ShopSuccessfuly
{
    /**
     * 开店处理
     */
    public function openSuccess(array & $data)
    {
        //写入店铺表
        $storeInforLogic = new StoreInformationLogic($data);
        
        $storeInforLogic->setType($_SESSION['store_type']);
        
        $inforData = $storeInforLogic->getResult();
        
        BaseModel::getInstance(StoreJoinCompanyModel::class);
        
        $shopLogic = new StoreLogic($data, $inforData);
        
        $result = $shopLogic->getResult();
        
        if (empty($result)) {
            $data = [];
            return ;
        }
        //写入可发布的商品分类
        $classLogic = new StoreManagementCategoryLogic($data['id']);
        
        $classLogic->setType($_SESSION['store_type']);
        
        $classData = $classLogic->getShopCatgeoryClass();
       
        $bindClassLogic = new StoreBindClassLogic($classData, $result);
        
        $status = $bindClassLogic->getResult();
        
        if (empty($status)) {
            $data = [];
            return ;
        }
        
        $result['account'] = $inforData[StoreInformationModel::$shopAccount_d];
        //添加 管理员账号
        $serllerLogic = new StoreSellerLogic($result);
        
        $status = $serllerLogic->getResult();
        
        if (empty($status)) {
            $data = [];
            return ;
        }
        
        //写入相册默认
        $albumLogic = new StoreAlbumClassLogic($result);
        
        $status = $albumLogic->getResult();
        
        if (empty($status)) {
            $data = [];
            return ;
        }
    }
}