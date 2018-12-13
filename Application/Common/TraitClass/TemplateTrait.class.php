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
namespace Common\TraitClass;

use Common\Tool\Tool;
use Admin\Model\FreightsModel;

/**
 * 模板数据
 * @author 王强
 */
trait TemplateTrait
{
    /**
     * 获取运费模板数据
     */
    public function getTemplateDataByMode()
    {
        if (empty($this->data)) {
            return array();
        }
       
        $idString = Tool::characterJoin($this->data, $this->splitKey);
     
        if (empty($idString)) {
            return $this->data;
        }
    
        $field = $this->getSelectField();
        
        $temp = $this->modelObj->where(FreightsModel::$id_d . ' in (' . $idString . ')')->getField($field);
     
        if (empty($temp)) {
            return  $this->data;
        }
        
        $data = $this->data;
        
        foreach ($data as $key => & $value) {
            if (array_key_exists($value[$this->splitKey], $temp)) {
                $value[$this->splitKey] = $temp[$value[$this->splitKey]];
            }
        }
        return $data;
    }
    /**
     * 获取要查询的字段
     * @return string;
     */
    abstract protected function getSelectField();
}