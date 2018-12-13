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

use Common\Model\GoodsDetailModel;


class GoodsDetailLogic extends AbstractGetDataLogic
{

    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;

        $this->modelObj = new GoodsDetailModel();

        $this->splitKey = $split;

    }

    /**
     * 获取商品详情
     */
    public function getResult()
    {
        $field = [
            GoodsDetailModel::$id_d,
            GoodsDetailModel::$goodsId_d,
            GoodsDetailModel::$detail_d
        ];
        $data = $this->modelObj
            ->field($field)
            ->where(GoodsDetailModel::$goodsId_d.'=:g_id')
            ->bind([':g_id' => $this->data[$this->splitKey]])
            ->find();
        
        $_SESSION['detail_id'] = $data[GoodsDetailModel::$id_d];
        
        $data[GoodsDetailModel::$detail_d] = htmlspecialchars_decode($data[GoodsDetailModel::$detail_d]);
        
        $data['ssd'] = 'txtforS-h-o-p-s-N';
        
        return $data;
    }
    
    /**
     * 保存商品详情
     * @param array $data
     * @param string $key
     * @return boolean
     */
    public function saveData ():bool
    {
        $data = $this->data;
        
        if (empty($data) || empty($_SESSION['detail_id'])) {
            $this->modelObj->rollback();
            return false;
        }
        
        $detail = [
            GoodsDetailModel::$id_d => $_SESSION['detail_id'],
            GoodsDetailModel::$detail_d => $data['detail'],
            GoodsDetailModel::$goodsId_d => $data['id'],
        ];
        
        $status = $this->modelObj->save($detail);
    
        if (!$this->modelObj->traceStation($status)) {
            return false;
        }
    
        $this->modelObj->commit();
    
        return true;
    }
    
    /**
     * 删除商品详情
     * @return boolean
     */
    public function deleteGoodsDetail()
    {
        $status = $this->modelObj
            ->where(GoodsDetailModel::$goodsId_d.' = :d_id')
            ->bind([':d_id' => $this->data['id']])
            ->delete();
        if (!$this->traceStation($status)) {
            return false;
        }
        return $status;
    }
    
    
    /**
     * 获取模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return GoodsDetailModel::class;
    }

    /**
     * @return mixed|void
     */
    protected function getSlaveColumnByWhere() :string
    {
        return GoodsDetailModel::$goodsId_d;
    }

    /**
     * @return array
     */
    public function getSlaveField() :array
    {

        return [
            GoodsDetailModel::$goodsId_d,
            GoodsDetailModel::$detail_d,
        ];
    }
}