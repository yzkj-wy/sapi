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

use Admin\Model\ArticleCategoryModel;


/**
 * 相册逻辑处理
 * @author 王强
 * @version 1.0
 */
class ArticleCategoryLogic extends AbstractGetDataLogic
{

    /**
     * 架构方法
     */
    public function __construct($data)
    {
        $this->data = $data;

        $this->modelObj = ArticleCategoryModel::getInitnation();
    }

    /**
     * 返回模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return ArticleCategoryModel::class;
    }

    /**
     * 获取结果
     */
    public function getResult()
    {

    }

    /**
     * 获取所有分类列表
     */
    public function getAllArticleCategory()
    {
        $data = $this->modelObj->field('id,name')
            -> where(['status' => 1])
            -> select();
        return $data;
    }
}

