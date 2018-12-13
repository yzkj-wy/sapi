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
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Logic;

use Common\Model\StoreBindClassModel;
use Common\Model\StoreManagementCategoryModel;
use Common\Model\StoreModel;
use Think\Cache;

/**
 * 可发布的分类结果
 * @author 王强
 */
class StoreBindClassLogic extends AbstractGetDataLogic
{
    /**
     * 店铺数据
     * @var array
     */
    private $storeInfo = [];
    
    /**
     * 临时数据
     * @var unknow
     */
    private $temp;
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct(array $args, array $storeInfo = [])
    {
        $this->data = $args;
        
        $this->storeInfo = $storeInfo;
        
        $this->modelObj = new StoreBindClassModel();
    }
    
    /**
     * 添加绑定分类
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        $data = $this->data;
        if (empty($data)) {
            $this->modelObj->rollback();
            return self::ADD_ERROR;
        }
        
        $addData = [];
        
        $i = 0;
        foreach  ($data as $key => $value) {
            $addData[$i][StoreBindClassModel::$storeId_d] = $this->storeInfo[StoreModel::$id_d];
            $addData[$i][StoreBindClassModel::$classOne_d] = $value[StoreManagementCategoryModel::$oneClass_d];
            $addData[$i][StoreBindClassModel::$classTwo_d] = $value[StoreManagementCategoryModel::$twoClass_d];
            $addData[$i][StoreBindClassModel::$classThree_d] = $value[StoreManagementCategoryModel::$threeClass_d];
            $addData[$i][StoreBindClassModel::$status_d] = 1;
            $i++;
        }
        
        $status = $this->modelObj->addAll($addData);
        
        if (!$this->modelObj->traceStation($status)) {
            return self::ADD_ERROR;
        }
        return $status;
    }
    
    
    /**
     * 获取绑定分类
     * @return array|array
     */
    public function getPublicationOfCommodityClass()
    {
        $cache = Cache::getInstance('', ['expire' => 300]);
        
        $key = 'srore'.$_SESSION['store_id'].'kjk';
        
        $data = $cache->get($key);
        
        if (empty($data)) {
            
            $this->searchTemporary =[
                StoreBindClassModel::$storeId_d => $_SESSION['store_id'],
            	StoreBindClassModel::$status_d => 1,
            ];
            
            $data = parent::getNoPageList();
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
     * 验证是否可添加规格
     * @return boolean
     */
    public function checkPublicationOfCommodityCategories()
    {
        $data = $this->getPublicationOfCommodityClass();
        
       
        if (empty($data)) {
            return false;
        }
        
        $i = 0;
       
        foreach ($data as $key => $value) {
            if ($value[StoreBindClassModel::$classOne_d] == $this->data['class_one'] &&
                $value[StoreBindClassModel::$classTwo_d] == $this->data['class_two'] &&
                $value[StoreBindClassModel::$classThree_d] == $this->data['class_three']
            ) {
               $i++; 
            }
        }

        return $i === 1;
    }
    
    /**
     * @return string[]
     */
    public function getPublicationOfCommodityOneClassIdString()
    {
        $data = $this->getPublicationOfCommodityClass();
        
        if (empty($data)) {
            return [''];
        }
        
        $classId = [];
        
        $idString = '';
        
        $twoString = '';
        
        $threeString = '';
        
        foreach ($data as $value) {
            $idString = ','.$value[StoreBindClassModel::$classOne_d];
            $twoString = ','.$value[StoreBindClassModel::$classTwo_d];
            $threeString = ','.$value[StoreBindClassModel::$classThree_d];
        }
        
        $classId['one_class'] = substr($idString, 1);
        
        $classId['two_class'] = substr($twoString, 1);
        
        $classId['three_class'] = substr($threeString, 1);
        
        return $classId;
    }
    
    
    /**
     * 得到分类的条件
     */
    public function getWhereByClass(array $data) :array
    {
        
        $str = [];
        
        foreach ($data as $value) {
            $str [] = $value[StoreBindClassModel::$classOne_d];
            $str [] = $value[StoreBindClassModel::$classTwo_d];
            $str [] = $value[StoreBindClassModel::$classThree_d];
        }
        return array_unique($str);
    }
    
    public function hideenComment()
    {
        return [
            StoreBindClassModel::$commisRate_d    
        ];
    }
    
    /**
     * 获取店铺字段分割键
     * @return string
     */
    public function getSplitStoreKey()
    {
        return StoreBindClassModel::$storeId_d;
    }
    
    /**
     * 获取模型类名
     */
    public function getModelClassName()
    {
        return StoreBindClassModel::class;   
    }
    
    /**
     * 更新店铺
     */
    public function save()
    {
        $data = $this->data;
    
        if (empty($data)) {
            return false;
        }
         
        $status = $this->modelObj->save($data);
         
        return $status;
    }
    
    /**
     * 验证消息
     * @return array
     */
    public function getValidateMessage() :array
    {
    	return [
    		'goods_class' => [
    			'required' => '经营分类必填'
    		]
    	];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getParseResultByAddAll()
     */
    protected function getParseResultByAddAll() :array
    {
   		$data = $this->data['goods_class'];
   		
   		$add = [];
   		
   		$i = 0;
   		
   		foreach ($data as $key => $value) {
   			
   			$data[$i] = [];
   			
   			$data[$i][StoreBindClassModel::$storeId_d] = $_SESSION['store_id'];
   			
   			$data[$i][StoreBindClassModel::$classOne_d] = $value['class_one'];
   			
   			$data[$i][StoreBindClassModel::$classTwo_d] = $value['class_two'];
   			
   			$data[$i][StoreBindClassModel::$classThree_d] = $value['class_three'];
   			
   			$data[$i][StoreBindClassModel::$status_d] = 0;
   			
   			$i++;
   		}
   		
   		return $data;
    }
    
    /**
     * 编辑保存
     */
    public function saveEdit() :bool
    {
    	$this->modelObj->startTrans();
    	
    	$status = $this->modelObj->where(StoreBindClassModel::$storeId_d.'='.$_SESSION['store_id'].' and '. StoreBindClassModel::$status_d.' = 0')->delete();
    	
    	if (!$this->traceStation($status)) {
    		$this->errorMessage = '绑定分类更新失败';
    		return false;
    	}
    	
    	$status = $this->addAll();
    	
    	if (!$this->traceStation($status)) {
    		$this->errorMessage = '绑定分类更新失败';
    		return false;
    	}
    	
    	$this->modelObj->commit();
    	
    	return true;
    }
}