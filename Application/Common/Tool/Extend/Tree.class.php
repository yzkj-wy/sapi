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

namespace Common\Tool\Extend;
use Common\Tool\Tool;

/**
 * @copyright 版权所有©
 * @name PHPTree
 * @author 王强 < QQ:2272597637 >
 * @desc PHP生成树形结构,无限多级分类
 * @version 1.2.0
 * @updated 2015-08-26
 */
class Tree extends Tool
{

    protected static $config = array(
        /* 主键 */
        'primary_key' 	=> 'id',
        /* 父键 */
        'parent_key'  	=> 'p_id',
        /* 展开属性 */
        'expanded_key'  => 'expanded',
        /* 叶子节点属性 */
        'leaf_key'      => 'leaf',
        /* 孩子节点属性 */
        'children_key'  => 'children',
        /* 是否展开子节点 */
        'expanded'    	=> false
    );

    /* 结果集 */
    protected static $result = array();

    /* 层次暂存 */
    protected static $level = array();
    /**
     * 树形结构
     * @var array
     */
    private $data = [];
    
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    /**
     * @name 生成树形结构
     * @param array 二维数组
     * @return mixed 多维数组
     */
    public  function makeTree($options=array() ,$index = 0){
        
        $dataset = self::buildData($this->data,$options);
       
        $r = self::makeTreeCore($index,$dataset,'normal');
        return $r;
    }
    public function __set($name, $value)
    {
        self::$config[$name] = $value;
    }
    /* 生成线性结构, 便于HTML输出, 参数同上 */
    public  function makeTreeForHtml($options=array()){

        $dataset = self::buildData($this->data,$options);
        $r = self::makeTreeCore(0,$dataset,'linear');
        return $r;
    }

    /* 格式化数据, 私有方法 */
    private static function buildData($data,$options){
        $config = array_merge(self::$config,$options);
        self::$config = $config;
        extract($config);

        $r = array();
        foreach($data as $item){
            $id = $item[$primary_key];
            $parent_id = $item[$parent_key];
            $r[$parent_id][$id] = $item;
        }

        return $r;
    }

    /* 生成树核心, 私有方法  */
    private static  function makeTreeCore($index,$data,$type='linear')
    {
        extract(self::$config);
        foreach($data[$index] as $id=>$item)
        { 
            if($type=='normal'){
                if(isset($data[$id]))
                {
                    $item[$expanded_key]= self::$config['expanded'];
                    $item[$children_key]= self::makeTreeCore($id,$data,$type);
                }
                else
                {
                    $item[$leaf_key]= true;
                }
                $r[] = $item;
            }else if($type=='linear'){
                $parent_id = $item[$parent_key];
                self::$level[$id] = $index==0?0:self::$level[$parent_id]+1;
                $item['level'] = self::$level[$id];
                self::$result[] = $item;
                if(isset($data[$id])){
                    self::makeTreeCore($id,$data,$type);
                }

                $r = self::$result;
            }
        }
        return $r;
    }
    /**
     * 获取数组维度
     */
    public  function arrayDepth( array $array)
    {
        if(empty($array)) return 0;
        
        $max_depth = 1;
        
        foreach ($array as $value) 
        {
            if (is_array($value)) 
            {
                $depth = $this->arrayDepth($value) + 1;
                if ($depth > $max_depth) 
                {
                    $max_depth = $depth;
                }
            }
        }
        return $max_depth;
    }
}


