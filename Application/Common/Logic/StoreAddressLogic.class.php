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
use Common\Model\StoreAddressModel;

/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class StoreAddressLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data, $split = null)
    {
       $this->data = $data;
       
       $this->splitKey = $split;
       
       $this->modelObj = StoreAddressModel::getInitnation();
    }
    
    /**
     * 获取店铺地址数据
     */
    public function getResult()
    {
        if (!isset($this->data[$this->splitKey])) {
            return [];
        }
        
        return $this->modelObj->where(StoreAddressModel::$storeId_d.'=%d', $this->data[$this->splitKey])->find();
    }
    
    public function getModelClassName()
    {
        return StoreAddressModel::class;
    }
    //验证数据
    public function addAddress(){
        $data = $this->data;
        
        $res =  $this->modelObj->addAddress($post);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
       
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            StoreAddressModel::$provId_d => [
                'required' => '请输入'.$comment[StoreAddressModel::$provId_d],
                'number' => $comment[StoreAddressModel::$provId_d].'必须是数字'
            ],
            StoreAddressModel::$city_d => [
                'required' => '请输入'.$comment[StoreAddressModel::$city_d],
                'number' => $comment[StoreAddressModel::$city_d].'必须是数字'
            ],
            StoreAddressModel::$dist_d => [
                'required' => '请输入'.$comment[StoreAddressModel::$dist_d],
                'number' => $comment[StoreAddressModel::$dist_d].'必须是数字'
            ],
            StoreAddressModel::$address_d => [
                'required' => '请输入'.$comment[StoreAddressModel::$address_d],
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
            StoreAddressModel::$provId_d => [
                'required' => true,
                'number' => true
            ],
            StoreAddressModel::$city_d => [
                'required' => true,
                'number' => true
            ],

            StoreAddressModel::$dist_d => [
                'required' => true,
                'number' => true
            ],
            
            StoreAddressModel::$address_d => [
                'required' => true,
                'number' => true
            ],
        ];
        return $validate;
    }
    
    /**
     * 
     * @return boolean
     */
    public function saveAddress()
    {
        $status = $this->saveData();
       
        if (!$this->traceStation($status)) {
            return false;
        }
        
        $this->modelObj->commit();
		
        
        return $status;
    }
    
    /**
     * 添加时处理参数
     * @return array
     */
    protected function getParseResultBySave() :array
    {
    	$data = $this->data;
        
        $data[StoreAddressModel::$id_d] = $this->data[$this->splitKey];
       
        unset($this->data[$this->splitKey]);
        
        return $data;
    }
    
}