<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Common\Model;


/**
 * 快递公司模型 
 */
class ExpressModel extends BaseModel implements IsExitsModel
{
    private static $obj;

	public static $id_d;	//索引ID

	public static $name_d;	//公司名称

	public static $status_d;	//状态1启用 2弃用

	public static $code_d;	//编号

	public static $letter_d;	//首字母

	public static $order_d;	//1常用0不常用

	public static $url_d;	//公司网址

	public static $ztState_d;	//是否支持服务站配送0否1是

	public static $tel_d;	//客服电话

	public static $discount_d;	//折扣
    
    public static function getInitnation()
    {
        $name = __CLASS__;
        return self::$obj = !(self::$obj instanceof $name) ? new self() : self::$obj;
    }
   
    /**
     * 根据其他模型数据 获取对应的数据
     * @param array $data
     * @param BaseModel $model
     * @param string $cacheKey
     * @return mixed|object
     */
    public function getExpressData(array $data, BaseModel $model, $cacheKey = 'EXPRESS_CACHE_DATA')
    {
        if (!$this->isEmpty($data) || !($model instanceof BaseModel)) {
            return array();
        }
        
        $expressData = S($cacheKey);
        
        if (empty($expressData)) {
            $expressData = $this->getDataByOtherModel($data, $model::$expId_d, [
                self::$id_d,
                self::$name_d,
            ], self::$id_d);
        
            if (empty($expressData)) {
                return array();
            }
            S($cacheKey, $expressData, 6);
        }
        return $expressData;
    }
    
    /**
     * 获取快递名字 
     */
    public function getExpressTitle($id)
    {
        if ( ($id = intval($id)) === 0)
        {
            return null;
        }
        return $this->where(self::$id_d.' = '.$id)->getField(self::$name_d);
    }
    
    /**
     * 获取 快递表 id 及其名称
     */
    public function getIdAndName ()
    {
        $data = S('EXPRESS_KEY_H_');
        
        if (empty($data)) {
            $data = $this->where(self::$status_d .' = 1')->getField(self::$id_d.','.self::$name_d);
        }  else {
            return $data;
        }
        
        S('EXPRESS_KEY_H_', $data, 100);
        
        return $data;
    }
    /**
     * {@inheritDoc}
     * @see \Common\Model\IsExitsModel::IsExits()
     */
    public function IsExits($post)
    {
        // TODO Auto-generated method stub
        
        if (empty($post)) {//空即是存在
            return true;
        }
        
        return $this->where(self::$name_d.'="%s"', $post)->getField(self::$id_d);
    }
    //获取快递列表
    public function getFreightListByWhere($where,$field,$order,$limit){
        $p = empty(I('get.page'))?0:I('get.page');
        if (empty($limit)) {
            $list = $this->where($where)->field($field)->order($order)->select();
        }else{
            $list = $this->where($where)->field($field)->page($p.",".$limit)->order($order)->select();
        } 
        $count = $this->where($where)->count();
        $page = ceil($count/$limit);       
        if (empty($list)) {
            return array('status'=>"","message"=>"获取失败");
        }
        return array('status'=>1,"message"=>"获取成功!","data"=>array("data"=>$list,"page"=>$page));
    }
    //获取快递名
    public function getExpressName(array $data){
        if (empty($data)) {
            return "";
        }
        foreach ($data as $key => $value) {
            $where['id'] = $value['carry_way'];
            $data[$key]['express_name'] = $this->where($where)->getField('name');
        }
        return $data;
    }
    //获取快递名
    public function getExpressNameOne($data){
        if (empty($data)) {
            return "";
        }
        
        $where['id'] = $data['carry_way'];
        $data['express_name'] = $this->where($where)->getField('name');
       
        return $data;
    }
    //添加快递公司
    public function addFreight($post){
        if (empty($post)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $res = $this->add($post);
        if (!$res) {           
            return array("status"=>"","message"=>"添加失败","data"=>"");
        }
        return array("status"=>1,"message"=>"添加成功","data"=>"");
    }
    //修改
    public function saveFreight($where,$data){
        if (empty($where)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        if (empty($data)) {
            return array("status"=>"","message"=>"参数错误","data"=>"");
        }
        $res = $this->where($where)->save($data);
        if (!$res) {           
            return array("status"=>"","message"=>"修改失败","data"=>"");
        }
        return array("status"=>1,"message"=>"修改成功","data"=>"");
    }
}