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
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreLogic;
use Common\Logic\OrderLogic;
use Common\Logic\GoodsLogic;
use Common\Logic\ComplainLogic;
use Admin\Logic\UserLogic;
use Common\Logic\AnnouncementLogic;
use Common\Logic\StoreMemberLogic;
use Common\Logic\StoreEvaluateLogic;
use Common\Logic\SystemConfigLogic;
/**
*企业入驻
**/
class IndexController 
{
    use IsLoginTrait;
    use InitControllerTrait;
    /**
     * 架构方法
     */              
    public function __construct(array $args =[])
    {   $this->init();
        $this->isNewLoginAdmin();
        $this->args = $args;                  
        $this->logic = new StoreLogic($args);
    }
    public function index(){
        
        $this->evaluate = new StoreEvaluateLogic($this->args);
        //店铺评分
        $score = $this->evaluate->score();
    	//店铺信息
    	$store = $this->logic->getStoreInfo();
    	//订单信息
    	$this->order = new OrderLogic($this->args);
    	$order = $this->order->getOrderInfo();
    	//商品信息
    	$this->goods = new GoodsLogic($this->args);
    	$goods = $this->goods->getGoodsNumBystatus();
    	//违规提醒
    	$this->ill = new ComplainLogic();
    	$illegal = $this->ill->getComplainNum();
    	//今日统计--订单
    	$orderToday = $this->order->orderToday();
    	//今日统计--客户
        $this->user = new UserLogic();
    	$userToday   = $this->user->userToday();
    	//今日统计--收益
    	$profitToday = $this->order->profitToday();
        //网站配置
        $this->config = new SystemConfigLogic($this->args);
        $config = $this->config->getConfig();

        //公告
        $this->announcement = new AnnouncementLogic();
        $announcement = $this->announcement->getAnnouncement();
        //销售情况统计
        $salesStatistics = $this->order->getSalesStatistics();
        //客户等级分析
        $this->userLevel = new StoreMemberLogic();
        $userLevel   = $this->userLevel->userRankAnalysis();
        $data = array(
            "score"=>$score,
            "store"=>$store, 
            "order"=>$order,
            "goods"=>$goods,
            "illegal"=>$illegal,
            "orderToday"=>$orderToday,
            "userToday"=>$userToday,
            "profitToday"=>$profitToday,
            "announcement"=>$announcement,
            "salesStatistics"=>$salesStatistics,
            "userLevel"=>$userLevel,
            "config"=>$config
        );
        if (!empty($data)) {
            $this->objController->ajaxReturnData($data,1,"获取成功!");
        }
        $this->objController->ajaxReturnData("",0,"获取失败!");
    } 
    public function qrcode(){
        $save_path = isset($_GET['save_path'])?$_GET['save_path']:'./Uploads/qrcode/';  //图片存储的绝对路径
        $web_path = isset($_GET['save_path'])?$_GET['web_path']:'/Uploads/qrcode/';        //图片在网页上显示的路径
        $qr_data = isset($_GET['qr_data'])?$_GET['qr_data']:'http://www.baidu.com/';
        $qr_level = isset($_GET['qr_level'])?$_GET['qr_level']:'H';
        $qr_size = isset($_GET['qr_size'])?$_GET['qr_size']:'5';
        $save_prefix = isset($_GET['save_prefix'])?$_GET['save_prefix']:'ZETA';
        if($filename = createQRcode($save_path,$qr_data,$qr_level,$qr_size,$save_prefix)){
            $pic = $web_path.$filename;
        }
        return $pic;    
    }
}