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
use Common\Model\PanicModel;
use Admin\Model\GoodsModel;
use Think\Page;

/**
 * 入驻申请成功逻辑处理
 * @author 王强
 * @version 1.0
 */
class PanicLogic extends AbstractGetDataLogic
{
    //其他表搜索条件
    private $otherWhere = [];

    /**
     * @param field_type $otherWhere
     */
    public function setAssociationWhere($otherWhere)
    {
        $this->otherWhere = $otherWhere;
    }

    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = [], $split = '')
    {
        $this->data = $args;

        $this->splitKey = $split;

        $this->modelObj = new PanicModel();
    }

    /**
     * 添加数据
     */
    public function getResult()
    {
    }

    public function getPanicSplitKey()
    {
        return PanicModel::$goodsId_d;
    }
    /**
     * 获取模型类名
     */
    public function getModelClassName()
    {
        return StoreSellerModel::class;
    }

    //抢购列表
    public function getPanicList(){
        $post = $this->data;
        $goodsModel = new GoodsModel();
        $filed = 'id,panic_price,goods_id,start_time,end_time,status,panic_num,panic_title';
        $page = empty($post['page'])?0:$post['page'];
        if(!empty($post['keyWords'])){
            $g_where['title'] = array('like','%'.$post['keyWords'].'%');
            $goods = $goodsModel->field('id')->where($g_where)->select();
            $ids = array_column($goods, 'id');
            $where['goods_id'] = array('IN',$ids);
        }
        $res = $this->modelObj->field($filed)->where($where)->page($page.',10')->order('create_time DESC')->select();
        if (!$res) {
            return array("status"=>0,"message"=>"暂无数据","data"=>"");
        }
        $count =  $this->modelObj->where($where)->count();
        $Page  = new \Think\Page($count,10);
        $show = $Page->show();
        $totalPages  = $Page->totalPages;
        $data['page'] = $page;
        $data['count'] = $count;
        $data['totalPages'] = $totalPages;
        return array("status"=>1,"message"=>"获取成功","data"=>$res);
    }

    //添加抢购列表
    public function getAddPanic(){
        $post = $this->data;
        $goodsModel = new GoodsModel();
        $result = $this->modelObj->field('goods_id')->where(['goods_id'=>$post['goods_id']])->find();
        if(!empty($result)){
            return array("status"=>0,"message"=>"商品已存在，请勿重复添加","data"=>'');
        }
        $post['store_id'] = session('store_id');
        $post['create_time'] = time();
        M()->startTrans();
        $res = $this->modelObj->add($post);
        if(!$res){
            M()->rollback();
            return array("status"=>0,"message"=>"添加失败","data"=>'');
        }
        $res = $goodsModel->where(['id'=>$post['goods_id']])->save(['status'=>5]);
        if ($res === false) {
            M()->rollback();
            return array("status"=>0,"message"=>"添加失败","data"=>'');
        }
        M()->commit();
        return array("status"=>1,"message"=>"添加成功","data"=>'');
    }
    //修改抢购列表
    public function getUpdatePanic(){
        $goodsModel = new GoodsModel();
        $post = $this->data;
        $result = $this->modelObj->field('id,goods_id')->where(['goods_id'=>$post['goods_id']])->find();
        if(!empty($result)){
            if($result['id']!=$post['id']){
                return array("status"=>0,"message"=>"商品已存在，请勿重复修改","data"=>'');
            }
        }
        $post['update_time'] = time();
        $where['id'] = $post['id'];
        M()->startTrans();
        $res = $this->modelObj->where($where)->save($post);
        if($res===false){
            M()->rollback();
            return array("status"=>0,"message"=>"修改失败","data"=>'');
        }
        $res = $goodsModel->where(['id'=>$post['goods_id']])->save(['status'=>5]);
        if ($res === false) {
            M()->rollback();
            return array("status"=>0,"message"=>"添加失败","data"=>'');
        }
        M()->commit();
        return array("status"=>1,"message"=>"修改成功","data"=>'');
    }
    //删除抢购列表
    public function getDelPanic(){
        $goodsModel = new GoodsModel();
        $id = $this->data['id'];
        $where['id'] = array('IN',$id);
        M()->startTrans();
        $goods_id = $this->modelObj->where(['id'=>$id])->getField('goods_id');
        $res = $this->modelObj->where($where)->delete();
        if(!$res){
            M()->rollback();
            return array("status"=>0,"message"=>"删除失败","data"=>'');
        }

        $res = $goodsModel->where(['id'=>$goods_id])->save(['status'=>0]);
        if($res === false){
            M()->rollback();
            return array("status"=>0,"message"=>"删除失败","data"=>'');
        }
        M()->commit();
        return array("status"=>1,"message"=>"删除成功","data"=>'');

    }

    //获取单条信息
    public function getFiledOne(){
        $post = $this->data;
        $goodsModel = new GoodsModel();
        $filed = 'id,panic_title,goods_id,panic_price,panic_num,quantity_limit,start_time,end_time';
        $where['id'] = $post['id'];
        $res = $this->modelObj->field($filed)->where($where)->find();

        $goods = $goodsModel->field('title,price_market,stock')->where(['id'=>$res['goods_id']])->find();
        $res['title'] = $goods['title'];
        $res['price_market'] = $goods['price_market'];
        $res['stock'] = $goods['stock'];

        if(!$res){
            return array("status"=>0,"message"=>"暂无数据","data"=>$res);
        }
        return array("status"=>1,"message"=>"获取成功","data"=>$res);
    }


    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();

        $message = [
            PanicModel::$panicTitle_d => [
                'required' => '请输入'.$comment[PanicModel::$panicTitle_d],
            ],
            PanicModel::$panicPrice_d => [
                'required' => '请输入'.$comment[PanicModel::$panicPrice_d],
            ],
            PanicModel::$goodsId_d => [
                'number' => '商品编号必须是数字',
            ],
            PanicModel::$quantityLimit_d => [
                'required' => '请输入'.$comment[PanicModel::$quantityLimit_d],
            ],
        ];

        return $message;
    }

    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate()
    {
        $validate = [
            PanicModel::$panicTitle_d => [
                'required' => true,
            ],
            PanicModel::$panicPrice_d => [
                'required' => true,
            ],
            PanicModel::$goodsId_d => [
                'required' => true,
            ],
            PanicModel::$quantityLimit_d => [
                'required' => true,
            ],
        ];
        return $validate;
    }
}