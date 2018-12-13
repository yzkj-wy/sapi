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
use Admin\Model\AlbumPicModel;
use Common\Tool\Tool;
use Think\Page;
use PlugInUnit\Validate\Children\Number;


/**
 * 相册逻辑处理
 * @author 王强
 * @version 1.0
 */
class AlbumPicLogic extends AbstractGetDataLogic
{

    /**
     * 架构方法
     */
    public function __construct($data)
    {
        $this->data = $data;

        $this->modelObj = AlbumPicModel::getInitnation();
    }

    /**
     * 返回模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return AlbumPicModel::class;
    }

    /**
     * 获取结果
     */
    public function getResult()
    {

    }

    /**
     * 获取相册图片数量
     */
    public function getAlbumPicNumber()
    {
        $model = $this->modelObj;
       
        foreach ($this->data as $key => $value){

            $this->data[$key]['pic_num'] = $model -> where(['alb_id' => $value['id']]) -> count();

        }
        return $this->data;
    }

    /**
     * 获取相册图片列表
     */
    public function getAlbumPicList()
    {

        if(empty($this->data['alb_id'])){
            $this->errorMessage = '请选择要查看的相册';
            return [];
        }
//         $alb_name = $this->isStoreAlbum();
//         if($alb_name === false){
//             return [];
//         }

        $this->searchTemporary = [
            AlbumPicModel::$ablId_d => (int)$this->data['alb_id']
        ];
        
        $res = $this->getDataList();
        
        return $res;

    }

    /**
     * 验证相册编号提示消息
     */
    public function getIndexMessageNotice()
    {

        $message = [
            AlbumPicModel::$ablId_d => [
                'required' => '请输入要查看的相册编号',
                'number' => '所属相册编号必须为数字'
            ]
        ];

        return $message;
    }

    /**
     * 判断要查看的相册是否为商家
     */
    private function isStoreAlbum()
    {
        $model = AlbumClassModel::getInitnation();
        $res = $model -> where(['store_id' => session('store_id'),'id' => $this->data['alb_id']]) -> find();
        if(empty($res)){
            $this->errorMessage = '不存在的相册';
            return  false;
        }
        return $res['alb_name'];
    }

    /**
     * 分页获取数据
     */
    private function getPageResult()
    {
        $model = $this->modelObj;

        //获取数据总条数
        $count = $model->where(['alb_id' => $this->data['alb_id']])->count();

        //获取分页配置
        $page_setting = C('PAGE_SETTING');
        $page = new Page($count, $page_setting['PAGE_SIZE']);

        //总页数
        $page_num = ceil($page->totalRows / $page->listRows);

        //分页查询
        $rows = $model->where(['alb_id' => $this->data['alb_id']]) ->limit($page->firstRow.','.$page->listRows)->select();
       
        foreach ($rows as $key => $value){

            $rows[$key]['pic_size'] = Tool::getFileSize($value['pic_size']);

        }

        return compact(['rows','page_num']);
    }

    /**
     * 判断图片是否为商家图片
     */
    private function isStorePic()
    {

        $model = AlbumClassModel::getInitnation();
        $alb_data = $model->find($this->data['alb_id']);
        if(empty($alb_data) || $alb_data['store_id'] != session('store_id')){
            $this->errorMessage = '请选择正确的图片';
            return false;
        }
        return $alb_data;
    }

    /**
     * 改变封面图片
     */
    public function changeCoverPic()
    {
        //验证是否为商家的图片
        $alb_data = $this->isStorePic();
        if($alb_data === false){
            return false;
        }
        //将数据表字段设为1(封面图片)
        $this->data['is_cover'] = 1;

        $this->modelObj->startTrans();
        //将该相册所有的图片设置为不是封面图片
        if($this->modelObj -> where(['alb_id' => $alb_data['id']]) -> setField(['is_cover' => 0]) === false){
            $this->modelObj->rollback();
            $this->errorMessage = '修改失败';
            return false;
        }

        //修改要设置为封面的图片
        if($this->modelObj->save($this->data) === false){
            $this->modelObj->rollback();
            $this->errorMessage = '修改图片状态失败';
            return false;
        }

        //设置相册封面
        $path = $this->modelObj->field('pic_path') -> find($this->data['id']);
        $path = $path['pic_path'];
        $model = AlbumClassModel::getInitnation();
        if($model->where(['id' => $this->data['alb_id']]) -> save(['alb_cover' => $path]) === false){
            $this->modelObj->rollback();
            $this->errorMessage = '修改相册封面失败';
        }
        return $this->modelObj->commit();
    }

    /**
     * 删除图片
     */
    public function delPics()
    {
        $id_info = $this->data['id'];

        //验证是否为商家的图片
        $alb_data = $this->isStorePic();
        if($alb_data === false || $this->isExistPic() === false){
            return false;
        }
        $this->modelObj->startTrans();
        $path = $this->modelObj->field('pic_path')->where('alb_id = ' . $this->data['alb_id'] . ' and id in (%s)' ,$id_info)->find();
        $path = $path['pic_path'];
        if(unlink(realpath(__ROOT__).$path) === false){
            $this->modelObj->rollback();
            $this->errorMessage = '删除图片失败';
            return false;
        }

        if($this->modelObj->where('alb_id = ' . $this->data['alb_id'] . ' and id in (%s)' ,$id_info)->delete() === false){
            $this->modelObj->rollback();
            $this->errorMessage = '删除图片数据失败';
            return false;
        }


        return $this->modelObj->commit();


    }

    /**
     * 查看图片是否存在
     */
    private function isExistPic()
    {
        $res = $this->modelObj->where(['id' => $this->data['id']])->find();
        if(empty($res)){
            $this->errorMessage = '图片不存在';
            return false;
        }
        return true;
    }

    /**
     * 保存图片信息到数据库
     */
    public function setGoodsPicInfoToDB()
    {
        //验证是否为商家的图片
        $alb_data = $this->isStorePic();
        if($alb_data === false){
            return false;
        }

        $this->modelObj->startTrans();
        $isCover = $this->modelObj->where(['alb_id' => $this->data['alb_id']]) -> find();
        if(empty($isCover)){

            //设置相册封面
            $this->data['is_cover'] = 1;
            $model = $this->modelObj;
            if($model->where(['id'=>$this->data['alb_id']])->save(['alb_cover' => $this->data['pic_path']]) === false){
                $this->modelObj->rollback();
                return false;
            }

        }
        
        if($this->modelObj->add($this->data) === false){
            $this->modelObj->rollback();
            return false;
        }
        return $this->modelObj->commit();

    }

    /**
     * 移动图片相册
     */
    public function moveAlbum()
    {
        //验证是否为商家的图片
        if($this->isStorePic() === false){
            return false;
        }
        //判断向移动的相册是否存在
        if($this->isStoreAlbum() === false){
            return false;
        }
        //移动照片
        $id = $this->data['id'];
        $alb_id = $this->data['alb_id'];
        if($this->modelObj->where('id in (' . $id . ')')->save(['alb_id' => $alb_id]) === false){
            $this->errorMessage = '移动照片失败';
            return false;
        }
        return true;

    }
    
   /**
    * 验证商品相册
    * @return boolean
    */
    public function getCheckDataByAlbumPic()
    {
        if (empty($this->data['image_space'])) {
            $this->errorMessage = '参数异常';
            return false;
        }
        
        
        foreach ($this->data['image_space'] as $key => $value) {
          
            if (!isset($value[AlbumPicModel::$ablId_d]) || !is_numeric($value[AlbumPicModel::$ablId_d])) {
                $this->errorMessage = '相册分类必须是数字';
                return false;
            }
            
            if (!isset($value[AlbumPicModel::$picPath_d]) || empty($value[AlbumPicModel::$picPath_d])) {
                $this->errorMessage = '图片路径必须存在';
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 添加相册图片数据
     */
    public function addPic()
    {
        $status = false;
        
        try {
            $status = $this->modelObj->addAll($this->data['image_space']);
            
            return $status;
        } catch (\Exception $e) {
           $this->errorMessage = $e->getMessage();
           return false;
        }
    }
    
    /**
     * 处理条件
     * @return array
     */
    protected function parseOption(array $options)
    {
        return $options;
    }
}

