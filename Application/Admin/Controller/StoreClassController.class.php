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
namespace Admin\Controller;

use Common\Controller\ProductController;
use Common\Model\BaseModel;
use Common\Model\StoreClassModel;
use Common\Tool\Tool;
use Common\Logic\StoreClassLogic;
use Common\TraitClass\IsLoginTrait;

class StoreClassController 
{
    use IsLoginTrait;
    /**
     * 控制器对象
     * @var ProductController
     */
    private $objController;
    
    /**
     * @param mixed 属性
     */
    private $args;
    
    public function __construct($args = null)
    {
        $this->isNewLoginAdmin();
        
        $this->args = $args;
    }
    
    /**
     * 店铺分类列表
     */
    public function classList()
    {
        //获取字段注释
        $model = BaseModel::getInstance(StoreClassModel::class);
        
        $comment = $model->getComment();
        
        //分类不会太多不必分页
        
        $data = $model->select();
        
        $imageType = C('image_type');
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->assign('data', $data);
        
        $this->objController->assign('model', StoreClassModel::class);
        
        $this->objController->assign('url_sort', U('updateSort'));
        
        $this->objController->assign('image_type', $imageType);
        
        $this->objController->assign('jsonImageType', json_encode($imageType));
        
        $this->objController->display();
    }
    
    /**
     * 添加店铺 页面
     */
    public function addStoreClass ()
    {
        $model = BaseModel::getInstance(StoreClassModel::class);
        
        $comment = $model->getComment();
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->assign('model', StoreClassModel::class);
        
        $this->objController->assign('storeClass', C('store_class_status'));
        
        $this->objController->display();
    }
    
    public function editStoreClass ()
    {
        $id = $this->args;
        
        $this->objController->errorArrayNotice($id);
        
        $model = BaseModel::getInstance(StoreClassModel::class);
        
        $comment = $model->getComment();
        
        $data = $model->find($id['id']);
        
        $this->objController->assign('storeClass', C('store_class_status'));
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->assign('model', StoreClassModel::class);
        
        $this->objController->assign('data', $data);
        
        $this->objController->display();
        
    }
    
    /**
     * 发送到数据表
     */
    public function getDataSendTable ()
    {
        $storData = $this->args;
        
        $validate = ['status', 'sc_bail', 'sc_sort'];
        
        Tool::checkPost($storData, ['is_numeric' => $validate], true, $validate) ? : $this->objController->ajaxReturnData(null, 0, '参数错误');
        
        $storeLogic = new StoreClassLogic($storData);
        
        $status = $storeLogic->addStore();
        
        $this->objController->promptPjax($status, $storeLogic->getError());
        
        $this->objController->updateClient($status, '添加');
    }
    
    /**
     * 店铺分类状态是否开启
     */
    public function editIsOpen ()
    {
        $args = $this->args;
        $validate = ['id', 'status'];
        Tool::checkPost($args, ['is_numeric' => $validate], true, $validate) ? : $this->objController->ajaxReturnData(null, 0, '修改失败');
    
        $status = BaseModel::getInstance(StoreClassModel::class)->save($args);
    
        $this->objController->updateClient($status, '修改');
    }
    
    /**
     * 更新排序
     */
    public function updateSort ()
    {
        $sortData = $this->args;
        $validate = ['id', 'sc_sort'];
        Tool::checkPost($sortData, ['is_numeric' => $validate], true, $validate) ? : $this->objController->ajaxReturnData(null, 0, '修改失败');
        
        $model = BaseModel::getInstance(StoreClassModel::class);

        $status = $model->save($sortData);
        
        $this->objController->updateClient($status, '修改');
    }
    
    /**
     * 更新数据发送到数据表
     */
    public function updateSendTable ()
    {
        $storData = $this->args;
        $validate = ['status', 'sc_bail', 'sc_sort'];
    
        Tool::checkPost($storData, ['is_numeric' => $validate], true, $validate) ? : $this->objController->ajaxReturnData(null, 0, '参数错误');
    
        $storeLogic = new StoreClassLogic($storData);
    
        $status = $storeLogic->saveStore();
    
        $this->objController->promptPjax($status, $storeLogic->getError());
    
        $this->objController->updateClient($status, '修改');
    }
    
    /**
     * 删除店铺
     */
    public function removeStore()
    {
        $id = $this->args;
        
        $validate = ['id'];
        
        $status = Tool::checkPost($id, ['is_numeric' => $validate], true, $validate);
       
        $this->objController->promptPjax($status, '参数错误');
        
        $model = BaseModel::getInstance(StoreClassModel::class);
        
        $status = $model->delete($id[StoreClassModel::$id_d]);
        
        $this->objController->updateClient($status, '删除');
    }
}