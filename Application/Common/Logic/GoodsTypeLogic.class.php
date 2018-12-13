<?php


namespace Common\Logic;


use Admin\Model\GoodsTypeModel;
use Think\Page;

class GoodsTypeLogic extends AbstractGetDataLogic
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

        $this->modelObj = new GoodsTypeModel();
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
        return GoodsTypeModel::class;
    }

    /**
     * 组合数据
     * @param array $data  其他模型 读取的数据
     * @param string $split 以哪个字段拼接相关联的字段
     * @return array
     */
    public function getDataByGoodsAttribute()
    {

        if (empty($this->data)) {
            return array();
        }
        $field = [
            GoodsTypeModel::$name_d,
            GoodsTypeModel::$id_d
        ];



        return $this->getDataByOtherModel($field,GoodsTypeModel::$id_d);
    }

    //获取分类结果
    public function getPageResult()
    {
        $model = $this->modelObj;
        $count = $model->where(['status'=>1,'store_id' => session('store_id')])->count();
        //获取分页配置
        $page_setting = C('PAGE_SETTING');
        $page = new Page($count, $page_setting['PAGE_SIZE']);
        //总页数
        $page_num = ceil($page->totalRows / $page->listRows);
        $rows = $model->where(['status'=>1,'store_id' => session('store_id')]) ->limit($page->firstRow.','.$page->listRows)->select();
        return compact(['rows','page_num']);
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();

        $message = [
            GoodsTypeModel::$name_d => [
                'required' => '请输入'.$comment[GoodsTypeModel::$name_d],
                'specialCharFilter' => $comment[GoodsTypeModel::$name_d].'不能输入特殊字符'
            ],
            GoodsTypeModel::$status_d => [
                'required' => '请输入'.$comment[GoodsTypeModel::$status_d],
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
            GoodsTypeModel::$name_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            GoodsTypeModel::$status_d => [
                'required' => true,
            ],

        ];
        return $validate;
    }

    /**
     * 添加类型
     * @param array $newdata 接收前台的数据
     * @return bool
     */
    public function addType(){

        try{

            if(empty($this->data) || $this->validateStatus() === false){
                return [];
            }
            $this->data['store_id'] = session('store_id');
            //保存商品规格表
            if(($spec_id = $this->modelObj->add($this->data))===false){
                $this->errorMessage = '类型基本信息保存失败';
                return false;
            }
            return true;

        }catch (\Exception $e){

            $this->errorMessage = '该类型已存在,请勿重复添加';
            return [];

        }

    }

    /**
     * 删除商品类型
     * @param int  $id
     * @return bool
     */
    public function deleteType(){

        //验证是否存在类型
        if($this->modelObj->isExistType($this->data['id']) === false){
            $this->errorMessage = '类型不存在';
            return false;
        }

        //删除商品类型表
        if(!$this->modelObj->where(['store_id'=> session('store_id') , 'id'=> $this->data['id']])->delete()){
            $this->errorMessage = '删除失败';
            return false;
        }
        return true;
    }

    /**
     * 获取类型详细信息
     * @param int $id
     */
    public function getGoodsTypeInfo()
    {
        //验证是否存在类型
        if($this->modelObj->isExistType($this->data['id']) === false){
            $this->errorMessage = '类型不存在';
            return false;
        }

        $row = $this->modelObj->where(['store_id'=> session('store_id') , 'id'=> $this->data['id']])->find($id);

        return $row;
    }

    /**
     * 修改商品类型
     * @param array $newdata 前端传过来的数据
     * @return bool
     */
    public function saveType(){

        try{

            //验证数据
            if(empty($this->data) || $this->validateStatus() === false){
                return [];
            }

            //验证是否存在类型
            if($this->modelObj->isExistType($this->data['id']) === false){
                $this->errorMessage = '分类不存在';
                return false;
            }

            if($this->modelObj->save($this->data)===false){
                $this->errorMessage = "修改商品类型失败";
                return false;
            }
            return true;

        }catch (\Exception $e){

            $this->errorMessage = '该类型已存在,请勿重复添加';
            return [];

        }

    }

    /**
     * 验证状态有效性
     */
    private function validateStatus()
    {
        //验证状态显示
        if(!empty($this->data['status'])){
            if($this->data['status'] != 0 && $this->data['status'] != 1){
                $this->errorMessage = '请选择正确的状态';
                return false;
            }
        }
        return true;
    }

    /**
     * 获取所有的商品类型
     */
    public function getAllType()
    {
        $data = $this->modelObj->getType();

        $types = [];
        $i = 0;

        foreach ($data as $key => $value){

            $types[$i]['id'] = $key;
            $types[$i]['name'] = $value;
            $i++;

        }
        return $types;
    }
}