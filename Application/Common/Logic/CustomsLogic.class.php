<?php
declare(strict_types=1);
namespace Common\Logic;
use Common\Tool\Tool;
use Common\Model\CommonModel;
use Common\Model\CusOrderModel;
use Common\Model\CusOrderGoodsModel;
use Common\Model\StoreModel;
use Common\Model\PayModel;
use Common\Model\OrderTlpayModel;
use Common\Model\GoodsModel;
use Think\SessionGet;
use Common\SessionParse\SessionManager;


/**
 * 逻辑处理层
 */
class CustomsLogic extends AbstractGetDataLogic
{


    /**
     * 订单数据
     * @var int
     */
    private $orderDataNumber = 0;

    /**
     * 店铺编号数据
     * @var array
     */
    private $storeId = [];

    /**
     * 支付数据
     * @var array
     */
    private $payData = [];


    /**
     * 订单编号（立即购买生成）
     * @var string
     */
    private $placeTheOrderId;

    /**
     * @return array
     */
    public function getPayData()
    {
        return $this->payData;
    }

    /**
     *
     * @return string
     */
    public function getPlaceTheOrderId(): string
    {
        return $this->placeTheOrderId;
    }

    public function getResult()
    {
        return [];
    }

    /**
     * 获取当前模型类名
     */
    public function getModelClassName(): string
    {
        return $this->placeTheOrderId;
    }

    /**
     * 构造方法
     *
     * @param array $data
     */
    public function __construct(array $data = [], $split = '')
    {
        $this->data = $data;
        $this->splitKey = $split;
//        $this->modelObj = new CusOrderModel ();
//        $this->order_goods_model = new CusOrderGoodsModel ();
//        $this->storeModel = new StoreModel ();
    }

    /**
     * 返回验证数据
     */
    public function getValidateByLogin()
    {
        $message = [
            'price_sum' => [
                'required' => '订单总价必填'
            ],
            'address_id' => [
                'required' => '收货地址必填'
            ],
            'translate' => [
                'required' => '是否需要发票必须',
                'number' => '是否需要发票必须是数字'

            ]
        ];
        return $message;
    }

    public function getValidateByGoods(): array
    {
        $message = [

            'invoice_id' => [
                'required' => '发票信息不能为空',
            ],
            'address_id' => [
                'number' => '发票地址必填'
            ]
        ];
        return $message;
    }


    public function getValidateByOrder()
    {
        $message = [
            'id' => [
                'number' => '订单ID必须'
            ]
        ];
        return $message;
    }

    public function getValidateByReturn()
    {
        $message = [
            'id' => [
                'number' => '订单ID必须'
            ],
            'goods_id' => [
                'number' => '商品ID必须'
            ]
        ];
        return $message;
    }

    /**
     * 订单生成检查
     * @return string[][]
     */
    public function getCartIdInfo(): array
    {
        $message = [
            'address_id' => [
                'number' => '收货地址必填'
            ],
            'invoice_id' => [
                'required' => '发票必须填写'
            ]
        ];
        return $message;
    }

    /**
     * 配件检查参数
     */
    public function getMessageValidateByParts(): array
    {
        return [
            'address_id' => [
                'number' => '地址编号必须是数字',
            ],
        ];
    }



    // 获取订单所有数据
    public function getOrderDetails()
    {
        $where ['id'] = $this->data ['id'];
        $where ['store_id'] = $this->data ['store_id'];
        $field = '*';
        $way = 'find';
        $rest = $this->modelObj->getOrderByWhere($where, $field, $way);
        if (empty ($rest)) {
            return array(
                "status" => 0,
                "data" => "",
                "message" => "暂无数据"
            );
        }

        $rest ['pay'] = M('pay')->where([
            'payname' => $rest ['payname'],
            'store_id' => $rest ['payname']
        ])->getField('*');

        $rest ['store'] = M('store')->where([
            'store_id' => $rest ['store_id']
        ])->getField('*');

        $rest ['goodlist'] = M('store')->field('id,brand_id,price_member,stock,selling,shelves,class_id,recommend,top,season_hot,update_time,create_time,
        goods_type,sort,p_id,status,comment_member,sales_sum,attr_type,extend,advance_date,store_id,type,approval_status,class_two,class_three,express_id', true)->where([
            'order_id' => $rest ['id']->select()
        ]);
        foreach ($rest ['goodlist'] as $key => $value) {
            $rest ['goodlist'][$key] = array_merge($rest ['goodlist'][$key],
                M('goods')->where([
                    'id' => $value ['goods_id']
                ])->getField('*'));
        }

        if ($rest ['order_status'] == 0) {

            SessionManager::SET_ORDER_DATA([
                $rest ['id'] => [
                    'total_money' => sprintf("%.5f", $rest ['price_sum'] + $rest ['freight'] + $rest ['taxFcy'] - $rest['coupon_deductible'], 2),
                    'order_id' => $rest ['id'],

                    'store_id' => $rest ['store_id']
                ],
            ]);

            SessionManager::SET_ORDER_GOODS_DATA($rest ['goodlist']);

            // 订单类型 0 普通订单 1优惠套餐订单
            SessionGet::getInstance('order_type_by_user', 0)->set();

        }
        return array(
            "status" => 1,
            "data" => $rest,
            "message" => "获取成功"
        );


    }

    // 获取线下订单所有数据
    public function getOfflineOrderDetails()
    {
        $where ['id'] = $this->data ['id'];
        $where ['store_id'] = $this->data ['store_id'];
        $field = '*';
        $way = 'find';
        $rest = $this->modelObj->getOrderByWhere($where, $field, $way);
        if (empty ($rest)) {
            return array(
                "status" => 0,
                "data" => "",
                "message" => "暂无数据"
            );
        }
        $pay = M('pay')->where([
            'payname' => $rest ['pay_name'],
            'store_id' => $rest ['pay_name']
        ])->getField('payCode,payName');
        $rest = array_merge($rest, $pay);
        $store = M('store')->where([
            'store_id' => $rest ['store_id']
        ])->getField('id,class_id,grade_id,user_id,store_state,store_sort,start_time,end_time,status,theme_id,store_collect,print_desc,store_sales,free_price,decoration_switch,
       decoration_only,image_count,is_own,build_all,bar_type,create_time,update_time,type,store_logo,commission,description,wx_accout,alipay_account,bank_account,credibility,mobile,person_name',true);
        $rest = array_merge($rest, $store);
        $address= M('store_address')->where([
            'store_id' => $rest ['store_id']
        ])->getField('id,store_zip,store_id',true);
        $rest ['shipper_city'] = M('region')->field('name')->where([
            'id' => $address ['prov_id']])->select();
        $rest ['shipper_prov'] = M('region')->field('name')->where([
            'id' => $address ['city']])->select();
        $rest ['shipper_dist'] = M('region')->field('name')->where([
            'id' => $address ['dist']])->select();
        $rest ['shipper_country'] = $address ['country'];
        $rest ['shipper_address'] = $address ['address'];
        $rest['taxFcy']=0;
        $rest['taxTotal']=0;
        $rest['weight']=0;
        $rest['netwt']=0;
        $rest ['goodlist'] = M('offline_order_goods')->field('add_time,save_time,id,order_sn_id',true)->where([
            'order_sn_id' => $rest ['id']])->select();
        foreach ($rest ['goodlist'] as $key => $value) {
            $rest ['goodlist'][$key] = array_merge($rest ['goodlist'][$key],
                M('goods')->where([
                    'id' => $value ['goods_id']
                ])->getField('*'));
            $rest['taxFcy']+=$rest ['goodlist'][$key]['taxFcy']*$rest ['goodlist'][$key]['goods_num'];
            $rest['taxTotal']+=$rest ['goodlist'][$key]['taxTotal']*$rest ['goodlist'][$key]['goods_num'];
            $rest['weight']+=$rest ['goodlist'][$key]['weight']*$rest ['goodlist'][$key]['goods_num'];
            $rest['netwt']+=$rest ['goodlist'][$key]['netwt']*$rest ['goodlist'][$key]['goods_num'];
        }

        //修正参数
        $rest['paymentOrderId']=$rest['payment_order_id'];
        unset($rest['payment_order_id']);
        $rest['realname']=$rest['real_name'];
        unset($rest['real_name']);
        $rest['IdNumber']=$rest['id_number'];
        unset($rest['id_number']);
        $rest['insurefee']=$rest['insure_fee'];
        unset($rest['insure_fee']);
        $rest['TYPE']=$rest['type'];
        unset($rest['type']);
        $rest['tpl']=$rest['tp_code '];
        unset($rest['tp_code ']);
        $rest['busiMode']=$rest['busi_mode'];
        unset($rest['busi_mode']);
        $rest['bak1']=$rest['bak_one'];
        unset($rest['bak_one']);
        $rest['bak2']=$rest['bak_two'];
        unset($rest['bak_two']);
        $rest['bak3']=$rest['bak_three'];
        unset($rest['bak_three']);
        $rest['bak4']=$rest['bak_four'];
        unset($rest['bak_four']);
        $rest['bak5']=$rest['bak_five'];
        unset($rest['bak_five']);

//        if(empty($rest)){
//            return array('status'=>0,'message'=>'暂无数据','data'=>'');
//        }





//        if ($rest ['order_status'] == 0) {
//
//            SessionManager::SET_ORDER_DATA([
//                $rest ['id'] => [
//                    'total_money' => sprintf("%.5f", $rest ['price_sum'] + $rest ['freight'] + $rest ['taxFcy'] - $rest['coupon_deductible'], 2),
//                    'order_id' => $rest ['id'],
//
//                    'store_id' => $rest ['store_id']
//                ],
//            ]);
//
//            SessionManager::SET_ORDER_GOODS_DATA($rest ['goodlist']);
//
//            // 订单类型 0 普通订单 1优惠套餐订单
//            SessionGet::getInstance('order_type_by_user', 0)->set();
//
//        }
        return $rest;
    }
}
