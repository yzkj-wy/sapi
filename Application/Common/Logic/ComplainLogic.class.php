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
use Common\Model\ComplainModel;

/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class ComplainLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = [])
    {
       $this->data = $data;
       $this->splitKey = $split;
       $this->modelObj = new ComplainModel();
    }
    
    public function getResult(){}
    
    public function getModelClassName()
    {
        return ComplainModel::class;
    }
    //查询投诉数量
    public function getComplainNum(){
    	$where['accused_id'] = $_SESSION['store_id'];
    	$where['complain_state'] = 0;
    	$count = $this->modelObj->getComplainNumByWhere($where);
    	return $count;
    }
}