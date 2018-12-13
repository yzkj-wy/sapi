<?php

// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>\n
// +----------------------------------------------------------------------
namespace Admin\Model;

use Common\Model\BaseModel;

/**
 * 促销模型
 * 
 * @author 王强
 * @version 1.0.1
 */
class PromGoodsModel extends BaseModel {
	private static $obj;
	public static $id_d; // 活动ID
	public static $name_d; // 促销活动名称
	public static $type_d; // 促销类型
	public static $expression_d; // 优惠体现
	public static $description_d; // 活动描述
	public static $startTime_d; // 活动开始时间
	public static $endTime_d; // 活动结束时间
	public static $status_d; // 活动状态 1 开启 0 关闭
	public static $group_d; // 适用范围
	public static $promImg_d; // 活动宣传图片
	public static $createTime_d; // 创建时间
	public static $updateTime_d; // 更新时间
	public static $storeId_d; // 店铺id
	public static $full_d; // 满多少
	public static $panicBuy_d; // 抢购数量
	public static $limitBuy_d; // 限购数量
	public static function getInitnation() {
		$name = __CLASS__;
		return static::$obj = ! (static::$obj instanceof $name) ? new static () : static::$obj;
	}
	
	/**
	 * 满减(满赠)列表
	 */
	public function getFullCutList($data) {
		if (! empty ( $data ['activeName'] )) {
			$where ['name'] = $data ['activeName'];
		}
		if ($data ['activeStatus'] == 1) { // 开启
			$where ['status'] = 1;
		}
		if ($data ['activeStatus'] == 2) { // 关闭
			$where ['status'] = 0;
		}
		
		$where ['store_id'] = session ( 'store_id' );
		if ($data ['full']) {
			$where ['type'] = $this->getTypeByName ( '满赠' );
			$activeType = '满赠';
		} else {
			$where ['type'] = $this->getTypeByName ( '满减' );
			$activeType = '满减';
		}
		
		$re = $this->where ( $where )->field ( 'id,name,type,group,start_time,end_time' )->page ( $data ['page'], 10 )->select ();
		$total = $this->where ( $where )->field ( 'id' )->count ();
		foreach ( $re as $v ) {
			$v ['range'] = $this->getMemberLevel ( $v ['group'] );
			$v ['activeType'] = $activeType;
			if ($data ['full']) {
				$v ['gift'] = $this->getGiftByPromId ( $v ['id'] ) ['title'];
			}
			$v ['start_time'] = date ( 'Y-m-d', $v ['start_time'] );
			$v ['end_time'] = date ( 'Y-m-d', $v ['end_time'] );
			$r [] = $v;
		}
		$page = ceil ( $total / 10 );
		$data = array (
						'data' => $r,
						'page' => $page,
						'page_size' => 10 
		);
		return $data;
	}
	
	/**
	 * 满减删除
	 */
	public function delPromGoods($data) {
		$re = $this->where ( [ 
						'id' => $data ['id'] 
		] )->delete ();
		if ($re) {
			M ( 'promotion_goods' )->where ( [ 
							'prom_id' => $data ['id'] 
			] )->delete ();
			
			return true;
		}
	}
	
	/**
	 * 查询会员等级名称
	 */
	public function getMemberLevel($id) {
		return M ( 'store_level_by_platform' )->where ( [ 
						'id' => $id 
		] )->getField ( 'level_name' );
	}
	/**
	 * 查询活动类型
	 */
	public function getTypeByName($name) {
		return M ( 'promotion_type' )->where ( [ 
						'promation_name' => $name 
		] )->getField ( 'id' );
	}
	
	/**
	 * 更新商品状态
	 */
	public function updGoodStatus($goods_id, $status) {
		M ( 'goods' )->where ( [ 
						'id' => $goods_id 
		] )->data ( $status )->save ();
	}
	
	/**
	 * 查找赠品
	 */
	public function getGiftByPromId($prom_id) {
		return M ( 'promotion_goods as a' )->join ( 'db_goods as b on b.id=a.goods_id' )->where ( [ 
						'prom_id' => $prom_id 
		] )->field ( 'b.id,title,price_market,stock' )->find ();
	}
	
	/**
	 * 更新前操作
	 * 
	 * {@inheritdoc}
	 *
	 * @see \Think\Model::_before_update()
	 */
	protected function _before_update(&$data, $options) {
		$data [static::$updateTime_d] = time ();
		
		return $data;
	}
	
	/**
	 * 添加修改促销
	 * 
	 * @param array $data
	 *        	post数据
	 * @param string $fun
	 *        	方法名
	 * @return boolean
	 */
	public function addProGoods(array $data, $fun = 'add') {
		if (empty ( $data ) || ! is_array ( $data ) || ! method_exists ( $this, $fun )) {
			return false;
		}
		if ($data ['start_time'] > $data ['end_time']) {
			$this->error = '开始时间不能大于结束时间';
			return false;
		}
		// $data[static::$group_d] = implode(',', $data[static::$group_d]);
		$data [static::$startTime_d] = strtotime ( $data [static::$startTime_d] );
		$data ['status'] = 1;
		$data [static::$endTime_d] = strtotime ( $data [static::$endTime_d] );
		if ($data ['gift']) { // 满赠
			$data ['type'] = $this->getTypeByName ( '满赠' );
			
			if (!is_numeric ( $data ['type'] )) {
				return array (
								'status' => '0',
								'message' => '促销类型错误',
								'data' => '' 
				);
			}
			
			if ($fun == 'save') {
				
				$gift ['goods_id'] = $data ['goods_id'];
				$this->where ( [ 
								'id' => $data ['id'] 
				] )->save ( $data );
				
				$result = M ( 'promotion_goods' )->where ( [ 
								'prom_id' => $data ['id'] 
				] )->save ( $gift );
				
				if ($result) {
					return array (
									'status' => 1,
									'message' => '修改成功',
									'data' => '' 
					);
				} else {
					return array (
									'status' => 0,
									'message' => '修改失败',
									'data' => '' 
					);
				}
			} else {
				
				$gift ['goods_id'] = $data ['goods_id'];
				$gift ['start_time'] = strtotime ( $data ['start_time'] );
				$gift ['end_time'] = strtotime ( $data ['end_time'] );
				$data ['store_id'] = session ( 'store_id' );
				$re = $this->$fun ( $data );
				$gift ['prom_id'] = $re;
				$re = M ( 'promotion_goods' )->add ( $gift );
				if ($re) {
					return array (
									'status' => 1,
									'message' => '添加成功',
									'data' => '' 
					);
				} else {
					return array (
									'status' => 0,
									'message' => '添加失败',
									'data' => '' 
					);
				}
			}
		} else { // 满减
			$data ['type'] = $this->getTypeByName ( '满减' );
			if ($fun == 'save') {
				$gift ['goods_id'] = $data ['goods_id'];
				
				$result = $this->where ( [ 
								'id' => $data ['id'] 
				] )->save ( $data );
				M ( 'promotion_goods' )->where ( [ 
								'prom_id' => $data ['id'] 
				] )->save ( $gift );
				if ($result) {
					return array (
									'status' => 1,
									'message' => '修改成功',
									'data' => '' 
					);
				} else {
					return array (
									'status' => 0,
									'message' => '修改失败',
									'data' => '' 
					);
				}
			} else {
				$data ['store_id'] = session ( 'store_id' );
				$re = $this->$fun ( $data );
				$gift ['goods_id'] = $data ['goods_id'];
				$gift ['start_time'] = strtotime ( $data ['start_time'] );
				$gift ['end_time'] = strtotime ( $data ['end_time'] );
				$gift ['prom_id'] = $re;
				$re = M ( 'promotion_goods' )->add ( $gift );
				if ($re) {
					return array (
									'status' => 1,
									'message' => '添加成功',
									'data' => '' 
					);
				} else {
					return array (
									'status' => 0,
									'message' => '添加失败',
									'data' => '' 
					);
				}
			}
		}
	}
	
	/**
	 * 删除数据
	 * 
	 * @param int $id        	
	 * @return boolean
	 */
	public function deletePro($id) {
		if (! is_numeric ( $id ) || $id == 0) {
			return false;
		}
		
		$this->startTrans ();
		$status = $this->delete ( $id );
		
		if (empty ( $status )) {
			$this->rollback ();
			return false;
		}
		return $status;
	}
	
	/**
	 * 获取配件表信息
	 */
	public function getGoodsAccessories($data) {
		$where ['store_id'] = session ( 'store_id' );
		$data ['page'] = empty ( $data ['page'] ) ? 0 : $data ['page'];
		$parts = M ( 'goods_accessories' )->field ( 'id,goods_id,sub_ids,status,create_time,update_time' )->where ( $where )->page ( $data ['page'], 10 )->select ();
		
		foreach ( $parts as $k => $v ) {
			$sub_ids = explode ( ",", $v ['sub_ids'] );
			
			$parts [$k] ['parts_num'] = count ( $sub_ids );
			// $parts[$k]['goods']=M('goods')->where(['id'=>['in',$sub_ids]])->select();
			if (! empty ( $data ['goods_id'] ) && ! in_array ( $data ['goods_id'], $sub_ids )) {
				
				unset ( $parts [$k] );
			}
			unset ( $parts [$k] ['sub_ids'] );
		}
		
		$total = M ( 'goods_accessories' )->field ( 'id' )->where ( $where )->count ();
		$page = ceil ( $total / 10 );
		$data = array (
						'data' => $parts,
						'page_size' => 10,
						'page' => $page 
		);
		return $data;
	}
	
	/**
	 * 获取推荐配置单条记录
	 */
	public function getPartsById($data) {
		$goods_ids = M ( 'goods_accessories' )->field ( 'status,goods_id,sub_ids' )->where ( [ 
						'id' => $data ['id'] 
		] )->find ();
		$goodsModel = M ( 'goods' );
		$last_data ['status'] = $goods_ids ['status'];
		$last_data ['goods'] = $goodsModel->where ( [ 
						'id' => $goods_ids ['goods_id'] 
		] )->field ( 'id,title,price_market,stock' )->find ();
		$sub_ids = explode ( ',', $goods_ids ['sub_ids'] );
		
		foreach ( $sub_ids as $v ) {
			$last_data ['sub_data'] [] = $goodsModel->where ( [ 
							'id' => $v 
			] )->field ( 'id,title,price_market,stock' )->find ();
		}
		return $last_data;
	}
	
	/**
	 * 添加编辑推荐配置
	 */
	public function addPart($data) {
		$add ['goods_id'] = $data ['goods_id'];
		$add ['sub_ids'] = implode ( ",", $data ['sub_ids'] );
		$add ['status'] = $data ['status'];
		$add ['store_id'] = session ( 'store_id' );
		
		if ($data ['id']) {
			$add ['update_time'] = time ();
			return M ( 'goods_accessories' )->where ( [ 
							'id' => $data ['id'] 
			] )->save ( $add );
		} else {
			
			$add ['create_time'] = time ();
			return M ( 'goods_accessories' )->add ( $add );
		}
	}
	
	/**
	 * 最佳组合列表
	 */
	public function getGoodsCombo($data) {
		$where ['store_id'] = session ( 'store_id' );
		
		$combo = M ( 'goods_combo' )->field ( 'id,goods_id,sub_ids,create_time,update_time' )->where ( $where )->page ( $data ['page'], 10 )->select ();
		
		$total = M ( 'goods_combo' )->field ( 'id' )->where ( $where )->count ();
		foreach ( $combo as $k => $v ) {
			$sub_ids = explode ( ",", $v ['sub_ids'] );
			
			$combo [$k] ['parts_num'] = count ( $sub_ids );
			// $parts[$k]['goods']=M('goods')->where(['id'=>['in',$sub_ids]])->select();
			if (! empty ( $data ['goods_id'] ) && ! in_array ( $data ['goods_id'], $sub_ids )) {
				
				unset ( $combo [$k] );
			}
			unset ( $combo [$k] ['sub_ids'] );
		}
		
		$page = ceil ( $total / 10 );
		$data = array (
						'data' => $combo,
						'page_size' => 10,
						'page' => $page 
		);
		return $data;
	}
	
	/**
	 * 添加编辑最佳组合
	 */
	public function addCombo($data) {
		$add ['goods_id'] = $data ['goods_id'];
		$add ['sub_ids'] = implode ( ",", $data ['sub_ids'] );
		$add ['store_id'] = session ( 'store_id' );
		
		if ($data ['id']) {
			$add ['update_time'] = time ();
			return M ( 'goods_combo' )->where ( [ 
							'id' => $data ['id'] 
			] )->save ( $add );
		} else {
			
			$add ['create_time'] = time ();
			return M ( 'goods_combo' )->add ( $add );
		}
	}
	
	/**
	 * 优惠套餐
	 */
	public function getPackage($data) {
		$package = M ( 'goods_package' )->where ( [ 
						'store_id' => session ( 'store_id' ) 
		] )->page ( $data ['page'], 10 )->select ();
		
		$total = M ( 'goods_package' )->where ( [ 
						'store_id' => session ( 'store_id' ) 
		] )->count ();
		$goods_packageModel = M ( 'goods_package_sub' );
		foreach ( $package as $k => $v ) {
			$goods_package = $goods_packageModel->where ( [ 
							'package_id' => $v ['id'] 
			] )->field ( 'id,goods_id' )->select ();
			
			$goods_packageId = array_column ( $goods_package, 'goods_id' );
			// var_dump($goods_packageId);die;
			$package [$k] ['goods_num'] = count ( $goods_package );
			$package [$k] ['goods_id'] = $goods_packageId [$k];
			if (! empty ( $data ['goods_id'] ) && ! in_array ( $data ['goods_id'], $goods_packageId )) {
				
				unset ( $package [$k] );
			}
		}
		
		$page = ceil ( $total / 10 );
		$data = array (
						'data' => $package,
						'page_size' => 10,
						'page' => $page 
		);
		
		return $data;
	}
	
	/**
	 * 获取优惠套餐单条信息
	 */
	public function getPackageById($data) {
		$goods_package = M ( 'goods_package_sub' )->where ( [ 
						'package_id' => $data ['id'] 
		] )->select ();
		$goodsModel = M ( 'goods' );
		$total_price = 0;
		$total_discount = 0;
		foreach ( $goods_package as $k => $v ) {
			$goodsData = $goodsModel->where ( [ 
							'id' => $v ['goods_id'] 
			] )->field ( 'title,price_market,stock' )->find ();
			$goods_package [$k] ['title'] = $goodsData ['title'];
			$goods_package [$k] ['price_market'] = $goodsData ['price_market'];
			$goods_package [$k] ['stock'] = $goodsData ['stock'];
			$total_price += $goodsData ['price_market'];
			$total_discount += $v ['discount'];
			unset ( $goods_package [$k] ['id'] );
		}
		$package ['goods_info'] = $goods_package;
		$package ['total_discount'] = $total_discount;
		$package ['total_price'] = $total_price;
		$package ['package_name'] = M ( 'goods_package' )->where ( [ 
						'id' => $data ['id'] 
		] )->getField ( 'title' );
		return $package;
	}
	
	/**
	 * 删除优惠套餐
	 */
	public function delPackage($data) {
		$package = M ( 'goods_package' )->where ( [ 
						'id' => $data ['id'] 
		] )->delete ();
		
		if ($package) {
			
			M ( 'goods_package_sub' )->where ( [ 
							'package_id' => $data ['id'] 
			] )->delete ();
			
			return true;
		} else {
			
			return false;
		}
	}
	
	/**
	 * 优惠套餐添加
	 */
	public function AddPackage($data) {
		$add ['total'] = $data ['total'];
		$add ['discount'] = $data ['discount'];
		$add ['store_id'] = session ( 'store_id' );
		$add ['title'] = $data ['title'];
		$goods_id = $data ['goods'];
		// $goods_id=array(
		// array(
		// 'goods_id' =>3213,
		// 'package_price'=>134,
		// ),
		// array(
		// 'goods_id'=>3212 ,
		// 'package_price'=>123,
		// ),
		// );
		$Goods_package_sub = M ( 'goods_package_sub' );
		if ($data ['id']) { // 修改
			$add ['update_time'] = time ();
			$r = M ( 'goods_package' )->where ( [ 
							'id' => $data ['id'] 
			] )->save ( $add );
			
			$re = $Goods_package_sub->where ( [ 
							'package_id' => $data ['id'] 
			] )->delete ();
			if ($re) {
				
				foreach ( $goods_id as $v ) {
					$Goods_package_sub->add ( [ 
									'package_id' => $data ['id'],
									'goods_id' => $v ['goods_id'],
									'discount' => $v ['package_price'] 
					] );
				}
				
				return true;
			} else {
				return false;
			}
		} else { // 添加
			
			$add ['create_time'] = time ();
			$r = M ( 'goods_package' )->add ( $add );
			
			foreach ( $goods_id as $v ) {
				$Goods_package_sub->add ( [ 
								'package_id' => $r,
								'goods_id' => $v ['goods_id'],
								'discount' => $v ['package_price'] 
				] );
			}
			return true;
		}
	}
	
	/**
	 * 抢购列表
	 */
	function getPanicBuy($data) {
		$where ['store_id'] = session ( 'store_id' );
		$where ['type'] = $this->getTypeByName ( '抢购' );
		$re = $this->where ( $where )->field ( 'id,status,start_time,end_time' )->page ( $data ['page'], 10 )->select ();
		
		$total = $this->where ( $where )->field ( 'id' )->count ();
		foreach ( $re as $k => $v ) {
			
			$goods = $this->getGiftByPromId ( $v ['id'] );
			
			$re [$k] ['title'] = $goods ['title'];
			$re [$k] ['price_market'] = $goods ['price_market'];
			$re [$k] ['stock'] = $goods ['stock'];
			$re [$k] ['start_time'] = $v ['start_time'];
			$re [$k] ['end_time'] = $v ['end_time'];
			if (! empty ( $data ['goods_id'] ) && $data ['goods_id'] != $goods ['id']) {
				unset ( $re [$k] );
			}
		}
		$page = ceil ( $total / 10 );
		$data = array (
						'data' => $re,
						'page_size' => 10,
						'page' => $page 
		);
		return $data;
	}
	
	/**
	 * 添加编辑抢购
	 */
	public function AddUpdPanic(array $data, $fun = 'add') {
		if (empty ( $data ) || ! is_array ( $data ) || ! method_exists ( $this, $fun )) {
			return false;
		}
		if ($data ['start_time'] > $data ['end_time']) {
			$this->error = '开始时间不能大于结束时间';
			return false;
		}
		
		$data ['type'] = $this->getTypeByName ( '抢购' );
		
		if ($fun == 'save') {
			
			$gift ['name'] = $data ['title'];
			$gift ['start_time'] = $data ['start_time'];
			$gift ['end_time'] = $data ['end_time'];
			$gift ['description'] = $data ['description'];
			$gift ['update_time'] = time ();
			$gift ['panic_buy'] = $data ['panic_buy']; // 抢购数量
			$gift ['limit_buy'] = $data ['limit_buy']; // 限购数量
			$re = $this->where ( [ 
							'id' => $data ['id'] 
			] )->save ( $gift );
			if ($re) {
				$panicGoods ['goods_id'] = $data ['goods_id'];
				$panicGoods ['start_time'] = $data ['start_time'];
				$panicGoods ['end_time'] = $data ['end_time'];
				$panicGoods ['activity_price'] = $data ['activity_price'];
				
				M ( 'promotion_goods' )->where ( [ 
								'prom_id' => $data ['id'] 
				] )->save ( $panicGoods );
			}
			
			// $this->updGoodStatus($data['goods_id'],$status['status']=5);
			return true;
		} else {
			$gift ['name'] = $data ['title'];
			$gift ['store_id'] = session ( 'store_id' );
			$gift ['start_time'] = $data ['start_time'];
			$gift ['end_time'] = $data ['end_time'];
			$gift ['description'] = $data ['description'];
			$gift ['create_time'] = time ();
			$gift ['panic_buy'] = $data ['panic_buy']; // 抢购数量
			$gift ['limit_buy'] = $data ['limit_buy']; // 限购数量
			$gift ['type'] = $data ['type'];
			$re = $this->$fun ( $gift );
			if ($re) {
				
				$panicGoods ['prom_id'] = $re;
				$panicGoods ['goods_id'] = $data ['goods_id'];
				$panicGoods ['start_time'] = $data ['start_time'];
				$panicGoods ['end_time'] = $data ['end_time'];
				$panicGoods ['activity_price'] = $data ['activity_price'];
				// $this->updGoodStatus($data['goods_id'],$status['status']=5);
				return M ( 'promotion_goods' )->add ( $panicGoods );
			}
		}
	}
	
	/**
	 * 抢购获取单条记录
	 */
	public function getPanicById($id) {
		$prom_goods = M ( 'prom_goods as a' )->join ( 'db_promotion_goods as b on b.prom_id=a.id' )->field ( 'a.id,name,a.start_time,a.end_time,description,goods_id,activity_price,panic_buy,limit_buy' )->where ( [ 
						'a.id' => $id 
		] )->select ();
		$goodsModel = M ( 'goods' );
		foreach ( $prom_goods as $k => $v ) {
			$goods = $goodsModel->where ( [ 
							'id' => $v ['goods_id'] 
			] )->field ( 'title,price_market,stock' )->find ();
			$prom_goods [$k] ['title'] = $goods ['title'];
			$prom_goods [$k] ['price_market'] = $goods ['price_market'];
			$prom_goods [$k] ['stock'] = $goods ['stock'];
			$prom_goods [$k] ['description'] = html_entity_decode ( $v ['description'] );
		}
		
		return $prom_goods;
	}
}