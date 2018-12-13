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

use Admin\Model\AlbumClassModel;


/**
 * 图片逻辑处理
 * @author 王强
 * @version 1.0
 */
class AlbumClassLogic extends AbstractGetDataLogic
{

    /**
     * 架构方法
     */
    public function __construct($data)
    {
        $this->data = $data;

        $this->modelObj = AlbumClassModel::getInitnation();
    }

    /**
     * 返回模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return AlbumClassModel::class;
    }

    /**
     * 获取结果
     */
    public function getResult()
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
            AlbumClassModel::$albName_d => [
                'required' => '请输入'.$comment[AlbumClassModel::$albName_d],
                'specialCharFilter' => $comment[AlbumClassModel::$albName_d].'不能输入特殊字符'
            ]
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
            AlbumClassModel::$albName_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
        ];
        return $validate;
    }

    /**
     * 添加相册
     */
    public function addAlbum()
    {

        //验证数据
        if(empty($this->data) || $this->validateSort() === false){
            return [];
        }
        try{

            if($this->modelObj->add($this->data) === false){
                $this->errorMessage = '相册添加失败';
                return false;
            }
            return true;

        }catch (\Exception $e){
            $this->errorMessage = '相册已存在';
            return false;
        }



    }

    /**
     * 验证排序数字有效性
     */
    private function validateSort()
    {
        //验证参数是否有alb_sort字段
        if(empty($this->data['alb_sort'])){
            return true;
        }
        //验证alb_sort字段必须为数字
        if(is_numeric($this->data['alb_sort']) === false){
            $this->errorMessage = '排序必须为数字';
            return false;
        }
        //验证alb_sort字段必须为0-255
        if($this->data['alb_sort'] >= 0 && $this->data['alb_sort'] <= 255){
            return true;
        }
        $this->errorMessage = '排序数字必须为0-255';
        return false;
    }


    /**
     * 删除相册和图片
     */
    public function deleteAlbum()
    {
        $alb_id = $this->data['id'];
        $this->modelObj->startTrans();
        //删除相册
        if($this->modelObj->where(['store_id'=> session('store_id') , 'id'=> $alb_id])->delete()===false){
            $this->modelObj->rollback();
            $this->errorMessage = '删除相册失败';
            return false;
        }
        //删除相册图片
        $result = M("AlbumPic")->where(['alb_id'=>$alb_id])->delete();
        if($result === false){
            $this->modelObj->rollback();
            $this->errorMessage = '删除相册图片失败';
            return false;
        }
        return $this->modelObj->commit();
    }


    /**
     * 编辑相册信息
     */
    public function editAlbum()
    {
        //验证数据
        if(empty($this->data) || $this->validateSort() === false){
            return [];
        }
        try{

            if($this->modelObj->where(['id'=>$this->data['id'],'store_id'=>session('store_id')])->save($this->data) === false){
                $this->errorMessage = '相册修改失败';
                return false;
            }
            return true;

        }catch (\Exception $e){
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取所有相册
     */
    public function getAllAlbum()
    {
        $data = $this->modelObj->field('id,alb_name')->where(['store_id' => session('store_id')])->select();
        return $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum() :array
     */
    protected function getTableColum() :array
    {
        return [
            AlbumClassModel::$id_d,
            AlbumClassModel::$albName_d,
            AlbumClassModel::$albSort_d,
            AlbumClassModel::$albCover_d,
            AlbumClassModel::$createTime_d,
            AlbumClassModel::$albDes_d
        ];
    }
}