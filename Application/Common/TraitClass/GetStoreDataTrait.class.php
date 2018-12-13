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
namespace Common\TraitClass;

/**
 * 获取店铺数据
 * @author 王强
 * @version 1.0.0
 */
trait GetStoreDataTrait 
{
    /**
     * 处理申请数据
     * @return array
     */
    public function getResult()
    {
        $userId = (int)$this->data['id'];
        
        if ($userId === 0) {
            return [];
        }
        
        $model = $this->modelObj;
        
        $field = [
            $model::$createTime_d,
            $model::$updateTime_d
        ];
         
        $data = $model->field($field, true)->where($model::$userId_d .'= %d', $userId)->find();
        if (empty($data)) {
            return [];
        }
        $_SESSION['approval_status'] = $data[$model::$status_d];
        return $data;
    }
}