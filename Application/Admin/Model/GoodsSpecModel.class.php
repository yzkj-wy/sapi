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
 * 商品规格
 * @author 王强
 * @version 1.0.0
 */
class GoodsSpecModel extends BaseModel
{
    protected $patchValidate = true;
    protected $_validate = [['name','require','商品类型不能为空']];


	public static $id_d;	//主键编号

	public static $name_d;	//规格名称

	public static $classOne_d;	//一级分类【id】

	public static $classTwo_d;	//二级分类

	public static $classThree_d;	//三级分类

	public static $sort_d;	//排序

	public static $status_d;	//状态显示【1显示 0 不显示  默认显示】

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间

    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }

    /**
     * 添加前操作
     */
    protected function _before_insert(&$data,$options)
    {
        $data['create_time'] = time();
        $data['update_time'] = time();
        return $data;
    }

    //更新前操作
    protected function _before_update(&$data, $options)
    {
        $data['update_time'] = time();
        return $data;
    }

    //过滤特殊的字符
    public function filterSpecChar($post_items){
        foreach ($post_items as $key => $val)  // 去除空格
        {
            $val = str_replace('_', '', $val); // 替换特殊字符
            $val = str_replace('@', '', $val); // 替换特殊字符

            $val = trim($val);
            if(empty($val))
                unset($post_items[$key]);
            else
                $post_items[$key] = $val;
        }
        return $post_items;
    }

    /**
     * 判断是否存在规格
     */
    public function isExistSpec($id)
    {
        $result = $this->where(static::$id_d . ' = %d ' , $id)->find();
        return empty($result) ? false : true ;
    }

}