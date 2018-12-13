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
use Common\Model\StoreCompanyBankInformationModel;

/**
 * 招商入驻银行信息
 * @author Administrator
 */
class StoreCompanyBankInformationLogic extends AbstractGetDataLogic
{
    /**
     * 初始化
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        
        $this->modelObj = new StoreCompanyBankInformationModel();
    }
    /**
     * 获取账号信息
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     * @return
     */
    public function getResult()
    {
        $storeId = (int)$this->data['store_id'];
        
        if ($storeId === 0) {
            return [];
        }
        
        return $this->modelObj->where(StoreCompanyBankInformationModel::$storeId_d.'=%d', $storeId)->find();
    }
    
    /**
     * 获取模型类名
     * @return string
     */
    public function getModelClassName ()
    {
        return StoreCompanyBankInformationModel::class;
    }
    //验证数据
    public function addData($post){
        
        $post = $this->data;
        $res =  $this->modelObj->addCompanyBank($post);
        if ($res['status'] == 0) { 
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>array('store_id'=>$post['store_id']));    
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            StoreCompanyBankInformationModel::$store_id => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$store_id],
                'number' => $comment[StoreCompanyBankInformationModel::$store_id].'必须是数字'
            ],
            StoreCompanyBankInformationModel::$accountName_d => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$accountName_d],
            ],
            StoreCompanyBankInformationModel::$companyAccount_d => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$companyAccount_d],
            ],
            StoreCompanyBankInformationModel::$branchBank_d => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$branchBank_d],
            ],
            StoreCompanyBankInformationModel::$settleName_d => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$settleName_d],
            ],
            StoreCompanyBankInformationModel::$settleAccount_d => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$settleAccount_d],
            ],
            StoreCompanyBankInformationModel::$settleBank_d => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$settleBank_d],
            ],
            StoreCompanyBankInformationModel::$certificateNumber_d => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$certificateNumber_d],
            ],
            StoreCompanyBankInformationModel::$registrationElectronic_d => [
                'required' => '请输入'.$comment[StoreCompanyBankInformationModel::$registrationElectronic_d],
            ],
            
        ];
        
        return $message;
    }
    
    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate()
    {
        $validate = [
            StoreCompanyBankInformationModel::$store_id => [
                'required' => true,
                'number' => true,
            ],
            StoreCompanyBankInformationModel::$accountName_d => [
                'required' => true,
            ],
            StoreCompanyBankInformationModel::$companyAccount_d => [
                'required' => true,
            ],
            StoreCompanyBankInformationModel::$branchBank_d => [
                'required' => true,
            ],
            StoreCompanyBankInformationModel::$settleName_d => [
                'required' => true,
            ],
            StoreCompanyBankInformationModel::$settleAccount_d => [
                'required' => true,
            ],
            StoreCompanyBankInformationModel::$settleBank_d => [
                'required' => true,
            ],
            StoreCompanyBankInformationModel::$certificateNumber_d => [
                'required' => true,
            ],
            StoreCompanyBankInformationModel::$registrationElectronic_d => [
                'required' => true,
            ],
            
        ];
        return $validate;
    }
    
    /**
     * 验证数据
     */
    public function getMessageValidateStore()
    {
    	return [
    		StoreCompanyBankInformationModel::$storeId_d => [
    			'number' => '店铺编号必须是数字'
    		]
    	];
    }
}