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
namespace Admin\Controller;

use Common\TraitClass\IsLoginTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\EditStatusTrait;
use Common\Logic\StoreMsgTplLogic;
use Common\Tool\Extend\CheckParam;

/**
 * 消息模板控制器
 * @author 王强
 */
class TemplateController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use EditStatusTrait;
    
    /**
     * 模板状态参数
     * @var array
     */
    private $tmpStatus = [ 
        [
            'name' => '启用',
            'value' => 1,
            'fork' => 'open',
            'check'=> 'checked="checked"'
        ],
        [
            'name' => '关闭',
            'value' => 0,
            'fork' => 'close',
            'check' => ''
        ]
    ];
    
    //不需要检测的键
    private $noCheck = [];
    
    public function __construct($args = null)
    {
        $this->isNewLoginAdmin();
         
        $this->args = $args;
    
        $this->logic = new StoreMsgTplLogic($args);
    
        $this->init();
    }
    
    /**
     * 模板列表
     */
    public function tmpList()
    {
        $comment = $this->logic->getComment();
        
        $this->objController->assign('tableComment', $this->logic->tableComment());
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->display();
    }
    
    /**
     * ajax
     */
    public function ajaxGetTmpList()
    {
        $data = $this->logic->getDataList();
        
        $imageType = C('image_type');
        
        $this->objController->assign('data', $data);
        
        $comment = $this->logic->getComment();
        
        $this->objController->assign('comment', $comment);
        
        $this->objController->assign('imageType', $imageType);
        
        $this->objController->assign('jsonImageType', json_encode($imageType));
        
        $this->objController->display();
    }
    
    /**
     * 公共方法处理状态
     */
    private function statusOperator()
    {
        $this->check();
        
        $status = $this->logic->save();
       
        $this->objController->updateClient($status, '修改状态');
    }
    
    /**
     * 站内信默认开关
     */
    public function siteMsgStatus()
    {   
        $this->validate = ['id', 'smt_message_switch'];
        
        $this->statusOperator();
    }
    
    /**
     * 站内信强制接收
     */
    public function stationLetterMandatoryReception()
    {
        $this->validate = ['id', 'smt_message_forced'];
    
        $this->statusOperator();
    }
    
    /**
     * 短信默认开关
     */
    public function smsIsOpen()
    {
        $this->validate = ['id', 'smt_short_switch'];
    
        $this->statusOperator();
    }
    
    /**
     * 短信强制接收
     */
    public function smsForcedReception()
    {

        $this->validate = ['id', 'smt_short_forced'];
        
        $this->statusOperator();
    }
    
    /**
     * 邮件默认开
     */
    public function isTheMailOpen()
    {
        $this->validate = ['id', 'smt_mail_switch'];
        
        $this->statusOperator();
    }
    
    /**
     * 邮件强制接收
     */
    public function mailMandatoryReception()
    {
        $this->validate = ['id', 'smt_mail_forced'];
        
        $this->statusOperator();
    }
    
    /**
     * 编辑
     */
    public function editTempData()
    {
        $this->objController->errorArrayNotice($this->args);
        
        $commentByAllColum = $this->logic->getModelObj()->getComment();
        
        $data = $this->logic->getFindOne();
        
        $this->satausSwitch();
        
        $this->objController->assign('data', $data);
        
        $this->objController->assign('tableComment', $this->logic->tableComment());
        
        $this->objController->assign('comment', $commentByAllColum);
        
        $this->objController->display();
  }
  
  /**
   * 状态开关
   */
  private function satausSwitch()
  {
      $smtStatus = $this->editStutas('smt_0','smt_1');
      
      $smsForce = $this->editStutas('force_0', "force_1");
      
      $mailStatus = $this->editStutas('mail_status_0', 'mail_status_1');
      
      $siteSMSForce = $this->editStutas('force_s_o', 'force_s_c');
      
      $mailForce = $this->editStutas('mail_force_0', 'mail_force_1');
      
      $this->objController->assign('smtStatus', $smtStatus);
      
      $this->objController->assign('siteSMSForce', $siteSMSForce);
      
      $this->objController->assign('smsForce', $smsForce);
      
      $this->objController->assign('mailStatus', $mailStatus);
      
      $this->objController->assign('mailForce', $mailForce);
      
      $this->objController->assign('tmpStatus', $this->tmpStatus);
  }
  
  /**
   * 修改label名称
   */
  private function editStutas($labelForOne, $labelForTwo)
  {
      $status = $this->tmpStatus;
      
      $status[0]['fork'] = $labelForOne;
      
      $status[1]['fork'] = $labelForTwo;
      return $status;
  }
  
  /**
   * 修改消息模板
   */
  public function updateTmpl()
  {
      $this->noCheck = ['smt_code'];
      
      $this->checkTmplParam();
      
      $status = $this->logic->save();
      
      $this->objController->promptPjax($status !== false, $this->logic->getErrorMessage());
      
      $this->objController->ajaxReturnData(['url' => U('tmpList')], 1, '更新成功');
      
  }
  
  /**
   * 检测模板参数
   */
  private function checkTmplParam()
  {
     $obj =  new CheckParam($this->args, $this->noCheck);
     
     $must = $obj->keyExits($this->logic->getTableColum());
     
     $isNumeric = $obj->isNumeric($this->logic->getCheckNumericKeys());
     
     $this->objController->promptPjax($must && $isNumeric, '数据有误');
  }
  
  /**
   * 修改消息模板
   */
  public function addTmpl()
  {
      $comment = $this->logic->getModelObj()->getComment();
      
      $this->satausSwitch();
      
      $this->objController->assign('tableComment', $this->logic->tableComment());
      
      $this->objController->assign('comment', $comment);
     
      $this->objController->display();
  }
  
  /**
   * 添加模板
   */
  public function addTmplData()
  {
      $this->checkTmplParam();
      
      $status = $this->logic->add();
      
      $this->objController->promptPjax($status, $this->logic->getErrorMessage());
      
      $this->objController->ajaxReturnData(['url' => U('tmpList')], 1, '更新成功');
  }
}