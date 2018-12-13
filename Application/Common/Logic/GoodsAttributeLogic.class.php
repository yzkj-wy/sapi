<?php


namespace Common\Logic;


use Admin\Model\GoodsAttributeModel;
use Admin\Model\GoodsTypeModel;
use Think\Page;
use Think\Cache;

class GoodsAttributeLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array  $data
     * @param string $split
     */
    public function __construct(array $data, $split = null)
    {
        $this->data = $data;

        $this->splitKey = $split;

        $this->modelObj = new GoodsAttributeModel();
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult(){}

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return GoodsAttributeModel::class;
    }

    
    //获取分类结果
    public function getPageResult($index = false)
    {

        $model = $this->modelObj;
        if($index) {
            $where = [
                'status'=>1,
                'store_id' => session('store_id'),
                'type_id' => $this->data['type_id']
            ];
        }else{
            $where = ['status'=>1,'store_id' => session('store_id')];
        }
        $count = $model->where($where)->count();
        //获取分页配置
        $page_setting = C('PAGE_SETTING');
        $page = new Page($count, $page_setting['PAGE_SIZE']);
        //总页数
        $page_num = ceil($page->totalRows / $page->listRows);
        $rows = $model->where($where) ->limit($page->firstRow.','.$page->listRows)->select();

        return compact(['rows','page_num']);

    }

    /**
     * 获取属性详细信息
     * @param int $id
     */
    public function getGoodsAttrInfo()
    {

        $row = $this->modelObj->where(['store_id'=> session('store_id') , 'id'=> $this->data['id']])->find();
        return $row;

    }

    /**
     * 添加属性
     * @return bool
     */
    public function addAttr(){

        //验证数据
        if(empty($this->data) || $this->isAvailableData()===false){
            return [];
        }

        $this->data['store_id'] = session('store_id');

        //保存商品属性表
        if(($spec_id = $this->modelObj->add($this->data))===false){
            $this->errorMessage = '属性基本信息保存失败';
            return false;
        }

        return true;

    }

    /**
     * 删除商品属性
     * @return bool
     */
    public function deleteAttr(){

        //验证是否存在属性
        if($this->modelObj->isExistAttr($this->data['id']) === false){
            $this->errorMessage = '不存在的属性';
            return false;
        }

        if(!$this->modelObj->where(['store_id'=> session('store_id') , 'id'=> $this->data['id']])->delete()){
            $this->errorMessage = '无效的属性编号';
            return false;
        }
        return true;
    }

    /**
     * 修改商品属性和商品属性选项
     * @param array $newdata 前端传过来的数据
     * @return bool
     */
    public function saveAttr(){

        //验证数据
        if(empty($this->data) || $this->isAvailableData()===false){
            return [];
        }

        //验证是否存在属性
        if($this->modelObj->isExistAttr($this->data['id']) === false){
            $this->errorMessage = '属性不存在,请先添加';
            return false;
        }

        //修改商品属性表
        if($this->modelObj->save($this->data)===false){
            $this->errorMessage = "修改商品属性失败";
            return false;
        }

        return $this->modelObj->commit();

    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();

        $message = [
            GoodsAttributeModel::$attrName_d => [
                'required' => '请输入'.$comment[GoodsAttributeModel::$attrName_d],
                'specialCharFilter' => $comment[GoodsAttributeModel::$attrName_d].'不能输入特殊字符'
            ],
            GoodsAttributeModel::$typeId_d => [
                'required' => '请输入'.$comment[GoodsAttributeModel::$typeId_d],
                'number' => $comment[GoodsAttributeModel::$typeId_d] . '必须为数字'
            ],
            GoodsAttributeModel::$attrIndex_d => [
                'required' => '请选择是否筛选',
                'number' => '选择检索必须是数字'
            ],
            GoodsAttributeModel::$inputType_d => [
                'required' => '请选择属性录入方式',
                'number' => '录入方式必须是数字'
            ],
            GoodsAttributeModel::$attrValues_d => [
                'required' => '请输入' . $comment[GoodsAttributeModel::$attrValues_d],
                'specialCharFilter' => $comment[GoodsAttributeModel::$attrValues_d] . '不能有特殊字符'
            ]
        ];
        return $message;
    }

    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate()
    {
        $validate = [
            GoodsAttributeModel::$attrName_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            GoodsAttributeModel::$typeId_d => [
                'required' => true,
                'number' => true
            ],

            GoodsAttributeModel::$attrIndex_d => [
                'required' => true,
                'number' => true
            ],
            GoodsAttributeModel::$inputType_d => [
                'required' => true,
                'number' => true
            ],
            GoodsAttributeModel::$attrValues_d = [
                'required' => true,
                'specialCharFilter' => true
            ]
        ];
        return $validate;
    }

    /**
     * 验证数据有效性
     */
    private function isAvailableData()
    {

        //验证是否检索(必须为0或1)
        if(!empty($this->data['attr_index'])){
            if($this->data['attr_index'] != 0 && $this->data['attr_index'] != 1){
                $this->errorMessage = '请选择正确的检索方式';
                return false;
            }
        }

        //验证录入方式
        if(!empty($this->data['input_type'])){
            if($this->data['input_type'] != 0 && $this->data['input_type'] != 1 && $this->data['input_type'] != 2){
                $this->errorMessage = '请选择正确的录入方式';
                return false;
            }
        }

        //验证类型是否存在
        $type = GoodsTypeModel::getInitnation();
        if($type->isExistType($this->data['type_id']) === false){
            $this->errorMessage = '不存在的类型,请先添加';
            return false;
        }

        return true;
    }

    /**
     * 改变筛选状态
     */
    public function changeIndex()
    {
        //验证状态显示
        if(!empty($this->data['attr_index'])){
            if($this->data['attr_index'] != 0 && $this->data['attr_index'] != 1){
                $this->errorMessage = '请选择正确的检索状态';
                return false;
            }
        }

        //验证是否存在规格
        if($this->modelObj->isExistAttr($this->data['id']) === false){
            $this->errorMessage = '不存在的属性';
            return false;
        }

        //修改商品规格表
        if($this->modelObj->save($this->data)===false){
            $this->errorMessage = "修改规格状态失败";
            return false;
        }

        return true;
    }

    /**
     * 改变检索时的验证
     */
    public function getChangeMessageNotice()
    {

        $message = [

            GoodsAttributeModel::$id_d => [
                'required' => '请选择要修改的属性编号',
                'number' => '属性编号必须为数字'
            ],
            GoodsAttributeModel::$attrIndex_d => [
                'required' => '请选择检索状态',
                'number' => '检索状态必须是数字'
            ]

        ];
        return $message;

    }
    
    /**
     * 根据分类获取属性
     * @return array
     */
    public function getAttributeByClass()
    {
        $key = $_SESSION['store_id'].'_sdk'.$this->data['class_three'];
        
        $cache = Cache::getInstance('', ['expire' => 60]);
        
        $data = $cache->get($key);
        
        if (empty($data)) {
            $field = GoodsAttributeModel::$id_d.','.GoodsAttributeModel::$attrName_d;
            
            $data = $this->modelObj
                ->where(GoodsAttributeModel::$classThree_d.'=:class_id')
                ->bind([':class_id' => $this->data[GoodsAttributeModel::$classThree_d]])
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
     * 根据商品类型编号 获取商品属性数据
     * @return array
     */
    public function getAttributeByTypeId ()
    {
        $cache = Cache::getInstance('', ['expire' => 60]);
        
        $key = $_SESSION['store_id'].'_dsk'.$this->data[GoodsAttributeModel::$classThree_d];
    
        $data = $cache->get($key);
        if (empty($data)) {
           $field = GoodsAttributeModel::$id_d.','.GoodsAttributeModel::$attrName_d.','.GoodsAttributeModel::$inputType_d.','.GoodsAttributeModel::$attrValues_d;
           
           $data = $this->modelObj
                ->field($field)
                ->where(GoodsAttributeModel::$classThree_d.'=:cid')
                ->bind([':cid' => $this->data[GoodsAttributeModel::$classThree_d]])
                ->select();
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
     * 验证消息
     * @return []
     */
    public function getMessageByGetAttribute()
    {
        $comment = $this->modelObj->getComment();
        
        return [
            GoodsAttributeModel::$classThree_d => [
                'number' => $comment[GoodsAttributeModel::$classThree_d].' 必须是数字'
            ]
        ];
    }
    
    /**
     * 验证
     */
    public function checkMessageByClassId()
    {
        $comment = $this->modelObj->getComment();
        
        return [
            GoodsAttributeModel::$classThree_d => [
                'number' => $comment[GoodsAttributeModel::$classThree_d].'必须是数字'
            ]
        ];
    }
    
    /**
     * 验证检索条件
     */
    public function validateType()
    {

        if(empty($this->data['type_id'])){
            $this->errorMessage = '条件不存在';
            return false;
        }

        //验证类型是否存在
        $type = GoodsTypeModel::getInitnation();
        if($type->isExistType($this->data['type_id']) === false){
            $this->errorMessage = '条件不存在';
            return false;
        }

        return true;
    }

}