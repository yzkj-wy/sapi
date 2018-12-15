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
use Common\Model\OfflineOrderModel;
use Common\Model\OfflineOrderGoodsModel;
use Admin\Model\GoodsModel;
use Admin\Model\GoodsImagesModel;
use Common\Tool\Tool;
use Common\Tool\Extend\ArrayChildren;
use Think\Cache;
use Common\SessionParse\SessionManager;
use Think\SessionGet;
/**
 * 逻辑处理层
 * @author 王波
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class OfflineOrderLogic extends AbstractGetDataLogic
{
    protected $orderStatus = [2, 3, 4];//获取指定订单状态

    /**
     * 订单数据
     * @var array
     */
    private $orderSendData = [];

    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split= null)
    {
        $this->data = $data;

        $this->modelObj = new OfflineOrderModel();
    }

    /**
     * 获取数据
     */
    public function getResult()
    {
    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return OfflineOrderModel::class;
    }
   //订单列表
    public  function getOrderList(){
        $post  = $this->data;
        if(!empty($post['order_sn_id'])){
            $where['order_sn_id'] = array('like','%'.$post['order_sn_id'].'%');
        }
        if (!empty($post['status'])) {
            $where['status'] = $post['status'];
        }else{
            if($post['status'] === '0'){
                $where['status'] = $post['status'];
            }
        }
        $page = empty($post['page'])?1:$post['page'];
        $store_id =  SessionGet::getInstance('store_id')->get();
        $where['store_id'] = $store_id;
        $data = $this->modelObj->where($where)->page($page,10)->order('add_time DESC')->select();
        if(empty($data)){
            return array('status'=>0,'message'=>'暂无数据','data'=>'');
        }
        $orderGoodsModel = new OfflineOrderGoodsModel();
        $goodsModel = new GoodsModel();
        $goodsImagesModel = new GoodsImagesModel();
        foreach($data as $key => $value){
            $orderGoods = $orderGoodsModel->where(['order_sn_id'=>$value['order_sn_id']])->select();
            foreach ($orderGoods  as $k =>$v){
                $goods = $goodsModel->field('title,p_id,price_member')->where(['id'=>$v['goods_id']])->find();
                $img = $goodsImagesModel->where(['goods_id'=>$goods['p_id'],'is_thumb'=>1])->getField('pic_url');
                $orderGoods[$key]['title'] = $goods['title'];
                $orderGoods[$key]['price_member'] = $goods['price_member'];
                $orderGoods[$key]['pic_url'] = $img;
            }
            $data[$key]['goods'] = $orderGoods;
        }
        $count = $this->modelObj->where($where)->count();
        $Page = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages = $Page->totalPages;
        $date['list'] = $data;
        $date['limit'] = 10;
        $date['page_size'] = $totalPages;
        $date['count'] = $count;
        return array('status'=>1,'message'=>'获取成功','data'=>$date);
    }
    //单条订单
    public  function getOrderDetail(){
        $post  = $this->data;
        $where['id'] = $post['id'];
        $data = $this->modelObj->where($where)->find();
        if(empty($data)){
            return array('status'=>0,'message'=>'暂无数据','data'=>'');
        }
        $orderGoodsModel = new OfflineOrderGoodsModel();
        $goodsModel = new GoodsModel();
        $goodsImagesModel = new GoodsImagesModel();
        $orderGoods = $orderGoodsModel->where(['order_sn_id'=>$data['order_sn_id']])->select();
        foreach ($orderGoods  as $key =>$value){
            $goods = $goodsModel->field('title,p_id,price_member')->where(['id'=>$value['goods_id']])->find();
            $img = $goodsImagesModel->where(['goods_id'=>$goods['p_id'],'is_thumb'=>1])->getField('pic_url');
            $orderGoods[$key]['title'] = $goods['title'];
            $orderGoods[$key]['price_member'] = $goods['price_member'];
            $orderGoods[$key]['pic_url'] = $img;

        }
        $payData = [];

        $payData[0]['order_id'] = $data['id'];

        $payData[0]['store_id'] = $data['store_id'];

        $payData[0]['total_money'] = round($data['actual_amount'],2);
        $this->payData = $payData;
        //支付数据
        SessionManager::SET_ORDER_DATA($payData);

        // 普通订单 0 套餐订单 1
        SessionManager::SET_ORDER_TYPE_BY_USER(0);

        SessionManager::REMOVE_GOODS_DATA_SOURCE();
        $data['goods'] = $orderGoods;
        return array('status'=>1,'message'=>'获取成功','data'=>$data);
    }
    //修改订单
    public  function getOrderSave(){
        $post  = $this->data;
        $where['id'] = $post['id'];
        $data = $this->modelObj->where($where)->save($post);
        if($data===false){
            return array('status'=>0,'message'=>'操作失败','data'=>'');
        }
        return array('status'=>1,'message'=>'操作成功','data'=>$data);
    }
    //支付成功修改订单状态
    public function saveStatus(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $data['status'] = 1;
        $data['save_time'] = time();
        $res = $this->modelObj->where($where)->save($data);
        return $res;
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
        $orderArray = [];
        $orderGoodsArray = [];

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
        ++$allColumn;
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
       $cnt_suc=$allRow-1;
        for($currentRow=2;$currentRow<=$allRow;$currentRow++) {
            //从哪列开始，A表示第一列
            for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {
                //数据坐标
                $address = $currentColumn . $currentRow;
                //读取到的数据，保存到数组$arr中
                $cell = $currentSheet->getCell($address)->getValue();
                //$cell = $data[$currentRow][$currentColumn];
                if ($cell instanceof PHPExcel_RichText) {
                    $cell = $cell->__toString();
                }
                if ($currentColumn == "A") {
                    $hz['order_sn_id'] = $cell;
                }
                if ($currentColumn == "B") {
                    $hz['create_time'] = $cell;
                }
                if ($currentColumn == "C") {
                    $hz['pay_name'] = $cell;
                }
                if ($currentColumn == "D") {
                    $hz['payment_order_id'] = $cell;
                }
                if ($currentColumn == "E") {
                    $hz['price_sum'] = $cell;
                }
                if ($currentColumn == "F") {
                    $hz['freight'] = $cell;
                }
                if ($currentColumn == "G") {
                    $hz['coupon_deductible'] = $cell;
                }
                if ($currentColumn == "H") {
                    $hz['tax_fcy'] = $cell;
                }
                if ($currentColumn == "I") {
                    $hz['actual_amount'] = $cell;
                }
                if ($currentColumn == "J") {
                    $hz['user_name'] = $cell;
                }
                if ($currentColumn == "K") {
                    $hz['real_name'] = $cell;
                }
                if ($currentColumn == "L") {
                    $hz['id_number'] = $cell;
                }
                if ($currentColumn == "M") {
                    $hz['mobile'] = $cell;
                }
                if ($currentColumn == "N") {
                    $hz['prov'] = $cell;
                }
                if ($currentColumn == "O") {
                    $hz['city'] = $cell;
                }
                if ($currentColumn == "P") {
                    $hz['dist'] = $cell;
                }
                if ($currentColumn == "Q") {
                    $hz['address'] = $cell;
                }
                if ($currentColumn == "R") {
                    $hz['shipper_name'] = $cell;
                }
                if ($currentColumn == "S") {
                    $hz['shipper_telephone'] = $cell;
                }
                if ($currentColumn == "T") {
                    $hz['insure_fee'] = $cell;
                }
                if ($currentColumn == "U") {
                    $hz['goods_id'] = $cell;
                }
                if ($currentColumn == "V") {
                    $hz['goods_num'] = $cell;
                }
                if ($currentColumn == "W") {
                    $hz['type'] = $cell;
                }
                if ($currentColumn == "X") {
                    $hz['tp_code'] = $cell;
                }
                if ($currentColumn == "Y") {
                    $hz['busi_mode'] = $cell;
                }
                if ($currentColumn == "Z") {
                    $hz['express_id'] = $cell;
                }
                if ($currentColumn == "AA") {
                    $hz['billno'] = $cell;
                }
                if ($currentColumn == "AC") {
                    $hz['bak_one'] = $cell;
                }
                if ($currentColumn == "AD") {
                    $hz['bak_two'] = $cell;
                }
                if ($currentColumn == "AE") {
                    $hz['bak_three'] = $cell;
                }
                if ($currentColumn == "AF") {
                    $hz['bak_four'] = $cell;
                }
                if ($currentColumn == "AH") {
                    $hz['bak_five'] = $cell;
                }
            }
            $arr[] = $hz;
        }
        $orderGoodsModel = new OfflineOrderGoodsModel();
        M()->startTrans();
        $store_id =  SessionGet::getInstance('store_id')->get();
        $orderArray = $this->arr_uniq($arr,'order_sn_id');
        foreach($orderArray as $k => $v){
            if(empty($value['payment_order_id'])){
                $v['status'] = 0;
            }else{
                $v['status'] = 1;
            }
            $v['add_time'] = time();
            $v['store_id'] = $store_id;
            if(!empty($v['order_sn_id'])){
                $res = $this->modelObj->add($v);
                if(!$res){
                    M()->rollback();
                    $data['data']= '';
                    $data['status']= 0;
                    $data['message']='导入失败';
                    return  $data;
                }
            }
        }
        foreach($arr as $key => $value){
            $orderGoodsArray['order_sn_id'] = $value['order_sn_id'];
            $orderGoodsArray['goods_id'] = $value['goods_id'];
            $orderGoodsArray['goods_num'] = $value['goods_num'];
            $orderGoodsArray['add_time'] = time();
            $orderGoodsArray['save_time'] = time();
            if(!empty($value['order_sn_id'])) {
                $ret = $orderGoodsModel->add($orderGoodsArray);
                if (!$ret) {
                    M()->rollback();
                    $data['data'] = '';
                    $data['status'] = 0;
                    $data['message'] = '导入失败';
                    return  $data;
                }
            }
        }

        M()->commit();

        $data['data']= '导入成功的条数'.$cnt_suc;
        $data['status']= 1;
        $data['message']='导入成功';
        return  $data;

    }
    /**
     * 二维数组按照指定键值去重
     * @param $arr 需要去重的二维数组
     * @param $key 需要去重所根据的索引
     * @return mixed
     */
    function arr_uniq($arr,$key)
    {
        $key_arr = [];
        foreach ($arr as $k => $v) {
            if (in_array($v[$key],$key_arr)) {
                unset($arr[$k]);
            } else {
                $key_arr[] = $v[$key];
            }
        }
        sort($arr);
        return $arr;
    }
}