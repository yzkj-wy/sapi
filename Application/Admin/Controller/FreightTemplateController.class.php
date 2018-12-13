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

use Common\Tool\Tool;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\FreightsLogic;
use Common\Logic\SendAddressLogic;
use Common\Logic\StoreLogic;

/**
 * 运费模板控制器 
 */
class FreightTemplateController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    private $method = [
        '按件',
        '按重量',
        '按体积'
    ];
    
    private $shippType = [
        '自定义运费',
        '卖家包邮'
    ];
    
    private $areaURL = [
        '/adminprov.php/FreightTemplate/addArea',
        '/adminprov.php/FreightTemplate/saveEdit'
    ];
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->isNewLoginAdmin();
    
        $this->args = $args;
    
        $this->logic = new FreightsLogic($args);
    
        $this->init();
    }
    
    /**
     * 运费模板列表 
     */
    public function lists()
    {
        $this->objController->assign('tableComment', $this->logic->tableComment());
        
        $this->objController->assign('comment', $this->logic->getComment());
        
        $this->objController->assign('rules', json_encode($this->logic->getCheckValidate()));
        
        $this->objController->assign('message', json_encode($this->logic->getSearchMessageNotice()));
        
        $this->objController->display();
    }
    
    /**
     * ajax获取数据
     */
    public function ajaxGetDataList()
    {
        Tool::connect('parseString');
        
        $storeLogic = new StoreLogic($this->args);
        
        $storeLogic->setSplitKey($this->logic->getStoreSplitKey());
        
        $where = $storeLogic->getAssociationCondition();
        
        $this->logic->setAssociationWhere($where);
        
        $freihtsData = $this->logic->getDataList();
       
        $this->objController->promptPjax($freihtsData['data']);
        
        $sendLogic = new SendAddressLogic($freihtsData['data'], $this->logic->getStockSplitKey());
        
        $freihtsData['data'] = $sendLogic->getSendAddressDataByFreight();
        
        $storeLogic->setData($freihtsData['data']);
        
        $freihtsData['data'] = $storeLogic->getStoreData();
        
        $imageType = C('image_type');
        
        $this->objController->assign('sendModel', $sendLogic->getModelClassName());
        
        $this->objController->assign('comment', $this->logic->getComment());
        
        $this->objController->assign('data', $freihtsData);
        
        $this->objController->assign('shippType', $this->shippType);
        
        $this->objController->assign('imageType', $imageType);
        
        $this->objController->assign('conditionURL', U('FreightCondition/specifyCondition', '', false));
        
        $this->objController->assign('modifyURL', U('modifyHtml', '', false));
        
        $this->objController->assign('imageTypeJson', json_encode($imageType));
        
        $this->objController->assign('method', $this->method);
        
        $this->objController->assign('storeModel', $storeLogic->getModelClassName());
        
        $this->objController->display();
    }
    
    /**
     * 添加页面 
     */
    public function addTemplateHtml ()
    {
        //获取发货仓库
        $sendAddressLogic  = new SendAddressLogic([]);
        
        $stock = $sendAddressLogic->getStatusOpenStock();
        
        $this->assiginTemplate();
        
        $this->objController->assign('stock', $stock);
        
        $this->objController->display();
    }
    
    /**
     * 编辑 
     * @param string $id 数据编号
     */
    public function modifyHtml ()
    {
        $this->objController->errorArrayNotice($this->args);
        
        //获取仓库
        $stock  = (new SendAddressLogic([]))->getStatusOpenStock();
        
        $freigthsData = $this->logic->getFindOne();
        
        $sendAddressLogic = new SendAddressLogic($freigthsData, $this->logic->getStockSplitKey());
        
        $this->assiginTemplate();
        
        $this->objController->assign('freightsData', $freigthsData);
        
        $this->objController->assign('stock', $stock);
        
        $this->objController->display();
        
    }
    
    /**
     * 页面状态赋值
     */
    private function assiginTemplate()
    {
        $this->objController->assign('comment', $this->logic->getComment());
        
        $this->objController->assign('doYouMailIt', C('do_you_mail_it'));
        
        $this->objController->assign('specifyConditionalMail', C('specify_conditional_mail'));
        
        $this->objController->assign('chargingMode', C('charging_mode'));
        
        $this->objController->assign('message', json_encode($this->logic->getMessageNotice()));
        
        $this->objController->assign('rules', json_encode($this->logic->getCheckAddOrUpdateValidate()));
    }
    
    /**
     * save 保存数据 
     */
    public function saveFreight ()
    {
       
        $this->checkPost();
        
        $status = $this->logic->saveData();
        
        $this->returnClient($status);
      
    }
    
    private function checkPost()
    {
        $status = $this->logic->checkParam();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
    }
    
    private function returnClient($status)
    {
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $this->objController->ajaxReturnData([
            'url' => U('lists')
        ]);
    }
    
    /**
     * 添加数据 
     * Array
        (
            [express_title] => 四川
            [stock_id] => 1
            [send_time] => 2
            [is_free_shipping] => 1
            [valuation_method] => 1
            [is_select_condition] => 0
        )
     */
    public function addFreights ()
    {
        $this->checkPost();
        
        $status = $this->logic->addData();
        
        $this->returnClient($status);
    }
}