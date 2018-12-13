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

use Common\Model\StoreMsgTplModel;

/**
 * 消息模板逻辑处理
 * @author Administrator
 *
 */
class StoreMsgTplLogic extends AbstractGetDataLogic
{
    /**
     * 消息键
     * @var string
     */
    private $tplKey;
    /**
     * 构造方法
     * @param array $data
     * @param string $tplKey
     */
    public function __construct(array $data = [], $tplKey = '')
    {
        $this->data = $data;
        
        $this->tplKey = $tplKey;
        
        $this->modelObj = new StoreMsgTplModel();
    }
    
    /**
     * 获取店铺消息数据
     */
    public function getResult()
    {
        $data = S($this->tplKey.'_bs');

        if (empty($data)) {
            $data = $this->modelObj->where(StoreMsgTplModel::$smtCode_d.'="%s"', $this->tplKey)->find();
        } else {
            return $data;
        }
        
        if (empty($data)) {
            return [];
        }
        
        S($this->tplKey, $data, 600);
        
        return $data;
        
    }
    
    /**
     * 获取模型类名
     */
    public function getModelClassName()
    {
        return StoreMsgTplModel::class;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::hideenComment()
     */
    public function hideenComment()
    {
        return [
            StoreMsgTplModel::$smtMail_content_d
        ];
    }
    
    /**
     * save
     */
    public function save()
    {
        $status = false;
        try {
            $status = $this->modelObj->save($this->data);
        } catch (\Exception $e) {
            $this->errorMessage = '已存在该模板编码';
        }
        
        return $status;
    }
    
    /**
     * add
     */
    public function add()
    {
        $status = false;
       
        try {
            $status = $this->modelObj->add($this->data);
        } catch (\Exception $e) {
            throw $e;
            $this->errorMessage = '已存在该模板编码';
        }
        
        return $status;
    }
    
    
    /**
     * 检测是为数字的键
     * @return array
     */
    public function getCheckNumericKeys()
    {
        return [
            StoreMsgTplModel::$smtMessage_switch_d,
            StoreMsgTplModel::$smtMessage_forced_d,
            StoreMsgTplModel::$smtShort_forced_d,
            StoreMsgTplModel::$smtMail_switch_d,
            StoreMsgTplModel::$smtMail_forced_d,
            StoreMsgTplModel::$id_d
        ];
    }
    
    /**
     * 重写方法
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getFindOne()
     */
    public function getFindOne()
    {
        $data = parent::getFindOne();
        
        $data[StoreMsgTplModel::$smtMail_content_d] = htmlspecialchars_decode($data[StoreMsgTplModel::$smtMail_content_d]);
        
        return $data;
        
    }
}