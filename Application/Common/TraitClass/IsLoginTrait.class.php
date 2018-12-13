<?php
namespace Common\TraitClass;


trait IsLoginTrait {
    
    //session存在时，不需要验证的权限
    private $notCheck = array('Index/index','Index/main',
            'Index/clear_cache','Index/edit_pwd','Index/logout');
        
    
    /**
     * 是否登录
     */
    private function isLogin()
    {
        if (empty($_SESSION['user_id'])) {
            $this->controllerObj->ajaxReturnData('', 10001, '请登录');
        }
    }
    
    /**
     * 判断后台用户[兼容老的]
     */
    public function isLoginAdmin()
    {
        //session不存在时，不允许直接访问
        if(!session('aid')){
            $this->objController->ajaxReturnData(['url' => $_SERVER['SERVER_NAME'] . U('Public/login')],0,'请先进行登录');
        }
        
        //当前操作的请求                 模块名/方法名
        if(in_array(CONTROLLER_NAME.'/'.ACTION_NAME, $this->notCheck)){
            return ;
        }
    }
    
    /**
     * 判断后台用户是否登录
     */
    public function isNewLoginAdmin()
    {
        //session不存在时，不允许直接访问

        if(empty($_SESSION['store_id'])){
            $this->objController->ajaxReturnData('', 10001,'请先进行登录');
        }
    }
}