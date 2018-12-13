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
use Common\Model\UserAddressModel;
use Common\Model\BaseModel;
use Admin\Model\CouponModel;
use Admin\Model\CouponListModel;
//短信工厂类->发货提示
use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\OrderLogic;
use Common\Logic\UserAddressLogic;
use Admin\Logic\UserLogic;
use Common\Logic\RegionLogic;
use Common\TraitClass\OrderPjaxTrait;
use Common\Logic\ExpressLogic;
use PlugInUnit\Validate\CheckParam;
use Common\Logic\OrderGoodsLogic;
use Think\Upload;
/**
 * 订单控制器
 * @author 王强
 * @copyright 亿速网络
 * @version  v1.1.2
 * @link http://yisu.cn
 */
class OrderController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use OrderPjaxTrait;
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
        
        $this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new OrderLogic($args);
    }
    
    
    //订单列表 - 全部订单
    public function orderList() :void
    {
    	$data = $this->ajaxGetData();
    	
    	$this->objController->promptPjax($data, $this->errorMessage);
    	
    	$this->objController->ajaxReturnData($data);
    }
    
    
    /**
     * 积分等优惠券等费用信息 
     */
    public function couponInformation () :void
    {
        Tool::checkPost($_POST, ['is_numeric' => array('id', 'monery')], true, ['id', 'monery']) ? : $this->ajaxReturnData(null, 0, '操作失败');
        
        $conpouListModel = BaseModel::getInstance(CouponListModel::class);
        
        $userCoupon = $conpouListModel->getUserByOrder($_POST['id']);
        
        $conpouModel = BaseModel::getInstance(CouponModel::class);
        
        $monery = $conpouModel->getCouponById($userCoupon[CouponListModel::$cId_d]);
        
        $this->objController->assign('conpouListModel',CouponListModel::class);
        
        $this->objController->assign('couponMonery', $monery); 
        
        $this->objController->display();
    }
    
    /**
     * 发货
     */
    public function sendGoods() :void
    {
        $order = $this->getOrder();
        
        $this->objController->promptParse($order, '没有数据集');
        //收货人信息
        
        $userAddressLogic = new UserAddressLogic($order, null);
        
        $receive = $userAddressLogic->receiveManByOrder();
        
        $addressModelClassName = $userAddressLogic->getModelClassName();
        
        $regionLogic = new RegionLogic($receive, $addressModelClassName);
        
        $receive = $regionLogic->getDefaultRegion();
        
        $this->objController->promptParse($receive,'没有数据集');
        
        $goodsInfo = $this->getOrderGoodsInfo($order, true);
        
        $this->objController->promptParse($goodsInfo,'没有商品数据集');
        
        $this->objController->assign('order', array_merge($order, $receive));
        $this->objController->assign('goodsInfo', $goodsInfo);
        $this->objController->assign('userAddressModel', $addressModelClassName);
        
        $this->objController->display();
    }
    
    /**
     * 订单列表
     */
    public function orderListByStore() :void
    {
        Tool::connect('parseString');
        
        $userLogic = new UserLogic($this->args, $this->logic->getUserSplitKey());
        
        $userIdWhere = $userLogic->getAssociationCondition();
        
        $this->logic->setAssociationWhere($userIdWhere);
        
        $data = $this->logic->getOrderListBySettlement();
        
        $this->objController->isEmpty($data['data']);
        
        $userLogic->setData($data['data']);
        
        $data['data'] = $userLogic->getUserByIds();
        
        $expressLogic = new ExpressLogic($data['data'], $this->logic->getExpressSplitKey());
        
        $data['data'] = $expressLogic->getResult();
        
        $this->objController->ajaxReturnData($data);
        
    }
    /**
     * 订单详情--确定发货
     * 王波
     */
    public function orderSendGoods() :void
    {
        //验证数据
    	$checkObj = new CheckParam($this->logic->getMessageValidate(), $this->args);
    	
    	$this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());
    	
    	$status = $this->logic->getOrderSendGoods();
    	
    	$this->objController->promptPjax($status, $this->logic->getErrorMessage());
    	
    	$orderGoodsLogic = new OrderGoodsLogic($this->logic->getOrderSendData());
    	
    	$status = $orderGoodsLogic->updateGoodsSendStatus();
    	
    	$this->objController->promptPjax($status, $orderGoodsLogic->getErrorMessage());
    	
        $this->objController->ajaxReturnData("");
    }
    /**
     * 订单详情--修改运单号
     * 王波
     */
    public function saveWaybill() :void
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        $class = $this->logic->getSaveWaybill();
        $this->objController->promptPjax($class['status'],$class['message']);
        $this->objController->ajaxReturnData($class['data'],$class['status'],$class['message']);
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
            $this->goods_import($filename, $exts);
        }
    }
    public function goods_import($filename, $exts='xls'){
        vendor("PHPExcel.PHPExcel");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel=new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        if($exts == 'xls'){
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader=new \PHPExcel_Reader_Excel5();
        }else if($exts == 'xlsx'){
            import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader=new \PHPExcel_Reader_Excel2007();
        }
        $arr=[];
        //记录导入成功的条数
        $cnt_suc=0;
        //记录导入失败的条数
        $cnt_fal=0;
        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=3;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $cell =$currentSheet->getCell($address)->getValue();
                //$cell = $data[$currentRow][$currentColumn];
                if($cell instanceof PHPExcel_RichText){
                    $cell  = $cell->__toString();
                }
                if($currentColumn=="A"){
                    $hz['order_sn_id']=$cell;
                }
                if($currentColumn=="B"){
                    $hz['create_time']=$cell;
                }
                if($currentColumn=="C"){
                    $hz['pay_name']=$cell;
                }
                if($currentColumn=="D"){
                    $hz['payment_orderId']=$cell;
                }
                if($currentColumn=="E"){
                    $hz['price_sum']=$cell;
                }
                if($currentColumn=="F"){
                    $hz['freight']=$cell;
                }
                if($currentColumn=="G"){
                    $hz['coupon_deductible']=$cell;
                }
                if($currentColumn=="H"){
                    $hz['tax_fcy']=$cell;
                }
                if($currentColumn=="I"){
                    $hz['actual_amount']=$cell;
                }
                if($currentColumn=="J"){
                    $hz['user_name']=$cell;
                }
                if($currentColumn=="K"){
                    $hz['real_name']=$cell;
                }
                if($currentColumn=="L"){
                    $hz['id_number']=$cell;
                }
                if($currentColumn=="M"){
                    $hz['mobile']=$cell;
                }
                if($currentColumn=="N"){
                    $hz['prov']=$cell;
                }
                if($currentColumn=="O"){
                    $hz['city']=$cell;
                }
                if($currentColumn=="P"){
                    $hz['dist']=$cell;
                }
                if($currentColumn=="Q"){
                    $hz['address']=$cell;
                }
                if($currentColumn=="R"){
                    $hz['insurefee']=$cell;
                }
                if($currentColumn=="S"){
                    $hz['goods_id']=$cell;
                }
                if($currentColumn=="T"){
                    $hz['goods_num']=$cell;
                }
                if($currentColumn=="U"){
                    $hz['type']=$cell;
                }
                if($currentColumn=="V"){
                    $hz['tp_code']=$cell;
                }
                if($currentColumn=="W"){
                    $hz['busi_mode']=$cell;
                }
                if($currentColumn=="X"){
                    $hz['express_id']=$cell;
                }
                if($currentColumn=="Y"){
                    $hz['billno']=$cell;
                }
                if($currentColumn=="Z"){
                    $hz['bak_one']=$cell;
                }
                if($currentColumn=="AA"){
                    $hz['bak_two']=$cell;
                }
                if($currentColumn=="AB"){
                    $hz['bak_three']=$cell;
                }
                if($currentColumn=="AC"){
                    $hz['bak_four']=$cell;
                }
                if($currentColumn=="AD"){
                    $hz['bak_five']=$cell;
                }
            }
            $arr[]=$hz;
        }
        $res = M('offlineOrder')->addAll($arr);
        if(!$res){
            $data['data']= '';
            $data['status']= 0;
            $data['message']='导入失败';
            $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
        }
        if($cnt_suc != 0){
            $data['data']= '导入成功的条数'.$cnt_suc;
            $data['status']= 1;
            $data['message']='导入成功';
            $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
        }else{
            $data['data']= '导入失败的条数'.$cnt_fal;
            $data['status']= 0;
            $data['message']='导入失败';
            $this->objController->ajaxReturnData($data['data'],$data['status'],$data['message']);
        }
    }

}