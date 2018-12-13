<?php

// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>\n
// +----------------------------------------------------------------------

namespace Admin\Model;

use Common\Model\BaseModel;

/**
 * 代金卷模型 
 */
class CouponModel extends BaseModel
{
    private static  $obj;

	public static $id_d;

	public static $name_d;

	public static $type_d;

	public static $money_d;

	public static $condition_d;

	public static $createnum_d;

	public static $sendNum_d;

	public static $useNum_d;

	public static $sendStart_time_d;

	public static $sendEnd_time_d;

	public static $useStart_time_d;

	public static $useEnd_time_d;

	public static $addTime_d;

	public static $updateTime_d;
    
	private $poopKey; // 代金券相关操作的键
	
   

	public static $storeId_d;	//店铺【id】

	public static $status_d;	//是否有效


    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }
    
    protected function _before_insert(& $data, $options)
    {
        $data[static::$updateTime_d] = time();
        $data[static::$addTime_d]    = time();
        $data[static::$sendNum_d]    = 0;
        $data[static::$useNum_d]     = 0;
        return $data;
    }
    
    protected function _before_update(& $data, $options)
    {

        $data[static::$updateTime_d] = time();
    
        return $data;
    }
    
    /**
     * 是否可以发放代金券 
     * @param int $sendNum 发放数量
     * @param int $id      代金卷编号
     * @return bool
     */
    public function isSendCoupon($sendNum, $id)
    {
        if (!is_numeric($sendNum) || !is_numeric($id)) {
            $this->error = '参数错误';
            return false;
        }
        
        $obj = $this->where(static::$id_d.'= "%s"', $id);
        
        //获取数量
        $data = $obj->find(array(
                'field' => array(
                    static::$createnum_d,
                    static::$sendStart_time_d,
                    static::$sendEnd_time_d,
                    static::$sendNum_d
                )
        ));
        
        if (empty($data)) {
            $this->error = '代金卷不存在';
            return false;
        }
        
        if ($data[static::$createnum_d] <= $data[static::$sendNum_d] || $data[static::$createnum_d] <= 0) {
            $this->error = '发放数量不足';
            return false;
        }
        
        // 是否在发放时间内
        $time = time();
        
        if ($time < $data[static::$sendStart_time_d] || $time > $data[static::$sendEnd_time_d]) {
            $this->error = '发放时间不允许';
            return false;
        }
        //记录发放数量
        
        $status = $this->where(static::$id_d.'= "%s"', $id)->setInc(static::$sendNum_d, $sendNum);
        
        return $status;
        
    }
    
    
    /**
     * 获取错误 
     */
    public function getCouponError() {
        return $this->error;
    }


    /**
     * 添加数据 
     */
    public function addData($data, $flag = false)
    {
        if (empty($data)) {
            return $data;
        }
        $data['use_start_time'] = strtotime($data['use_start_time']);
        $data['use_end_time']   = strtotime($data['use_end_time']);
        if($flag){
          $re=$this->where(['id'=>$data['id']])->save($data);

            if($re){
                return array(
                    'status'=>1,
                    'message'=>'修改成功',
                    'data'=>''
                );
            }else {
                return array(
                    'status' => 0,
                    'message' => '修改失败',
                    'data' => ''
                );
            }
        }else{
            $data['store_id']=session('store_id');

            $coupons_name=$this->where(['store_id'=>session('store_id')])->field('name')->select();
          $last_name=array_column($coupons_name,'name');
            if(in_array($data['name'],$last_name)){
                return array(
                    'status'=>0,
                    'message'=>'已有优惠券，不能重复生成',
                    'data'=>''
                );
            }


          $re=  $this->add($data);
            if($re){
                return array(
                    'status'=>1,
                    'message'=>'添加成功',
                    'data'=>''
                );
            }else {
                return array(
                    'status' => 0,
                    'message' => '添加失败',
                    'data' => ''
                );
            }

        }



    }
    
    /**
     * 获取优惠额度 
     */
    public function getCouponById($id)
    {
        if (($id = intval($id)) === 0) {
            return 0;
        }
        
        $data = $this->where(static::$id_d.'='.$id)->getField(static::$money_d);
        return $data;
    }
    
    /**
     * 获取代金券优惠数据 
     */
    public function getCouponData (array $data, $splitKey)
    {
        if (!$this->isEmpty($data) || empty($this->poopKey)) {
            return $data;
        }
       
        $temp = $flag = array();

        $temp = $data;
        foreach ($data as $key => &$value)
        {
            if ( $value[$splitKey] == -1) {//-1 表示代金券
                $value[$this->poopKey] = (int)$value[$this->poopKey];//代金券编号取整
                continue;
            } else {
                $flag[$key] = $value;
            }
            unset($data[$key]);
        }
      
        if (empty($data)) {
            return $temp;
        }
        
        $receiveData = $this->getDataByOtherModel($data, $this->poopKey, [
            static::$id_d,
            static::$name_d,
        ], static::$id_d);
        return array_merge($receiveData, $flag);
    }
    
    /**
     * @return the $poopKey
     */
    public function getPoopKey()
    {
        return $this->poopKey;
    }
    
    /**
     * @param field_type $poopKey
     */
    public function setPoopKey($poopKey)
    {
        $this->poopKey = $poopKey;
    }
    //优惠券列表
    public function couponList($data){
     $re= $this->where(['store_id'=>session('store_id')])
            ->field(
                'id,name,money,use_end_time,createnum,send_num,use_num'
            )
            ->page($data['page'],10)
            ->select();
        $total=$this->where(['store_id'=>session('store_id')])
            ->field('id')
            ->count();
        $page=ceil($total/10);
        return array(
            'data'=>$re,
            'page_size'=>10,
            'page'=>$page,
        );
    }
    //会员列表
    public function getMemberList($data){

        if(!empty($data['level_id'])){
            $where['level_id']=$data['level_id'];
        }


        $where['a.store_id'] = $_SESSION['store_id'];
        
        $member_id=M('store_member as a')
            ->join('left join db_store_member_level as b on b.level_id=a.id')
            ->where($where)
            ->field('member_id,total_transaction')
            ->select();
        $userModel=M('User');

        $store_member_level=M('store_member_level as a');



        foreach($member_id as $k=>$v){

            $user = $userModel
                ->field('mobile,user_name,email')
                ->where(['id'=>$v['member_id']])
                ->find();

            $where1['money_big']=['egt',$v['total_transaction']];
            $where1['money_small']=['elt',$v['total_transaction']];
            $member_id[$k]['level_name']=$store_member_level
                ->join('db_store_level_by_platform as b on b.id=a.level_id')
                ->where($where1)
                ->getField('level_name');

            $member_id[$k]['user_name'] = $user['user_name'];

            $member_id[$k]['mobile'] = $user['mobile'];

            $member_id[$k]['email'] = $user['email'];

        }

        $levelname=M('store_member_level as a')
            ->join('db_store_level_by_platform as b on b.id=a.level_id')
            ->field('a.id,level_name')
            ->where(['store_id'=>session('store_id')])
            ->select();

        $re['member']=$member_id;

        $re['level_name']=$levelname;

        return $re;
    }
    //优惠券列表搜索
    public function getMemberListSearch($data){


            $money= M('store_member_level')
            ->where(['id'=>$data['id']])
            ->field('level_id,money_big,money_small')
            ->find();

            $levelName=M('store_level_by_platform')
                ->where(['id'=>$money['level_id']])
                ->getField('level_name');

        $where['total_transaction']=array('between',[$money['money_small'],$money['money_big']]);

             $member=M('store_member')
                 ->field('member_id')
                 ->where($where)
                ->select();

        $userModel=M('user');

        if(!empty($data['mobile'])) {
            $where1['mobile']=$data['mobile'];
        }

        if(!empty($data['email'])) {
            $where1['email']=$data['email'];
        }

        if(!empty($data['keywords'])){
            $where1['user_name']=array('like','%'.$data['keywords'].'%');
        }

        foreach($member as $k=>$v){

                $where1['id']=$v['member_id'];

                $re[]=$userModel
                ->where($where1)
                ->field('id as member_id, mobile,user_name,email')
                ->find();
               $re[$k]['level_name'] = $levelName;

               if(empty($re[$k]['member_id'])) {
                  unset($re[$k]);
               }

        }

        return $re;
    }
}