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
use Admin\Model\IntegralGoodsModel;



/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class IntegralLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split= null)
    {
       $this->data = $data;

       $this->modelObj = new IntegralGoodsModel();

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

    }
//积分列表
    public function logList(){

        $data=$this->data;

        return $this->modelObj->IntegralList($data);

}
    //修改积分
    public function logUpd(){

        $data=$this->data;

        $res =$this->modelObj->addUpdIntegral($data);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }
    //获取单条信息
    function logGetInfoById(){
        $id=$this->data['id'];

        $res =$this->modelObj->getInfoById($id);
        if (!$res) {
            return array("status"=>0,"message"=>'暂无数据',"data"=>"");
        }
        return array("status"=>1,"message"=>'获取成功',"data"=>$res);
    }



//是否显示
    public function logIsShow(){

        $data=$this->data;

        return $this->modelObj->IsShow($data);

    }

//积分添加
    public function logAdd(){
        $data=$this->data;
        $res =$this->modelObj->addUpdIntegral($data);
        if ($res['status'] == 0) {
            return array("status"=>0,"message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }

//系统消息删除
public function logDelete(){
    $data=$this->data;
    $r= M('integral_goods')
        ->where(['id'=>$data['id']])
        ->delete();
    if ($r) {
        return array("status"=>"1","message"=>'删除成功',"data"=>"");
    }
    return array("status"=>'0',"message"=>'删除失败',"data"=>'');
}


    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();

        $message = [
            'goods_id' => [
                'required' => '请添加商品',
                'number' => $comment['goods_id'].'必须是数字'
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
            'goods_id' => [
                'required' => true,
                'number' => true
            ],

        ];
        return $validate;
    }



}