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
namespace Admin\Logic;

use Common\Logic\AbstractGetDataLogic;
use Admin\Model\GoodsClassModel;
use Common\Tool\Extend\ArrayChildren;
use Common\Tool\Extend\PinYin;
use Think\Cache;
use Common\Tool\Extend\Tree;
use Common\Tool\Tool;
/**
 * 商品分类逻辑处理
 * @author 王强
 * @version 1.0
 */
class GoodsClassLogic extends AbstractGetDataLogic
{
    /**
     * 分类键名
     * @var array
     */
    private $keyByClass = [
        'one_class',
        'two_class',
        'three_class'
    ];
    
    /**
     * 分类数据
     * @var unknown
     */
    private $bindClassData = [];
    
    /**
     * 整合分类
     * @var array
     */
    private $totalClassData = []; //
    
    
    /**
     * 架构方法
     */
    public function __construct($data, $split = null, array $bindData = [])
    {
        $this->data = $data;

        $this->splitKey = $split;

        $this->bindClassData = $bindData;
        
        $this->modelObj = GoodsClassModel::getInitnation();
    }

    /**
     * 获取商品分类
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
       
        $data = $this->getClassData();
  
        if (empty($data)) {
            return [];
        }
        
        $data = array_chunk($data, 3, true);
        
        $receive = [];
        $tmp = [];
        
        foreach ($data as $key => $value) {
            
            foreach ($value as $name) {
                $tmp []= $name[GoodsClassModel::$className_d];
            }
            $receive [] = $tmp;
            $tmp = [];
        }
        return $receive;
        
    }

    /**
     * 获取分类数据
     * @return array
     */
    private function getClassData()
    {
        $where = implode(',', $this->data);
        
        if (empty($where)) {
            return [];
        }
        
        $field = GoodsClassModel::$id_d.','.GoodsClassModel::$className_d.','.GoodsClassModel::$fid_d;
        
        $data = $this->modelObj->where(GoodsClassModel::$id_d.' in (%s)', $where)->order('SUBSTRING_INDEX("' . $where . '",' . GoodsClassModel::$id_d . ', 1)')->getField($field);
        
        return $data;
    }

    /**
     * 处理分类数据
     * @return array
     */
    public function parseClassData()
    {
        $data = $this->getClassData();
        
        if (empty($data)) {
            return [];
        }
        return $this->buildGoodsClassData($data);
    }
    
    
    /**
     * 生成商品分类数据
     * @param array $data
     */
    protected function buildGoodsClassData(array $data)
    {
    	foreach ($this->bindClassData as $key => & $value) {
    		
    		$value['one_name'] = $data[$value['class_one']][GoodsClassModel::$className_d];
    		
    		$value['two_name'] = $data[$value['class_two']][GoodsClassModel::$className_d];
    		
    		$value['three_name'] = $data[$value['class_three']][GoodsClassModel::$className_d];
    	}
    	
    	return $this->bindClassData;
    }

    /**
     * 添加商品分类
     */
    public function addClass()
    {

        try{

            if(empty($this->data) || $this->isAvailableData() === false){
                return [];
            }

            if($this->modelObj->isChild($this->data['fid']) === false || $this->data['fid'] == $this->data['id']){
                $this->errorMessage = '无效的上级分类';
                return [];
            }

            $this->data['store_id'] = session('store_id');
            //保存商品分类表
            if(!$this->modelObj->add($this->data)){
                $this->errorMessage = '添加分类失败';
                return false;
            }
            return true;
        }catch (\Exception $e){

            $this->errorMessage = '该分类已存在!';
            return [];

        }

    }

    /**
     * 删除商品分类和子级分类
     */
    public function deleteClass()
    {

        //验证是否存在分类
        if($this->modelObj->isExistClass($this->data['id']) === false){
            $this->errorMessage = '不存在的分类';
            return false;
        }

        $classIds = $this->modelObj->getAllChildId($this->data['id']);
        if(!$this->modelObj->delAllClassById($classIds)){
            $this->errorMessage = '删除失败,稍后再试';
            return false;
        }

        return true;

    }

    /**
     * 返回模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return GoodsClassModel::class;
    }

    /**
     * 获取商品分类
     * @return array
     */
    public function getClassDataByStatus()
    {
        //获取商品列表
        $classData = S('classData');
        if (empty($classData)) {
            $classData = $this->modelObj->where(array(
                GoodsClassModel::$hideStatus_d => 1,
            ))->getField(GoodsClassModel::$id_d.','.GoodsClassModel::$className_d);
    
        } else {
            return $classData;
        }
    
        if (empty($classData)) {
            return array();
        }
        
        $pinObj = new PinYin();
        
        foreach ($classData as $key => & $value) {
            $pinObj->setStr($value);
            $value = $pinObj->getFirstEnglish().' '. $value;
        }
        
        $classData = (new ArrayChildren($classData))->sortByValue(); //保持键名排序
        
        S('classData', $classData, 60);
    
        return $classData;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice()
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();

        $message = [
            GoodsClassModel::$className_d => [
                'required' => '请输入'.$comment[GoodsClassModel::$className_d],
                'specialCharFilter' => $comment[GoodsClassModel::$className_d].'不能输入特殊字符'
            ],
            GoodsClassModel::$hideStatus_d => [
                'required' => '请选择显示状态',
                'number' => '显示状态必须是数字'
            ],
            GoodsClassModel::$sortNum_d => [
                'required' => '请输入分类排序',
                'number' => '序号必须是数字'
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
            GoodsClassModel::$className_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            GoodsClassModel::$hideStatus_d => [
                'required' => true,
                'number' => true
            ],
            GoodsClassModel::$sortNum_d => [
                'required' => true,
                'number' => true
            ]
        ];
        return $validate;
    }

    /**
     * 获取分类详细信息
     */
    public function getGoodsClassInfo()
    {

        //验证是否存在分类
        if($this->modelObj->isExistClass($this->data['id']) === false){
            $this->errorMessage = '不存在的分类';
            return false;
        }

        $result = $this->modelObj->where(['store_id'=> session('store_id') , 'id'=> $this->data['id']])->find();
        return $result;
    }

    /**
     * 编辑商品分类
     */
    public function saveClass()
    {

        try{

            if(empty($this->data) || $this->isAvailableData() === false){
                return [];
            }

            if($this->modelObj->isChild($this->data['fid']) === false || $this->data['fid'] == $this->data['id']){
                $this->errorMessage = '无效的上级分类';
                return [];
            }

            if($this->modelObj->save($this->data)===false){
                $this->errorMessage = "修改商品类型失败";
                return false;
            }
            return true;

        }catch (\Exception $e){

            $this->errorMessage = '该分类已存在!';
            return [];
        }


    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice()
     */
    public function getChangeMessageNotice()
    {

        $message = [
            GoodsClassModel::$id_d => [
                'required' => '请输入分类编号',
                'number' => '分类编号必须为数字'
            ],
            GoodsClassModel::$hideStatus_d => [
                'required' => '请选择显示状态',
                'number' => '显示状态必须是数字'
            ],

        ];
        return $message;
    }

    /**
     * 修改商品分类显示状态
     */
    public function changeStatus()
    {
        if(empty($this->data) || $this->isAvailableData() === false){
            return [];
        }
        if($this->modelObj->save($this->data)===false){
            $this->errorMessage = "修改失败,请稍后再试";
            return false;
        }
        return true;
    }

    /**
     * 判断数据有效性
     */
    private function isAvailableData()
    {

        //验证状态显示
        if(!empty($this->data['hide_status'])){
            if($this->data['hide_status'] != 0 && $this->data['hide_status'] != 1){
                $this->errorMessage = '请选择正确的显示状态';
                return false;
            }
        }

        return true;
    }

    /**
     * 获取所有分类
     */
    public function getAllClass()
    {

        $class = $this->modelObj->getClassData();
        sort($class);

        return $class;
    }

    /**
     * 分级获取分类
     */
    public function getGoodsClass()
    {
        $fid = $this->data['fid'];
        if(empty($fid)){
            $fid = 0;
        }
        $data = $this->modelObj->getClassByFid($fid);

        return $data;


    }

    /**
     * 根据class_id获取分类名称
     * @param array $data  其他模型 读取的数据
     * @param string $split 以哪个字段拼接相关联的字段
     * @return array
     */
    public function getDataByClassId()
    {

        if (empty($this->data)) {
            return array();
        }
        $field = [
            GoodsClassModel::$className_d,
            GoodsClassModel::$id_d,
        ];



        return $this->getDataByOtherModel($field,GoodsClassModel::$id_d);
    }

     /**
     * 检查分类编号
     * @return string[][]
     */
    public function checkClassId()
    {
        return [
            'goods_class_id' => [
                'number' => '商品分类必须是数字'
            ]
        ];
    }

     /**
     * 根据编号获取缓存分类
     * @return array
     */
    public function getCacheByClass()
    {
        
        $cache = Cache::getInstance('', ['expire' => 30]);
        
        $key = 'cache_class_'.$this->data['goods_class_id'].'_sdf';
        
        $data  = $cache->get($key);
        
        if (empty($data)) {
            $data =  $this->modelObj
                ->field(GoodsClassModel::$id_d.','.GoodsClassModel::$className_d.','.GoodsClassModel::$fid_d)
                ->where(GoodsClassModel::$fid_d.' = %d and '.GoodsClassModel::$hideStatus_d.' = 1', $this->data['goods_class_id'])
                ->select();
        } else {
            return $data;
        }
        
        $data = (new ArrayChildren($data))->convertIdByData(GoodsClassModel::$id_d);
        
        $cache->set($key, $data);
        
        return $data;
    }
    
    /**
     * 根据可发布商品类目获取商品分类
     */
    public function getClassDataByPublicationOfCommodity()
    {
       
        if (empty($this->data[$this->keyByClass[$this->data['class_number']]])) {
            return [];
        }
        
        $field = GoodsClassModel::$id_d.','.GoodsClassModel::$className_d;
        
        $classData = $this->modelObj
            ->where(GoodsClassModel::$id_d.' in ( %s)', $this->data[$this->keyByClass[$this->data['class_number']]])
            ->getField($field);
        return $classData;
    }
    
    /**
     *  重组分类数据
     */
    public function buildClass ()
    {
    	$array = array(
    			'field' => $this->getTableColum(),
    			'where' => [GoodsClassModel::$fid_d => 0, GoodsClassModel::$hideStatus_d => 1],
    			'order' => GoodsClassModel::$sortNum_d.' desc '.','.GoodsClassModel::$createTime_d.' desc '
    	);
    	
    	$data = $this->getDataByPage($array);
    	
    	if (empty($data['data'])) {
    		return array();
    	}
    	
    	$second = $this->getNextClass($data['data']);
    
    	$three  = $this->getNextClass($second);
    	
    	$data['data'] = array_merge($second, $three, $data['data']);
    	
    	$data['data'] = (new Tree($data['data']))->makeTreeForHtml(array(
    			'parent_key' => GoodsClassModel::$fid_d
    	));
    	
    	if (empty($data['data'])) {
    		return [];
    	}
    	
    	return $data;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
     */
   	protected function getTableColum():array
   	{
   		return [
   			GoodsClassModel::$id_d,
   			GoodsClassModel::$className_d,
   			GoodsClassModel::$picUrl_d,
   			GoodsClassModel::$fid_d
   		];
   	}
    
    /**
     * 处理条件
     * @return array
     */
    protected function parseOption(array $options) :array
    {
    	return $options;
    }
    
    
    /**
     * 获取下级分类
     * @param array $data
     * @return []
     */
    protected function getNextClass (array $data)
    {
    	if (empty($data)) {
    		return array();
    	}
    	
    	$idString = Tool::characterJoin($data, GoodsClassModel::$id_d);
    	
    	$second = $this->modelObj->field($this->getTableColum())->where(GoodsClassModel::$fid_d.' in (%s) and '.GoodsClassModel::$hideStatus_d.' = 1', $idString)->select();
    	if (empty($second)) {
    		return array();
    	}
    	
    	return $second;
    }
    
    protected function getPageNumber() :int
    {
    	return 5;
    }
}