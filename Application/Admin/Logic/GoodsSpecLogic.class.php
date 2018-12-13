<?php
namespace Admin\Logic;

use Think\Cache;
use Admin\Model\GoodsSpecModel;
use Admin\Model\GoodsTypeModel;
use Common\Logic\AbstractGetDataLogic;

/**
 * 商品规格逻辑处理
 */
class GoodsSpecLogic extends AbstractGetDataLogic
{

    /**
     * 构造方法
     * 
     * @param array $data            
     * @param string $split            
     */
    public function __construct(array $data, $split = null)
    {
        $this->data = $data;
        
        $this->splitKey = $split;
        
        $this->modelObj = new GoodsSpecModel();
        
        $this->covertKey = GoodsSpecModel::$name_d;
    }

    /**
     * 获取规格组
     * 
     * @return mixed|NULL|unknown|string[]|unknown[]|object|mixed|NULL|unknown|string[]|unknown[]
     */
    public function getDataBySpecial()
    {
        $cache = Cache::getInstance('', [
            'expire' => 60
        ]);
        
        $key = 'session_' . $_SESSION['store_id'] . '_sp';
        
        $data = $cache->get($key);
        
        if (empty($data)) {
            $data = $this->modelObj->getField(GoodsSpecModel::$id_d . ',' . GoodsSpecModel::$name_d);
        } else {
            return $data;
        }
        
        $cache->set($key, $data);
        
        return $data;
    }
    
    /**
     * 根据 规格组编号 获取 数据
     * @return array
     */
    public function getDataBySpecItem()
    {
        $field = [
            GoodsSpecModel::$id_d,
            GoodsSpecModel::$name_d
        ];
        
        return $this->getDataByOtherModel($field, GoodsSpecModel::$id_d);
        
    }
    
    
    /**
     * 根据分类获取规格
     * @return array
     */
    public function getDataBySpec()
    {
        $this->searchTemporary = [
            GoodsSpecModel::$classThree_d => $this->data['id']
        ];
        
        return $this->getNoPageList();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getResult()
     */
    public function getResult()
    {}

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
     */
    public function getModelClassName()
    {
        return GoodsSpecModel::class;
    }

    /**
     * 获取规格详细信息
     * @param int $id            
     */
    public function getGoodsSpecInfo()
    {
        $cache = Cache::getInstance('', [
            'expire' => 60
        ]);
        
        $key = 'spec_' . $this->data['id'] . '_data';
        
        $data = $cache->get($key);
        
        if (empty($data)) {
            $field = GoodsSpecModel::$id_d . ',' . GoodsSpecModel::$name_d . ',' . GoodsSpecModel::$sort_d;
            
            $spec = $this->modelObj->field($field)
                ->where(GoodsSpecModel::$classThree_d . '=:id and ' . GoodsSpecModel::$status_d . ' = 1')
                ->bind([
                ':id' => $this->data['id']
            ])
                ->getField($field);
        } else {
            return $spec;
        }
        
        $cache->set($key, $data);
        
        return $spec;
    }

    /**
     * 获取主键
     */
    public function getSplitKeyById()
    {
        return GoodsSpecModel::$id_d;
    }

    /**
     * 添加规格和规格项
     * 
     * @param array $newdata
     *            接收前台的数据
     * @return bool
     */
    public function addSpec()
    {
        try {
            // 验证数据
            if (empty($this->data) || $this->isAvailableData() === false) {
                return [];
            }
            $this->data['store_id'] = session('store_id');
            $this->modelObj->startTrans();
            // 保存商品规格表
            if (($spec_id = $this->modelObj->add($this->data)) === false) {
                $this->errorMessage = '规格基本信息保存失败';
                $this->modelObj->rollback();
                return false;
            }
            // 保存到规格项表
            $spec_item_model = M("GoodsSpecItem");
            $post_items = explode("\\" . PHP_EOL, $this->data['items']);
            $post_items = $this->modelObj->filterSpecChar($post_items);
            $post_items = array_unique($post_items);
            $arr = [];
            foreach ($post_items as $key => $val) {
                $arr[] = [
                    'spec_id' => $spec_id,
                    'item' => $val
                ];
            }
            if (! empty($arr)) {
                if ($spec_item_model->addAll($arr) === false) {
                    $this->errorMessage = "保存规格选项失败";
                    $this->modelObj->rollback();
                    return false;
                }
            }
            return $this->modelObj->commit();
        } catch (\Exception $e) {
            $this->errorMessage = '该规格已存在,请勿重复添加';
            return [];
        }
    }

    /**
     * 修改商品规格和商品规格选项
     * 
     * @param array $newdata
     *            前端传过来的数据
     * @return bool
     */
    public function saveSpec()
    {
        try {
            
            // 验证数据
            if (empty($this->data) || $this->isAvailableData() === false) {
                return [];
            }
            
            $this->modelObj->startTrans();
            // 修改商品规格表
            if ($this->modelObj->save($this->data) === false) {
                $this->errorMessage = "修改商品规格失败";
                $this->modelObj->rollback();
                return false;
            }
            
            // 修改到规格选项表
            $goods_spec_item_model = M("GoodsSpecItem");
            $spec_id = $this->data['id'];
            $post_items = explode(PHP_EOL, $this->data['items']);
            $post_items = array_unique($post_items);
            
            // 前端传过来的规格选项
            $post_items = $this->modelObj->filterSpecChar($post_items);
            // 数据库中存在的规格选项
            $already_rs = $goods_spec_item_model->where([
                'spec_id' => $spec_id
            ])->getField("id,item");
            foreach ($post_items as $k => $v) {
                if (in_array($v, $already_rs)) {
                    $alr_exist[] = $v;
                } else {
                    $new_add[] = $v;
                }
            }
            // 如果为空时，删除数据库中存在的spec_id
            if (empty($post_items)) {
                if (($goods_spec_item_model->where([
                    'spec_id' => $spec_id
                ])->delete()) === false) {
                    $this->errorMessage = "删除多余规格项失败";
                    $this->modelObj->rollback();
                    return false;
                }
            }
            // 如果存在的数据，数据库不表，多余的删除
            if ($alr_exist) {
                $arr['spec_id'] = $spec_id;
                $arr['item'] = [
                    [
                        'not in',
                        $alr_exist
                    ]
                ];
                if (($goods_spec_item_model->where($arr)->delete()) === false) {
                    $this->errorMessage = "删除多余规格项失败";
                    $this->modelObj->rollback();
                    return false;
                }
            }
        } catch (\Exception $e) {
            
            $this->errorMessage = '该规格名称已存在';
            return [];
        }
        
        if ($new_add) {
            foreach ($new_add as $val1) {
                $arr1[] = [
                    'spec_id' => $spec_id,
                    'item' => $val1
                ];
            }
        }
        
        // 新的规格选项，添加到数据库
        if (! empty($arr1)) {
            if ($goods_spec_item_model->addAll($arr1) === false) {
                $this->errorMessage = "保存规格选项失败";
                $this->modelObj->rollback();
                return false;
            }
        }
        return $this->modelObj->commit();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getMessageNotice()
     */
    public function getMessageNotice() :array
    {
        // TODO
        $comment = $this->modelObj->getComment();
        
        $message = [
            GoodsSpecModel::$name_d => [
                'required' => '请输入' . $comment[GoodsSpecModel::$name_d],
                'specialCharFilter' => $comment[GoodsSpecModel::$name_d] . '不能输入特殊字符'
            ],
            GoodsSpecModel::$typeId_d => [
                'required' => '请输入' . $comment[GoodsSpecModel::$typeId_d]
            ],
            GoodsSpecModel::$sort_d => [
                'required' => '请输入' . $comment[GoodsSpecModel::$sort_d],
                'number' => $comment[GoodsSpecModel::$sort_d] . '必须是数字'
            ],
            'items' => [
                'required' => '请输入规格项'
            ]
        ];
        return $message;
    }

    /**
     * 获取验证规则
     * 
     * @return boolean[][]
     */
    public function getCheckValidate() :array
    {
        $validate = [
            GoodsSpecModel::$name_d => [
                'required' => true,
                'specialCharFilter' => true
            ],
            GoodsSpecModel::$typeId_d => [
                'required' => true
            ],
            
            GoodsSpecModel::$sort_d => [
                'required' => true,
                'number' => true
            ],
            'items' => [
                'required' => true
            ]
        ];
        return $validate;
    }

    /**
     * 验证数据有效性
     */
    private function isAvailableData()
    {
        // 验证状态显示
        if (! empty($this->data['status'])) {
            if ($this->data['status'] != 0 && $this->data['status'] != 1) {
                $this->errorMessage = '请选择正确的显示状态';
                return false;
            }
        }
        
        // 验证类型是否存在
        $type = GoodsTypeModel::getInitnation();
        if ($type->isExistType($this->data['type_id']) === false) {
            $this->errorMessage = '不存在的类型,请先添加';
            return false;
        }
        
        return true;
    }

    /**
     * 删除商品规格表和删除商品规格项表
     * 
     * @param int $id            
     * @return bool
     */
    public function deleteSpec()
    {
        
        // 验证是否存在规格
        if ($this->modelObj->isExistSpec($this->data['id']) === false) {
            $this->errorMessage = '不存在的规格';
            return false;
        }
        
        $this->modelObj->startTrans();
        // 删除商品规格表
        if ($this->modelObj->where([
            'store_id' => session('store_id'),
            'id' => $this->data['id']
        ])->delete() === false) {
            $this->modelObj->rollback();
            $this->errorMessage = '删除商品规格失败';
            return false;
        }
        // 删除商品规格项表
        $result = M("GoodsSpecItem")->where([
            'spec_id' => $this->data['id']
        ])->delete();
        if ($result === false) {
            $this->modelObj->rollback();
            $this->errorMessage = '删除商品规格项失败';
            return false;
        }
        return $this->modelObj->commit();
    }

    /**
     * 改变显示状态
     */
    public function changeStatus()
    {
        // 验证状态显示
        if (! empty($this->data['status'])) {
            if ($this->data['status'] != 0 && $this->data['status'] != 1) {
                $this->errorMessage = '请选择正确的显示状态';
                return false;
            }
        }
        
        // 验证是否存在规格
        if ($this->modelObj->isExistSpec($this->data['id']) === false) {
            $this->errorMessage = '不存在的规格';
            return false;
        }
        
        // 修改商品规格表
        if ($this->modelObj->save($this->data) === false) {
            $this->errorMessage = "修改规格状态失败";
            return false;
        }
        
        return true;
    }

    /**
     * 改变状态时的验证
     */
    public function getChangeMessageNotice() :array
    {
        $message = [
            GoodsSpecModel::$id_d => [
                'required' => '请选择要修改的规格编号',
                'number' => '规格编号必须为数字'
            ],
            GoodsSpecModel::$status_d => [
                'required' => '请输入规格显示状态',
                'number' => '显示状态必须是数字'
            ]
        ];
        return $message;
    }

    /**
     * 规格组合处理
     * 
     * @return array
     */
    public function getBuildBySpecialMessage()
    {
        return [
            'spec' => [
                'required' => '规格不能为空'
            ]
        ];
    }

    /**
     * 验证商品分类
     * 
     * @return []
     */
    public function getMessageByGoodsClass()
    {
        return [
            GoodsSpecModel::$classThree_d => [
                'number' => '商品三级分类必须是数字'
            ],
            GoodsSpecModel::$classTwo_d => [
                'number' => '商品二级分类必须是数字'
            ],
            GoodsSpecModel::$classOne_d => [
                'number' => '商品二级分类必须是数字'
            ]
        
        ];
    }

    /**
     * 根据商品分类获取规格组
     * @return array
     */
    public function getSpecGroupByGoodsClass()
    {
        $cache = Cache::getInstance('', [
            'expire' => 30
        ]);
        
        $key = 'asd' . $_SESSION['store_id'] . 'kjk'.'_d'.$this->data['goods_id'].'_ds'.$this->data[GoodsSpecModel::$classThree_d];
        
        $data = $cache->get($key);
        
        if (!empty($data)) {
            
            return $data;
        }
        
        $this->searchTemporary = [
        	GoodsSpecModel::$classThree_d => $this->data[GoodsSpecModel::$classThree_d]
        ];
        
        $data = $this->getNoPageList();
        
        if (empty($data)) {
            return [];
        }
        
        $cache->set($key, $data);
        
        return $data;
    }

    /**
     * 获取缓存key
     * 
     * @return string
     */
    protected function getCacheKey():string
    {
        if (empty($_SESSION['store_id'])) {
            throw new \Exception('系统异常');
        }
        
        $key = 'special_' . $_SESSION['store_id'] . '_data';
        
        return $key;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Common\Logic\AbstractGetDataLogic::getTableColum()
     */
    protected function getTableColum() :array
    {
        return [
            GoodsSpecModel::$id_d,
            GoodsSpecModel::$name_d,
            GoodsSpecModel::$classOne_d,
            GoodsSpecModel::$classTwo_d,
            GoodsSpecModel::$classThree_d
        ];
    }

    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Logic\AbstractGetDataLogic::likeSerachArray()
     */
    protected function likeSerachArray() :array
    {
        return [
          GoodsSpecModel::$name_d  
        ];
    }
    
    /**
     * 根据商品规格项获取分类编号
     * @return array
     */
    public function getClassIdBySpecItem()
    {
        if (empty($this->data[$this->splitKey])) {
            return $this->data;
        }
        
        $field = [
            GoodsSpecModel::$classOne_d,
            GoodsSpecModel::$classTwo_d,
            GoodsSpecModel::$classThree_d
        ];
        
        $data = $this->modelObj
            ->field($field)
            ->where(GoodsSpecModel::$id_d.' = :id')
            ->bind([':id' => $this->data[$this->splitKey]])
            ->find();
        
        return $data;
    }
    
    /**
     * 商品编辑 规格验证
     * @return string[][]
     */
    public function getMessageBySpec()
    {
        return [
            GoodsSpecModel::$classThree_d => [
                'number' => '参数必须是数字'
            ],
            'goods_id'   =>[
                'number' => 'goods_id必须是数字'
            ],
        ];
    }
}