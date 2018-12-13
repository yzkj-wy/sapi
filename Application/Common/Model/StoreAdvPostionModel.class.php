<?php
namespace Common\Model;

/**
 * 广告类别
 * @author Administrator
 *
 */
class StoreAdvPostionModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//广告位置id

	public static $apName_d;	//广告位置名

	public static $apClass_d;	//广告类别【0图片1文字2幻灯3Flash】

	public static $apDisplay_d;	//广告展示方式：0幻灯片1多广告展示2单广告展示

	public static $isUse_d;	//广告位是否启用：0不启用1启用

	public static $apWidth_d;	//广告位宽度

	public static $apHeight_d;	//广告位高度

	public static $advNum_d;	//拥有的广告数

	public static $clickNum_d;	//广告位点击率

	public static $defaultContent_d;	//广告位默认内容

	public static $maxHeight_d;	//最小高度

	public static $maxWidth_d;	//最大宽度

	public static $createTime_d;	//添加时间

	public static $updateTime_d;	//更新时间


    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new static() : self::$obj;
    }
    //获取广告类别(二维数组)
    public function getAdvPostionByAd($ad){ 
        foreach ($ad as $key => $value) {
           	$where['id'] = $value['ap_id'];
           	$postion  = $this->field("ap_name,ap_class")->where($where)->find();
           	$ad[$key]['ap_name'] = $postion['ap_name'];
           	$ad[$key]['ap_class'] = $postion['ap_class'];
        }   
        return $ad;
    }
    public function getAdvPostionList($field,$page){ 
        $page = empty($page)?0:$page;
        $res = $this->field($field)->page($page.",10")->order("create_time DESC")->select();
        $count =  $this->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;  
        return array("data"=>$res,"count"=>$count,"size"=>10,"totalPages"=>$totalPages);
    }
    public function getAdvPostion($where,$field){ 
        $res = $this->field($field)->where($where)->order("create_time DESC")->select(); 
        return $res;
    }
}