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

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\EditStatusTrait;
use Common\Logic\StoreLogic;
use Common\TraitClass\AjaxGetApprovalStoreListTrait;
use Admin\Logic\StoreJoinCompanyLogic;
use Admin\Logic\UserLogic;
use Common\Logic\StorePersonLogic;
use Common\Logic\StoreAddressLogic;
use PlugInUnit\Validate\CheckParam;
use Common\TraitClass\GETConfigTrait;
class StoreController
{
    use InitControllerTrait;
    use GETConfigTrait;
    use IsLoginTrait;

    use EditStatusTrait;

    use AjaxGetApprovalStoreListTrait;

    private $getDisplay = '';

    /**
     * 回调方法
     * @var array
     */
    private $invokeMethod = [
    	'person',
        'companyData'
    ];

    public function __construct( $args = null )
    {
        $this->init();

        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new StoreLogic( $args );

        $this->init();
    }


    /**
     * 获取店铺相关审核信息
     */
    protected function getStoreInfo()
    {
    	$type = $this->logic->getStoreType();
    
    	$this->type = $type;
    	
        $method = $this->invokeMethod[ $type ];

        return $this->$method();
    }

    /**
     * 设置结算周期
     */
    public function billCycle()
    {
        $checkParam = new CheckParam( $this->args );

        $vaildate = [ 'id','bill_cycle' ];

        $isNumeric = $checkParam->isNumeric( $vaildate );

        $isMust = $checkParam->keyExits( $vaildate );

        $this->objController->promptPjax( $isMust && $isMust,'参数错误' );

        $status = $this->logic->save();

        $this->objController->updateClient( $status,'结算周期设置' );
    }

    /**
     * 公司店铺消息
     * @return array
     */
    private function companyData() :array
    {
    	$userId = $this->logic->getStoreUserId();
    	
    	$joinStoreLogic = new StoreJoinCompanyLogic( ['id' => $userId] );

        $joinData = $joinStoreLogic->getResult();
        
        $userLogic = new UserLogic( $joinData,$joinStoreLogic->getUserSplitKey() );

        $joinData = array_merge( $joinData,$userLogic->getUserName() );
        
        return $joinData;
    }

    /**
     * 个人店铺申请
     */
    private function person() :array
    {
    	$userId = $this->logic->getStoreUserId();
    	
    	$joinStoreLogic = new StorePersonLogic( ['id' => $userId] );

        $joinData = $joinStoreLogic->getResult();
       
        $userLogic = new UserLogic( $joinData,$joinStoreLogic->getUserSplitKey() );

        $joinData = array_merge( $joinData,$userLogic->getUserName() );

        return $joinData;
    }

    /**
     * @description 查询店铺设置信息 接口
     */
    public function StoreInfo()
    {
        $data = $this->logic->getInfo();
        $data['domain_config'] = $this->getNoCacheConfig('two_domain_name_set');
        $this->objController->ajaxReturnData( $data );
    }

    /**
     * @description 保存店铺信息
     */
    public function SetUpInfo()
    {
        
        $status = $this->logic->saveStoreAndAddress();

        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $storeAddressLogic = new StoreAddressLogic($this->args, $this->logic->getSplitKeyByAddress());
        
        $checkObj = new CheckParam($storeAddressLogic->getMessageNotice(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
        
        $status = $storeAddressLogic->saveAddress();
		
        $this->objController->promptPjax($status, $storeAddressLogic->getErrorMessage());
        
        $this->objController->ajaxReturnData('');
    }
    
    /**
     * 获取店铺 粗略信息
     */
    public function getRoughInformation()
    {
    	$this->objController->ajaxReturnData($this->logic->geStoreRoughCache());
    }
    
}