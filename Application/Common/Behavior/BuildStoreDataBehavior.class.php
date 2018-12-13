<?php
namespace Common\Behavior;

use Common\Logic\StoreGradeLogic;
use Common\Tool\Tool;
use Common\Logic\StoreClassLogic;
use Common\Logic\StoreSellerLogic;

class BuildStoreDataBehavior
{
    /**
     * 参数数组
     * @var array
     */
    private $args = [];
    
    private $obj ;
    
    public function __construct(array $args = [])
    {
        $this->args = $args[0];
        
        $this->obj = $args[1];
    }
    
    public function buildStore()
    {
        //等级数据
        $modelObj = $this->obj->getLogic()->getModelObj();
        
        $splitGradeKey = $modelObj::$gradeId_d;
        
        $logic = new StoreGradeLogic($this->args, $splitGradeKey);
        
        Tool::connect('parseString');
        
        $data = $logic->getGradeData();
        
        $modelClass = $logic->getModelClassName();
        
        $spliClassKey = $modelObj::$classId_d;
        
        $imageType = C('image_type');
        
        //分类数据
        $classLogic = new StoreClassLogic($data, $spliClassKey);
        
        $data = $classLogic->getStoreClassData();
        
        //
        $sellerLogic = new StoreSellerLogic($data, $modelObj::$id_d);
        
        $data = $sellerLogic->getSellerDataByStore();
        
        $this->obj->getObjController()->assign('sellerModel', $sellerLogic->getModelClassName());
        
        $this->obj->getObjController()->assign('classModel', $classLogic->getModelClassName());
        
        $this->obj->getObjController()->assign('gradeModel', $modelClass);
        
        $this->obj->getObjController()->assign('imageType', $imageType);
        
        $this->obj->getObjController()->assign('jsonImageType', json_encode($imageType));
        
        $this->obj->getObjController()->assign('shopType', C('shop_type'));
        
        $this->obj->getObjController()->assign('storeStatus', C('store_status'));
        
        $this->obj->getObjController()->assign('isOwn', C('is_own'));
        
        return $data;
    }
}