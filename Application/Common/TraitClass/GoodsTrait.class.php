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

use Admin\Logic\GoodsImagesLogic;
use Common\Behavior\ShopSuccessfuly;
use Common\Logic\GoodsLogic;
use Common\Model\BaseModel;
use Common\Logic\BrandLogic;
use Admin\Model\GoodsClassModel;
use Common\Tool\Tool;
use Common\Logic\StoreLogic;
use Common\Model\StoreModel;
use Admin\Logic\GoodsClassLogic;

/**
 * 商品操作公共部分 
 * @author 王强
 */
trait GoodsTrait
{
    /**
     * 商品分类数组
     * @var array
     */
    private $classData = [];
    
    private $displayPage= 'Goods/lookGoods';
    
    /**
     * 商品列表公共部分
     */
    protected function commonList()
    {
//        $brandLogic = new BrandLogic();
//
//        $brandList = $brandLogic->getResult();
        
        //获取商品列表
        $classData = (new GoodsClassLogic($this->args))->getClassDataByStatus();
        
        
        $_SESSION['class_data'] = $classData;
        //获取商品品牌列表
//        $array['brandList'] = $brandList;
        //获取商品分类列表
        $array['classList'] = $classData;

        //获取审核状态列表
//        $array['approval_status'] = array_flip(C('approval_status'));

        return $array;
        
    }
    
    /**
     * 列表
     */
    public function approvalList()
    {

        $data = $this->commonList();

        $this->objController->ajaxReturnData($data);
    }
    
    /**
     * 获取顶级分类
     * @param int $id
     * @param int $forNumber
     * @return unknown
     */
    public function getTopClass (&$id, $forNumber = 2)
    {
        $this->objController->errorNotice($id);
    
        $classModel = BaseModel::getInstance(GoodsClassModel::class);
    
        $classId  = $classModel->getTop($id, $forNumber);
    
        return $classId;
    }
    
    /**
     * 商品分类接口分级获取
     */
    public function goodsCategory()
    {
        $validata = ['class_name'];
        Tool::checkPost($_POST, ['is_numeric' => $validata], true, $validata) ? : $this->objController->ajaxReturnData(null, 0, '参数错误');
    
        $result = D("GoodsClass")->getListByCondition($_POST['class_name']);
    
        $this->objController->updateClient($result);
    }
    
    /**
     * 查看商品
     * @param number $id spu编号
     * @return
     */
    public function lookGoods ()
    {
        //检测编号
        $this->objController->errorArrayNotice($this->args);
    
        $id = $this->args['id'];
    
        Tool::connect('parseString');
        
        $imageType = C('image_type');
        
        $goodsData  = $this->logic->getGoodsDataByParentId(BaseModel::getInstance(GoodsClassModel::class));
        
        $storeLogic = new StoreLogic($goodsData);
        
        $storeLogic->setSplitKey($this->logic->getStoreSplitKey());
        
        $goodsData = $storeLogic->getStoreData();
        
        $this->objController->assign('approvalStatus', C('approval_status'));
        
        $this->objController->assign('storeModel', $storeLogic->getModelClassName());
        
        $this->objController->assign('proGoods', $goodsData);
        
        $this->objController->assign('comment', $this->logic->getCommentByGoodsDetail());
        
        $this->objController->assign('goodsClassModel', GoodsClassModel::class);
    
        $this->objController->assign('imageType', $imageType);
        
        $this->objController->assign('jsonImageType', json_encode($imageType));
        
        return $this->objController->display($this->displayPage);
    }
    
    /**
     * 上下架
     */
    public function isShelves()
    {
        $this->validate = ['id', 'shelves'];
    
        $this->statusUpdate();
    }
    
    /**
     * 状态修改（多商品）
     */
    private function statusUpdate()
    {
        $this->isCheckShelves();
    
        $status = $this->logic->saveData();
         
        $this->objController->updateClient($status, '上架');
    }
    
    //检测是否上架
    private function isCheckShelves()
    {
        $this->check();
    
        $status = $this->logic->isAproval();
    
        $this->objController->promptPjax($status, '审核未通过，不能执行该操作');
    }
    
    /**
     * 单个商品修改
     */
    private function singleCommodity()
    {
        $this->isCheckShelves();
    
        $status = $this->logic->singleCommodity();
         
        $this->objController->updateClient($status, '上架');
    }
    
    /**
     * 修改其中一个 商品的上架状态
     */
    public function isShelve()
    {
        $this->validate = ['id', 'shelves'];
    
        $this->singleCommodity();
    }
    
    
    /**
     * 推荐（商品组）
     */
    public function isRecommends()
    {
        $this->validate = ['id', 'recommend'];
    
        $this->statusUpdate();
    }
    
    /**
     * 推荐（单个商品）
     */
    public function isRecommend()
    {
        $this->validate = ['id', 'recommend'];
    
        $this->singleCommodity();
    }
    
    /**
     * 申请列表数据
     */
    public function ajaxGetApprovalList()
    {

        //获取筛选信息
        $goodsLogic = new GoodsLogic($this->args);
        $data = $goodsLogic->getDataList();

        $goodsImageLogic = new GoodsImagesLogic($data['data'], $this->logic->getIdSplitKey());

        Tool::connect('parseString');

        $goodsData = $goodsImageLogic->getSlaveDataByMaster();

        showData($goodsData, 1);

        //获取店铺信息（组装搜索条件）

//        $split = $this->logic->getStoreSplitKey();
//
//        $storeLogic->setSplitKey($split);
//
//        Tool::connect('parseString');
//
//        $storeWhere = $storeLogic->getAssociationCondition();
//
//        $this->logic->setAssociationWhere($storeWhere);
//
//        $data = $this->getDataSource();
        //获取店铺信息
//        $storeLogicCopy = clone $storeLogic;
//
//        $storeLogicCopy->setData($data['data']);
//
//        $storeLogicCopy->setSplitKey($split);
//
//        $data['data'] = $storeLogicCopy->getStoreData();

        //品牌
//        $brandLogic = new BrandLogic();
//
//        $brand = $brandLogic->getResult();

//        $comment = $this->logic->getComment();

//        $imageType = C('image_type');

//        $this->objController->assign('storeModel', $storeLogic->getModelClassName());

//        $this->objController->assign('comment', $comment);

//        $this->objController->assign('data', $data);

//        $this->objController->assign('brand', $brand);

//        $this->objController->assign('imageType', $imageType);

//        $this->objController->assign('jsonImageType', json_encode($imageType));

//        $this->objController->assign('classData', $_SESSION['class_data']);

//        $this->objController->assign('approvalStatus', C('approval_status'));

        $this->objController->ajaxReturnData($data);
    }
    
    /**
     * 获取数据
     */
    protected function getDataSource()
    {
        return $this->logic->getDataList();
    }
    
}