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

namespace Admin\Controller;

use Common\Controller\AuthController;
use Admin\Model\GoodsClassModel;
use Admin\Model\GoodsModel;
use Admin\Model\BrandModel;
use Common\Model\BaseModel;
use Common\Tool\Tool;
use Think\Page;
use Common\TraitClass\SearchTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\IntegralLogic;
use Common\TraitClass\IsLoginTrait;

/**
 * 积分商品管理
 * 后台仅需要设置积分管理
 */
class IntegralGoodsController extends AuthController 
{

    use IsLoginTrait;
    use SearchTrait;
    use InitControllerTrait;

    /**
     * 构造方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
        $this->isNewLoginAdmin();
        $this->args = $args;

        $this->logic =new IntegralLogic($args);

    }


	/**
	 * 获取积分商品的列表
	 */
	public function integralList(){

        $result=$this->logic->logList();

        $this->objController->ajaxReturnData($result);

	}
/**
 * 是否显示
 */
    public function is_show(){

        $result=$this->logic->logIsShow();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'操作失败');
        }

    }
    /**
     * 积分删除
     */
    public function delete(){

        $re=$this->logic->LogDelete();


        $this->objController->ajaxReturnData($re['data'],$re['status'],$re['message']);

    }

    /**
     * 添加积分
     */
    public function addIntegral(){

        $result=$this->logic->logAdd();
        $this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);
    }

    /**
     * 修改积分
     */
    public function updIntegral(){

        $result=$this->logic->logUpd();
        $this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);

    }

    /**
     * 获取单条信息的接口
     */
    public function getInfoById(){
        $result=$this->logic->logGetInfoById();
        $this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);
    }



}