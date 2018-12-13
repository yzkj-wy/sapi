<?php
namespace Common\Logic;
use Admin\Model\CouponModel;
use Admin\Model\CouponListModel;
use Common\Logic\AbstractGetDataLogic;

/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class CouponsLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split = null)
    {
        $this->data = $data;

        $this->modelObj = new CouponModel();

    }
    public function getResult()
    {

    }

    public function getModelClassName()
    {
        return StoreAddressModel::class;
    }
	//优惠券列表
	public function logList(){
	
	    $data=$this->data;
	
	    return $this->modelObj->couponList($data);
	}


	//添加优惠券
	public function logAddCoupon(){
	    $data=$this->data;
	
	    return $this->modelObj->addData($data);
	
	}
	//编辑优惠券
	public function logUpdCoupon(){
	
	    $data=$this->data;
	
	    return $this->modelObj->addData($data,true);
	
	}
    //编辑获取单个信息
    public function logGetCouponsById(){
        $data=$this->data;

      return  M('coupon')->where(['id'=>$data['id']])->find();
    }




    //会员列表
    public function logMemberList(){

        $data=$this->data;
        return $this->modelObj->getMemberList($data);

    }

    //会员列表搜索
    public function logListSearch(){

        $data=$this->data;

        return $this->modelObj->getMemberListSearch($data);
    }

    public function logSendCoupon(){
        $data=$this->data;
        if (empty($data)) {
            return array('data'=>'','status'=>0,'message'=>'数据不能为空' );
        }
        $couponListModel= new CouponListModel();
        $type = $this->modelObj->where(['id'=>$data['c_id']])->getField('type');
        foreach($data['member_id'] as $key=>$value){
            $info[$key]['c_id']=$data['c_id'];
            $info[$key]['type']= $type;
            $info[$key]['user_id']=$value['member_id'];
            $info[$key]['send_time']=time();
        }
        $re=$couponListModel->addAll($info);
        if (!$re){
            return array('data'=>'','status'=>0,'message'=>'操作失败');
        }
        return array('data'=>'','status'=>1,'message'=>'操作成功');
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->getComment();

        $message = [
            'name' => [
                'required' => '请输入'.$comment['name'],
            ],

            'money'=> [
                'required' => '请输入'.$comment['money'],
                'number' => $comment['money'].'必须是数字'
            ],
            'condition'=> [
                'required' => '请输入消费金额',
                'number' => '消费金额必须是数字'
            ],
            'createnum'=>[
                'required'=>'请输入'.$comment['createnum'],
                'number' => $comment['createnum'].'必须是数字'
            ],

             'use_start_time'=>[
             'required'=>'请输入'.$comment['use_start_time'],
            ],
             'use_end_time'=>[
                 'required'=>'请输入'.$comment['use_end_time'],
             ]
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
            'name' => [
                'required' => true,
            ],
            'money'=> [
                'required' => true,
                'number' => true
            ],

            'condition' => [
                'required' => true,
                'number' => true
            ],
            'createnum' => [
                'required' => true,
                'number' => true
            ],
            'use_start_time' => [
                'required' => true,
                'number' => true
            ],
            'use_end_time' => [
                'required' => true,

            ],
        ];
        return $validate;
    }

}
