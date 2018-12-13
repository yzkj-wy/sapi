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

/* 数据库配置 */

return array(

    'LOAD_EXT_CONFIG'    => 'db', // 加载数据库配置文件

    'IMG_ROOT_PATH' => '/Uploads/goods/',

    'domin'         => 'http://'.$_SERVER['SERVER_NAME'],

    'accept'   => [

        'http://agent.yizehuitong.com',

        'http://localhost:8085'

    ], // 跨域域名设置

    'URL_MODEL' => 1, // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE 模式); 3 (兼容模式) 默认为PATHINFO 模式



    'DATA_CACHE_TYPE'   => 'File',

    'TMPL_CACHE_ON' => true,//禁止模板编译缓存

    'HTML_CACHE_ON' => true,//禁止静态缓存

    'TMPL_CACHE_TIME' => 6,

    "PRODUCT_PAGE"=>32,

    'balanceId' => 4,

    //自定义命名空间

    'URL_ROUTER_ON' => true,



    'store_class_status' => [ // radio

        [

            'name' => '启用',

            'value' => 1,

            'fork' => 'open'

        ],

        [

            'name' => '关闭',

            'value' => 0,

            'fork' => 'close'

        ]

    ],

    //

    'status_c' => [ // radio

        [

            'name' => '是',

            'value' => 1,

            'fork' => 'open'

        ],

        [

            'name' => '否',

            'value' => 0,

            'fork' => 'close'

        ]

    ],



    'SESSION_OPTIONS' => [

        'auto_start' => 1

    ],



    'AUTOLOAD_NAMESPACE' => [

        'Extend' => 'Extend/',

    ],

);