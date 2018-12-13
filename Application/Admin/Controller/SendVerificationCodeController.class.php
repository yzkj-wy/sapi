<?php
declare(strict_types=1);
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Admin\Logic\UserLogic;
use Common\TraitClass\SmsVerification;
use Common\TraitClass\GETConfigTrait;
use PlugInUnit\Validate\CheckParam;

/**
 * 发送验证码
 * @author Administrator
 */
class SendVerificationCodeController
{
	use InitControllerTrait;
	
	use SmsVerification;
	
	use GETConfigTrait;
	
	public function __construct(array $args =[])
	{
		$this->args = $args;
		
		$this->init();
		
		$this->logic = new UserLogic($args);
	}
	
	/**
	 * @name 注册发送验证码
	 * @author 王强
	 * @des 注册发送验证码
	 * @updated 2017-12-20
	 */
	public function registerSendMsg()
	{
		$checkObj = new CheckParam($this->logic->getRuleByRegSendSms(), $this->args);//检测参数, $this->logic->getMessageByRegSendSms() 里定义好的检测方法，类似jQuery Validate自动验证方法
		
		$status = $checkObj->checkParam();//检测参数，类似jQuery Validate自动验证方法
		
		$this->objController->promptPjax($status, $checkObj->getErrorMessage());//获取失败提示并返回
		
		$ret = $this->logic->checkUserMobileIsExits();//逻辑处理
		
		$this->objController->promptPjax($ret, $this->logic->getErrorMessage());
		
		//读取短信配置
		$this->key = 'alipay_config';
		
		$data = $this->getGroupConfig();
		
		$this->config = $data;
		
		$this->mobile = $this->args['mobile'];
		
		$res = $this->sendVerification();
		
		$this->objController->promptPjax($res, $this->error);//获取失败提示并返回
		
		$this->objController->ajaxReturnData('');//返回数据
	}
}