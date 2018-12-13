<?php
namespace Admin\Controller;

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\CancelOrder;
use Common\Logic\OrderGoodsLogic;

/**
 * @author 王强
 * @version 1.0.0
 */
class CancelMoneyController
{
    use IsLoginTrait;
    
    use InitControllerTrait;
    
    use CancelOrder;
    
    /**
     * 架构方法
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new OrderGoodsLogic($args);
    
        $this->init();
    }
    
  
}