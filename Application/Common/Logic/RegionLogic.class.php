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
declare(strict_types=1);
namespace Common\Logic;

use Common\Logic\AbstractGetDataLogic;
use Common\Model\RegionModel;
use Common\Tool\Tool;
use Common\Tool\Extend\ArrayChildren;
use Think\Page;
use Think\ModelTrait\Select;

/**
 * 用户逻辑处理
 * 
 * @author Administrator
 */
class RegionLogic extends AbstractGetDataLogic
{
    /**
     * 其他模型对象
     * @var \stdClass
     */
    private $relation;
    /**
     * 构造方法
     * @param array $data            
     */
    public function __construct(array $data = [], string $relation = null)
    {
        $this->data = $data;
        
        $this->relation = $relation;
        
        $this->modelObj = new RegionModel();
    }

    /**
     * 获取用户数据
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        
    }
    
    /**
     * 获取收货地址
     */
    public function getDefaultRegion()
    {
        $area = $this->data;
        
        $region = $this->getRegion();
         
        if (empty($region)) {
            return array();
        }
        
        $model = $this->relation;
        $area[$model::$provId_d] = $region[$area[$model::$provId_d]];
        $area[$model::$city_d]   = $region[$area[$model::$city_d]];
        $area[$model::$dist_d]   = $region[$area[$model::$dist_d]];
    
        return $area;
    }
    
    /**
     * 获取地区
     */
    protected  function getRegion ()
    {
        $model = $this->relation;
        
        $area = $this->data;
        
        $where = $area[$model::$provId_d].','.$area[$model::$city_d].','.$area[$model::$dist_d];
        $region = $this
            ->modelObj
            ->where(RegionModel::$id_d .' in ('.$where.')')
            ->getField(RegionModel::$id_d.','.RegionModel::$name_d);
        return $region;
    }
   
    public function getModelClassName()
    {
        return RegionModel::class;
    }
    
    /**
     * 获取包邮地区
     */
    public function getFreightArea ()
    {
        if (empty($this->data)) {
            return array();
        }
         
        $idString = Tool::characterJoin($this->data, $this->splitKey);
       
        if (empty($idString)) {
            return array();
        }
    
        $data =  $this->modelObj->where(RegionModel::$id_d .' in ('. $idString.')')->getField(RegionModel::$id_d.','.RegionModel::$name_d);
        return $data;
    }
    
    /**
     * 获取省市
     */
    public function getCityAndPro()
    {
        $page = 5;//$this->data['page'];
        if (($page = intval($page))=== 0) {
            return array();
        }
    
        $count = S('count');
    
        if (empty($count)) {
            $count = $this->modelObj->where(RegionModel::$parentid_d .' = 0')->count();
            S('count', $count, 3600);
        }
         
    
        $middle = ceil($count/6);
    
        $pageObj = new Page($count, $middle);
    
        $start = ($page-1) * $middle;
    
        $prov = $this->modelObj->field(array(
            RegionModel::$id_d,
            RegionModel::$name_d,
            RegionModel::$parentid_d
        ))->where(RegionModel::$parentid_d .' = 0')->limit($pageObj->firstRow, $pageObj->listRows)->select();
    
        if (empty($prov)) {
            return array();
        }
    
        $idString = Tool::characterJoin($prov, RegionModel::$id_d);
    
        if (empty($idString)) {
            return array();
        }
        $city = $this->modelObj->field(array(
            RegionModel::$id_d,
            RegionModel::$name_d,
            RegionModel::$parentid_d
        ))->where(RegionModel::$parentid_d .' in ('.$idString.')')->select();
    
        $area = array_merge($prov, $city);
    
        //线性输出
        $area = Tool::connect('Tree', $area)->makeTreeForHtml(array(
            'parent_key' => RegionModel::$parentid_d
        ));
    
    
        $initArea = array();
        //static::$fid_d = RegionModel::$parentid_d;
    
        $area = (new ArrayChildren($area))->convertIdByData(RegionModel::$id_d);#$this->covertKeyById($area, self::$id_d);
        
        $buildData = array();
        $buildData['area'] = $area;
        $buildData['page'] = $pageObj->show();
        unset($prov, $city, $initArea);
        return $buildData;
    }
    //获取 省份
    public function getProv(){
        $res =  $this->modelObj->getProv();
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }
    
    /**
     * 获取下级地区
     */
    public function getUpDataById () :array
    {
    	$data = $this->modelObj
    		->field(RegionModel::$id_d.','.RegionModel::$name_d.','.RegionModel::$parentid_d)
    		->where(RegionModel::$parentid_d.'= %d', $this->data[RegionModel::$id_d])
    		->select();
    	
    	if (empty($data)) {
    		$this->errorMessage = '没有地区';
    		
    		return [];
    	}
    		
    	return $data;
    }
    
    /**
     * 检查数据
     */
    public function getCheckValidateByRegion() :array
    {
    	return [
    		RegionModel::$id_d => [
    			'number' => 'id必须是数字'
    		]
    	];
    }
    
    /**
     * 获取省市
     */
    public function getProvAndCity($limit = null)
    {   $page = $this->data['page'];
        $res =  $this->modelObj->getProvAndCity($page,$limit);
        return $res;
    }
}