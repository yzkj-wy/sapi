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
namespace Common\Logic;

use Admin\Model\GoodsSpecItemModel;
use Think\Cache;
use PlugInUnit\Validate\Children\SpecialCharFilter;
use PlugInUnit\Validate\Children\Number;

class GoodsSpecItemLogic extends AbstractGetDataLogic
{
    /**
     * 商品分类id 字符串
     * @var string
     */
    private $classIdString = '';
    
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;

        $this->modelObj = new GoodsSpecItemModel();

        $this->splitKey = $split;

    }

    /**
     * 获取店铺地址数据
     */
    public function getResult()
    {

    }
    
    /**
     * 获取规格组关联字段
     * @return string
     */
    public function getSplitKeyBySpec()
    {
        return GoodsSpecItemModel::$specId_d;
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    protected function hideenComment()
    {
        return [

        ];
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
        return [
            GoodsSpecItemModel::$item_d
        ];
    }

    /**
     * 返回主键
     * @return string
     */
    public function getIdSplitKey()
    {
        return GoodsSpecItemModel::$id_d;
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return GoodsSpecItemModel::class;
    }

    /**
     * 查询规格项
     */
    public function getSpecItems() :array
    {
        $data = $this->data;
        foreach($data as $k => $v)
        {       // 获取规格项
            $arr = $this->modelObj->getSpecItem($v['id']);

            $data[$k]['spec_item'] = implode(' , ', $arr);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();

        $message = [
            GoodsSpecItemModel::$item_d => [
                'required' => '请输入'.$comment[GoodsSpecItemModel::$item_d],
                'specialCharFilter' => $comment[GoodsSpecItemModel::$item_d].'不能输入特殊字符'
            ]
        ];

        return $message;
    }

    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate() :array
    {
        $validate = [
            GoodsSpecItemModel::$item_d => [
                'required' => true,
                'specialCharFilter' => true
            ]
        ];
        return $validate;
    }

    /**
     * 规格项排列
     */
    public function specItemArrange()
    {
        $data = $this->data;
     
        if(empty($data)){
            return [];
        }
        
        $idString = $this->getIdString();
        
        $key = md5($idString.'_'.$_SESSION['store_id']);

        $cache = Cache::getInstance('', ['expire' => 45]);

        $specData = $cache->get($key);
        
        if (empty($specData)) {
            $specData = $this->getSlaveDataByMaster();
        } else {
            return $specData;
        }
        
        if (empty($specData)) {
            return $data;
        }
        $cache->set($key, $specData);

        return $specData;
    }

    /**
     * 数据处理组合
     * @param array $slaveData
     * @param string $slaveColumnWhere
     * @return array
     */
    protected function parseSlaveData(array $slaveData, $slaveColumnWhere) :array
    {
        $data = $this->data;
        
        $specGroup = [];
        
        $specGroup = [
            'group'     => $data,
            'children'  => $slaveData,
        ];
        
        return $specGroup;
    }
    
     /**
     * 获取从表生成where条件的字段（根据主表数据查从表数据的附属方法）
     */
    protected function getSlaveColumnByWhere() :string
    {
        return GoodsSpecItemModel::$specId_d;
    }


    /**
     * 检测数据
     */
    public function checkValidateBySpec()
    {
        $comment = $this->modelObj->getComment();

        return [
            GoodsSpecItemModel::$specId_d => [
                'number' => $comment[GoodsSpecItemModel::$specId_d].'必须是数字',
            ],
        ];
    }

    /**
     * 获取规格数据
     */
    public function getSpecData()
    {
        $cache = Cache::getInstance('', ['expire' => 60]);

        $key = 'ssd_'.$this->data[GoodsSpecItemModel::$specId_d].'_'.$_SESSION['store_id'];

        $data = $cache->get($key);

        if (empty($data)) {

            $field = [
                GoodsSpecItemModel::$specId_d,
                GoodsSpecItemModel::$id_d,
                GoodsSpecItemModel::$item_d
            ];
            
            $data = $this->modelObj
                    ->field($field)
                    ->where(GoodsSpecItemModel::$specId_d.'=:spec_id and '.GoodsSpecItemModel::$storeId_d.'='.$_SESSION['store_id'])
                    ->bind([':spec_id' => $this->data[GoodsSpecItemModel::$specId_d]])
                    ->select();

        } else {
            return $data;
        }

        if (empty($data)) {
            return [];
        }

        $cache->set($data);

        return $data;
    }
    
    /**
     * 商品添加时处理
     * @return mixed|NULL|unknown|string[]|unknown[]|object
     */
    public function getDataBySpecialItem()
    {
        $cache = Cache::getInstance('', ['expire' => 60]);
       
        $spec = implode(',', array_keys($this->data['spec']));
        
        $key = 'item_'.$_SESSION['store_id'].base64_encode($spec);
        
        $data = $cache->get($key);
        
        if (empty($data)) {
        
            $field =  GoodsSpecItemModel::$id_d . ',' . GoodsSpecItemModel::$item_d . ',' . GoodsSpecItemModel::$specId_d;
        
            $data = $this->modelObj
            ->where(GoodsSpecItemModel::$specId_d.' in(%s) and '.GoodsSpecItemModel::$storeId_d.'='.$_SESSION['store_id'], $spec)
            ->getField($field);
        } else {
            return $data;
        }
        
        if (empty($data)) {
            return [];
        }
        
        $cache->set($key, $data);
        
        return $data;
    }
    
    /**
     * 根据规格生成对应得 商品名称
     * @return array
     */
    public function getGoodsNameByItem()
    {
        if (empty($this->data['item'])) {
            return array();
        }
        
        $data = $this->data['item'];
       
        $itemIds = str_replace('_', ',', implode('_', array_keys($data)));
         
        if (empty($itemIds)) {
            return array();
        }
    
        $itemData = $this->modelObj->where( array(GoodsSpecItemModel::$id_d => array('in', $itemIds)) )->getField(GoodsSpecItemModel::$id_d.','.GoodsSpecItemModel::$item_d);
    
        if (empty($itemData)) {
            return array();
        }
    
        $flag = null;
    
        $name = null;
    
        $titleKey = 'title';
        $title    = $_SESSION['goods_info']['title'];
        
        foreach ($data as $key => & $value) {
    
            $flag = explode('_', $key);
    
            foreach ($flag as $itemValue) {
    
                if (!array_key_exists($itemValue, $itemData)) {
                    continue;
                }
                $name .= ' '.$itemData[$itemValue];
                $value[$titleKey] = $title . ' '.substr($name, 1);
            }
            $name = null;
        }
       
        return $data;
    }
    
    /**
     * 规格项验证消息（添加）
     * @return bool
     */
    public function CheckMessageBySpecialItem()
    {
        if (empty($this->data['item'])) {
            return false;
        }
        
        $item = $this->data['item'];
        
        $spec = new SpecialCharFilter("");
        
        foreach ($item as $value) {
            
            $spec->setData($value);
            
            if (!$spec->check()) {
                $this->errorMessage = '请不要输入特殊字符';
                return false;
            }
        }
        return true;
    }
    
    /**
     * 规格项验证消息（保存）
     */
    public function CheckMessageSaveBySpecialItem()
    {
        if (empty($this->data['spec'])) {
            return false;
        }
        
        $spec = new SpecialCharFilter("");
        
        $number = new Number(0);
        
        foreach ($this->data['spec'] as $value) {
            
            $spec->setData($value[GoodsSpecItemModel::$item_d]);
            
            if (!$spec->check()) {
                $this->errorMessage = '请不要输入特殊字符';
                return false;
            }
            
            $number->setData($value[GoodsSpecItemModel::$id_d]);
            
            if (!$number->check()) {
                $this->errorMessage = 'id 必须是数字';
                return false;
            }
        }
        return true;
    }
    
    /**
     * 添加规格
     * @return boolean|boolean|string|unknown
     */
    public function addSpecItem()
    {
        if (empty($this->data[GoodsSpecItemModel::$specId_d])) {
            $this->errorMessage = '数据错误';
            return false;
        }
        
        $addData = [];
        
        $i = 0;
        
        foreach ($this->data['item'] as $key => $value) {
            
            $addData[$i][GoodsSpecItemModel::$specId_d] = $this->data[GoodsSpecItemModel::$specId_d];
            
            $addData[$i][GoodsSpecItemModel::$item_d]   = $value;
            
            $addData[$i][GoodsSpecItemModel::$storeId_d]   = $_SESSION['store_id'];
            
            $i++;
        }
        
        $status = false;
        
         try {
             $status = $this->modelObj->addAll($addData);
         } catch (\Exception $e) {
             $this->errorMessage = $e->getMessage();
             return false;
         }
         return $status;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum() :array
     */
    protected function getTableColum() :array
    {
        return [
            GoodsSpecItemModel::$id_d,
            GoodsSpecItemModel::$item_d,
            GoodsSpecItemModel::$specId_d
        ];
    }
    
    /**
     * 批量保存商品规格项
     */
    public function saveListBySpec()
    {
        $sql = $this->buildUpdateSql();
        
        return $this->modelObj->execute($sql);
    }
        
    /**
     * 要更新的数据【已经解析好的】
     * @return array
     */
    protected function getDataToBeUpdated():array
    {
        $parseData = $this->data['spec'];
        
        $arr = [];
        foreach ($parseData as $key => $value) {
            $arr[$value[GoodsSpecItemModel::$id_d]][] = $value[GoodsSpecItemModel::$item_d];
        }
        return $arr;
    }
    
    /**
     * 要更新的字段
     * @return array
     */
    protected function getColumToBeUpdated():array
    {
        return [
            GoodsSpecItemModel::$item_d
        ];
    }
    
}