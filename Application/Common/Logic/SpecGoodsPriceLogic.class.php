<?php
namespace Common\Logic;

use Common\Logic\AbstractGetDataLogic;
use Admin\Model\SpecGoodsPriceModel;
use Common\Tool\Extend\ArrayChildren;


/**
 * 商品规格处理
 * @author 王强
 */
class SpecGoodsPriceLogic extends AbstractGetDataLogic
{
    /**
     * 规格数据
     * @var array
     */
    private $specData = [];
    
    /**
     * 要添加的规格
     * @var array
     */
    private $addSpecData = [];
    
    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = null)
    {
        $this->data = $data;
    
        $this->splitKey = $split;
    
        $this->modelObj = new SpecGoodsPriceModel();
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        // TODO Auto-generated method stub
        return SpecGoodsPriceModel::class;
    }

    
    /**
     * @desc 组合规格项
     * @param array $request   笛卡尔数组
     * @param array $attr      规格项数组
     * @return string;
     */
    public function getAttributeBuildGoodsInfo(array $request, array $attr)
    {
        // 检测参数数据
        
        if (empty($request) || empty($attr)) {
            return '';
        }
        
        $keySpecGoodsPrice = array();
         
        // 商品编辑时 数据处理
        if (!empty($_SESSION['goodsIdArr'])) {
    
            $id = array_keys($_SESSION['goodsIdArr']);
    
            $id = implode(',', $id);
    
            $keySpecGoodsPrice = $this->modelObj
            ->field(SpecGoodsPriceModel::$barCode_d, true)
            ->where(SpecGoodsPriceModel::$goodsId_d .' in ('.$id.')')
            ->select();
            $keyString = $array =  array();
            foreach ($keySpecGoodsPrice as $name => $value) {
    
                if (array_key_exists($value[SpecGoodsPriceModel::$goodsId_d], $_SESSION['goodsIdArr'])) {
                    $value = array_merge($value, $_SESSION['goodsIdArr'][$value[SpecGoodsPriceModel::$goodsId_d]]);
                }
                 
                $keyString[$value[SpecGoodsPriceModel::$key_d]] = $value;
            }
            //规格项
            $_SESSION['goodsIdArr'] = null;
        }
        // * @param array $attribute 规格表数组
        $attribute = $this->data;
       
        $cloName = $request['arrayKeys'];
        $str = "<table class='table table-bordered' id='spec_input_tab'>";
        $str .="<tr>";
        // 显示第一行的数据
        foreach ($cloName as $k => $v)
        {
            $str .=" <td><b>{$attribute[$v]}</b></td>";
        }
        $str .="<td><b>普通价格</b></td>
                <td><b>会员价格</b></td>
                <td><b>库存</b></td>
                <td><b>商品重量</b></td>
                <td><b>商品编码</b></td>
             </tr>";
    
        $goodsModel = $this->modelByGoods;
        foreach ($request['cartesianProduct'] as $key => $value)
        {
            $str .="<tr>";
            $itemKeyName = array();
            $flag = 0;
            foreach($value as $k2 => $v2)
            {
                $str .="<td>{$attr[$v2]['item']}</td>";
                $itemKeyName[$v2] = $attribute[$attr[$v2]['spec']].':'.$attr[$v2]['item'];
            }//
            ksort($itemKeyName);
            $itemKey = implode('_', array_keys($itemKeyName));
            if(!empty($keyString) ) {
                $str .="<td><input type='text' data-key='{$itemKey}' name='price_market' value='{$keyString[$itemKey]['price_market']}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
    
                $str .="<td><input type='text' data-key='{$itemKey}' name='price_member' value='{$keyString[$itemKey]['price_member']}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
    
                $str .="<td><input type='text' data-key='{$itemKey}'  name='stock' value='{$keyString[$itemKey]['stock']}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
    
                $str .="<td><input type='text' data-key='{$itemKey}' name='weight' value='{$keyString[$itemKey]['weight']}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
    
                $str .="<td><input type='hidden' data-key='{$itemKey}'  name='".SpecGoodsPriceModel::$goodsId_d."'
                value='{$keyString[$itemKey][SpecGoodsPriceModel::$goodsId_d]}' />
                <input type='hidden' data-key='{$itemKey}' name='".SpecGoodsPriceModel::$id_d."' value='{$keyString[$itemKey][SpecGoodsPriceModel::$id_d]}' />
                <input data-key='{$itemKey}' name='".SpecGoodsPriceModel::$sku_d."' value='{$keyString[$itemKey][SpecGoodsPriceModel::$sku_d]}' />
                </td>";
            } else {
                $str .="<td><input type='text' data-key='{$itemKey}'  name='price_market'  onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
    
                $str .="<td><input type='text' data-key='{$itemKey}'  name='price_member'  onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
    
                $str .="<td><input type='text' data-key='{$itemKey}' value='0' name='stock'  onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
                 
                $str .="<td><input type='text' data-key='{$itemKey}' name='weight'  onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' /></td>";
    
                $str .="<td><input type='text' data-key='{$itemKey}' name='".SpecGoodsPriceModel::$sku_d."' /></td>";
            }
            $str .='</tr>';
        }
        $str .='</table>';
        return $str;
    }
    
    /**
     * @param array $goodsId 商品编号数组
     * @return
     */
    public function addSpecByGoods( array $goodsId)
    {
    	$data = empty($this->addSpecData) ? $this->data : $this->addSpecData;
        
        if (empty($data) || empty($goodsId)) {
            return array();
        }
    
        $specId = array_keys($data);
    
        $build  = array();
         
    
        $skuFirstCheck = [];
    
        foreach ($data as $key => $value) {
            $build[] = $this->modelObj->create($value);
            $skuFirstCheck [] = $value[SpecGoodsPriceModel::$sku_d];
        }
        $arrayObj = new ArrayChildren($skuFirstCheck);
        
        if ($arrayObj->isSameValueByArray()) {
            $this->modelObj->rollback();
    
            $this->errorMessage = '不允许重复编码';
    
            return false;
        }    
    	
        foreach ($goodsId as $key => $value) {
            $build[$key][SpecGoodsPriceModel::$goodsId_d] = $value;
        }
        
        foreach ($specId as $key => $value) {
            $build[$key][SpecGoodsPriceModel::$key_d]     = $value;
    
            if (isset($build[$key][SpecGoodsPriceModel::$id_d])) {
                unset(  $build[$key][SpecGoodsPriceModel::$id_d] );
            }
        }
        
        try {
            $status = $this->modelObj->addAll($build);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            
            $this->modelObj->rollback();
            return false;
        }
       
        if ($status === false) {
            $this->errorMessage = '添加失败';
            
            $this->modelObj->rollback();
        }
    
        $this->modelObj->commit();
        //添加
        return $status;
    }
    
    /**
     * 保存编辑
     * @param array $id 商品序号
     * @return 
     */
    public function saveEdit(array $id)
    {
        $data = $this->data;
        
        if (empty($data) || !is_array($data))
        {
            return $data;
        }
         
        $saveData = array();
        foreach ($data as $key => $value) {
            if (empty($value[SpecGoodsPriceModel::$id_d])) {
                continue;
            }
            $saveData[$key] = $value;
            unset($data[$key]);
        }
        
        //标记变量 是添加还是更新
        $status = false;
         
        if (is_array($id) && !empty($id)) {
        	
        	$this->addSpecData = $data;
        	
            $status = $this->addSpecByGoods($id);
        }
        
        if (empty($saveData)) {
    		
            return $status;
        }
		
        $this->modelObj->startTrans();
        
        $this->specData = $saveData;
       
        $sql = $this->buildUpdateSql();
        try {
            $status = $this->modelObj->execute($sql);
        }catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
       
        if (!$this->modelObj->traceStation($status)) {
            return false;
        }
        $this->modelObj->commit();
        return $status;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getColumToBeUpdated():array
     */
    protected function getColumToBeUpdated():array
    {
        return [
            SpecGoodsPriceModel::$key_d,
            SpecGoodsPriceModel::$sku_d,
        ];
    }
    
    /**
     * 69
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getDataToBeUpdated():array
     */
    protected function getDataToBeUpdated():array
    {
        $saveData = $this->specData;
        //批量更新
        $pasrseData = array();
        foreach ($saveData as $key => $value)
        {
            $pasrseData[$value[SpecGoodsPriceModel::$id_d]][] = $key;
            $pasrseData[$value[SpecGoodsPriceModel::$id_d]][] = $value[SpecGoodsPriceModel::$sku_d];
        }
        return $pasrseData;
    }
    
    /**
     * 通过顶级商品删除子类商品
     * @return bool
     */
    public function deleteGoodsBySpec()
    {
        $status = $this->modelObj->where(SpecGoodsPriceModel::$pId_d.' = :pId')->bind([':pId' => $this->data['id']])->delete();
        
        if (!$this->traceStation($status)) {
            return false;
        }
        
        $this->modelObj->commit();
        
        return $status;
    }
    
    /**
     * 删除子类具体商品
     * @return bool
     */
    public function deleteGoodsBySKU()
    {
        $status = $this->modelObj->where(SpecGoodsPriceModel::$goodsId_d.' = :id')->bind([':id' => $this->data['id']])->delete();
        
        if (!$this->traceStation($status)) {
            return false;
        }
        
        $this->modelObj->commit();
        
        return $status;
    }
    
    /**
     * 获取规格项编号
     * @return [];
     */
    public function getSpecItemPrice()
    {
        if (empty($this->data['id_string'])) {
            return [];
        }
        
        $specData = $this->modelObj->where(SpecGoodsPriceModel::$goodsId_d.' in (%s)', $this->data['id_string'])->getField("GROUP_CONCAT(`key` SEPARATOR '_') AS items_id");
        if (empty($specData)) {
            return [];
        }
        
        return array_unique(explode('_', $specData));
    }
}