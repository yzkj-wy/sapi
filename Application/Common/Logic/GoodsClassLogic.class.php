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
use Admin\Model\GoodsClassModel;

/**
 * 逻辑处理层
 * @author 王强
 * @version 1.0.0
 * @tutorial what are you doing with you ? I don't know I true or false. Look at the black bord ,listen to me tall forly.
 */
class GoodsClassLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct($data)
    {
       $this->data = $data;
       
       $this->modelObj = new GoodsClassModel();
    }
    
    public function getResult(){}
    
    public function getModelClassName()
    {
        return ComplainModel::class;
    }
    //查询商品顶级分类
    public function getGoodsTopClass(){
    	$field = 'id,class_name,fid';
    	$where['fid'] = 0;
    	$where['hide_status'] = 1;
    	$res = D('GoodsClass')->field($field)->where($where)->select();
    	if (empty($res)) {
    		return array("status"=>"","message"=>"获取失败","data"=>"");
    	}
    	return array("status"=>1,"message"=>"获取成功","data"=>$res);
    }
    //查询商品询下级分类
    public function getGoodsNextClass(){
    	$data = $this->data;
    	$field = 'id,class_name,fid';
    	$where['fid'] = $data['id'];
    	$where['hide_status'] = 1;
    	$res = D('GoodsClass')->field($field)->where($where)->select();
    	if (empty($res)) {
    		return array("status"=>"","message"=>"获取失败","data"=>"");
    	}
    	return array("status"=>1,"message"=>"获取成功","data"=>$res);
    }
}