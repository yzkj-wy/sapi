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

use Common\Model\UserAddressModel;

/**
 * 用户地址逻辑处理
 * @author Administrator
 */
class UserAddressLogic extends AbstractGetDataLogic
{
    
    /**
     * @return the $regionWhere
     */
    public function getRegionWhere()
    {
        return $this->regionWhere;
    }

    /**
     * 构造方法
     * @param array $data
     */
    public function __construct($data, $split)
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = new UserAddressModel();
        
        $this->covertKey = UserAddressModel::$realname_d;
    }
    
    /**
     * 根据商品信息【 查询地址】
     */
    public function getResult()
    {
        $field =  [
            UserAddressModel::$id_d,
            UserAddressModel::$provId_d,
            UserAddressModel::$realname_d,
            UserAddressModel::$mobile_d,
            UserAddressModel::$city_d,
            UserAddressModel::$dist_d,
        ];
        $orderData = $this->getDataByOtherModel($field, UserAddressModel::$id_d);
        return $orderData;
    }
    
    /**
     * 根据订单信息 查询用户信息
     */
    public function receiveManByOrder()
    {
        if ( empty($this->data['address_id'])) {
            return array();
        }
        $field = array(UserAddressModel::$id_d, UserAddressModel::$userId_d, UserAddressModel::$status_d);
        $addressInfo = $this->modelObj->field($field, true) ->where('id = %d', $this->data['address_id'])->find();
        
        return $addressInfo;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return UserAddressModel::class;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            UserAddressModel::$realname_d => [
                'required' => '请输入'.$comment[UserAddressModel::$realname_d],
            ],
            UserAddressModel::$mobile_d => [
                'required' => '请输入'.$comment[UserAddressModel::$mobile_d],
                'number' => $comment[UserAddressModel::$mobile_d].'必须是数字'
            ],
            UserAddressModel::$provId_d => [
                'required' => '请输入'.$comment[UserAddressModel::$provId_d],
                'number' => $comment[UserAddressModel::$provId_d].'必须是数字'
            ],
            UserAddressModel::$city_d => [
                'required' => '请输入'.$comment[UserAddressModel::$city_d],
                'number' => $comment[UserAddressModel::$city_d].'必须是数字'
            ],
            UserAddressModel::$dist_d => [
                'required' => '请输入'.$comment[UserAddressModel::$dist_d],
                'number' => $comment[UserAddressModel::$dist_d].'必须是数字'
            ],
            UserAddressModel::$address_d => [
                'required' => '请输入'.$comment[UserAddressModel::$address_d],
            ],
            UserAddressModel::$id_d => [
                'required' => 'id必须存在',
                'number' => 'id必须是数字'
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
            UserAddressModel::$realname_d => [
                'required' => true,
            ],
            UserAddressModel::$mobile_d => [
                'required' => true,
                'number' => true
            ],
            UserAddressModel::$provId_d => [
                'required' => true,
                'number' => true
            ],
            UserAddressModel::$city_d => [
                'required' => true,
                'number' => true
            ],
            UserAddressModel::$dist_d => [
                'required' => true,
                'number' => true
            ],
            UserAddressModel::$address_d => [
                'required' => true,
            ],
            UserAddressModel::$id_d => [
                'required' => true,
                'number' => true
            ],
        ];
        return $validate;
    }
    //修改收货地址 获取单条数据
    public function getAddressInfo(){
        $where['id'] = $this->data['id'];
        $address = M("UserAddress")->field("id,realname,mobile,prov,city,dist,address")->where($where)->find();
        if (empty($address)) {
            return array("status"=>0,"message"=>"获取失败","data"=>"");
        }
        $address['prov_name'] = M("region")->where(['id'=>$address['prov']])->getField("name");
        $address['city_name'] = M("region")->where(['id'=>$address['city']])->getField("name");
        $address['dist_name'] = M("region")->where(['id'=>$address['dist']])->getField("name");
        return array("status"=>1,"message"=>"获取成功","data"=>$address);

    }
    //修改收货地址
    public function getAddressSave(){
        $post = $this->data;
        $post['update_time'] = time();
        $res =  $this->modelObj->where($where)->save($post);
        if ($res === false) {
            return array("status"=>0,"message"=>"修改失败","data"=>"");
        }
        return array("status"=>1,"message"=>"修改成功","data"=>"");
    }
}