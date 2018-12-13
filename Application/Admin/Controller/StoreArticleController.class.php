<?php

namespace Admin\Controller;

use Common\Model\StoreArticleModel;
use Common\Model\StoreModel;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreArticleLogic;

class StoreArticleController
{
    use InitControllerTrait;

    use IsLoginTrait;


    /**
     * 构造方法
     * @param array $args
     */
    public function __construct()
    {
        $this->isNewLoginAdmin();

        $this->args = I( 'post.data' );

        $this->args[ 'store_id' ] = $_SESSION[ 'store_id' ];


        $this->logic = new StoreArticleLogic( $this->args );

        $this->init();
    }

    public function select()
    {
        $data = $this->logic->getDataList();
        $this->objController->ajaxReturnData($data);
    }

    /**
     * @description 店铺 文章添加 接口
     */
    public function add()
    {
        $status = $this->logic->addData();
        if( $status ){
            $this->objController->ajaxReturnData( [],1,'添加成功' );
        }
        $this->objController->ajaxReturnData( [],0,'添加失败' );

    }

    public function del()
    {
        if( !isset( $this->args[ 'id' ] ) ){
            $this->objController->ajaxReturnData( [],0,'删除失败-1' );
        }
        $store_id = ( new StoreArticleModel() )->init( [ 'id' => $this->args[ 'id' ] ] )->getStoreId();
        if( $store_id != $_SESSION[ 'store_id' ] ){
            $this->objController->ajaxReturnData( [],0,'删除失败-2' );
        }
        $status = $this->logic->delete();
        if( $status ){
            $this->objController->ajaxReturnData( [],1,'删除成功' );
        }
        $this->objController->ajaxReturnData( [],0,'删除失败-3' );

    }

    public function editInfo()
    {
        $data = $this->logic->getFindOne();
        $this->objController->ajaxReturnData($data);
    }

    public function save()
    {
        $store_id = ( new StoreArticleModel() )->init( [ 'id' => $this->args[ 'id' ] ] )->getStoreId();
        if( $store_id != $_SESSION[ 'store_id' ] ){
            $this->objController->ajaxReturnData( [],0,'更新失败-2' );
        }
        $status = $this->logic->saveData();
        if($status){
            $this->objController->ajaxReturnData([]);
        }
        $this->objController->ajaxReturnData([],0,'保存失败');

    }



}