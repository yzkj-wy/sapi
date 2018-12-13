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

use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use Common\Logic\BrandLogic;
/**
 * 品牌控制器
 * @author Admin
 */
class BrandController 
{
    use IsLoginTrait;
    
    use InitControllerTrait;

    public function __construct(array $args =[])
    {  
    	$this->args = $args;
    	
        $this->init();

        $this->isNewLoginAdmin();

        $this->logic = new BrandLogic($args);
    }

    /**
     * 获取品牌列表
     */
    public function getBrandList()
    {
        $this->objController->ajaxReturnData($this->logic->getResult());
    }
}