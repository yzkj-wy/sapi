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
 * 店铺经营类目
 * @author Administrator
 *
 */
class StoreManagementCategoryModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//

	public static $storeId_d;	//入驻表编号

	public static $oneClass_d;	//一级类目

	public static $twoClass_d;	//二级类目

	public static $threeClass_d;	//三级类目

	public static $status_d;	//入驻类型 0公司入驻  1 企业入驻

    
    public static function getInitnation()
    {
        $class = __CLASS__;
        return  self::$obj= !(self::$obj instanceof $class) ? new self() : self::$obj;
    }
    //添加
    public function addManagement($data){
        if (empty($data)) {
        	return array('status'=>0,"mes"=>"数据出错!");
        }
        M()->startTrans();
        $one   = $data['one_class'];
        $two   = $data['two_class'];
        $three = $data['three_class'];
        $date = array();
        foreach ($one as $key => $value) {
            $date[$key]['one_class'] = $one[$key];
            $date[$key]['two_class'] = $two[$key];
            $date[$key]['three_class'] = $three[$key];
            $date[$key]['store_id'] = $data['store_id'];
            $date[$key]['status'] = $data['status'];
        }
        $res = $this->addAll($date);
        if (!$res) {
            M()->rollback();
            return array('status'=>0,"mes"=>"失败");
        }
        return array('status'=>1,'data'=>$res,"mes"=>"成功");
    }
}