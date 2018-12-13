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

namespace Common\TypeParse\SonType;

use Common\TypeParse\AbstractParse;
use Common\TypeParse\ActionRunInterface;
use Home\Model\GoodsCartModel;
/**
 * 数组类型解析
 * @author 王强
 * @version 1.0.1
 */
class ArrayType extends AbstractParse implements ActionRunInterface
{
    private $error;
    /**
     * @return the $error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritDoc}
     * @see \Common\TypeParse\ActionRunInterface::actionRun()
     */
    public function actionRun()
    {
        // TODO Auto-generated method stub
        $data = self::$typeData;
        sort($data);
        $number = rand(0, count($data) - 1);
        return $data[$number];
    }
    /**
     * {@inheritDoc}
     * @see \Common\TypeParse\ActionRunInterface::parseDataBaseByUser($model = null)
     */
    public function parseDataBaseByUser($model = null)
    {
        // TODO Auto-generated method stub
        
        if (!is_object($model)) {
            return false;
        }
        
        $data = self::$typeData;
        
        return $model->deleteGoods($data);
    }
    
    /**
     * 处理多商品加入购物车
     * @param array $data
     */
    public function parseGoodsCart(array $data)
    {
        if (empty($data)) {
            return false;
        }
    
        $model = $this->getModel();
    
        //表的数据
        $dataBaseByCart = self::$typeData;
    
        //[data] 数据库数据
        foreach ($dataBaseByCart as $key => & $value) {
    
            if (!array_key_exists($value[GoodsCartModel::$goodsId_d],  $data)) {
                continue;
            }
            $value[GoodsCartModel::$goodsNum_d] += (int)$data[$value[GoodsCartModel::$goodsId_d]][GoodsCartModel::$goodsNum_d];
            unset($data[$value[GoodsCartModel::$goodsId_d]]);
        }
    
        $model->setData($data);
        //处理
        return $model->paeseCartByGoodsData($dataBaseByCart);
    }
    
    /**
     * 检测传参
     * @version 1.0
     */
    public function checkValue ()
    {
        $data = self::$typeData;
        
        if (empty($data)) {
            return null;
        }
        
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                array_walk_recursive($value, [$this,'parseData']);
            } else {
                if (empty($value) && $value !== 0 && $value !== '0') {
                    $this->error .= ','.$key;
                    return null;
                } else {
                    $this->parseData($value);
                }
            }
        }
        return $data;
    }
    
    private function parseData (& $param)
    {
        if (empty($param) && $param !== 0 && $param !== '0') {
            $param = null;
            return null;
        }
        $param = addslashes(strip_tags($param));
        return $param;
    }
    
}