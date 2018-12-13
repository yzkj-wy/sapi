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
namespace Common\Model;

/**
 * @author 王强
 * @version 1.0
 */
class StoreGradeModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//索引ID

	public static $levelName_d;	//等级名称

	public static $goodsLimit_d;	//允许发布的商品数量

	public static $albumList_d;	//允许上传图片数量

	public static $spaceLimit_d;	//上传空间大小，单位MB

	public static $templateNumber_d;	//选择店铺模板套数

	public static $price_d;	//开店费用(元/年)

	public static $description_d;	//申请说明

	public static $upperLimit_d;	//销售上限

	public static $lowerLimit_d;	//销售下限金额

	public static $status_d;	//是否启用【0否1是】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
    
    protected function _before_insert(&$data, $options)
    {
        $data[self::$createTime_d] = time();
        
        $data[self::$updateTime_d] = time();
        
        return $data;
    }
    
    protected function _before_update(&$data, $options)
    {
    
        $data[self::$updateTime_d] = time();
    
        return $data;
    }
    //获取店铺等级
    public function getStoreGrade(){
        $where['status'] = 1;
        $field = "id,level_name,goods_limit,album_list,space_limit,template_number,price,description,upper_limit,lower_limit";
        $res = $this->field($field)->where($where)->order('id')->select();
        if (empty($res)) {
            return array('status'=>0,"mes"=>"失败!");
        }
        return array('status'=>1,"mes"=>"成功","data"=>$res);
    }
}