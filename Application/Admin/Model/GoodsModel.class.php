<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------
namespace Admin\Model;

use Common\Tool\Tool;
use Common\Model\BaseModel;
use Common\TraitClass\callBackClass;
use Common\TraitClass\MethodTrait;
use Common\Tool\Extend\CombineArray;

class GoodsModel extends BaseModel
{
    use callBackClass;
    
    use MethodTrait;

    private static $obj;

    protected $selectColum;

	public static $id_d;	//主键编号

	public static $brandId_d;	//品牌编号

	public static $title_d;	//商品标题

	public static $priceMarket_d;	//市场价

	public static $priceMember_d;	//会员价

	public static $stock_d;	//库存

	public static $selling_d;	//是否是热销   0 不是   1 是

	public static $shelves_d;	//0下架，1表示选择上架

	public static $classId_d;	//商品分类ID

	public static $recommend_d;	//1推荐 0不推荐

	public static $code_d;	//商品货号

	public static $top_d;	//顶部推荐

	public static $seasonHot_d;	//当季热卖

	public static $description_d;	//商品简介

	public static $updateTime_d;	//最后一次编辑时间

	public static $createTime_d;	//创建时间

	public static $goodsType_d;	//商品类型

	public static $sort_d;	//排序

	public static $pId_d;	//父级产品 SPU

	public static $status_d;	//0没有活动，1尾货清仓，2，最新促销，3积分商城,4打印耗材,5优惠套餐

	public static $commentMember_d;	//评论次数

	public static $salesSum_d;	//商品销量

	public static $attrType_d;	//商品属性编号【为goods_type表中数据】

	public static $extend_d;	//扩展分类

	public static $advanceDate_d;	//预售日期

	public static $weight_d;	//重量

	public static $storeId_d;	//店铺【编号】

	public static $type_d;	//是否是店铺商品

	public static $approvalStatus_d;	//审核状态【0未审核， 1审核通过， 2审核失败】
    
	public static $classTwo_d;         // 二级分类
	
	public static $classThree_d;       // 三级分类

	public static $expressId_d;	//运费模板编号

    public static $countryvar_d;  //原产国海关代码
    public static $itemnovar_d;  //商品货号
    public static $entgoodsnovar_d;  //企业商品货号(海关）
    public static $goodsinfovar_d;  //主要商品描述
    public static $unitvar_d;  //计量单位代码
    public static $notevar_d;  //促销活动，商品单价偏离市场价格的
    public static $wraptypevar_d;  //包装种类(海关)
    public static $note2var_d;  //包装种类(国检）
    public static $inspProdCbecCodevar_d;  //商品在国检备案的编号
    public static $inspProdSpecsvar_d;  //商品规格型号（国检）
    public static $netwt_d;  //净重
    public static $unit1_d;  //法定第一计量单位
    public static $unit2_d;  //法定第二计量单位
    public static $qty1_d;  //法定第一数量
    public static $qty2_d;  //法定第二数量
    public static $taxTotal_d;  //税费
    public static $taxFcy_d;  //代扣税款

    public static function getInitnation()
    {
        $name = __CLASS__;
        return static::$obj = ! (static::$obj instanceof $name) ? new static() : static::$obj;
    }

    /**
     * 重写父类方法
     */
    protected function _before_insert(& $data, $options)
    {
        $data[static::$createTime_d] = time();
        $data[static::$updateTime_d] = time();
        
        $data[static::$sort_d] = 50;
        $data[static::$storeId_d] = session('store_id');
        return $data;
    }

    /**
     * 重写父类方法
     */
    protected function _before_update(& $data, $options)
    {

        $data[static::$updateTime_d] = time();
        
        return $data;
    }

    /**
     * add
     */
    public function add($data = '', $options = array(), $replace = false)
    {
        if (empty($data[static::$classId_d])) {
            
            return false;
        }
        
        // $flag = static::flag($data, static::$classId_d);
        
        // // $data[static::$classId_d] = $flag;
        
        // $data = $this->create($data);
        
        // 新加字段天数转为时间戳即为预售日期时间戳
        if ($data['stock'] == 0) {
            $data['advance_date'] = time() + $data['advance_date'] * 24 * 60 * 60;
        } else {
            $data['advance_date'] = $data['advance_date'] * 24 * 60 * 60;
        }
        return parent::add($data, $options, $replace);
    }

   
    /**
     * 求和,求出商品推荐类型的位运算值.
     * 
     * @param type $shelves            
     * @return int
     */
    protected function calcShelves($shelves)
    {
        if (isset($shelves)) {
            return array_sum($shelves);
        } else {
            return 0;
        }
    }

    /**
     * 根据订单信息 查询商品数据
     */
    public function getOrderInfo(array $data)
    {
        if (empty($data)) {
            return array();
        }
        $id = Tool::characterJoin($data);
       
        if (empty($id)) {
            return array();
        }
        
        $field = [
            self::$id_d,
            self::$title_d
        ];
        
        $dataArray = $this->field($field)
            ->where('id in (' . $id . ')')
            ->select();
        
        if (empty($dataArray)) {
            return array();
        }
        
        $obj = new CombineArray($dataArray, self::$id_d);
        
        $data = $obj->parseCombine($data, 'goods_id');
        return $data;
        
    }

    /**
     * 删除商品
     * 
     * @param int $id
     *            商品的id
     * @return bool
     */
    public function delGoods($id)
    {
        if (($id = intval($id)) === 0) {
            return false;
        }
        
        // 查找父级
        $pId = $this->where(static::$pId_d . '=%d', $id)->getField(static::$id_d . ',' . static::$pId_d);
        if (empty($pId)) {
            return $this->delete($id);
        }
        
        $this->startTrans();
        
        $idArray = array_keys($pId);
        
        $status = $this->where(static::$id_d . ' in (' . implode(',', array_keys($pId)) . ',' . $id . ')')->delete();
        
        if ($status === false) {
            $this->rollback();
            return false;
        }
        
        return $idArray;
    }


    public function getInfoGoods($id)
    {
        // 获取商品的基本信息
        $row = $this->find($id);
        // 由于在前端展示的时候,需要使用到2个状态,所以我们变成一个json对象
        $tmp = [];
        if ($row['shelves'] & 1) {
            $tmp[] = 1;
        }
        if ($row['shelves'] & 2) {
            $tmp[] = 2;
        }
        $row['shelves'] = json_encode($tmp);
        unset($tmp);
        
        return $row;
    }

    /**
     * 创造where 条件[122111423]
     * 
     * @param array $data
     *            筛选数据
     * @return array
     */
    public function bulidWhere(array $data)
    {
        if (empty($data) || ! is_array($data)) {
            return array();
        }
        
        $data = $this->create($data);
        
        $data = Tool::buildActive($data);
        
        if (! empty($data[static::$title_d])) {
            $data[static::$title_d] = array(
                'like',
                '%' . $data[static::$title_d] . '%'
            );
        }
        return $data;
    }

    /**
     * 更新商品属性
     */
    public function saveAttrType(array $post)
    {
        if (! $this->isEmpty($post)) {
            return false;
        }
        
        $this->startTrans();
        
        $goodsId = (int) $_POST['goods_id'];
        
        $status = $this->where(static::$pId_d . '= %d or ' . static::$id_d . '= %d', [
            $goodsId,
            $goodsId
        ])->save([
            static::$attrType_d => $_POST[static::$attrType_d]
        ]);
        return $this->traceStation($status);
    }


    /**
     * 根据订单信息 获取单挑数据
     */
    public function getReturnGoods($id)
    {
        if (($id = intval($id)) === 0) {
            return null;
        }
        
        $data = $this->field([
            static::$createTime_d,
            static::$updateTime_d
        ], true)
            ->where(static::$id_d . '=%d', $id)
            ->find();
        
        if (empty($data)) {
            return array();
        }
        
        return empty($data[$this->selectColum]) ? $data : $data[$this->selectColum];
    }

    

    /**
     * 修改商品活动状态
     */
    public function editStatus($goosId, $status = 0)
    {
        $status = $this->editPoopStatus($goosId);
        if (! $this->traceStation($status)) {
            return false;
        }
        
        $this->commit();
        
        return $status;
    }

    /**
     * 修改 促销状态
     * 
     * @param int $goosId            
     * @param int $status            
     */
    public function editPoopStatus($goosId, $status = 0)
    {
        $int = [
            $goosId,
            $status
        ];
        if (! $this->foreachDataTypeIsEmpty($int)) {
            $this->rollback();
            return false;
        }
        $status = $this->save([
            static::$id_d => $goosId,
            static::$status_d => $status
        ]);
        return $status;
    }

    /**
     * 根据促销条件获取匹配的商品
     * 
     * @param array $where
     *            条件
     * @return array
     */
    public function getPromotionGoodsByOption(array $where = array())
    {
        $funArray = C('pro_type');
        
        $fun = $funArray[$_GET['type']];
        
        $where = array_merge($where, $this->$fun());
        
        $where[self::$status_d] = 0; // 搜索的没有活动的产品
        
        $goodsData = $this->getDataByPage(array(
            'field' => array(
                self::$id_d,
                self::$title_d,
                self::$priceMember_d,
                self::$stock_d
            ),
            'where' => $where,
            'order' => self::$createTime_d . self::DESC . ',' . self::$updateTime_d . self::DESC
        ));
        
        return $goodsData;
    }

    /**
     * 价格 条件
     */
    protected function lt()
    {
        return [
            self::$priceMember_d => [
                'lt',
                $_GET['price']
            ]
        ];
    }

    /**
     * 固定金额出售[优惠金额]
     */
    protected function gt()
    {
        return [
            self::$priceMember_d => [
                'gt',
                $_GET['price']
            ]
        ];
    }

    /**
     * 打折
     */
    protected function undefined()
    {
        return [];
    }

    /**
     * 批量设置状态[事务]
     * 
     * @param array $data            
     * @param int $status            
     */
    public function setGoodsStatus(array $data)
    {
        if (! $this->isEmpty($data)) {
            $this->rollback();
            return false;
        }
        
        $key = [ // 要更新的键
            self::$status_d
        ];
        
        $temp = array();
        
        // 组装批量更新语句
        foreach ($data as $value) {
            $temp[$value][] = self::PROMOTION;
        }
        
        $isPuss = $this->saveStatus($temp, $key);
        
        return true;
    }

    /**
     * 辅助优惠促销方法
     * 
     * @param array $temp            
     * @param array $key            
     * @return boolean
     */
    private function saveStatus(array $temp, array $key)
    {
        $tableName = $this->getTableName();
        
        $sql = $this->buildUpdateSql($temp, $key, $tableName);
        $status = $this->execute($sql);
        
        if (! $this->traceStation($status)) {
            return false;
        }
        return true;
    }

    /**
     * 促销编辑时 商品状态处理
     * 
     * @param array $data            
     */
    public function validateGoodsStatus(array $data)
    {
        if (! $this->isEmpty($data)) {
            $this->rollback();
            return false;
        }
        $validateData = $this->compareDataByArray($data); // 验证是否更新商品状态
        
        if (empty($data)) {
            return true;
        }
        
        $temp = array();
        foreach ($validateData as $value) {
            if (! in_array($value, $data, true)) {
                $temp[$value][] = self::NOACTIVITY;
            } else {
                $temp[$value][] = self::PROMOTION;
            }
        }
        $key = [
            self::$status_d
        ];
        
        $status = $this->saveStatus($temp, $key);
        
        return $status;
    }

    /**
     * 获取分页数据
     */
    public function getPageByGoodsData(array $where)
    {
        $pageNumber = C('PAGE_SETTING.ADMIN_GOODS_LIST');
        $field = [
            'id',
            'title',
            'code',
            'class_id',
            'recommend',
            'price_market',
            'price_member',
            'sort',
            'stock',
            'shelves',
            'latest_promotion',
            'create_time'
        ];
        
        $data = $this->getDataByPage([
            'field' => $field,
            'where' => $where,
            'order' => self::$id_d . self::DESC . ', ' . self::$sort_d . self::DESC
        ], $pageNumber);
        
        return $data;
    }

    /**
     * 获取商品数量[只获取子类商品的，因为 父类商品不显示 只是起到纽带作用]
     */
    public function getGoodsTotal()
    {
        return $this->where(self::$pId_d . ' > 0')->count();
    }


    /**
     *
     * @return the $selectColum
     */
    public function getSelectColum()
    {
        return $this->selectColum;
    }

    /**
     *
     * @param field_type $selectColum            
     */
    public function setSelectColum($selectColum)
    {
        $this->selectColum = $selectColum;
    }

    /**
     *
     * @return the $idArray
     */
    public function getIdArray()
    {
        return $this->idArray;
    }

    /**
     *
     * @param multitype: $idArray            
     */
    public function setIdArray($idArray)
    {
        $this->idArray = $idArray;
    }
    
    /**
     * 最新促销
     *
     * @var int
     */
    const PROMOTION = 0x02;
    
    /**
     * 没有活动
     *
     * @var int
     */
    const NOACTIVITY = 0x00;
    //根据条件统计商品数量
    public function getGoodsNumByWhere($where){
        $count  = $this->where($where)->count();
        return $count;
    }
}