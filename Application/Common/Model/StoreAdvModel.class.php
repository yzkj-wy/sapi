<?php
namespace Common\Model;

/**
 * 广告
 * @author Administrator
 *
 */
class StoreAdvModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//广告自增标识编号

	public static $apId_d;	//广告位id

	public static $advTitle_d;	//广告内容描述

	public static $advContent_d;	//广告内容

	public static $adKey_d;	//广告键

	public static $advStart_date_d;	//广告开始时间

	public static $advEnd_date_d;	//广告结束时间

	public static $slideSort_d;	//幻灯片排序

	public static $storeId_d;	//店铺【ID】

	public static $clickNum_d;	//广告点击率

	public static $isAllow_d;	//会员购买的广告是否通过审核【0未审核1审核已通过2审核未通过】

	public static $buyStyle_d;	//购买方式

	public static $goldpay_d;	//购买所支付的金币

	public static $createTime_d;	//添加时间

	public static $updateTime_d;	//更新时间

	public static $adUrl_d;	//广告链接

	public static $status_d;	//是否显示0显示1不显示


    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new static() : self::$obj;
    }
    public function getAdvList($where,$field,$page){ 
        $page = empty($page)?0:$page;
        $res = $this->field($field)->where($where)->page($page.",10")->order("slide_sort")->select();
        $count =  $this->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;  
        return array("data"=>$res,"count"=>$count,"size"=>10,"totalPages"=>$totalPages);
    }
    //添加
    public function getAdvAdd($data){ 
        $adv_content = $data['adv_content'];
        $post = [];
        foreach ($adv_content as $key => $value) {
            $post[$key]['store_id'] = $data['store_id'];
            $post[$key]['adv_end_date'] = $data['adv_end_date'];
            $post[$key]['adv_start_date'] = $data['adv_start_date'];
            $post[$key]['create_time'] = $data['create_time'];
            $post[$key]['update_time'] = $data['update_time'];
            $post[$key]['status'] =0;
            $post[$key]['adv_content'] = $value;
            $post[$key]['ap_id'] =$data['ap_id'];
            $post[$key]['adv_title'] =$data['adv_title'];
            $post[$key]['ad_url'] =$data['ad_url'];
            $post[$key]['slide_sort'] =$data['slide_sort'];
        }
        $rest =  $this->addAll($post);
        if (!$rest) {
        	return array("data"=>"","status"=>0,"message"=>"添加失败");
        }
        return array("data"=>$rest,"status"=>1,"message"=>"添加成功");
    }
    //修改
    public function getAdvSave($where,$data){ 
        $rest =  $this->where($where)->save($data);
        if ($rest===false) {
        	return array("data"=>"","status"=>0,"message"=>"修改失败");
        }
        return array("data"=>$rest,"status"=>1,"message"=>"修改成功");
    }
    //删除
    public function getAdvDel($where){ 
        $rest =  $this->where($where)->delete();
        if (!$rest) {
        	return array("data"=>"","status"=>0,"message"=>"删除失败");
        }
        return array("data"=>$rest,"status"=>1,"message"=>"删除成功");
    }
    //获取单条数据 
    public function getAdvInfo($data){
    	$where['id'] = $data['id'];
    	$field = "id,ap_id,adv_title,adv_content,ad_url,slide_sort,status,adv_start_date,adv_end_date";
    	$res = $this->field($field)->where($where)->find();
    	if (empty($res)) {
        	return array("data"=>"","status"=>0,"message"=>"获取失败");
        }
        return array("data"=>$res,"status"=>1,"message"=>"获取成功");
    }
}