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

use Common\Logic\StoreGradeLogic;
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\EditStatusTrait;
use Common\Tool\Extend\CheckParam;

/**
 * 店铺等级控制器
 * @author 王强
 */
class ShopTypeController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use EditStatusTrait;
    
    private $keyExits = [
        'id',
        'level_name',
        'goods_limit',
        'album_list',
        'space_limit',
        'template_number',
        'price',
        'status'
    ];
    
    /**
     * @var bool
     */
    public function __construct($args = null)
    {
        $this->isNewLoginAdmin();
       
        $this->args = $args;
        
        $this->logic = new StoreGradeLogic($args);
        
        $this->init();
        
    }
    
    /**
     * 等级列表
     */
    public function shopList()
    {
        $list = $this->logic->getNoPageList();
        
        $imageType = C('image_type');
        
        //注释
        $comment = $this->logic->getComment();
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->assign('data', $list);
        
        $this->objController->assign('image_type', $imageType);
        
        $this->objController->assign('jsonImageType', json_encode($imageType));
        
        $this->objController->assign('table_comment', $this->logic->tableComment());
        
        $this->objController->display();
    }
    
    /**
     * 编辑等级
     */
    public function editGrade ()
    {
        $this->objController->errorArrayNotice($this->args);
        
        $comment = $this->logic->getComment();
        
        $data = $this->logic->getFindOne();
        
        $this->objController->assign('storeClass', C('store_class_status'));
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->assign('data', $data);
        
        $this->objController->display();
    }
    
    /**
     * 保存
     */
    public function saveGrade()
    {
        $this->addOrSaveNotify();
        
        $status = $this->logic->save();
        
        $this->objController->updateClient($status, '更新');
    }
    
    /**
     * 添加店铺等级页面
     */
    public function addGradeHtml()
    {
        $comment = $this->logic->getComment();
        
        $this->objController->assign('storeClass', C('store_class_status'));
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->display();
    }
    
    /**
     * 添加
     */
    public function addGrade()
    {
        $this->addOrSaveNotify();
    
        $status = $this->logic->add();
    
        $this->objController->promptPjax($status, $this->logic->getError());
        
        $this->objController->updateClient($status, '添加');
    }
    
    /**
     * 添加更新提示
     */
    private function addOrSaveNotify()
    {
        $checkObj = new CheckParam($this->args);
        
        $kExits = $this->keyExits;
        
        $isExits  = $checkObj->keyExits($kExits);
        
        unset($kExits[1]);
        
        $isNumeric = $checkObj->isNumeric($kExits);
        
        $this->objController->promptPjax($isNumeric && $isExits, '数据类型不正确');
    }
}