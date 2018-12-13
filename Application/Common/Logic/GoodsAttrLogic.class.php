<?php
namespace Common\Logic;

use Admin\Model\GoodsAttrModel;
use Admin\Model\GoodsAttributeModel;
use Common\Tool\Event;
use Common\Tool\Tool;
use PlugInUnit\Validate\Children\Number;

class GoodsAttrLogic extends AbstractGetDataLogic
{

    /**
     * 临时数据
     * @var unknown
     */
    protected $tempData ;
    
    /**
     * 
     * @var array
     */
    protected $dbFields = [];
    
    /**
     * 构造方法
     * @param array $data            
     * @param string $split            
     */
    public function __construct(array $data, $split = null)
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = new GoodsAttrModel();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {}

    /**
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return GoodsAttrModel::class;
    }
    
    /**
     * 生成商品属性 添加所需的HTML
     * @param array $goodsAttributeData 商品属性数据
     * @param BaseModel $goodsAttributeModel 商品属性模型对象
     * @return string
     */
    public function buildHtmlString ()
    {
        $goodsAttributeData = $this->data;
        
        $this->tempData = $this->data;
    
        //获取属性值数据 空
        $data = $this->getAttributeValueData();
        if (empty($data)) {
            foreach ($data as $name => &$vo) { //设置默认值
                
                $field = array_diff($this->getTableColum(), $this->dbFields);
                
                Tool::isSetDefaultValue($vo, $field, '');
            }
        }
        
        
        $str = null;
    
        $addDelAttr = ''; // 加减符号
        
        $tmp = '';
        
        foreach ($data as $key => $value) {
    
            $str .= "<tr class='{$value['id']}'>";
            $addDelAttr = '';
            // 单选属性 或者 复选属性
            if($value['attr_type'] == 1 || $value['attr_type'] == 2)
            {
                //                     if($k == 0)
                    //                         $addDelAttr .= "<a onclick='GoodsOption.addAttribute(this)' href='javascript:void(0);'>[+]</a>&nbsp&nbsp";
                    //                         else
                        //                             $addDelAttr .= "<a onclick='GoodsOption.delAttribute(this)' href='javascript:void(0);'>[-]</a>&nbsp&nbsp";
            }
    
            $str .= "<td>$addDelAttr {$value['attr_name']}</td> <td>";
            
            // 手工录入
            if ($value['input_type'] == 0)
            {
                $str .= "<input type='hidden' value='{$value[$this->splitKey]}' name='attr_{$this->splitKey}[{$value[$this->splitKey]}][{$this->splitKey}]' />";
                
                $tmp = isset($value['attr_goods_value']) ? $value['attr_goods_value'] : '';
                
                $str .= "<input type='text' size='40' value='{$tmp}' name='attr_{$this->splitKey}[{$value[$this->splitKey]}][attr_goods_value]' />";
                
                if (!empty($value['attr_goods_id'])) {
                    $str .= "<input type='hidden' value='{$value['attr_goods_id']}' name='attr_{$this->splitKey}[{$value[$this->splitKey]}][attr_goods_id]' />";
                }
            }
          
            // 从下面的列表中选择（一行代表一个可选值）
            if($value['input_type'] == 1)
            {
                $str .= "<input type='hidden' value='{$value[$this->splitKey]}' name='attr_{$this->splitKey}[{$value[$this->splitKey]}][{$this->splitKey}]' />";
                
                if (!empty($value['attr_goods_id'])) {
                    $str .= "<input type='hidden' value='{$value['attr_goods_id']}' name='attr_{$this->splitKey}[{$value[$this->splitKey]}][attr_goods_id]' />";
                }
                
                $str .= "<select name='attr_{$this->splitKey}[{$value[$this->splitKey]}][attr_goods_value]'>";
                
                $tmpOptionVal = explode("\n", trim($value['attr_values']));
               
                foreach($tmpOptionVal as $k2 => $v2)
                {
                    // 编辑的时候 有选中值
                    $v2 = preg_replace("/\s/","",$v2);
                    if($value['attr_goods_value'] == $v2) {
                        $str .= "<option selected='selected' value='{$v2}'>{$v2}</option>";
                    } else {
                        $str .= "<option value='{$v2}'>{$v2}</option>";
                    }
                }
                $str .= "</select>";
            }
            // 多行文本框
            if($value['input_type'] == 2)
            {
                if (!empty($value['attr_goods_id'])) {
                    $str .= "<input type='hidden' value='{$value['attr_goods_id']}' name='attr_{$this->splitKey}[{$value[$this->splitKey]}][attr_goods_id]' />";
                }
                $str .= "<input type='hidden' value='{$value[$this->splitKey]}' name='attr_{$this->splitKey}[{$value[$this->splitKey]}][{$this->splitKey}]' />";
                
                $str .= "<textarea cols='40' rows='3' name='attr_{$this->splitKey}[{$value[$this->splitKey]}][{$this->splitKey}]'>".isset($value['attr_goods_value']) ? $value['attr_goods_value']:''."</textarea>";
            }
            $str .= "</td></tr>";
        }
        return $str;
    }
    
    /**
     * 根据商品属性编号 获取属性值数据
     */
    public function getAttributeValueData ()
    {
    
        $field = [
            GoodsAttrModel::$id_d .' as attr_goods_id',
            GoodsAttrModel::$attrValue_d.' as attr_goods_value',
            GoodsAttrModel::$attributeId_d
        ];
        
        new Event('parseWhere', $this);
        
        $attrValue = $this->getDataByOtherModel($field, GoodsAttrModel::$attributeId_d);
        
        if (empty($attrValue)) {
            $this->dbFields = $field;
            return array();
        }
         
        return $attrValue;
    }
    
    /**
     * 处理条件
     * @return string
     */
    public function parseWhere($where)
    {
        return $where. ' and '.GoodsAttrModel::$goodsId_d.'='.$_SESSION['insertId'];
    }
    
    /**
     * 获取消息
     * @return array
     */
    public function getMessageByAttr()
    {
        return [
            'attr_id' => [
                'required' => '属性必须存在'
            ]
        ];
    }
    
    /**
     * 添加属性值
     */
    public function addAttributeData ()
    {
        $data = $this->data;
        
        if (empty($data['attr_id'])) {
            return false;
        }
    
        $attrValue = $data['attr_id'];
    
        $i = 0;
        $tmpData = [];
        
        $time = time();
        
        foreach ($attrValue as $key => $value) {
            $tmpData[$i][GoodsAttrModel::$attributeId_d] = $key;
            $tmpData[$i][GoodsAttrModel::$goodsId_d]     = $_SESSION['insertId'];
            $tmpData[$i][GoodsAttrModel::$attrValue_d]   = $value['attr_goods_value'];
            $tmpData[$i][GoodsAttrModel::$createTime_d]  = $time;
            $tmpData[$i][GoodsAttrModel::$updateTime_d]  = $time;
            $i++;
        }
        
        $status =  $this->modelObj->addAll($tmpData);
         
        return $status;
    }
    
    /**
     * 更新商品属性
     * <pre>
     * attr_id => Array
     (
     12 => 翻盖
     11 => 55
     10 => 33
     )
     </pre>
     */
    public function editAttributeData ()
    {
        $post = $this->data;
        
        if (empty($post)) {
            return false;
        }
        if (empty($this->getGoodsParamByAttributeIdAndGoodsId())) {
            $status = $this->addAttributeData();
            
            return $status;
        }
        
        
        $sql = $this->buildUpdateSql();
        
        $status = $this->modelObj->execute($sql);
    
        return $status;
    }
    
    /**
     * 根据商品编号和 商品属性编号获取数据
     */
    public function getGoodsParamByAttributeIdAndGoodsId()
    {
        $post = $this->data['attr_id'];
        
        $id = implode(',', array_keys($post));
        
        if (empty($id)) {
            return [];
        }
        
        $data = $this->modelObj->where(GoodsAttrModel::$attributeId_d.' in (%s) and ' . GoodsAttrModel::$goodsId_d.' = :gid', $id)->bind([':gid' => $_SESSION['insertId']])->select();
        
        return $data;
    }
    
    /**
     * 编辑商品属性消息
     */
    public function getMessageByGoodsAttr()
    {
        if (empty($this->data['attr_id'])) {
            return false;
        }
        
        $data = $this->data['attr_id'];
        
        $number = new Number(0);
        
        foreach ($data as $key => $value) {
            
            if (empty($value['attr_goods_value'])) {
                
                $this->errorMessage = '商品属性值不能为空';
                
                return false;
            }
            
            $number->setData($key);
            
            if (!$number->check() || $key <=0 ) {
                $this->errorMessage = '商品属性编号必须是数字';
                return false;
            }
        }
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getColumToBeUpdated():array
     */
    protected function getColumToBeUpdated():array
    {
        return [
            GoodsAttrModel::$attrValue_d,
            GoodsAttrModel::$attributeId_d,
            GoodsAttrModel::$updateTime_d
        ];
    }
    
    /**
     * 要更新的数据【已经解析好的】
     * @return array
     */
    protected function getDataToBeUpdated():array
    {
        $data = $this->data['attr_id'];
        
        $tmp = [];
        
        $time = time();
        
        foreach ($data as $key => $value) {
            
            $tmp[$value['attr_goods_id']] = [];
            
            $tmp[$value['attr_goods_id']][] = $value['attr_goods_value'];
            
            $tmp[$value['attr_goods_id']][] = $value['id'];
            
            $tmp[$value['attr_goods_id']][] = $time;
        }
        
        return $tmp;
    }
    
}