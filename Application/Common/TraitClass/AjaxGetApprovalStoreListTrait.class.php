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

use Admin\Logic\UserLogic;
use Common\Tool\Tool;
use Common\Logic\StoreAddressLogic;
use Common\Model\RegionModel;
use Common\Model\BaseModel;
use Admin\Model\UserModel;
use Think\Hook;
use Common\Behavior\ShopSuccessfuly;
use Common\Tool\Event;
use Common\ConstClass\StoreConst;
use Common\Logic\RegionLogic;

/**
 * 获取列表
 * @author 王强
 * @version 1.0
 */
trait AjaxGetApprovalStoreListTrait
{
	/**
	 * 店铺类型
	 * @var integer
	 */
	private $type;
    /**
     * ajax 获取数据
     */
    public function ajaxGetListData()
    {
        $paramBySearch = $this->args;
    
        //字段注释
        $comment = $this->logic->getComment();
        
        // 用户搜索条件
        $searchLogic = new UserLogic($paramBySearch, $this->logic->getUserSplitKey());
    
        Tool::connect('parseString');
    
        $userWhere = $searchLogic->getSearchBuildWhere();
      
        $sellerWhere = Event::insertClassCallBack('buildSearch', $this);
        
        $where = array_merge($userWhere, $sellerWhere);
        
        $this->logic->setAssociationWhere($where);
    
        $data = $this->logic->getDataList();
        //回调处理（店铺等级什么的。。。）
        $receiveData = Event::insertClassCallBack('buildStore', [$data['data'], $this]);
        
        if (!empty($receiveData)) {
            $data['data'] = $receiveData;
        }
                
        $this->objController->isEmpty($data['data']);
    
        //克隆对象
        $userLogic = clone $searchLogic;
   
        $userLogic->setData($data['data']);
    
        $data['data'] = $userLogic->getResult();
    
        $this->objController->assign('status', $this->statusByStore);
    
        $this->objController->assign('storeList', $data);
    
        $this->objController->assign('userModel', $userLogic->getModelClassName());
    
        $this->objController->assign('comment', $comment);
    
        $this->objController->display();
    }
    
    /**
     * 查看申请人的店铺信息
     */
    public function lookStoreInfor ()
    {
    
        //公司及联系人信息
        $approvalData = $this->getStoreInfo(); 
       	
        $this->objController->promptPjax($approvalData, '数据异常');
        
        $approvalData['type'] = $this->type;
        
        $storeAddress = new StoreAddressLogic($approvalData, $this->logic->getPrimaryKey());
    
        $result = $storeAddress->getResult();
   
        $storeAddressModelName = $storeAddress->getModelClassName();
        
        $regLogic = new RegionLogic($result, $storeAddressModelName);
    	
        $result = $regLogic->getDefaultRegion();
        
        $data = [
        	'region' => $result,
        	'store' => $approvalData
        ];
        
        $this->objController->ajaxReturnData( $data );
    }
    
    /**
     * 拒绝审核
     */
    public function refuse()
    {
        $this->adopt(StoreConst::APPROVAL_FAIL);
    }
    
    /**
     * 缴费审核失败
     */
    public function paymentAudit()
    {
        
        $this->adopt(StoreConst::PAYMWNT_AUDIT_FAIL);
    }
    
    /**
     * 开店成功
     */
    public function shopSuccessfully()
    {
        Hook::add('openSuccess', ShopSuccessfuly::class);
        $this->adopt(StoreConst::SHOP_SUCCESSFULLY);
    }
    
    /**
     * 通过审核
     */
    public function adopt($arg = 2)
    {
        $data = $this->args;
    
        //检测参数
        $status = Tool::checkPost($data, ['is_numeric' => ['id'], 'remark'], true, ['id']);
    
        $this->objController->promptPjax($status, '参数错误');
    
        $this->logic->setStatus($arg);
        
        $status = $this->logic->approval();
    
        $this->objController->promptPjax($status, '审核');
        
        Hook::listen('openSuccess', $data);
        
        $this->objController->promptPjax($data, '审核失败');
        
        $this->logic->getModelObj()->commit();
        
        $this->objController->ajaxReturnData(['url' => U('storeList')]);
    }
}