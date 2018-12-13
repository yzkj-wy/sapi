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
use Common\TraitClass\callBackClass;
use Common\Model\IsExitsModel;
use Common\TraitClass\SkuCheckTrait;

/**
 * 商品规格模型 
 * @author 王强
 * @version 1.0.1
 */
class SpecGoodsPriceModel extends BaseModel implements IsExitsModel
{
    use callBackClass;  
    use SkuCheckTrait;
    private static $obj;
    
	public $splitKey; //分隔符

	public static $id_d;	//id

	public static $goodsId_d;	//商品id

	public static $key_d;	//规格键名

	public static $barCode_d;	//商品条形码

	public static $sku_d;	//SKU

	private $modelByGoods;
	

	public static $pId_d;	//商品父级【编号】


    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = !(static::$obj instanceof $name) ? new static() : static::$obj;
    }
    
    /**
     * @param field_type $modelByGoods
     */
    public function setModelByGoods($modelByGoods)
    {
        $this->modelByGoods = $modelByGoods;
    }
    
    
    
    /**
     * 添加 商品-规格对应 
     * @param array $data 规格数据
     * @param array $goodsId 商品编号
     * @return 
     */
    public function addSpecByGoods(array $data, array $goodsId)
    {
        
        if (empty($data) || empty($goodsId)) {
            return array();
        }
        
        
        $specId = array_keys($data);
        
        $build  = array();
       
        $sku    = null;
        
        $skuFirstCheck = [];
        
        foreach ($data as $key => $value) {
            $build[] = $this->create($value);
            $sku .= ',"'.$value[static::$sku_d].'"';
            $skuFirstCheck [] = $value[static::$sku_d];
        }
       
        if ($this->isSameValueByArray($skuFirstCheck)) {
            $this->rollback();
            
            $this->error = '不允许重复编码';
            
            return false;
        }
        //检查sku 是否重复
        
        $sku     = substr($sku, 1);
        
        $skuByCount = $this->IsExits($sku);
        
        $this->skuCountBySku = $skuByCount;
        
        $isExtis = $this->checkAdd();
        
        if ($isExtis === true) {// 存在
            $this->rollback();
            return false;
        }
        
        
        foreach ($goodsId as $key => $value) {
            $build[$key][static::$goodsId_d] = $value;
        }
        foreach ($specId as $key => $value) {
            $build[$key][static::$key_d]     = $value;
            
            if (isset($value[static::$id_d])) {
                unset(  $build[$key][static::$id_d] );
            }
        }
      
        $status = $this->addAll($build);
        if (!$this->traceStation($status)) {
            return false;
        }
        
        $this->commit();
        //添加
        return $status;
    }
    
    /**
     * 删除规格 商品
     */
    public function deleteGoods ($goodsId)
    {
        if (!$this->isEmpty($goodsId)) {
            $this->rollback();
            return false;
        }
        
        $status = $this->where(static::$goodsId_d.' in ('.implode(',', $goodsId).')')->delete();
        return $this->traceStation($status);
    }
    
   /**
    * 删除一个商品 
    */
    public function deleteSpecById($id)
    {
        if (($id = intval($id)) === 0) {
            $this->rollback();
            return false;
        }
        
        $status = $this->where(static::$goodsId_d.' = %d ', $id)->delete();
        
        if ($status === false) {
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    
    /**
     * 根据sku 获取数据 
     * @param string $skuIdString sku字符串
     * @return array
     */
    public function getSpecDataBySku($skuIdString)
    {
        if (empty($skuIdString)) { //没有 即不存在
            return array();
        }
        return $this->where(static::$sku_d.' in ('.$skuIdString.')')->getField(static::$id_d.','.static::$sku_d);
    }
    
    /**
     * 检查是否存在相同的sku
     * {@inheritDoc}
     * @see \Common\Model\IsExitsModel::IsExits()
     */
    public function IsExits($post)
    {
        // TODO Auto-generated method stub
        if (empty($post)) { //没有 即不存在
            return false;
        }
        
        $skuData = $this->getSpecDataBySku($post);
        if (empty($skuData)) {
            return false;
        }
        
        $post = str_replace('"', '', $post);
       
        $skuIdArray = explode(',', $post);
        
        $skuData = array_merge($skuData, $skuIdArray);        
        
        $array = array_count_values($skuData); //统计出现的次数
        
        return $array;
    }
}