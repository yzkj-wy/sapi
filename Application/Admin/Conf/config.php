<?php
return array(
    'URL_HTML_SUFFIX' => '', // 伪静态
    
    // 订单状态【-1:,0 ，1，2，，3，4，5，6，7，8，9, 10：，11】
    'order' => array(
        'CancellationOfOrder' => '取消订单',
        'NotPaid' => '未支付',
        'YesPaid' => '已支付',
        'InDelivery' => '发货中',
        'AlreadyShipped' => '已发货',
        'ReceivedGoods' => '已收货',
        'ReturnAudit' => '退货审核中',
        'AuditFalse' => '审核失败',
        'AuditSuccess' => '审核成功',
        'Refund' => '退款中',
        'ReturnMonerySucess' => '退款成功',
        'ToBeShipped' => '代发货',
        'ReceiptOfGoods' => '待收货'
    ),
    /**
     * 退货
     */
    'returnGoods' => [ // 0审核中1审核失败2审核通过3退货中4换货中5换货完成6退货完成7已撤销
        '审核中',
        '审核失败',
        '审核通过',
        '退货中',
        '换货中',
        '换货完成',
        '退货完成',
        '已撤销'
    ],
    'goods_picture_number' => 8, // 上传商品图片数量
    'store_status' => [
        '关闭',
        '开启'
    ],
    
    'is_own' => [
        '否',
        '是'
    ],
    
    'is_receive' => [ // 是否收到货
        '',
        '未收到',
        '收到'
    ],
    'admin_log_type' => [ // 操作类型：0新增1修改2删除
        0 => '新增',
        1 => '修改',
        2 => '删除'
    ],
    'SHOW_PAGE_TRACE' => false,
    'ORDER_NUMBER' => 7,
    'PAGE_NUMBER' => 5,
    'union_page_number' => 10,
    'admin_title' => 'shopsn电商系统',
    'upload_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/upload.php/Upload/index',
    'front_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/index.php/',
    'do_you_mail_it' => [ // 是否包邮
        '自定义运费',
        '卖家包邮'
    ],
    
    'charging_mode' => [ // 计费方式
         '按件数',
         '按重量',
         '按体积'
    ],
    'pro_type' => [ // 优惠类型配置
        'undefined',
        'gt',
        'gt'
    ],
    'specify_conditional_mail' => [ // 是否指定条件包邮
        '否',
        '是'
    ],
    
    'platform_pay' => [ // 支付平台
        'pc',
        '移动设备（Phone）'
    ],
    'admin_title' => 'yisu后台管理',
    'qr_image' => './Uploads/qrCode/',
    'water' => './Public/Admin/img/logo/water.png',
    // 管理员提示
    'title' => [
        'recommend' => '优先在首页楼层对应分类展示', // 推荐解释
        'hot' => '在首页热卖栏中显示'
    ], // 热卖解释
    
    //状态
    'approval_status' => [
        '未审核',
        '审核通过',
        '拒绝审核'
    ],
    // 微信公众号数据
    'we_chat_type' => [
        '公众号',
        '服务号',
        '企业号'
    ],
    
    'msg_setting' => [
        [
            'status' => false,
            'class'  => 'Common\Logic\StoreMsgLogic'
        ],
        [
            'status' => false,
            'class'  => 'Common\Strategy\SendMessage\Sms'
        ],
        [
            'status' => false,
            'class'  => 'Common\Strategy\SendMessage\Mail'
        ]
    ],
    
    
    'default_album' => '默认相册',
    
    'page_class'    => 'Think\AjaxPage',
    
    'refund_type'    => [
        '\Common\Refund\Special\ReturnGoods',
        '\Common\Refund\Special\ReturnMoney',
        '\Common\Refund\Special\ExchangeGoods'
    ],
    //运单配置
    'waybill_image_save_config' => '/uploadNum/1/input/textfield/path/waybill/callBack/checkImage/config/waybill_image_config',
    'create_thumb_file'         => 'http://center.shopsn.cn/upload.php/CreateImageThumb/createThumb',
    'unlink_image'              => 'http://center.shopsn.cn/upload.php/DeleteImage/deleteImageArray',
    'create_ad_image'           => 'http://center.shopsn.cn/upload.php/StoreAdvPictureUpload/uploadImageToLocalAdv',
    'unlink_image_no_thumb'     => 'http://center.shopsn.cn/upload.php/DeleteImage/deleteImageByNoThumb',
    'settle' => [
        '默认',
        '店家已确认',
        '店家拒绝',
        '平台已审核',
        '平台拒绝',
        '结算完成'
    ],
	'AUTH_CONFIG' => [
		'AUTH_ON'           => true,                      // 认证开关
		'AUTH_TYPE'         => 1,                         // 认证方式，1为实时认证；2为登录认证。
		'AUTH_GROUP'        => 'store_auth_group',        // 用户组数据表名
		'AUTH_GROUP_ACCESS' => 'store_auth_group_access', // 用户-用户组关系表
		'AUTH_RULE'         => 'store_auth_menu',         // 权限规则表
		'AUTH_USER'         => 'store_seller'             // 用户信息表
	]
);