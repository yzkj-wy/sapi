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
use Common\Model\StoreMsgModel;
use Common\Model\StoreMsgTplModel;

/**
 * 招商入驻银行信息
 *
 * @author Administrator
 */
class StoreMsgLogic extends AbstractGetDataLogic
{

    /**
     * 消息模板设置
     *
     * @var array
     */
    private $tmplConfig;

    /**
     * 备用
     *
     * @var string
     */
    private $className = '';

    /**
     * 初始化
     *
     * @param array $data            
     * @param array $tmplConfig            
     */
    public function __construct(array $data, array $tmplConfig = [])
    {
        $this->data = $data;
        
        $this->tmplConfig = $tmplConfig;
        
        $this->modelObj = new StoreMsgModel();
    }

    /**
     * 发送消息
     * {@inheritdoc}
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     * @return bool
     */
    public function getResult()
    {
    }
    
    
    /**
     * 获取模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return StoreMsgModel::class;
    }

    /**
     * 发送站内信
     */
    public function sendMessage()
    {
        $content = $this->tmplConfig[StoreMsgTplModel::$smtMessage_content_d];
      
        // 您的商品没有通过管理员审核，原因：“{$remark}”。SPU：{$common_id}， 名称：{$title}。
        $content = str_replace([
            '{$remark}',
            '{$common_id}',
            '{$title}'
        ], [
            $this->data['remark'],
            $this->data['id'],
            $this->data['title']
        ], $content);
        
        $add = [
            StoreMsgModel::$smtCode_d => 'goods_verify',
            StoreMsgModel::$smContent_d => $content,
            StoreMsgModel::$storeId_d => $this->data['store_id']
        ];
        
        $status = $this->modelObj->add($add);
        
        if (! $this->traceStation($status)) {
            return self::ADD_ERROR;
        }
        
        $this->modelObj->commit();
        
        return $status;
    }
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
     */
    protected function getTableColum() :array
    {
    	return [
    		StoreMsgModel::$id_d,
    		StoreMsgModel::$smContent_d,
    		StoreMsgModel::$smAddtime_d
    	];
    }
}