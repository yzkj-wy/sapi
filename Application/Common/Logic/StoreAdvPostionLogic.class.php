<?php
namespace Common\Logic;

use Common\Logic\AbstractGetDataLogic;

use Common\Model\StoreAdvPostionModel;
/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class StoreAdvPostionLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data, $split = null)
    {
       $this->data = $data;
       
       $this->splitKey = $split;
       
       $this->modelObj = new StoreAdvPostionModel();
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
            StoreAdvPostionModel::$advTitle_d => [
                'required' => '请输入'.$comment[StoreAdvPostionModel::$advTitle_d],
            ],
            StoreAdvPostionModel::$apId_d => [
                'required' => '请输入'.$comment[StoreAdvPostionModel::$apId_d],
                'number' => $comment[StoreAdvPostionModel::$apId_d].'必须是数字'
            ],
            StoreAdvPostionModel::$advStart_date_d => [
                'required' => '请输入'.$comment[StoreAdvPostionModel::$advStart_date_d],
            ],
            StoreAdvPostionModel::$advEnd_date_d => [
                'required' => '请输入'.$comment[StoreAdvPostionModel::$advEnd_date_d],
            ],
            StoreAdvPostionModel::$adUrl_d => [
                'required' => '请输入'.$comment[StoreAdvPostionModel::$adUrl_d],
            ],
            StoreAdvPostionModel::$slideSort_d => [
                'required' => '请输入'.$comment[StoreAdvPostionModel::$slideSort_d],
                'number' => $comment[StoreAdvPostionModel::$slideSort_d].'必须是数字'
            ],
        ];
        
        return $message;
    }
    
    /**
     * 上传图片验证
     * @return []
     */
    public function getMessageByPic()
    {
        return [
            'tmp_name' => [
                'checkIsPicture' => '请上传图片'
            ]
        ];
    }
    
    /**
     * 验证图片宽高度
     * @return bool
     */
    public function checkImageWidthAndHeight()
    {
        
        $field = [
            StoreAdvPostionModel::$apHeight_d,
            StoreAdvPostionModel::$apWidth_d,
            StoreAdvPostionModel::$maxHeight_d,
            StoreAdvPostionModel::$maxWidth_d
        ];
        
        $data = $this->modelObj->field($field)->where(StoreAdvPostionModel::$id_d.'=:id')->bind([':id' => $this->data['id']])->find();
        
        if (empty($data)) {
            $this->errorMessage = '不存在 广告位配置';
            return false;
        }
        
        $imageInfo = getimagesize($_FILES['adv_content']['tmp_name']);
        
        $width = $imageInfo[0];
        
        $height = $imageInfo[1];
        
        if ($width > $data[StoreAdvPostionModel::$maxWidth_d] || $width < $data[StoreAdvPostionModel::$apWidth_d]) {
           
            $this->errorMessage = '宽度必须介于'.$data[StoreAdvPostionModel::$apWidth_d].'~'.$data[StoreAdvPostionModel::$maxWidth_d].'之间，此图宽度'.$width;
            
            return false;
        }
        
        if ($height > $data[StoreAdvPostionModel::$maxHeight_d] || $width < $data[StoreAdvPostionModel::$apHeight_d]) {
            
            $this->errorMessage = '高度必须介于'.$data[StoreAdvPostionModel::$apHeight_d].'~'.$data[StoreAdvPostionModel::$maxHeight_d].'之间，此图高度'.$height;
            
            return false;
        }
        return true;
    }
    
    /**
     * 获取验证规则
     * @return boolean[][]
     */
    public function getCheckValidate()
    {
        $validate = [
            StoreAdvPostionModel::$advTitle_d => [
                'required' => true,
            ],
            StoreAdvPostionModel::$apId_d => [
                'required' => true,
                'number' => true
            ],
            StoreAdvPostionModel::$advStart_date_d => [
                'required' => true,
            ],
            StoreAdvPostionModel::$advEnd_date_d => [
                'required' => true,
            ],
            StoreAdvPostionModel::$adUrl_d => [
                'required' => true,
            ],
            StoreAdvPostionModel::$slideSort_d => [
                'required' => true,
                'number' => true
            ],
        ];
        return $validate;
    }
    //获取广告位置列表
    public function getAdPostionList(){
        $post = $this->data;
        $field = "id,ap_name,is_use,ap_width,ap_height,max_height,max_width";
        $res = $this->modelObj->getAdvPostionList($field,$post['page']);
        if (!empty($res['data'])) {
            return array("status"=>1,"message"=>"获取成功","data"=>$res);
        }else{
            return array("status"=>1,"message"=>"暂无数据","data"=>"");
        }
    }  
    //获取广告位置列表
    public function getAdPostion(){
        $post = $this->data;
        $field = "id,ap_name,is_use,ap_width,ap_height,max_height,max_width";
        $where['is_use'] = 1;
        $res = $this->modelObj->getAdvPostion($where,$field);
        if (!empty($res)) {
            return array("status"=>1,"message"=>"获取成功","data"=>$res);
        }else{
            return array("status"=>1,"message"=>"暂无数据","data"=>"");
        }
    }  
}