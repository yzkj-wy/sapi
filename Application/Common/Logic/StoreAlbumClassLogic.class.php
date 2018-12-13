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

use Common\Model\StoreAlbumClassModel;
use Common\Model\StoreModel;

/**
 * 店铺相册逻辑处理
 * @author 王强
 */
class StoreAlbumClassLogic extends AbstractGetDataLogic
{
    public function __construct(array $data)
    {
        $this->data = $data;
        
        $this->modelObj = new StoreAlbumClassModel();
    }
    
    public function getResult()
    {
        $data = $this->data;
        
        if (empty($data)) {
            $this->modelObj->rollback();
            return self::ADD_ERROR;
        }
        
        $addData = [];
        
        $addData[StoreAlbumClassModel::$aclassName_d] = C('default_album');
        
        $addData[StoreAlbumClassModel::$storeId_d] = $data[StoreModel::$id_d];
        
        $addData[StoreAlbumClassModel::$uploadTime_d] = time();
        
        $addData[StoreAlbumClassModel::$isDefault_d] = 1;
        
        $status = $this->modelObj->add($addData);
        
        if (!$this->modelObj->traceStation($status)) {
            return self::ADD_ERROR;
        }
        
        return $status;
        
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        // TODO Auto-generated method stub
        return StoreAlbumClassModel::class;
    }

}