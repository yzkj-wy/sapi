<?php
namespace Common\Logic;
use Admin\Model\OrderModel;
use Admin\Model\PromGoodsModel;



class PromotionLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;

        $this->modelObj = new PromGoodsModel();

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
        return OrderModel::class;
    }

    /**
     * 满减列表
     */
    public function logFullCut(){

        $data=$this->data;

        return $this->modelObj->getFullCutList($data);

    }

    /**
     * 满减删除
     */
    public function logDelFullCut(){

        $data=$this->data;

        return $this->modelObj->delPromGoods($data);

    }

    /**
     * 满减添加
     */
    public function logAddFullCut(){

        $data=$this->data;

        return $this->modelObj->addProGoods($data);


    }

    /**
     * 满减满赠修改获取单条信息
     */
    public function logGetInfoById(){
        $data=$this->data;
        $result= M('prom_goods')->field('id,name,full,expression,description,start_time,end_time,group')->where(['id'=>$data['id']])->find();
        $result['goods']=M('promotion_goods as p')->join('left join db_goods as g on p.goods_id=g.id ')->field('p.goods_id,g.title,g.price_market,g.stock')->where(['prom_id'=>$result['id']])->find();


        $result['description']=html_entity_decode($result['description']);

        return $result;
    }





    /**
     * 满减修改（编辑）
     */
    public function logUpdFullCut(){

        $data=$this->data;

        return $this->modelObj->addProGoods($data,'save');
    }
    /**
     * 满减适用范围
     */
    public function logFullCutUseRange(){

        return M('store_member_level as a')
            ->join('db_store_level_by_platform as b on b.id=a.level_id')
            ->where(['store_id'=>session('store_id')])
            ->field('b.id,level_name')
            ->select();
    }

    /**
     * 满赠列表
     */
    public function logFullGift(){

        $data=$this->data;
        $data['full']=true;
        return $this->modelObj->getFullCutList($data);

    }
    //满赠删除
    public function logDelFullGit(){

        $data=$this->data;

        return $this->modelObj->delPromGoods($data);
    }
    /**
     * 满赠添加
     */

    public function logAddFullGift(){

        $data=$this->data;

        $data['gift']=true;

        return $this->modelObj->addProGoods($data);

    }

    /**
     * 满赠修改编辑
     */
    public function logUpdFullGift(){

        $data=$this->data;

        $data['gift']=true;

        return $this->modelObj->addProGoods($data,'save');

    }


    /**
     * 推荐配件列表
     */
    public function logParts(){

        $data=$this->data;

        return $this->modelObj->getGoodsAccessories($data);
 
    }

    /**]
     * @return mixed
     * 获取推荐配置单条记录
     */
	public  function logPartsById(){
	    $data=$this->data;
	
	    return $this->modelObj->getPartsById($data);
	}




    /**
     * 推荐配件是否有效
     */

    public function logIsUse(){
        $data=$this->data;
        $id=$data['id'];
        if($data['status']==1){//有效
            $sta['status']=1;
        }else{
            $sta['status']=0;
        }
       return  M('goods_accessories')->where(['id'=>$id])->save($sta);
    }
    /**
     * 删除推荐配件
     */
    public function delParts(){

        $data=$this->data;

        return M('goods_accessories')->where(['id'=>$data['id']])->delete();

    }
    /**
     * 选择商品
     */

    public function ChoiceGoods(){

        $data=$this->data;
        $where['store_id'] = session('store_id');
        $where['p_id'] = array("NEQ",0);
        $where['approval_status'] = '1';
        $where['shelves'] = '1';
        $goods =  M('goods')->field('id,title,price_market,price_member,stock')->where($where)->page($data['page'],20)->select();
        $count      = M('goods')->where($where)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();
        $totalPages = $Page->totalPages;
        if (empty($goods)) {
            return array("status"=>0,"message"=>"暂时没有数据","data"=>"");
        }
        return array("status"=>1,"message"=>"获取成功","data"=>array("goods"=>$goods,"count"=>$count,"totalPages"=>$totalPages,"page_size"=>20));

    }

    /**
     * 添加推荐配置
     */

    public function addPart(){

        $data=$this->data;

        return $this->modelObj->addPart($data);

    }
    /**
     * 修改编辑推荐配置
     */
    public function updPart(){

        $data=$this->data;

        return $this->modelObj->addPart($data);
    }

    /**
     *最佳组合
     */

    public function logCombo(){


        $data=$this->data;

        return $this->modelObj->getGoodsCombo($data);

    }

    /**
     * 删除最佳组合
     */

    public function delCombo(){

        $data=$this->data;

        return M('goods_combo')->where(['id'=>$data['id']])->delete();

    }

    /**
     * 添加最佳组合
     */
    public function logAddCombo(){

        $data=$this->data;

        return $this->modelObj->addCombo($data);

    }
    /**
     * 最佳组合获取单条记录
     */
    public function loggetComboById(){
        $where['id']=$this->data['id'];
        $combo=M('goods_combo')
            ->field('goods_id,sub_ids')
            ->where($where)
            ->find();
        $goodsModel=M('goods');
        $last_data['goods']=$goodsModel->where(['id'=>$combo['goods_id']])->field('id,title,price_market,stock')->find();

            $sub_ids=explode(",",$combo['sub_ids']);

        foreach($sub_ids as $v){
            $last_data['sub_data'][]=$goodsModel->where(['id'=>$v])->field('id,title,price_market,stock')->find();
        }

        return $last_data;



    }


    /**
     * 修改编辑最佳组合
     */
    public function updCombo(){

        $data=$this->data;

        return $this->modelObj->addCombo($data);
    }

    /**
     * 优惠套餐
     */
    public function logPackage(){

        $data=$this->data;

        return $this->modelObj->getPackage($data);

    }

    /**
     * 删除优惠套餐
     */

    public function logDelPackage(){

        $data=$this->data;

        return $this->modelObj->delPackage($data);

    }
    /**
     * 优惠套餐获取单条记录
     */
    public function loggetPackageById(){
        $data=$this->data;

        return $this->modelObj->getPackageById($data);
    }


    /**
     * 优惠套餐添加
     */
    public function logAddPackage(){
        $data=$this->data;

        return $this->modelObj->AddPackage($data);
    }

    /**
     * 优惠套餐修改
     */

    public function logUpdPackage(){
        $data=$this->data;

        return $this->modelObj->AddPackage($data);
    }
    /**
     * 抢购
     */

    public function logPanicBuy(){
        $data=$this->data;

        return $this->modelObj->getPanicBuy($data);
    }

    /**
     * 抢购删除
     */
    public function logDelPanic(){
        $data=$this->data;

        return $this->modelObj->delPromGoods($data);
    }

    /**
     * 抢购添加
     */
    public function logAddPanic(){
        $data=$this->data;

        return $this->modelObj->AddUpdPanic($data);

    }

    /**
     * 获取抢购单条记录
     */
    public function loggetPanicById(){

        $id=$this->data['id'];

       return $this->modelObj->getPanicById($id);
    }

    /**
     * 抢购修改
     *
     */

    public function logUpdPanic(){

        $data=$this->data;

        return $this->modelObj->AddUpdPanic($data,'save');

    }



    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();

        $message = [
            'name' => [
                'required' => '请输入'.$comment['name'],
            ],

            'full'=> [
                'required' => '请输入消费金额',
                'number' => '消费金额必须是数字'
            ],
            'expression'=> [
                'required' => '请输入减价金额',
                'number' => '减价金额必须是数字'
            ],

            'start_time'=> [
                'required' => '请输入'.$comment['start_time'],
            ],
            'end_time'=> [
                'required' => '请输入'.$comment['end_time'],
            ],
            'group'=> [
                'required' => '请输入'.$comment['group'],
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
            'name' => [
                'required' => true,
            ],
            'full' => [
                'number' => true
            ],

            'expression' => [
                'number' => true,
            ],

        ];
        return $validate;
    }



}