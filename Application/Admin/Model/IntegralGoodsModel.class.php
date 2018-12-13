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
 * 积分模型
 * @author 王强
 * @version 1.0.1
 */
class IntegralGoodsModel extends BaseModel
{

	public static $id_d;	//

	public static $goodsId_d;	//商品ID

	public static $integral_d;	//需要的积分

	public static $delayed_d;	//积分最少被领取时间【最少0,最大999】

	public static $status_d;	//是可兑换

	public static $createTime_d;	//创建时间

	public static $money_d;	//金额【换取商品需要另外添加的钱】

	public static $updateTime_d;	//修改时间

	public static $storeId_d;	//店铺【id】

	public static $isShow_d;	//是否显示【1显示 0不显示】

    private static $obj ;
    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = ! (static::$obj instanceof $name) ? new static() : static::$obj;
    }

//积分列表
    public function IntegralList($data){


        $integral=$this->where(['store'=>$_SESSION['store_id']])
            ->page($data['page'],10)
            ->field('id,goods_id,integral,create_time,is_show')
            ->select();

        $total=$this->where(['store'=>$_SESSION['store_id']])->field('id')->count();

        $goodModel=M('Goods');

        foreach($integral as $k=>$v){
            $goods=$goodModel->where(['id'=>$v['goods_id']])->field('title,price_member,stock')->find();
            $integral[$k]['title']=$goods['title'];
            $integral[$k]['price_member']=$goods['price_member'];
            $integral[$k]['stock']=$goods['stock'];
            if(!empty($data['goods_id']) && $data['goods_id']!=$v['goods_id']){
                unset($integral[$k]);
            }

        }
        $page=ceil($total/10);
        $data=array(
            'data'=>$integral,
            'page_size'=>10,
            'page'=>$page,
        );
        return $data;

}


//是否显示
    public function IsShow($data){
        $where['id']=$data['id'];
        if($data['isShow']==1){
            $info['is_show']=1;
           return $this->where($where)->save($info);
        }else{
            $info['is_show']=0;
            return $this->where($where)->save($info);
        }

    }

//修改和添加积分
    public function addUpdIntegral(array $add){
        if (empty($add)) {
            return array('status'=>0,"mes"=>"请传参数!");
        }
		
        try {
        
	        if(!empty($add['id'])){
	            $add['update_time']=time();
	            $res = $this->where(['id'=>$add['id']])->save($add);
	        }else{
	            $add['create_time']=time();
	            $add['store_id']=session('store_id');
	            $res = $this->add($add);
	
	        }
	        M('goods')->where(['id'=>$add['goods_id']])->save(['status'=>3]);
	        if (!$res) {
	            return array('status'=>0,"mes"=>"操作失败");
	        }
        } catch (\Exception $e) {
        	
        	return [
        		'status' => 0,
        		'data' => '',
        		'mes' => $e->getMessage().',或者重复添加'
        	];
        }
        return array('status'=>1,'data'=>$res,"mes"=>"操作成功");
    }

    function getInfoById($id){
        $integral=$this->where(['id'=>$id])->field('id,goods_id,integral,delayed,money,is_show,create_time')->find();

        if($integral) {
            $goods = M('goods')->where(['id' => $integral['goods_id']])->field('title,stock,price_member')->find();
        $integral['title']=$goods['title'];
        $integral['stock']=$goods['stock'];
        $integral['price_member']=$goods['price_member'];
            return $integral;
        }
    }


}