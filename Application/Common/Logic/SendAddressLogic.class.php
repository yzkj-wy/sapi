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

use Admin\Model\SendAddressModel;
use Common\Model\RegionModel;
/**
 * 发货地址列表
 * @author 王强
 */
class SendAddressLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = "")
    {
        $this->data = $data;
    
        $this->splitKey = $split;
    
        $this->modelObj = new SendAddressModel();
    }
    

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult(){}
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return SendAddressModel::class;
    }
    
    /**
     * 根据模板数据 获取 发货地址数据
     */
    public function getSendAddressDataByFreight()
    {
        $field = [
            SendAddressModel::$id_d,
            SendAddressModel::$stockName_d,
        ];
        
        return $this->getDataByOtherModel($field, SendAddressModel::$id_d);
    }
    
    /**
     * 获取发货仓信息
     * @return unknown|mixed|object
     */
    public function getStatusOpenStock ()
    {
        $data = S('openSendAddress');
        if (empty($data)) {
            $data = $this->modelObj->where(SendAddressModel::$status_d.' = %d', 1)->getField(SendAddressModel::$id_d.','.SendAddressModel::$stockName_d);
        } else {
            return $data;
        }
        
        if (empty($data)) {
            return [];
        }
        S('openSendAddress', $data, 5);
        
        return $data;
    }
    //获取发货仓库列表
    public function getAddressList(){
        $where['store_id'] = $_SESSION['store_id'];
        $address = $this->modelObj->getAddressByWhere($where);
        if ($address['status'] == 1) {
            $region = new RegionModel;
            $list   = $region->getRegionByData($address['data']);
            return $list;
        }else{
            return $address;
        } 
    }
    //获取发货地址详情
    public function getAddressDetail(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $address = $this->modelObj->getAddressByID($where);
        if ($address['status'] == 1) {
            $region = new RegionModel;
            $list   = $region->getRegionByAddress($address['data']);
            return $list;
        }else{
            return $address;
        } 
    }
    //获取已开启仓库
    public function getAlreadyOpened(){
        $where['store_id'] = $_SESSION['store_id'];
        $where['status'] = 0;
        $field = "id,stock_name";
        $address = $this->modelObj->getAddressByWhere($where,$field);
        return $address;
    }
    //修改发货仓库
    public function addressSave(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $post['store_id'] = $_SESSION['store_id'];
        $post['update_time'] = time();
        $address = $this->modelObj->saveAddress($where,$post);
        return $address;
    }
    //添加发货仓库
    public function addressAdd(){
        $post = $this->data;
        $post['store_id'] = $_SESSION['store_id'];
        $post['create_time'] = time();
        $address = $this->modelObj->addressAdd($post);
        return $address;
    }
    //删除发货仓库
    public function addressDel(){
        $post = $this->data;
        $id = $post['id'];
        $address = $this->modelObj->delAddress($id);
        return $address;
    }
    //搜索仓库
    public function addressSearch(){
        $post = $this->data;
        $where['stock_name'] = array('like','%'.$post["stock_name"].'%');
        $where['store_id'] = $_SESSION['store_id'];
        $address = $this->modelObj->getAddressByWhere($where);
        if ($address['status'] == 1) {
            $region = new RegionModel;
            $list   = $region->getRegionByData($address['data']);
            return $list;
        }else{
            return $address;
        }
        
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            SendAddressModel::$stockName_d => [
                'required' => '请输入'.$comment[SendAddressModel::$stockName_d],
            ],
            SendAddressModel::$prov_d => [
                'required' => '请输入'.$comment[SendAddressModel::$prov_d],
                'number' => $comment[SendAddressModel::$prov_d].'必须是数字'
            ],
            SendAddressModel::$city_d => [
                'required' => '请输入'.$comment[SendAddressModel::$city_d],
                'number' => $comment[SendAddressModel::$city_d].'必须是数字'
            ],
            SendAddressModel::$dist_d => [
                'required' => '请输入'.$comment[SendAddressModel::$dist_d],
                'number' => $comment[SendAddressModel::$dist_d].'必须是数字'
            ],
            SendAddressModel::$addressDetail_d => [
                'required' => '请输入'.$comment[SendAddressModel::$addressDetail_d],               
            ],
            SendAddressModel::$status_d => [
                'required' => '请输入'.$comment[SendAddressModel::$status_d],
                'number' => $comment[SendAddressModel::$status_d].'必须是数字'
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
            SendAddressModel::$stockName_d => [
                'required' => true,
            ],
            SendAddressModel::$prov_d => [
                'required' => true,
                'number' => true,
            ],
            SendAddressModel::$city_d => [
                'required' => true,
                'number' => true,
            ],
            SendAddressModel::$dist_d => [
                'required' => true,
                'number' => true,
            ],
            SendAddressModel::$addressDetail_d => [
                'required' => true,
            ],
            SendAddressModel::$status_d => [
                'required' => true,
                'number' => true,
            ],
        ];
        return $validate;
    }
}