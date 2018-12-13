<?php
declare(strict_types = 1);
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Common\TraitClass;

trait NoticeTrait
{
	
	
	
	/**
	 * 提示client
	 *
	 * @param array  $data
	 *            要检测的数据
	 * @param string $checkKey
	 *            要检测的键
	 * @param string $message
	 *            信息
	 * @param bool   $isValidate
	 *            是否检测建
	 */
	public function promptPjax($data, $message = '暂无数据，请添加', $checkKey = null,$isValidate = false) :void
	{
		if (is_numeric($data)) {
			return ;
		}
		
		if (empty($data)) {
			$this->ajaxReturnData(null, 0, $message);
		} elseif (is_array($data) && empty($data[ $checkKey ]) && $isValidate) {
			$this->ajaxReturnData(null, 0, $message);
		}
	}
	
	public function alreadyInDataPjax($data, $message = '已存在该数据') :void
	{
		if ($data == 0) {
			return ;
		}
		
		if (!empty($data)) {
			$this->ajaxReturnData(null, 0, $message);
		}
		return ;
	}
	
	/**
	 * ajax 返回数据
	 */
	public function ajaxReturnData($data, $status = 1, $message = '操作成功') :void
	{
		$this->ajaxReturn(array(
			'status'  => $status,
			'message' => $message,
			'data'    => $data,
		));
	}
	
	public function updateClient($insert_id, $message) :void
	{
		$status = !is_numeric($insert_id) ? 0 : 1;
		$message = !is_numeric($insert_id) ? $message . '，失败' : $message . '，成功';
		$this->ajaxReturnData($insert_id, $status, $message);
	}
	
	public function addClient($insert_id) :void
	{
		$status = empty($insert_id) ? 0 : 1;
		$message = empty($insert_id) ? '添加失败' : '添加成功';
		$this->ajaxReturnData($insert_id, $status, $message);
	}
	
     /**
     * 判断数字编号
     * @param int $id 数字编号
     */
    public function errorNotice(& $id)
    {
        
        if (( $id = intval($id) ) === 0) {
            $this->ajaxReturnData([],0,'参数错误');
        }
    }
    
    /**
     * 检测参数
     * @param array $data
     */
    public function errorArrayNotice (array & $data)
    {
        if (empty($data['id']) || (( $data['id'] = intval($data['id']) ) === 0) ) {
            $this->ajaxReturnData([],1,'参数错误');
        } 
    }
    
	
	/**
	 * @param unknown $data
	 */
	public function isEmpty($data) :void
	{
		if (empty($data)) {
			echo '';
			die();
		}
	}
}