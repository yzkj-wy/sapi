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

namespace Admin\Controller;

use Common\Controller\AuthController;
use Common\Model\BaseModel;
use Admin\Model\GroupModel;
use Common\TraitClass\SearchTrait;
use Common\Tool\Tool;
use Admin\Model\GoodsModel;

/**
 * 团购管理
 */
class GroupController extends AuthController
{
    use SearchTrait;

    /**
     * 添加 团购 
     */
    public function addGroupData()
    {
        Tool::checkPost($_POST, array('is_numeric' => array('goods_id', 'price', 'goods_num')), true, array(
            'goods_id', 'price', 'goods_num', 'title', 'end_time','start_time'
        )) ? true : $this->ajaxReturnData(null, 0, '参数错误');
        
        $model = BaseModel::getInstance(GroupModel::class);
       
        //是否存在
        
        $data = $model->isExist($_POST['goods_id']);

        if(empty($data)){//添加
            $status = $model->addProGoods($_POST);

        }else{//修改
            $status = $model->addProGoods($_POST,'save');
        }

        $this->promptPjax($status, '失败');

        return $this->updateClient($status, '添加');
    }
    
    /**
     *  团购删除
     */
    public function deleteData()
    {
        Tool::checkPost($_POST, array('is_numeric' => array('id')), true, array('id')) ? true : $this->ajaxReturnData(null, 0, '操作失败');
        
        $status = BaseModel::getInstance(GroupModel::class)->where(GroupModel::$id_d.'= "%s"', $_POST['id'])->delete();
        
        return $this->updateClient($status, '删除');
    }

    //团购列表
    public function groupList(){
        $model=BaseModel::getInstance(GroupModel::class);
        $r=$model->getList($_POST);

        $this->ajaxReturnData($r);
    }

}