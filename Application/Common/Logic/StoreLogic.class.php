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

use Common\Model\StoreAddressModel;
use Common\Model\RegionModel;
use Common\Model\StoreModel;
use Common\Model\StoreJoinCompanyModel;
use Common\Model\StoreInformationModel;
use Common\TraitClass\GETConfigTrait;
use Think\Cache;

/**
 * 店铺逻辑处理
 * @author Administrator
 */
class StoreLogic extends AbstractGetDataLogic
{
  use GETConfigTrait;
    /**
     * 店铺信息
     * @var array
     */
    private $storeInfo = [];

    /**
     * 构造方法
     * @param array $args
     */
    public function __construct( array $args,array $storeInfo = [] )
    {
        $this->data = $args;

        $this->storeInfo = $storeInfo;

        $this->modelObj = new StoreModel();

        $this->covertKey = StoreModel::$shopName_d;
    }

    /**
     * 实现方法
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        $data = $this->data;

        if( empty( $data ) ){
            $this->modelObj->rollback();
            return [];
        }

        $id = $data[ StoreModel::$id_d ];

        $resouce = $_SESSION[ 'temp_com_data' ][ $id ];

        $addData                          = [];
        $addData[ StoreModel::$userId_d ] = $resouce[ StoreJoinCompanyModel::$userId_d ]; //用户编号

        $addData[ StoreModel::$shopName_d ] = $this->storeInfo[ StoreInformationModel::$shopName_d ];//店铺名称

        $addData[ StoreModel::$startTime_d ] = time();//开店时间

        $addData[ StoreModel::$endTime_d ] = strtotime( date( 'Y-m-d 23:59:59',strtotime( '+1 day' ) ) . " +" . intval( $this->storeInfo[ StoreInformationModel::$shopLong_d ] ) . " year" );//开店结束时间

        $addData[ StoreModel::$gradeId_d ] = $this->storeInfo[ StoreInformationModel::$levelId_d ];//店铺等级

        $addData[ StoreModel::$type_d ] = $_SESSION[ 'store_type' ];//店铺类型

        $addData[ StoreModel::$classId_d ] = $this->storeInfo[ StoreInformationModel::$shopClass_d ];//店铺分类编号

        $addData[ StoreModel::$addressId_d ] = $resouce[ StoreJoinCompanyModel::$storeAddress_d ];

        $addData[ StoreModel::$storeState_d ] = 1;

        $status = $this->modelObj->add( $addData );

        if( !$this->modelObj->traceStation( $status ) ){
            return [];
        }

        $addData[ StoreModel::$id_d ] = $status;
        return $addData;
    }

    /**
     * 获取模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return StoreModel::class;
    }

    /**
     * 获取用户分割键
     * @return string
     */
    public function getUserSplitKey()
    {
        return StoreModel::$userId_d;
    }

    /**
     * 获取详情页表注释
     */
    public function detailComment()
    {

        $field = [ StoreJoinCompanyModel::$createTime_d,StoreJoinCompanyModel::$updateTime_d ];

        return $this->modelObj->getComment( $field );

    }


    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment()
    {
        return [
            StoreModel::$printDesc_d,
            StoreModel::$freePrice_d,
            StoreModel::$storeSales_d,
            StoreModel::$decorationOnly_d,
            StoreModel::$buildAll_d,
            StoreModel::$barType_d,
            StoreModel::$isDistribution_d,
            StoreModel::$decorationSwitch_d,
            StoreModel::$themeId_d,
            StoreModel::$storeCollect_d,
            StoreModel::$createTime_d,
            StoreModel::$updateTime_d,
            StoreModel::$imageCount_d,
            StoreModel::$storeAddress_d
        ];
    }

//    /**
//     * @description
//     * @return array
//     */
//    protected function searchTemporary()
//    {
//        return [
//            StoreModel::$id_d => $_SESSION[ 'store_id' ]
//        ];
//    }

    public function searchTemporary()
    {
        $this->searchTemporary = [
            StoreModel::$id_d => $_SESSION[ 'store_id' ]
        ];
    }

    /**
     * 获取店铺数据
     * @return array
     */
    public function getStoreData()
    {
        if( empty( $this->data ) ){
            return [];
        }

        $field = [
            StoreModel::$id_d,
            StoreModel::$shopName_d
        ];

        return $this->getDataByOtherModel( $field,StoreModel::$id_d );

    }

    /**
     * 更新店铺
     */
    public function save()
    {
        $data = $this->data;

        if( empty( $data ) ){
            return false;
        }

        $status = $this->modelObj->save( $data );

        return $status;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
        return [
            StoreModel::$shopName_d
        ];
    }

    /**
     * 获取店铺数据
     * @return array
     */
    public function getStoreInfo()
    {
        $where[ 'id' ]         = $_SESSION[ 'store_id' ];
        $field                 = "id,shop_name,store_logo,user_id,create_time";
        $store                 = $this->modelObj->getStoreById( $where,$field );
        $storeInformationModel = new StoreInformationModel();
        $info['shop_name']   = $store['data']['shop_name'];
        $infoField             = "shop_long";
        $information           = $storeInformationModel->getInfo($info,$infoField );
        $shop_long = "+".$information['data']['shop_long'];
        $b = date("Y-m-d",$store['data']['create_time']);
        $c = "+".$information['data']['shop_long'];
        $a = date("Y-m-d", strtotime($c."year",$store['data']['create_time']));
        $user_name = M('User')->where(['id'=>$store['data']['user_id']])->getField('user_name');
        $data  =array(
            "id" =>$store['data']['id'],
            "user_name" =>$user_name,
            "shop_name" =>$store['data']['shop_name'],
            "store_logo" =>$store['data']['store_logo'],
            "last_login_time" =>$_SESSION['last_login_time'],
            "last_login_ip" =>$_SESSION['last_login_ip'],
            "shop_long" =>"截止至 ".$a,
        );
        return $data;
    }

    public function getInfo()
    {
    	$storeData = $this->getStoreById();
    	
        if (empty($storeData)) {
            return [];
        }
        
        $storeAddressModel = new StoreAddressModel();
        
        $storeAddress = [];
        
        $storeAddress      = $storeAddressModel->field( StoreAddressModel::$id_d . ',' . StoreAddressModel::$storeId_d,true )
            ->where( [ StoreAddressModel::$storeId_d => $_SESSION['store_id'] ] )
            ->find();
            
        if (empty($storeAddress)) {
            return [
                'store' => $storeData,
                'address' => []
            ];
        }
        $region = new RegionModel();

        $storeAddress['prov_name'] = $region->where(['id'=>$storeAddress['prov_id']])->getField('name');
        $storeAddress['city_name'] = $region->where(['id'=>$storeAddress['city']])->getField('name');
        $storeAddress['dist_name'] = $region->where(['id'=>$storeAddress['dist']])->getField('name');
        return [
            'store' => $storeData,
            'address' => $storeAddress
        ];
    }
    
    
    /**
     * 店铺地址相关字段
     * @return string
     */
    public function getSplitKeyByAddress()
    {
        return StoreModel::$storeAddress_d;
    }
    
    /**
     * @description 分割数组
     */
    public function saveStoreAndAddress()
    {
        $this->modelObj->startTrans();
        $domain_name = $this->data['domain_name'];
        $str="/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";
        if (!preg_match($str,$domain_name)){
            return false;
        }
        $status = $this->saveData();
        if(!$this->traceStation($status)){
            
            return false;
        }
        return $status;
    }
    
    
    /**
     * 添加时处理参数
     * @return array
     */
    protected function getParseResultBySave() :array
    {
        $data = $this->data;
        
        $data[StoreModel::$id_d] = $_SESSION['store_id'];
        
        return $data;
    }
    
    /**
     * 获取店铺信息
     * @return array
     */
    public function getStoreById()
    {
    	$storeData         = $this->modelObj->field(StoreModel::$printDesc_d, true)->where( [ StoreModel::$id_d => $_SESSION[ 'store_id' ] ] )->find();
    	
    	return $storeData;
    }
    
    /**
     * 获取店铺信息（名称及其logo）
     * @return array
     */
    public function getStoreRough()
    {
    	$storeData         = $this->modelObj->field(StoreModel::$shopName_d.','.StoreModel::$storeLogo_d.','.StoreModel::$mobile_d)->where( [ StoreModel::$id_d => $_SESSION[ 'store_id' ] ] )->find();
    	
    	return $storeData;
    }
    
    /**
     * 获取店铺信息并缓存
     */
    public function geStoreRoughCache()
    {
    	$cache = Cache::getInstance('', ['expire' => 600]);
    	
    	$key = $_SESSION['store_id'].'storerough';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getStoreRough();
    	
    	if (empty($data)) {
    		return [];
    	}
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 获取店铺信息并缓存
     */
    public function getOrderInfoCache()
    {
    	$cache = Cache::getInstance('', ['expire' => 600]);
    	
    	$key = $_SESSION['store_id'].'douyouknow';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->getStoreById();
    	
    	if (empty($data)) {
    		return [];
    	}
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 获取店铺类型
     * @throws \Exception
     * @return string
     */
    public function getStoreType()
    {
    	$cache = Cache::getInstance('', ['expire' => 6000]);
    	
    	$key = $_SESSION['store_id'].'storetype';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->modelObj->where([ StoreModel::$id_d => $_SESSION[ 'store_id' ] ])->getField(StoreModel::$type_d);
    	
    	if ($data === null) {
    		throw new \Exception('店铺异常');
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    /**
     * 获取店铺类型
     * @throws \Exception
     * @return string
     */
    public function getStoreUserId()
    {
    	$cache = Cache::getInstance('', ['expire' => 6000]);
    	
    	$key = $_SESSION['store_id'].'storeuser';
    	
    	$data = $cache->get($key);
    	
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$data = $this->modelObj->where([ StoreModel::$id_d => $_SESSION[ 'store_id' ] ])->getField(StoreModel::$userId_d);
    	
    	if ($data === null) {
    		throw new \Exception('店铺异常');
    	}
    	
    	$cache->set($key, $data);
    	
    	return $data;
    }
    
    public function getMessageNotice() :array
    {
    	return [
    		StoreModel::$shopName_d => [
    			'required' => '店铺名称必填',
    			'specialCharFilter' => '只能输入中英文及其数字'
    		],
    		StoreModel::$storeAddress_d => [
    			'number' => '店铺地址必填'
    		],
    		StoreModel::$startTime_d => [
    			'number' => '开始时间必须是数字'
    		],
    		StoreModel::$endTime_d => [
    			'number' => '结束时间必须是数字'
    		]
    	];
    }
}