<?php
namespace Admin\Controller;

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\StoreBillLogic;
class StoreBillController
{
    use InitControllerTrait;

    use IsLoginTrait;


    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = null)
    {
        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new StoreBillLogic($args);

        $this->init();
    }
    /**
     * @description 商户结算查询 接口
     */
    public function index()
    {
        $data = $this->logic->getList();
        $this->objController->ajaxReturnData($data);

    }














}