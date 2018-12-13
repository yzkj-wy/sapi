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
use Admin\Model\UserModel;
use Common\Logic\StorePersonLogic;
use Common\TraitClass\AjaxGetApprovalStoreListTrait;

class PersonApprovalController
{
    use AjaxGetApprovalStoreListTrait;
    /**
     * 控制器对象
     * @var ProductController
     */
    private $objController;
    
    private $statusByStore = [
        '已提交申请',
        '缴费完成 ',
        '审核成功',
        '审核失败',
        '缴费审核失败',
        '审核通过开店'
    ];
    
    /**
     * @param mixed 属性
     */
    private $args;
    
    /**
     * 店铺逻辑处理层对象
     */
    private $logic;
    
    private $logicClassName;
    
    private $getDisplay = '';
    
    
    public function __construct($args = null)
    {
        $this->objController = new ProductController();
        
        $this->logic = new StorePersonLogic($args);
        
        $this->args = $args;
        
        $this->logicClassName = $this->logic->getModelClassName();
        
        $this->objController->assign('model', $this->logicClassName);
    }
    
    /**
     * 列表
     */
    public function storeList()
    {
        BaseModel::getInstance(UserModel::class);
         
        //获取字段注释
        $comment = $this->logic->getComment();
        $this->objController->assign('comment', $comment);
        
        $this->objController->assign('userModel', UserModel::class);
        
        $this->objController->display();
    }
    
    /**
     * 获取店铺相关审核信息
     */
    protected function getStoreInfo()
    {
        return  $this->logic->getFindOne();
    }
}
