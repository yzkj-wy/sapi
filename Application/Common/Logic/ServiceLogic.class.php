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

use Admin\Logic\UserLogic;
use Admin\Model\ServiceTypeModel;
use Admin\Model\UserModel;
use Common\Logic\AbstractGetDataLogic;
use Admin\Model\ServiceModel;
use Common\Tool\Tool;
use Common\Tool\Extend\ArrayChildren;
use MongoDB\Driver\Server;

/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class ServiceLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param array $data
     */
    public function __construct(array $data, $split= null)
    {
       $this->data = $data;

       $this->modelObj = new ServiceModel();

        $this->servicetype=new ServiceTypeModel();

       //$this->splitKey = $split === null ? ServiceModel::$id_d : $split;
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
//管理类型列表
    public function logTypeList(){

       // $data=$this->data;

        return $this->servicetype->_getTypeList();

}
    //添加管理类型
    public function logAddtype(){

        $data=$this->data;

        $res =$this->servicetype->addType($data);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }
//根据id获取详情
public function loggetTypeDetailById(){

    $data=$this->data;

   return M('service_type')->where(['id'=>$data['type_id']])->field('name,
status,sort')->find();
}



    //删除管理类型
public function logdeltype(){
    $data=$this->data;

    $res =M('service_type')->where(['id'=>$data['type_id']])->delete();
    if ($res) {
        return array("status"=>"","message"=>'删除成功',"data"=>"");
    }else{
        return array("status"=>$res['status'],"message"=>'删除失败',"data"=>$res['data']);
    }

}



    //客服类型是否启用
    public function logtypeIsUse(){
        $data=$this->data;

        return $this->servicetype->isUse($data);

    }



//管理类型列表
    public function logManageList(){

       // $data=$this->data;

        $result=$this->modelObj->manageList();
        foreach($result as $k=>$v){
            $r=$this->servicetype->getTypeNameById($v['servicetype_id']);
            $result[$k]['serviceType']=$r['name'];
        }
        return $result;

    }
    //获取详情根据id
    public function loggetDetailById(){
        $data=$this->data;
      return  M('service')->where(['id'=>$data['service_id']])->find();

    }




//客服管理--是否显示
    public function logIsShow(){

        $data=$this->data;

        return $this->modelObj->IsShow($data);

    }
//客服管理--是否主客服
    public function logIsMainServer(){

        $data=$this->data;

        return $this->modelObj->IsMainService($data);
    }
    
    /**
     * 检查数据
     */
    public function checkValidate() :array
    {
    	return [
    		'sort' => [
    			'number' => '排序必须是数字且介于${0-255}'
    		]
    	];
    }
    
	//客服管理--添加
    public function logAddservice(){
        $data=$this->data;
        $res =$this->modelObj->addService($data);
        if ($res['status'] == 0) {
            return array("status"=>"","message"=>$res['mes'],"data"=>"");
        }
        return array("status"=>$res['status'],"message"=>$res['mes'],"data"=>$res['data']);
    }
    
	//客服管理删除
	public  function  logdelservice(){
	    $data=$this->data;
	    $res =M('service')->where(['id'=>$data['service_id']])->delete();
	    if ($res) {
	        return array("status"=>"","message"=>'删除成功',"data"=>"");
	    }else{
	        return array("status"=>$res['status'],"message"=>'删除失败',"data"=>$res['data']);
	    }
	}


    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->servicetype->getComment();

        $message = [
            'name' => [
                'required' => '请输入'.$comment['name'],
            ],

            'status'=> [
                'required' => '请输入'.$comment['status'],
                'number' => $comment['status'].'必须是数字'
            ],
            'sort'=> [
                'number' => $comment['sort'].'必须是数字'
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
            ServiceTypeModel::$status_d => [
                'required' => true,
                'number' => true
            ],
            ServiceTypeModel::$sort_d => [

                'number' => true
            ],

            ServiceTypeModel::$name_d => [
                'required' => true,
            ],

        ];
        return $validate;
    }



}