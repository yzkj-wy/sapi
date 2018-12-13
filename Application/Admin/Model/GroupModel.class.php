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
 * @desc 团购模型 
 * @author 王强
 * @version 1.0.1
 */
class GroupModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//团购ID

	public static $title_d;	//活动名称

	public static $startTime_d;	//开始时间

	public static $endTime_d;	//结束时间

	public static $goodsId_d;	//商品ID

	public static $price_d;	//团购价格

	public static $goodsNum_d;	//商品参团数

	public static $buyNum_d;	//商品已购买数

	public static $orderNum_d;	//已下单人数

	public static $virtualNum_d;	//虚拟购买数

	public static $description_d;	//本团介绍

	public static $recommended_d;	//是否推荐 0.未推荐 1.已推荐

	public static $lookNum_d;	//查看次数

	public static $updateTime_d;	//更新时间

	public static $createTime_d;	//添加时间


    /**
     * 获取类的实例
     * @return \Admin\Model\GroupModel

	public static $storeId_d;	//店铺id

	public static $auditingStatus_d;	//审核状态 0为待审核 1为已审核


	public static $storeId_d;	//店铺id

	public static $auditingStatus_d;	//审核状态 0为待审核 1为已审核


	public static $storeId_d;	//店铺id

	public static $auditingStatus_d;	//审核状态 0为待审核 1为已审核

     */
    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }
    
    /**
     * 重写父类方法
     */
    protected  function _before_insert(& $data, $options)
    {
        $data[static::$createTime_d] = time();
        $data[static::$updateTime_d] = time();
    
        return $data;
    }
     
    /**
     * 重写父类方法
     */
    protected function _before_update(& $data, $options)
    {

        $data[static::$updateTime_d] = time();
    
        return $data;
    }
    
    /**
     * 添加促销商品
     * @param array $data post 数据
     * @param string $fun 方法名
     * @return boolean
     */



    public function addProGoods(array $data, $fun = 'add')
    {
        if (empty($data) || !is_array($data) || !method_exists($this, $fun)) {
            return false;
        }
         
        if ( $data[static::$startTime_d] > $data[static::$endTime_d]) {
            $this->error = '开始时间不能大于结束时间';
            return false;
        }
        $data[static::$startTime_d] = strtotime($data[static::$startTime_d]);
        $data[static::$endTime_d]   = strtotime($data[static::$endTime_d]);
        $data['store_id']=session('store_id');
        $data['create_time']=time();
        if($fun='add'){

            M('goods')->where(['id'=>$data['goods_id']])->save(['status'=>6]);
            return $this->$fun($data);
        }
        if($fun='save'){
            M('goods')->where(['id'=>$data['goods_id']])->save(['status'=>6]);
            return $this->where(['id'=>$data['id']])->$fun($data);
        }

    }

    //团购列表
    public function getList($data){
        $where['store_id']=session('store_id');
        if($data['title']){
             $where['title']=array('like','%'.$data['title'].'%');
        }
        if(!empty($data['auditing_status'])&&$data['auditing_status']==0){//待审核
            $where['auditing_status']=0;
        }

        if($data['auditing_status']==1){//已审核
            $where['auditing_status']=1;
        }
         if(!empty($data['active_status'])&&$data['active_status']==0){//未开始

             $where['start_time']=array('gt',time());
         }
        if($data['active_status']==1){//进行中
            $where['start_time']=array('lt',time());
            $where['end_time']=array('gt',time());
        }

      $re=$this->where($where)
                ->field(
                    'id,title,start_time,end_time,goods_num,auditing_status'
                )
                ->page($data['page'],10)
                ->select();
        $total=$this->where($where)
            ->field('id')
            ->count();
        $page=ceil($total/10);
        $data=array(
            'data'=>$re,
            'page_size'=>10,
            'page'=>$page,
        );
        return $data;

    }

    //是否存在

    public function isExist($goods_id){

       return $this->where(['goods_id'=>$goods_id,'store_id'=>session('store_id')])->find();
}

}