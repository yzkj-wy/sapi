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
declare(strict_types=1);
namespace Common\Logic;
use Common\Logic\AbstractGetDataLogic;
use Common\Model\StoreInformationModel;

class StoreInformationLogic extends AbstractGetDataLogic
{
    private $type = 0;
    /**
     * 架构方法
     * @param mixed $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        
        $this->modelObj = StoreInformationModel::getInitnation();
    }
    
    /**
     * @param number $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * 获取店铺经营信息
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        $id = (int)$this->data['store_id'];
        
        if ($id === 0) {
        	$this->errorMessage = '数据异常';
            return [];
        }
        return $this->modelObj->where(StoreInformationModel::$storeId_d.'=%d and '.StoreInformationModel::$status_d.'= '.$this->type, $id)->find();
    }
    //添加数据
    public function addData(){
        $data = $this->data;
        $res =  $this->modelObj->addInformation($data);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        } 
        M()->commit();
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }
    
    /**
     * 返回要隐藏注释的字段
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment()
    {
        return [StoreInformationModel::$status_d];
    }
    
    /**
     * 返回模型类名
     */
    public function getModelClassName ()
    {
        return StoreInformationModel::class;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            StoreInformationModel::$store_id => [
                'required' => '请输入'.$comment[StoreInformationModel::$store_id],
                'number' => $comment[StoreInformationModel::$store_id].'必须是数字'
            ],
            StoreInformationModel::$shopAccount_d => [
                'required' => '请输入'.$comment[StoreInformationModel::$shopAccount_d],
            ],
            StoreInformationModel::$shopName_d => [
                'required' => '请输入'.$comment[StoreInformationModel::$shopName_d],
            ],
            StoreInformationModel::$levelId_d => [
                'required' => '请输入'.$comment[StoreInformationModel::$levelId_d],
            ],
            StoreInformationModel::$shopLong_d => [
                'required' => '请输入'.$comment[StoreInformationModel::$shopLong_d],
            ],
            StoreInformationModel::$shopClass_d => [
                'required' => '请输入'.$comment[StoreInformationModel::$shopClass_d],
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
            StoreInformationModel::$store_id => [
                'required' => true,
                'number' => true
            ],
            StoreInformationModel::$shopAccount_d => [
                'required' => true,
            ],
            StoreInformationModel::$shopName_d => [
                'required' => true,
            ],
            StoreInformationModel::$levelId_d => [
                'required' => true,
            ],
            StoreInformationModel::$shopLong_d => [
                'required' => true,
            ],
            StoreInformationModel::$shopClass_d => [
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
    		StoreInformationModel::$storeId_d => [
    			'number' => '店铺编号必须是数字'
    		]
    	];
    }
    
    /**
     * 店铺等级关联字段
     */
    public function getSplitKeyByLevel() :string
    {
    	return StoreInformationModel::$levelId_d;
    }
    
    /**
     * 店铺等级关联字段
     */
    public function getSplitKeyByStore() :string
    {
    	return StoreInformationModel::$storeId_d;
    }
    
}