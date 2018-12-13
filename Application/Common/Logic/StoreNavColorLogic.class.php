<?php
/**
 * Created by PhpStorm.
 * User: qingCai
 * Date: 2018/1/22 0022
 * Time: 11:17
 */
namespace Common\Logic;
use Common\Model\StoreNavColorModel;
class StoreNavColorLogic extends AbstractGetDataLogic
{
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;

        $this->modelObj = new StoreNavColorModel();
    }

    public function getList()
    {
        $this->searchTemporary = [
            StoreNavColorModel::$storeId_d => $_SESSION['store_id']
        ];
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
        return StoreNavColorModel::class;
    }
}