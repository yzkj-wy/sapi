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

use Common\Logic\AbstractGetDataLogic;
use Common\Model\ExpressModel;
use Common\Tool\Extend\ArrayChildren;
use Common\TraitClass\TemplateTrait;
use Common\Tool\Tool;
use Common\Tool\Extend\PinYin;

/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class ExpressLogic extends AbstractGetDataLogic
{
    use TemplateTrait;
    
    /**
     * 注释
     * @var array
     */
    private $tempComment = [];
    
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data, $split = null)
    {
       $this->data = $data;
       
       $this->splitKey = $split;
       
       $this->modelObj = new ExpressModel();
       
       $this->covertKey = ExpressModel::$name_d;
    }
    
    /**
     * 获取数据
     */
    public function getResult()
    {
        //获取运送方式
        
        $field = [
            ExpressModel::$id_d,
            ExpressModel::$name_d,
        ];
        
        $expressData = $this->getDataByOtherModel($field, ExpressModel::$id_d);
        
        return $expressData;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return ExpressModel::class;
    }
    
    /**
     * 获取数据并缓存
     */
    public function getDataSource()
    {
        $data = S('EXPRESS_PERFIX_SUCCESS');
        
        if (empty($data)) {
            $field = [
                ExpressModel::$url_d
            ];
            $data = $this->modelObj->field($field, true)->select(); 
        } else  {
            return $data;
        }
        
        if (empty($data)) {
            return [];
        }
        
        $data = (new ArrayChildren($data))->convertIdByData(ExpressModel::$id_d);
        
        S('EXPRESS_PERFIX_SUCCESS', $data, 60);
        
        return $data;
    }
    
    /**
     * 获取快递名字
     */
    public function getExpressTitle()
    {
        if ( empty($this->data[$this->splitKey]) || ($id = intval($this->data[$this->splitKey])) === 0)
        {
            return null;
        }
        return $this->modelObj->where(ExpressModel::$id_d.' = '.$id)->getField(ExpressModel::$name_d);
    }
   
    
    /**
     * @return string
     */
    protected function getSelectField()
    {
        return $field = ExpressModel::$id_d.','.ExpressModel::$name_d;
    }
    
    /**
     * 获取默认开启的快递 五秒钟缓存
     */
    public function getDefaultOpen()
    {
        $data = S('expressData');
    
        if (empty($data)) {
            
            $field = ExpressModel::$id_d.','.ExpressModel::$name_d.','.ExpressModel::$discount_d;
            
            $data = $this->modelObj->where(ExpressModel::$status_d .' = 1')->getField($field);
        } else {
            return $data;
        }

        if (empty($data)) {
            return array();
        }
        
        S('expressData', $data, 15);
        
        return $data;
    }
    
    /**
     * 获取默认开启的快递 五秒钟缓存【带英文首字母】
     * @return []
     */
    public function getDefaultOpenEnglish()
    {
        $data = $this->getDefaultOpen();
    
        if (empty($data)) {
            return [];
        }
        
        $pinObj = new PinYin();
        
        foreach ($data as $key => & $value) {
            $pinObj->setStr($value[ExpressModel::$name_d]);
            $value[ExpressModel::$name_d] = $pinObj->getFirstEnglish().' '.$value[ExpressModel::$name_d];
        }
        return $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment()
    {
        return [ExpressModel::$letter_d];
    }
    
    /**
     * 搜索检测键值
     * @return boolean[][]
     */
    public function getSearchValidate()
    {
        return [
            ExpressModel::$name_d => [
              'specialCharFilter' => true,
            ],
        ];
    }
    
    /**
     * 搜索检测提示消息
     * @return string[][]
     */
    public function getSearchMessage()
    {
        $comment = $this->modelObj->getComment();
        return [
            ExpressModel::$name_d => [
                'specialCharFilter' => $comment[ExpressModel::$name_d].'不允许有特殊字符',
            ],
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        $validate =  [
            ExpressModel::$name_d => [
                'required' => '请输入'.$comment[ExpressModel::$name_d],
                'specialCharFilter' => $comment[ExpressModel::$name_d].'不能输入特殊字符'
            ],
            ExpressModel::$status_d => [
                'required' => '请输入'.$comment[ExpressModel::$status_d],
                'number'   => $comment[ExpressModel::$status_d].'必须是数字'
            ],
            ExpressModel::$code_d => [
                'required'          => '请输入'.$comment[ExpressModel::$code_d],
                'specialCharFilter' => $comment[ExpressModel::$name_d].'不能输入特殊字符'
            ],
            
            ExpressModel::$order_d => [
                'required' => '请输入'.$comment[ExpressModel::$order_d],
                'number'   => $comment[ExpressModel::$order_d].'必须是数字'
            ],
            
            ExpressModel::$url_d => [
                'required' => '请输入'.$comment[ExpressModel::$url_d],
                'checkURL' => $comment[ExpressModel::$url_d].'必须是有效的URL'
            ],
            
            ExpressModel::$ztState_d => [
                'required' => '请输入'.$comment[ExpressModel::$ztState_d],
                'number' => $comment[ExpressModel::$ztState_d].'必须是数字'
            ],
            
            ExpressModel::$tel_d => [
                'required' => '请输入'.$comment[ExpressModel::$tel_d],
                
            ],
            
            ExpressModel::$discount_d => [
                'required' => '请输入'.$comment[ExpressModel::$discount_d],
                'number' => $comment[ExpressModel::$discount_d].'必须是数字'
            ],
        ];
        
        $this->tempComment = $comment;
        
        return ($validate);
    }
    
    /**
     * 验证字段
     * @return boolean[][]
     */
    public function getCheckVildate()
    {
        $validate =  [
            ExpressModel::$name_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            ExpressModel::$status_d => [
                'required' => true,
                'number'   => true
            ],
            ExpressModel::$code_d => [
                'required'          => true,
                'specialCharFilter' => true
            ],
        
            ExpressModel::$order_d => [
                'required' => true,
                'number'   => true
            ],
        
            ExpressModel::$url_d => [
                'required' => true,
                'checkURL' => true
            ],
        
            ExpressModel::$ztState_d => [
                'required' => true,
                'number' => true
            ],
        
            ExpressModel::$tel_d => [
                'required' => true,
            
            ],
        
            ExpressModel::$discount_d => [
                'required' => true,
                'number'   => true
            ],
        ];
        
        return $validate;
    }
    
    /**
     * @return the $tempComment
     */
    public function getTempComment()
    {
        return $this->tempComment;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::addData()
     */
    public function addData()
    {
        $this->data[ExpressModel::$letter_d] = (new PinYin($this->data[ExpressModel::$name_d]))->getFirstEnglish();
        return parent::addData();
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
        return [
            ExpressModel::$name_d
        ];
    }
    //获取快递列表
    public function freightList(){
        $field = "id,name,status,code,url,order,zt_state";
        $order = "id DESC";
        $limit = 10;
        $list = $this->modelObj->getFreightListByWhere(null,$field,$order,$limit);
        return $list;
    }
    //获取已开启快递列表
    public function freightAlreadyOpened(){
        $field = "id,name";
        $order = "id DESC";
        $where['status'] = 1;
        $list = M('Express')->field($field)->where($where)->order($order)->select();
        if (!empty($list)) {
            return array("data"=>$list,"message"=>"获取成功","status"=>1);
        }
        return array("data"=>"","message"=>"获取失败","status"=>"");
    }
    //搜索快递列表
    public function freightSearch(){
        $post = $this->data;
        $where['name'] = array('like','%'.$post["name"].'%');
        $field = "id,name,status,code,url,order,zt_state";
        $order = "id DESC";
        $limit = 10;
        $list = $this->modelObj->getFreightListByWhere($where,$field,$order,$limit);
        return $list;
    } 
    //搜索快递列表
    public function getExpressSearch(){
        $post = $this->data;
        $where['name'] = array('like','%'.$post["name"].'%');
        $where['status'] = 1; 
        $list = $this->modelObj->field($field)->where($where)->order($order)->select();
        if(!empty($list)) {
            return array("data"=>$list,"message"=>"获取成功","status"=>1);
        }
        return array("data"=>"","message"=>"获取失败","status"=>"");
    } 
     //添加快递公司
    public function freightAdd(){
        $post = $this->data;
        $res  = $this->modelObj->addFreight($post);
        return $res;
    }
    //修改是否启用
    public function statusSave(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $field['status'] = $post['status'];
        $res  = $this->modelObj->saveFreight($where,$field);
        return $res;
    }
    //修改是否启用
    public function orderSave(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $field['order'] = $post['order'];
        $res  = $this->modelObj->saveFreight($where,$field);
        return $res;
    }
    //是否支持服务站配送
    public function ztStateSave(){
        $post = $this->data;
        $where['id'] = $post['id'];
        $field['zt_state'] = $post['zt_state'];
        $res  = $this->modelObj->saveFreight($where,$field);
        return $res;
    }
}