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
declare(strict_types=1);
namespace Admin\Controller;

use Common\Tool\Tool;

use Common\Model\BaseModel;

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\OfflineOrderLogic;

//use Common\TraitClass\OrderPjaxTrait;
use Think\SessionGet;
use Think\Upload;
/**
 * 订单控制器
 * @author 王强
 * @copyright 亿速网络
 * @version  v1.1.2
 * @link http://yisu.cn
 */
class OfflineOrderController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
//    use OrderPjaxTrait;
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
       //$this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new OfflineOrderLogic($args);
    }

    /**
     *导入excel，批量发货
     */
    public function importUpload(){
        header("Content-Type:textml;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Headers:content-type");
        header("Access-Control-Request-Method:GET,POST");
        if(strtoupper($_SERVER['REQUEST_METHOD'])=='OPTIONS'){
            exit;
        }
        $upload = new Upload();// 实例化上传类
        $upload->maxSize   =     31457280000 ;// 设置附件上传大小
        $upload->exts      =     array('xls', 'xlsx');// 设置附件上传类
        $upload->savePath  =      'order/'; // 设置附件上传目录
        // 上传文件
 
        $info   =   $upload->uploadOne($_FILES['file']);
        $filename = $upload->rootPath.$info['savepath'].$info['savename'];
        $exts = $info['ext'];
        //print_r($info);exit;
        if(!$info) {// 上传错误提示错误信息
            $data['data']=$upload->getError();
            $data['status']=0;
            $data['message']='上传失败';
            $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
        }else{// 上传成功
            $res = $this->logic->offlineorders_import($filename, $exts);
            $this->objController->ajaxReturnData($res['data'],$res['status'],$res['message']);
        }
    }
//上传goods
    public function importGoodsUploads(){
        header("Content-Type:textml;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Headers:content-type");
        header("Access-Control-Request-Method:GET,POST");
        if(strtoupper($_SERVER['REQUEST_METHOD'])=='OPTIONS'){
            exit;
        }
        $upload = new Upload();// 实例化上传类
        $upload->maxSize   =     31457280000 ;// 设置附件上传大小
        $upload->exts      =     array('xls', 'xlsx');// 设置附件上传类
        $upload->savePath  =      'order/'; // 设置附件上传目录
        // 上传文件

        $info   =   $upload->uploadOne($_FILES['file']);
        $filename = $upload->rootPath.$info['savepath'].$info['savename'];
        $exts = $info['ext'];
        //print_r($info);exit;
        if(!$info) {// 上传错误提示错误信息
            $data['data']=$upload->getError();
            $data['status']=0;
            $data['message']='上传失败';
            $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
        }else{// 上传成功
            $res = $this->logic->goods_import($filename, $exts);
            $this->objController->ajaxReturnData($res['data'],$res['status'],$res['message']);
        }
    }

    //订单列表
    public function orderList(){
        $data = $this->logic->getOrderList();

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }
    //单条订单数据
    public function orderDetail(){
        $data = $this->logic->getOrderDetail();

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }
    //修改订单数据
    public function orderSave(){
        $data = $this->logic->getOrderSave();

        $this->objController->promptPjax($data, $this->errorMessage);

        $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
    }
}