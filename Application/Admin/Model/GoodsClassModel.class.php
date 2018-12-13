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
use Common\Tool\Tool;
use Common\Tool\Event;
use Common\TraitClass\FlagTrait;
use Common\Tool\Extend\UnlinkPicture;
use Think\Hook;
use Think\Log;
use Think\Page;

/**
 * 商品分类模型
 */
class GoodsClassModel extends BaseModel
{
    use FlagTrait;

    protected  $dataClass;
    
    /**
     * 更新数据
     */
    private static $obj;


    
	private $totalClassData = [];
	
    /***
     * @var  array $classLevel 分类层级
     */
	private $classLevel = array();

	public static $id_d;	//id

	public static $className_d;	//分类名字

	public static $createTime_d;	//创建时间

	public static $sortNum_d;	//排序

	public static $updateTime_d;	//更新时间 

	public static $hideStatus_d;	//是否显示【1 显示  0隐藏】

	public static $picUrl_d;	//图片

	public static $fid_d;	//父id

	public static $type_d;	//1为商品 2旅游 3合伙人 4会员

	public static $shoutui_d;	//是否推荐【1 为推荐   0为不推荐】

	public static $isShow_nav_d;	//是否显示在导航栏0：是；1：否

	public static $description_d;	//分类介绍

	public static $cssClass_d;	//css样式

	public static $hotSingle_d;	//热卖单品【1表示是，2表示否】

	public static $isPrinting_d;	//是否推荐打印耗材【1表示是，0表示否】

	public static $isHardware_d;	//是否办公硬件推荐【1表示是，0表示否】

	public static $pcUrl_d;	//pc 广告分类图


    public static function getInitnation()
    {
        $class = __CLASS__;
        return  static::$obj= !(static::$obj instanceof $class) ? new static() : static::$obj;
    }

    /**
     * 获取所有数据
     */
    public function getlist(){
        $rows = $this->where(['hide_status'=>1])->select();
        return $this->getTree($rows);
    }
    
    /**
     * 根据条件 获取信息 
     */
    public function getListByCondition ($id)
    {
        if (!is_numeric($id)) {
            return array();
        }
        
        $data = $this
                ->where(static::$fid_d.'=%d and '.static::$hideStatus_d.' = 1', (int)$id)
                ->getField(static::$id_d.','.static::$className_d);
        
        return (array)$data;
    }
    
    /**
     * @param unknown $id
     * @return string[]
     */
    public function getParents($id){
        $rows = $this->where(['hide_status'=>1])->select();
        $row = $this->getTree($rows,0,0,$id);
        return $row;
    }

    /**
     * 树形菜单
     */
    public function getTree($arr,$pid=0,$deep=0,$id=-1){

        static $data = array();
        foreach($arr as $row){
            if($row['fid'] ==$pid &&$row['fid']!=$id&&$row['id']!=$id){
                $row['deep'] = $deep;
                $row ['txt'] = str_repeat("&nbsp",$deep*5).$row['class_name'];
                $data[] = $row;
                $this->getTree($arr,$row['id'],$deep+1,$id);
            }
        }
        return $data;
    }

    /**
     * 商品分类修改
     * @param array $newdata 前端提交过来的数据
     * @return bool
     */
    public function editGoodsClass(array $newdata){
        
        if (!$this->isEmpty($newdata)) {
            return false;
        }
        
        $pic = $this->where(static::$id_d.'=%d', $newdata[static::$id_d])->getField(static::$picUrl_d);
        
        if (!empty($pic) && $pic !== $newdata[static::$picUrl_d]) {//图片不同 删除原来的
            $status = Tool::partten(array($pic), UnlinkPicture::class);
            Log::write('删除分类图片是否成功（1：yes，0：no）：'.$status, Log::DEBUG);
        }
        $status = $this->save($newdata);
        return $status;
    }
    
    /**
     * 商品分类删除
     * @param int $id 商品分类id
     * @return bool|mixed
     */
    public function delGoodsClass(){
        $rows = $this->getlist();
        foreach($rows as $row){
            //父类编号等于当前编号
            if($row['fid'] == $id){
                return false;
            }
        }
        return $this->delete($id);
    }

    /**
     * 添加前操作
     */
    protected function _before_insert(&$data,$options)
    {
        $data['create_time'] = time();
        $data['type']        = 1;
        $data['update_time'] = time();
        return $data;
    }

    //获取全部编号
    public function getAllClassId(array $options)
    {
        if (empty($options))
        {
            return array();
        }

        return parent::select($options);
    }

    //更新前操作
    protected function _before_update(&$data, $options)
    {

        $data['update_time'] = time();
        return $data;
    }

    /**
     * 根据商品属性 获取数据
     * @param string $idString 分类id字符串
     * @param string $transform 要变换的字段
     * @return array
     */
    public function getClassNameByGoodsAttribute(array $attribute, $transform)
    {
        if (empty($attribute) || empty($transform)) {
            return array();
        }

        foreach ($attribute as $key => &$value)
        {
            if (!empty($value[$transform]))
            {

                $value[$transform] = $this->where(static::$id_d.'='.$value[$transform])->getField(static::$className_d);

            }
        }
        return $attribute;
    }

    /**
     * 获取全部子集分类
     * @param array $where 查询条件
     * @param array $field 查询的字段
     * @return string
     */
    public function getChildren(array $where = null, array $field = null)
    {
        // 根据地区编号  查询  该地区的所有信息
        $video_data   = parent::select(array(
            'where' => $where,
            'field' => $field,
        ));
        if (empty($video_data))
        {
            return array();
        }
        $pk    = $this->getPk();
        static $children = array();
        foreach ($video_data as $key => &$value)
        {
            if(!empty($value[$pk]))
            {
                $where['fid'] = $value[$pk];
                $child = $this->getChildren(array('fid' => $value[$pk]), $field);
                $children[$key] = $value;
                if (!empty($child))
                {
                    $children[$key]['children'] = $child;
                }
                unset($video_data[$key], $child);
            }
        }
        return $children;
    }

    /**
     * 移除分类商品
     * @param $id 分类商品的id
     * @return mixed
     */
    public function delGoodsShop($id){
        $category_ids = $this->getCategory($id);
        $category_ids = rtrim($category_ids,",");
        //删除商品分类id的商品
        $results = M("Goods")->where(['class_id'=>['in',$category_ids]])->delete();
        return $results;
    }

    /**
     * 寻找子类的id
     * @param integer $category_id 父级分类
     * @return string $category_ids 该父级分类的子类
     */
    private  function getCategory($category_id ){
        $category_ids = $category_id.",";
        $child_category = $this -> field("id,class_name")->where(['fid'=>$category_id])->select();
        foreach( $child_category as $key => $val ){
            $category_ids .= $this->getCategory( $val["id"] );
        }
        return $category_ids;
    }
    
    /**
     * 获取上一级分类数据 
     */
    public function getParentOne($id)
    {
        if (($id = intval($id)) === 0) {
            $this->error = '没有上级分类';
            return array();
        }
        
        return $this->where(static::$id_d.'=%d', $id)->getField(static::$id_d.','.static::$className_d);
        
    }

    /**
     * 获取分类级数
     * @param int $forNumber 要获取的分类级数
     */
    public function getTop(&$id, $forNumber = 2)
    {
        $data = $this->getClassData();
        $levelId = $id;
        if (empty($data)) {
            return array();
        }
        
        $flag = array();
        foreach ($data as $key => $value) {
            $flag[$key] = $value[static::$fid_d];
        }
        $level = array();
        while($flag[$id]) {
            $id = $flag[$id];
            $level[$id] = $id;
        }
        sort($level);
        $level[] = $levelId;
        if (empty($level[$forNumber])) {
            return array();
        }
        return $data[$level[$forNumber]];
    }
    
    /**
     * 获取扩展分类集合 
     * @param integer $extendClassId 扩展分类编号
     * @return array
     */
    public function getExtendCollection ($extendClassId )
    {
        if (($extendClassId = intval($extendClassId) ) === 0) {
            return array();
        }
        //------------------------扩展分类
        $extendId      = $extendClassId;
        	
        $extendSecond  = $extendClassId;
        	
        $extendThree   = $extendClassId;
        //获取顶级分类
        $extendClass = $this->getTop($extendId, 0);
        
        //二级
        $extendClassSecondData = $this->getTop($extendSecond, 1);
        //三级
        $threeClassThreeData = $this->getTop($extendThree, 2);
        Tool::isSetDefaultValue($extendClass, [
            static::$id_d => 0,
            static::$className_d => ''
        ]);
        Tool::isSetDefaultValue($extendClassSecondData, [
            static::$id_d => 0,
            static::$className_d => ' '
        ]);
        Tool::isSetDefaultValue($threeClassThreeData, [
            static::$id_d => 0,
            static::$className_d => ' '
        ]);
        $extendClassData = [
            $extendClass[static::$id_d]      => $extendClass[static::$className_d],
            $extendClassSecondData[static::$id_d]  => $extendClassSecondData[static::$className_d],
            $threeClassThreeData[static::$id_d]   => $threeClassThreeData[static::$className_d]
        ];
        
        
        $_SESSION['extendTop'] = $extendClass[static::$id_d];
        
        $_SESSION['second']    =  $extendClassSecondData[static::$id_d];
        
        return [
            'extendTop' => $extendClass[static::$id_d],
            'second'    => $extendClassSecondData[static::$id_d],
            'classData' => $extendClassData
        ];
    }
    
    public function getClassData()
    {
        $data = S('CLASS_DATA');
        $field = static::$id_d.','.static::$fid_d.','.static::$className_d;
        
        Event::listen('parseFieldGoodsClass', $field);//监听
        
        if (empty($data)) {
            $data = $this->where(['store_id' => session('store_id')])->getField($field);
            if (empty($data)) {
                return array();
            }
            S('CLASS_DATA', $data, 15);
        }
        
        return $data;
    }
    
    /**
     *获取 一二级分类
     */
    public function getOneAndSecondClass ()
    {
        $data = S('ONE_AND_SECOND_CLASS_DATA');
        $field = $this->trueTableName.'.'.static::$id_d.','.$this->trueTableName.'.'.static::$className_d;
        
        
        if (empty($data)) {
            $data = $this->where(static::$fid_d.'= 0')->getField(static::$id_d.','.static::$className_d);
            if (empty($data)) {
                return array();
            }
            
            $pIdString = implode(',', array_keys($data));
            
            $second = (array)$this->where(static::$fid_d.' in ('.addslashes($pIdString).')')->getField(static::$id_d.','.static::$className_d);
           
            
            foreach ($second as $key => $value)
            {
                $data[$key] = $value;
            }
            
            S('ONE_AND_SECOND_CLASS_DATA', $data, 15);
        }
        return $data;
    }
    
    /**
     *  重组分类数据
     */
    public function buildClass ()
    {

        $data = $this->getDataByPage(array(
            'field' => array(
                static::$cssClass_d,
            ),
            'where' => [static::$fid_d => 0,'store_id'=>session('store_id')],
            'order' => static::$sortNum_d.static::DESC.','.static::$createTime_d.static::DESC
        ), C('PAGE_NUMBER'), true);
        
        if (empty($data)) {
            return array();
        }

        $second = $this->getNextClass($data['data']);

        $three  = $this->getNextClass($second);

        $data['data'] = array_merge($this->totalClassData, $data['data']);

        $data['data'] = Tool::connect('Tree',$data['data'])->makeTreeForHtml( array(
            'parent_key' => static::$fid_d
        ));

        $this->dataClass = $data['data'];

        $flagArray = array();
        $flagArray['data'] = $this->dataClass;
        $flagArray['page'] = $data['page'];
        return $flagArray;
    }
    
    /**
     * 获取一级分类 
     */
    public function getTopClass ()
    {
        return $this->where(static::$fid_d.' = 0 and '.static::$hideStatus_d.' = 1 and '.static::$storeId_d. ' = '.session('store_id'))->getField(static::$id_d.','.static::$className_d);
    }
    
    /**
     * 根据编号 获取分类 
     */
    public function getClassById ($id)
    {
        if ( ($id = intval($id)) === 0) {
            return array();
        }
        return $this->field(static::$id_d.','.static::$className_d)->where(static::$fid_d.' = %d and '.static::$hideStatus_d.' = 1', $id)->select();
    }
    
    /**
     * 获取下级分类 
     */
    public function getNextClass (array $data)
    {
        if (!$this->isEmpty($data)) {
            return array();
        }
        
        $idString = Tool::characterJoin($data, static::$id_d);
        $second = $this->field(static::$cssClass_d, true)->where(static::$fid_d.' in ('.$idString.')')->select();

        if (empty($second)) {
            return array();
        }
        
        foreach ($second as $key => $value) {
            $data[$key] = $value;
        }

        $this->totalClassData = array_merge($this->totalClassData, $data);
        return $second;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Model\IsExitsModel::IsExits()
     */
    public function IsExits($post)
    {
        // TODO Auto-generated method stub
        
        if (empty($post[self::$className_d])) {
            return true;
        }
        
        $isExits = $this->where(self::$className_d.'="%s"', $post[self::$className_d])->getField(self::$id_d);
        
        return empty($isExits) ? false : true;
    }

    /**
     * 判断是否存在父类id
     */
    public function isChild($fid)
    {
        if(empty($fid)){
            return true;
        }
        $isChild =  $this->where(self::$id_d . '="%s"',$fid)->find();
        if(empty($isChild)){
            return false;
        }
        return true;
    }

    /**
     * 分页读取数据
     */
    public function getDataByPage(array $options, $pageNumer = 10, $isNoSelect = false, $pageObj = Page::class)
    {
        if (empty($options) || ! is_int($pageNumer)) {
            return array();
        }

        if (! empty($_SESSION['where']) && is_array($_SESSION['where'])) {
            $count = $this->where($_SESSION['where'])->count();

            $_SESSION['where'] = null;
        } else {
            $count = ! empty($options['where']) ? $this->where($options['where'])->count() : $this->count();
        }

        $page = new $pageObj($count, $pageNumer);
        $param = empty($_POST) ? $_GET : $_POST;
        Hook::listen('Search', $param);

        $page->parameter = $param;

        $options['limit'] = $page->firstRow . ',' . $page->listRows;

        $data = $this->getAttribute($options, $isNoSelect);

        $array['data'] = $data;

        $array['page'] = ceil($page->totalRows / $page->listRows);

        return $array;
    }

    /**
     * 根据编号 获取分类
     */
    public function getClassByFid ($id)
    {
        if (!is_numeric($id)) {
            return array();
        }
        return $this->field(static::$id_d.','.static::$className_d)->where(static::$fid_d.' = ' . $id .' and '.static::$hideStatus_d.' = 1 and '.static::$storeId_d. ' = '.session('store_id'))->select();
    }

    /**
     * 获取所有子级分类的id
     */
    public function getAllChildId($id)
    {
        $classIds = $this->getCategory($id);
        $classIds = rtrim($classIds,",");
        return $classIds;
    }

    /**
     * 删除分类和子级分类
     */
    public function delAllClassById($ids)
    {
        return $this->where(static::$storeId_d . ' = ' . session('store_id') . ' and ' . static::$id_d . ' in (%s)',$ids)->delete();
    }

    /**
     * 判断分类是否存在
     */
    public function isExistClass($id)
    {
        $result = $this->where(static::$id_d . ' = %d ' , $id)->find();
        return empty($result) ? false : true ;
    }
}