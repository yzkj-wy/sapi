<?php
/**
 * Created by PhpStorm.
 * User: qingCai
 * Date: 2018/1/22 0022
 * Time: 11:17
 */
namespace Common\Logic;
use Common\Model\StoreNavModel;
class StoreNavLogic extends AbstractGetDataLogic
{
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;

        $this->modelObj = new StoreNavModel();
    }

    public function getList()
    {
        return $this->getDataList();

    }

    public function getResult()
    {

    }
    /**
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return StoreNavModel::class;
    }
    
    protected function likeSerachArray() :array
    {
        return [
            StoreNavModel::$name_d
        ];
    }
}