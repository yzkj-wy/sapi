<?php
namespace Common\Logic;

use Common\Logic\AbstractGetDataLogic;
use Common\Model\StoreAdvModel;
use Common\Model\StoreAdvPostionModel;
use Common\Tool\Extend\CURL;
/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class StoreAdvLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data, $split = null)
    {
       $this->data = $data;
       
       $this->splitKey = $split;
       
       $this->modelObj = new StoreAdvModel();
       $this->postionModel = new StoreAdvPostionModel();
    }
    
    /**
     * 获取店铺地址数据
     */
    public function getResult()
    {
        
    }
    
    public function getModelClassName()
    {

    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $message = [
            StoreAdvModel::$advTitle_d => [
                'required' => '请输入'.$comment[StoreAdvModel::$advTitle_d],
            ],
            StoreAdvModel::$apId_d => [
                'required' => '请输入'.$comment[StoreAdvModel::$apId_d],
                'number' => $comment[StoreAdvModel::$apId_d].'必须是数字'
            ],
            StoreAdvModel::$advStart_date_d => [
                'required' => '请输入'.$comment[StoreAdvModel::$advStart_date_d],
            ],
            StoreAdvModel::$advEnd_date_d => [
                'required' => '请输入'.$comment[StoreAdvModel::$advEnd_date_d],
            ],
            StoreAdvModel::$adUrl_d => [
                'required' => '请输入'.$comment[StoreAdvModel::$adUrl_d],
            ],
            StoreAdvModel::$advContent_d => [
                'required' => '请输入'.$comment[StoreAdvModel::$advContent_d],
            ],
            StoreAdvModel::$slideSort_d => [
                'required' => '请输入'.$comment[StoreAdvModel::$slideSort_d],
                'number' => $comment[StoreAdvModel::$slideSort_d].'必须是数字'
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
            StoreAdvModel::$advTitle_d => [
                'required' => true,
            ],
            StoreAdvModel::$apId_d => [
                'required' => true,
                'number' => true
            ],
            StoreAdvModel::$advStart_date_d => [
                'required' => true,
            ],
            StoreAdvModel::$advEnd_date_d => [
                'required' => true,
            ],
            StoreAdvModel::$adUrl_d => [
                'required' => true,
            ],
            StoreAdvModel::$advContent_d => [
                'required' => true,
            ],
            StoreAdvModel::$slideSort_d => [
                'required' => true,
                'number' => true
            ],
        ];
        return $validate;
    }
    //获取广告列表
    public function getAdList(){
        $post = $this->data;
        $where['store_id'] = $_SESSION['store_id'];
        // $where['adv_end_date'] = array("GT",time());
        // $where['adv_start_date'] = array("LT",time());
        $field = "id,ap_id,adv_title,adv_content,ad_url,slide_sort,status";
        $res = $this->modelObj->getAdvList($where,$field,$post['page']);
        if (!empty($res['data'])) {
            $res['data'] = $this->postionModel->getAdvPostionByAd($res['data']);
            return array("status"=>1,"message"=>"获取成功","data"=>$res);
        }else{
            return array("status"=>1,"message"=>"暂无数据","data"=>"");
        }
    }
    //广告添加
    public function getAdAdd(){
        $post = $this->data;
        $post['store_id'] = $_SESSION['store_id'];
        $post['adv_end_date'] = strtotime($post['adv_end_date']);
        $post['adv_start_date'] = strtotime($post['adv_start_date']);
        $post['update_time'] = $time = time();
        $post['create_time'] = $time;
        $post['status'] =0;
        $res = $this->modelObj->getAdvAdd($post);       
        return $res;    
    }
    //广告修改
    public function getAdSave(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $post['adv_end_date'] = strtotime($post['adv_end_date']);
        $post['adv_start_date'] = strtotime($post['adv_start_date']);
        $post['update_time'] = time();
        if (!empty($post['img_new'])) {
            $date['fileName'] = $post['adv_content'];
            $post['adv_content'] = $post['img_new'];  
        }
        $res = $this->modelObj->getAdvSave($where,$post);
        if ($res['status'] == 1) {
            if (!empty($date)) {
                $curlFile = new CURL($date, C('unlink_image_no_thumb'));
                $curlFile->deleteFile();
                return $res;
            }
            return $res;
        }
        return $res;
        
    }
    
    //广告修改
    public function getAdReveal(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $post['update_time'] = time();        
        $res = $this->modelObj->getAdvSave($where,$post);   
        return $res;       
    }
    //广告-删除
    public function getAdDel(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $img = M('StoreAdv')->where($where)->getField('adv_content');
        if (!empty($img)) {
            $date['fileName'] = $img;
        }
        $res = $this->modelObj->getAdvDel($where);
        if ($res['status'] == 1) {
            if (!empty($date)) {
                $curlFile = new CURL($date, C('unlink_image_no_thumb'));
                $curlFile->deleteFile();
                return $res;
            }
            return $res;
        }     
        return $res;
        
    }
    //广告搜索
    public function getAdSearch(){
        $post = $this->data;
        if (!empty($post['adv_title'])) {
            $where['adv_title'] = $post['adv_title'];
        }
        if (!empty($post['ap_id'])) {
            $where['ap_id'] = $post['ap_id'];
        }
        $where['store_id'] = $_SESSION['store_id'];
        $field = "id,ap_id,adv_title,adv_content,ad_url,slide_sort,status";
        $res = $this->modelObj->getAdvList($where,$field,$post['page']);
        if (!empty($res['data'])) {
            $res['data'] = $this->postionModel->getAdvPostionByAd($res['data']);
            return array("status"=>1,"message"=>"获取成功","data"=>$res);
        }else{
            return array("status"=>1,"message"=>"暂无数据","data"=>"");
        }
    }
    //获取单条数据
    public function getAdInfo(){ 
        $post = $this->data;
        $res = $this->modelObj->getAdvInfo($post); 
        return $res;
    }
}