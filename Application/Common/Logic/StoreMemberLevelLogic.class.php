<?php
namespace Common\Logic;

use Common\Model\StoreMemberLevelModel;

/**
 * 店铺会员等级
 * @author 王强
 *
 */
class StoreMemberLevelLogic extends AbstractGetDataLogic
{
    /**
     * 构造方法
     * @param unknown $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        
        $this->modelObj = new StoreMemberLevelModel();
        
        $this->splitKey = $split;
        
        $this->covertKey = StoreMemberLevelModel::$levelId_d;
    }
    
    /**
     * 获取店品牌数据
     */
    public function getResult()
    {
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return StoreMemberLevelModel::class;
    }
    
    /**
     * 获取会员等级相关字段
     * @return unknown
     */
    public function getSplitKeyByLevelId()
    {
        return StoreMemberLevelModel::$levelId_d;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum() :array
     */
    protected function getTableColum() :array
    {
        return [
            StoreMemberLevelModel::$id_d,
            StoreMemberLevelModel::$levelId_d,
            StoreMemberLevelModel::$discount_d,
            StoreMemberLevelModel::$conditionMoney_d,
            StoreMemberLevelModel::$conditionNum_d,
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice() :array
     */
    public function getMessageNotice() :array
    {
        $comment = $this->modelObj->getComment();
        
        return [
            StoreMemberLevelModel::$id_d => [
                'number' => $comment[StoreMemberLevelModel::$id_d].' 必须是数字',
            ],
            StoreMemberLevelModel::$levelId_d => [
                'number' => $comment[StoreMemberLevelModel::$levelId_d].' 必须是数字',
            ],
            StoreMemberLevelModel::$discount_d => [
                'number' => $comment[StoreMemberLevelModel::$discount_d].' 必须是数字',
            ],
            StoreMemberLevelModel::$conditionMoney_d => [
                'number' => $comment[StoreMemberLevelModel::$conditionMoney_d].' 必须是数字',
            ],
        ];
    }
    
    /**
     * 保存时处理参数
     * @return array
     */
    protected function getParseResultBySave() :array
    {
         $data = $this->data;
         
         if (empty($data)) {
             return [];
         }
         
         $data[StoreMemberLevelModel::$storeId_d] = $_SESSION['store_id'];
         $data[StoreMemberLevelModel::$updateTime_d] = time();
         return $data;
    }
    
    /**
     * 添加时处理参数
     * @return array
     */
    protected function getParseResultByAdd() :array
    {
        $data = $this->data;
        
        if (empty($data)) {
            return [];
        }
        
        $time = time();
        
        $data[StoreMemberLevelModel::$storeId_d] = $_SESSION['store_id'];
        $data[StoreMemberLevelModel::$updateTime_d] = $time;
        $data[StoreMemberLevelModel::$createTime_d] = $time;
        return $data;
    }
}