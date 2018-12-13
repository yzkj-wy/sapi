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
namespace Admin\Controller;

use Admin\Logic\GoodsClassLogic;
use Admin\Logic\GoodsImagesLogic;
use Admin\Model\GoodsAttributeModel;
use Admin\Model\GoodsClassModel;
use Admin\Model\GoodsModel;
use Common\Logic\GoodsDetailLogic;
use Common\Logic\GoodsLogic;
use Common\Logic\GoodsSpecItemLogic;
use Common\Logic\SpecGoodsPriceLogic;
use Common\Model\BaseModel;
use Common\Tool\Tool;
use Common\TraitClass\GoodsTrait;
use Common\TraitClass\InitControllerTrait;
use Common\TraitClass\IsLoginTrait;
use PlugInUnit\Validate\CheckParam;

/**
 * 商品后台管理
 * @author Administrator
 */
class GoodsController
{
    use InitControllerTrait;
    
    use IsLoginTrait;
    
    use GoodsTrait;
   
    /**
     * 构造方法
     * @param array $args
     */
    public function __construct($args = null)
    {
        $this->init();

        $this->isNewLoginAdmin();

        $this->args = $args;

        $this->logic = new GoodsLogic($args);
    }
    
    /**
     * 修改商品
     * @param int  $id 商品id
     */
    public function modifyGoods()
    {
        //检查参数
        $this->objController->errorArrayNotice($this->args);
    
        Tool::connect('parseString');
    
        //获取商品通用信息
        $goodsInfo = $this->logic->getFindOne();
    
        $this->objController->promptPjax($goodsInfo, '非法');
    
        $this->objController->promptPjax($goodsInfo, $this->logic->getErrorMessage());
        //关联详情信息
        $goodsDetailLogic = new GoodsDetailLogic($goodsInfo,$this->logic->getIdSplitKey());
    
        $detail = $goodsDetailLogic -> getResult();
    
        $this->objController->ajaxReturnData([
            'goods' => $goodsInfo,
            'goods_detail' => $detail,
            //             'image' => $images,
        ]);
    
    }
    
	/**
	 * 保存商品 【第一步】
	 */
	public function saveGoods()
	{
	    $this->checkParamByClient();
	    
	    //重组数据 根据规格生成对应名字
	    $status = $this->logic->saveGoods();
	    
	    $this->objController->promptPjax($status, $this->logic->getErrorMessage());
	    
	    $goodsDetailLogic = new GoodsDetailLogic($this->args);
	   
	    $status = $goodsDetailLogic->saveData();
	    
	    $this->objController->promptPjax($status, $goodsDetailLogic->getErrorMessage());
	    
	    $this->objController->ajaxReturnData('');
	}

	/**
	 * 规格修改
	 * item => Array
        (
            '439_1144' => Array
                (
                    [price] => 67.00
                    [preferential] => 36.85
                    [price_vip] => 36.85
                    [store_count] => 0
                    [weight] => 0.100
                    [goods_id] => 2050
                    [id] => 1442
                    [sku] => 106-502
                ),

            '439_1143'=> Array
                (
                    [price] => 31.00
                    [preferential] => 33.55
                    [price_vip] => 33.55
                    [store_count] => 0
                    [weight] => 0.100
                    [goods_id] => 2051
                    [id] => 1443
                    [sku] => 106-501
                ),

        )
	 */
	public function editSpecGoods()
	{
	    $this->objController->promptPjax($this->logic->getMessageBySpec(), $this->logic->getErrorMessage());
	    
        //重组数据 根据规格生成对应名字
        $specItemLogic = new GoodsSpecItemLogic($this->args);

        $item = $specItemLogic->getGoodsNameByItem();
       
        $this->objController->promptPjax($item);

        //保存到商品表中
        $this->logic->setData($item);
        
        $id         = $this->logic->updateData();
        
        $this->objController->promptPjax($id !== false, $this->logic->getErrorMessage());

        $specPriceLogic = new SpecGoodsPriceLogic($this->args['item']);
        
//         Tool::connect('parseString');

        $status = $specPriceLogic->saveEdit($this->logic->getIdArray());
      
        $error = $specPriceLogic->getErrorMessage();
        
        $this->objController->promptPjax($status, $error);
        
        $this->objController->ajaxReturnData(true);

	}

	/**
	 * 商品属性 添加
	 */
	public function goodsAttribute()
	{
	    Tool::checkPost($_GET, array('is_numeric' => array('attribute_id')), true, array('attribute_id')) ? true : $this->objController->ajaxReturnData(null, 0, '参数错误');
	    //获取商品属性
	    $attributeModel = BaseModel::getInstance(GoodsAttributeModel::class);

	    $attribute      = $attributeModel->getAttribute(array(
	        'field' => array($attributeModel::$createTime_d, $attributeModel::$updateTime_d),
	        'where' => array($attributeModel::$status_d => 1, $attributeModel::$goodsClassId_d => $_GET['attribute_id'])
	    ), true);


	    //形成树
	    Tool::connect('Tree', $attribute);
	    $attribute = Tool::makeTree(array('parent_key' => $attributeModel::$pId_d));
	    $this->objController->assign('attribute', $attribute);
	    $this->objController->assign('model', GoodsAttributeModel::class);
	    $this->objController->display();
	}

    /**
     * 返回树形结构
     */
    public function buildTree()
    {
        $this->objController->ajaxReturnData($this->getClass());
    }

	/**
	 * @desc  生成Excel
	 * @param unknown $expTitle
	 * @param unknown $expCellName
	 * @param unknown $expTableData
	 */
	public function exportExcel($expTitle,$expCellName,$expTableData){
		$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
		$fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		$cellNum = count($expCellName);
		$dataNum = count($expTableData);
		vendor("PHPExcel.PHPExcel");

		$objPHPExcel = new \PHPExcel();
		$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

		$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
		// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
		for($i=0;$i<$cellNum;$i++){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
		}
		// Miscellaneous glyphs, UTF-8
		for($i=0;$i<$dataNum;$i++){
			for($j=0;$j<$cellNum;$j++){
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
			}
		}
		ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	/**
	 * 全部导出excel
	 * 当前页导出execl
	 *
	 * 通过当前页数（p）来进行判断是全部导出还是当前页导出
	 *     1.如果有p参数，就是当前页导出、
	 *     2.如果没有p参数，就是全部导出
	 */
	public function expGoods(){
		$tj_value = json_decode($_GET['tj_value'],true);
		$cond = [];
	    $tj_value['class_id']?$cond['class_id']=$tj_value['class_id']:false;
	    $tj_value['brand_id']?$cond['brand_id']=$tj_value['brand_id']:false;
	    $tj_value['shelves']?$cond['shelves']=$tj_value['shelves']:false;
	    $tj_value['title']?$cond['title']=['like','%'.$tj_value['title'].'%']:false;
		//获取p参数
		$current_page = $tj_value['p'];
		$cond['p_id'] = 0;
		$xlsName  = "goods";
		$xlsCell  = array(
			array('id','id'),
			array('title','商品名称'),
			array('code','货号'),
			array('class_id','商品分类'),
			array('price_market','市场价'),
			array('price_member','会员价'),
			array('stock','库存'),
			array('shelves','是否上架'),
			array('recommend','是否推荐'),
			array('sort','排序'),
		);
		$goodsClassModel = M("GoodsClass");
		$xlsModel = M('Goods');
		if($current_page){//当前页导出excel
			$page_setting = C('PAGE_SETTING');
			$xlsData  = $xlsModel
				->field('id,title,code,class_id,price_market,price_member,stock,shelves,recommend,sort')
				->where($cond)
				->page($current_page,$page_setting['PAGE_SIZE'])
				->order(['create_time'=>'desc','sort'])
				->select();
		}else{//全部导出excel
			$xlsData  = $xlsModel
				->field('id,title,code,class_id,price_market,price_member,stock,shelves,recommend,sort')
				->where($cond)
				->order(['create_time'=>'desc','sort'])
				->select();
		}
		foreach($xlsData as &$v){
			if($v['shelves'] == 1){
				$v['shelves'] = "是";
			}else{
				$v['shelves'] = "否";
			}
			if($v['recommend'] == 1){
				$v['recommend'] = "是";
			}else{
				$v['recommend'] = "否";
			}
			//用商品分类表里面的class_name来替换class_id
			$v['class_id'] = $goodsClassModel->where(['id'=>$v['class_id']])->getField('class_name');
		}
		unset($v);
		$this->exportExcel($xlsName,$xlsCell,$xlsData);

	}

	/**
	 * 批量删除方法
	 */
	public function old_array_change($end_array)
	{
		
	}
	
	/**
	 * 覆盖Trait方法
	 */
	protected function getDataSource()
	{
	    return $this->logic->getAleardyDataList();
    }

	/**
     * 发布商品(通用信息)
     */
	public function addGoodsInfo()
    {
        //验证数据
        $this->checkParamByClient();
       
        //发布商品
        $result = $this->logic->addGoodsInfo();
        
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData(session('goods_pid'));
    }

    /**
     * 一级商品列表
     */
    public function topGoodsList()
    {
        Tool::connect('ArrayChildren');
        $data = $this->logic->getTopGoodsList(); 

        $this->objController->promptPjax($data['data']);

        Tool::connect('parseString');

        $goodsImageLogic = new GoodsImagesLogic($data['data'],$this->logic->getIdSplitKey());
        
        $data['data'] = $goodsImageLogic -> getSlaveDataByMaster();
        
        $this->objController->ajaxReturnData($data);
    }

    /**
     * 删除商品(SPU)
     */
    public function delTopGoods()
    {
        $this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
        
        $result = $this->logic->delTopGoods();
       
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());
        
        $goodsDetailLogic = new GoodsDetailLogic($this->args);
        
        $this->objController->promptPjax($goodsDetailLogic->deleteGoodsDetail(), $goodsDetailLogic->getErrorMessage());
       
        $goodsImages = new GoodsImagesLogic($this->args);
        
        $status = $goodsImages->deleteImagesByGoods();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $goodsSpecLogic = new SpecGoodsPriceLogic($this->args);
       
        $this->objController->promptPjax($goodsSpecLogic->deleteGoodsBySpec(), $goodsSpecLogic->getErrorMessage());
        //接口返回数据
        $this->objController->ajaxReturnData([], 1, '删除成功');
    }

    /**
     * 删除一个商品(SKU)
     */
    public function deleteOneGood ()
    {
        
        $this->objController->promptPjax($this->logic->checkIdIsNumric(), $this->logic->getErrorMessage());
        
        $status = $this->logic->deleteGoodById();
        
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());
        
        $goodsSpecLogic = new SpecGoodsPriceLogic($this->args);
        
        $this->objController->promptPjax($goodsSpecLogic->deleteGoodsBySKU(), $goodsSpecLogic->getErrorMessage());
        
        $this->objController->ajaxReturnData($status);
    }
    
    
    /**
     * 是否上架
     */
    public function isShelve()
    {
        //验证数据
        $checkObj = new CheckParam($this->logic->getMessageByShelves(), $this->args);
        
        $this->objController->promptPjax($checkObj->checkParam(), $checkObj->getErrorMessage());

        $result = $this->logic->changeShelve();
       
        $this->objController->promptPjax($result, $this->logic->getErrorMessage());

        $this->objController->ajaxReturnData([],1,'切换成功');

    }

    /**
     * 一级商品列表
     */
    public function childGoodsList()
    {
        //验证数据
        $status = $this->logic->checkIdIsNumric();
        $this->objController->promptPjax($status, $this->logic->getErrorMessage());

        $data = $this->logic->getChildGoodsList();

        Tool::connect('parseString');

        $goodsClassLogic = new GoodsClassLogic($data,GoodsModel::$classId_d);
        $data = $goodsClassLogic->getDataByClassId();
        sort($data);
        $this->objController->ajaxReturnData($data);
    }
    /**
     * 根据规格添加商品
     */
    public function addSpecGoods()
    {
        //验证数据
        $this->objController->promptPjax($this->logic->getMessageBySpec(), $this->logic->getErrorMessage());
        //重组数据 根据规格生成对应名字
        $specItemLogic = new GoodsSpecItemLogic($this->args);
        
        //生成商品名称键值对
        $item  = $specItemLogic->getGoodsNameByItem();

        $this->objController->promptPjax($item);

        //先生成商品
        $this->logic->setData($item);
        
        $insertId =  $this->logic->addSpecDataByGoods();

        //根据规格 生成 对应数量的商品
        $this->objController->promptPjax($insertId, '未知错误，请仔细核对在提交');
    
        $specGoodsLogic = new SpecGoodsPriceLogic($item);
        //生成 商品-规格对应
        $status    = $specGoodsLogic->addSpecByGoods($insertId);

        $this->objController->promptPjax($status, $specGoodsLogic->getErrorMessage());
        $this->objController->ajaxReturnData([],1,'保存成功');
    }
}