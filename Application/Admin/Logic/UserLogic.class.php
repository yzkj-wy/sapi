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
namespace Admin\Logic;

use Common\Logic\AbstractGetDataLogic;
use Admin\Model\UserModel;
use Common\Tool\Tool;
use Think\Cache;
use Common\Tool\Extend\CombineArray;

/**
 * 用户逻辑处理
 * 
 * @author Administrator
 */
class UserLogic extends AbstractGetDataLogic
{

    /**
     * 构造方法
     * @param array $data            
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = UserModel::getInitnation();
       
        $this->covertKey = UserModel::$userName_d;
    }

    /**
     * 获取用户数据
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {
        $data = $this->data;

        if (empty($data)) {
            return [];
        }

        foreach($data as $key => $value){
            $where['money_big'] = array('EGT',$value['total_transaction']);
            $where['money_small'] = array('ELT',$value['total_transaction']);
            $where['store_id'] = $_SESSION['store_id'];
            $level_id = M('store_member_level')->where($where)->getField('level_id');
            $data[$key]['level_name'] = M('store_level_by_platform')->where(['id'=>$level_id])->getField('level_name');
            $data[$key]['average'] = round($value['total_transaction'] / $value['transaction_number'],2);
        }
        $this->data = $data;
        $field = [
            UserModel::$id_d,
            UserModel::$userName_d
        ];
       
        $dataUser = $this->getDataByOtherModel($field, UserModel::$id_d);
       
        $_SESSION['data_user'] = $dataUser;
        return $dataUser;
    }
    
    /**
     * 获取用户数据
     * @return string
     */
    public function getUserName()
    {
        $data = $this->getUserInfo();
        
        if (empty($data[UserModel::$userName_d])) {
            return [];
        }
        
        $result = $this->data;
        
        $result[UserModel::$userName_d] = $data[UserModel::$userName_d];
        
        return $result;
    }
    
    
    /**
     * 获取用户信息
     * @return array
     */
    public function getUserInfo() :array
    {
        $id = (int)$this->data[$this->splitKey];

        $cache = Cache::getInstance('', ['expire' => 60]);
        
        $key = $id.'_sd'.$_SESSION['store_id'];
        
        $useInfo =$cache->get($key);
        
        if (!empty($userName)) {
          return $useInfo;
        }
        
        
        $field = [
        	UserModel::$createTime_d,
        	UserModel::$updateTime_d,
        ];
        
        $useInfo = $this->modelObj->field($field, true)->where(UserModel::$id_d.'=%d', $id)->find();
        
        if (empty($useInfo)) {
            return [];
        }
        
        $cache->set($key, $useInfo);
        
        return $useInfo;
        
    }
    
    /**
     * 根据订单信息 查询用户信息
     */
    public function userInfoByOrder()
    {

        if ( empty($this->data['user_id'])) {
            return array();
        }
    
        $field = array(UserModel::$userName_d, UserModel::$email_d, UserModel::$mobile_d);
        return $userInfo = $this->modelObj->field($field) ->where('id = %d', $this->data['user_id'])->find();
    }
    

    /**
     *
     * @return the $split
     */
    public function getSplit()
    {
        return $this->split;
    }

    /**
     *
     * @param string $split            
     */
    public function setSplit($split)
    {
        $this->split = $split;
    }

    /**
     * 返回模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return UserModel::class;
    }


    /**
     * 根据订单信息 查询会员信息
     */
    public function getOrderUserInfo()
    {

        $field = [
            UserModel::$id_d,
            UserModel::$userName_d
        ];

        $dataArray = $this->getDataByOtherModel($field, UserModel::$id_d);
        if (empty($dataArray)) {
            return $this->data;
        }


        return $dataArray;
    }
    //获取今日会员数
    public function userToday(){
        $today = date('Y-m-d', time());
        $start = strtotime($today.' 00:00:00');  
        $end  = strtotime($today.' 23:59:59');
        // $where['store_id'] = $_SESSION['store_id'];
        $where['create_time'] = array('between',array($start,$end));
        $count = $this->modelObj->getNumberByWhere($where);       
        return $count;
    }
    
    /**
     * 根据用户编号获取用户
     */
    public function getUserByIds()
    {
        $field = [
            UserModel::$id_d,
            UserModel::$userName_d,
            UserModel::$mobile_d
        ];
        
        $dataUser = $this->getDataByOtherModel($field, UserModel::$id_d);
        
        return $dataUser;
    }
    
    /**
     * 根据退货信息查找 订单数据
     */
    public function getUserByOrderReturn() :array
    {
    	$cacheKey = md5(json_encode($this->data));
    	
    	$cache = Cache::getInstance('', ['expire' => 60]);
    	
    	$data = $cache->get($cacheKey);
    	
    	if (!empty($data)) {
    		
    		return $data;
    	}
    	
    	
    	$field = [
    		UserModel::$id_d,
    		UserModel::$userName_d,
    	];
    	
    	$data = $this->getDataByOtherModel($field, UserModel::$id_d);
    	
    	if (empty($data)) {
    		return array();
    	}
    	
    	$cache->set($cacheKey, $data);
    	
    	return $data;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
    	return [
    		UserModel::$userName_d
    	];
    }
    
    /**
     * 获取用户数据
     */
    public function getUserData()
    {
    	$id = Tool::characterJoin($this->data, $this->splitKey);
    	
    	if (empty($id)) {
    		return array();
    	}
    	
    	$field = [
    		UserModel::$id_d,
    		UserModel::$userName_d
    	];
    	
    	$dataArray = $this->modelObj->field($field)
    		->where('id in (' . $id . ')')
    		->select();
    	if (empty($dataArray)) {
    		return $this->data;
    	}
    	
    	$obj = new CombineArray($dataArray, UserModel::$id_d);
    	
    	$data = $obj->parseCombineList($this->data, $this->splitKey);
    	
    	return $data;
    }
    
    /**
     * @name 注册发送验证码验证规则
     */
    public function getRuleByRegSendSms() :array
    {
    	$message = [
    		UserModel::$mobile_d => [
    			'required'          => '请输入手机号码',
    			'checkMobile' => '手机号码不能输入特殊字符',
    		],
    	];
    	return $message;
    }
    
    /**
     * @name 修改商户密码 验证会员
     * @des 注册发送验证码
     * @updated 2017-12-16
     */
    public function checkUserMobileIsExits() :bool
    {
    	$args = [
    		$this->data['mobile'],
    	];
    	
    	$field = [
    		UserModel::$id_d,
    	];
    	
    	$data = $this->modelObj
    		->field($field)
    		->where(UserModel::$mobile_d . '= %d', $args)
    		->find();
    	if (empty($data)) {
    		$this->errorMessage = '与绑定的手机号码不一致';
    		return false;
    	}
    	return true;
    }
}