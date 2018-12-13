<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Common\Controller\AuthController;
use Common\TraitClass\SearchTrait;
use Common\TraitClass\InitControllerTrait;
use Common\Logic\PromotionLogic;
use Common\TraitClass\IsLoginTrait;
/**
 * 促销管理 
 */
class PromotionController extends AuthController
{
    use IsLoginTrait;
    use SearchTrait;
    use InitControllerTrait;
    /**
     * 促销
     * @var int
     */
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->init();
       // $this->isNewLoginAdmin();
        $this->args = $args;

        $this->logic =new PromotionLogic($args);

    }
    /**
     * 满减 列表
     */
    public function fullCut()
    {
        $result= $this->logic->logFullCut();

        $this->objController->ajaxReturnData($result);

    }

    /**
     * 满减满赠修改获取单条信息
     */
    public function getInfoById(){
       $result= $this->logic->logGetInfoById();//添加
        if($result){
            $this->objController->ajaxReturnData($result,1,'操作成功');
        }else{
            $this->objController->ajaxReturnData('',0,'暂无数据');
        }

    }


    /**
     *满减修改
     */

    public function updFullCut(){
        //验证数据
        $this->checkParamByClient();
        $result=$this->logic->logUpdFullCut();//添加
        $this->objController->promptPjax($result['status'],$result['message']);
        $this->objController->ajaxReturnData($result['data'],1,$result['message']);

    }

    /**
     *满减添加
     */

    public function addFullCut(){

        //验证数据
    	$this->checkParamByClient();
        $result=$this->logic->logAddFullCut();//添加
        $this->objController->promptPjax($result['status'],$result['message']);
        $this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);

    }

    /**
     * 满减删除
     */

    public function deleteFullCut(){

        $result=$this->logic->logDelFullCut();

        if(!empty($result)){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'操作失败');
        }

    }

    /**
     * 满减(赠)适用范围
     */

    public function fullCutUseRange(){

        $result=$this->logic->logFullCutUseRange();

        $this->objController->ajaxReturnData($result);

    }


    /**
     * 满赠列表
     */
    public function FullGift()
    {
        $result=$this->logic->logFullGift();

        $this->objController->ajaxReturnData($result);
    }

    /**
     * 满赠删除
     */

    public function deleteFullGift(){

        $result=$this->logic->logDelFullGit();

        if(!empty($result)){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'操作失败');
        }

    }
    /**
     *满赠添加
     */

    public function addFullGift(){

        //验证数据
    	$this->checkParamByClient();
        $result=$this->logic->logAddFullGift();//添加
        $this->objController->promptPjax($result['status'],$result['message']);
        $this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);

    }
    /**
     * 满赠修改
     */
    public function updFullGift(){
        //验证数据
    	$this->checkParamByClient();
        $result=$this->logic->logUpdFullGift();//添加
        $this->objController->promptPjax($result['status'],$result['message']);
        $this->objController->ajaxReturnData($result['data'],1,$result['message']);
    }
    /**
     * 满减满赠适用范围
     */
    public function getUseRange(){
       
        $result=$this->logic->logGetUseRange();

        if(!empty($result)){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'暂无数据');
        }
    }

    /**
     * 推荐配件
     */

    public function parts(){

        $result=$this->logic->logParts();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'暂无数据');
        }


    }

    /**
     * 推荐配件获取单条记录
     */
    public function getpartsById(){
        $result=$this->logic->logPartsById();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'暂无数据');
        }
    }


    /**
     * 是否有效推荐配件
     */

    public function isUse(){

        $result=$this->logic->logIsUse();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'修改失败');
        }

    }

    /**
     * 推荐配件删除
     */

    public function deleteParts(){

        $result=$this->logic->delParts();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'删除失败');
        }

    }

    /**
     * 选择商品
     */

    public function ChoiceGoods(){
            
            $result=$this->logic->ChoiceGoods();

            $this->objController->ajaxReturnData($result['data'],$result['status'],$result['message']);
    }

    /**
     *添加推荐配送
     */
    public function addParts(){

        $result=$this->logic->addPart();

        $this->objController->ajaxReturnData($result);

    }
    /**
     *修改推荐配送
     */
    public function updParts(){

        $result=$this->logic->updPart();

        $this->objController->ajaxReturnData($result);

    }

    /**
     * 最佳组合
     */

    public function combo(){

        $result=$this->logic->logCombo();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'暂无数据');
        }
    }
    /**
     * 最佳组合删除
     */

    public function deleteCombo(){

        $result=$this->logic->delCombo();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'删除失败');
        }

    }

    /**
     *添加最佳组合
     */
    public function addCombo(){

        $result=$this->logic->logAddCombo();

        $this->objController->ajaxReturnData($result);

    }

    /**
     *获取最佳组合单条记录
     */
    public function getComboById(){

        $result=$this->logic->loggetComboById();

        if($result['goods']){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'获取失败');
        }

    }


    /**
     *修改最佳组合
     */
    public function updCombo(){

        $result=$this->logic->updCombo();

        $this->objController->ajaxReturnData($result);

    }

    /**
     * 优惠套餐
     */

    public function package(){

        $result=$this->logic->logPackage();

        $this->objController->ajaxReturnData($result);

    }

    /**
     * 优惠套餐删除
     */

    public function deletePackage(){

        $result=$this->logic->logDelPackage();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'删除失败');
        }
    }

    /**
     * 优惠套餐添加
     */
    public function addPackage(){

        $result=$this->logic->logAddPackage();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'删除失败');
        }

    }

    /**
     * 优惠套餐获取单条信息
     */

    public function getPackageById(){
        $result=$this->logic->loggetPackageById();

        if($result['goods_info']){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'数据获取失败');
        }



    }


    /**
     * 优惠套餐修改
     */

    public function updPackage(){

        $result=$this->logic->logUpdPackage();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'修改失败');
        }
    }


    /**
     * 抢购
     */

    public function panicBuy(){
        //$result=$this->logic->getDataList();


        $result=$this->logic->logPanicBuy();

        $this->objController->ajaxReturnData($result);

    }

    /**
     * 抢购删除
     */

    public function deletePanic(){

        $result=$this->logic->logDelPanic();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('','删除失败','');
        }
    }

    /**
     * 抢购添加
     */

    public function addPanic(){
        $result=$this->logic->logAddPanic();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('','添加失败','');
        }
    }

    /**
     * 获取抢购单条记录
     */

    public function getPanicById(){
        $result=$this->logic->loggetPanicById();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('',0,'获取失败');
        }


    }


    /**
     * 抢购修改
     */

    public function updPanic(){

        $result=$this->logic->logUpdPanic();

        if($result){
            $this->objController->ajaxReturnData($result);
        }else{
            $this->objController->ajaxReturnData('','删除失败','');
        }
    }

}