<?php
//000000086400a:21:{i:0;a:9:{s:5:"field";s:2:"id";s:4:"type";s:16:"int(11) unsigned";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:3:"PRI";s:7:"default";N;s:5:"extra";s:14:"auto_increment";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:8:"退货id";}i:1;a:9:{s:5:"field";s:8:"order_id";s:4:"type";s:7:"int(11)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:3:"MUL";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:14:"订单【id】";}i:2;a:9:{s:5:"field";s:11:"tuihuo_case";s:4:"type";s:11:"varchar(50)";s:9:"collation";s:15:"utf8_general_ci";s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:0:"";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"退货理由";}i:3;a:9:{s:5:"field";s:11:"create_time";s:4:"type";s:7:"int(11)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"申请时间";}i:4;a:9:{s:5:"field";s:15:"revocation_time";s:4:"type";s:7:"int(11)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"撤销时间";}i:5;a:9:{s:5:"field";s:11:"update_time";s:4:"type";s:7:"int(11)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"审核时间";}i:6;a:9:{s:5:"field";s:8:"goods_id";s:4:"type";s:7:"int(11)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";N;s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:23:"退货的商品【id】";}i:7;a:9:{s:5:"field";s:7:"explain";s:4:"type";s:12:"varchar(300)";s:9:"collation";s:15:"utf8_general_ci";s:4:"null";s:3:"YES";s:3:"key";s:0:"";s:7:"default";N;s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:20:"退货(退款)说明";}i:8;a:9:{s:5:"field";s:5:"price";s:4:"type";s:11:"float(11,2)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:4:"0.00";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"退货金额";}i:9;a:9:{s:5:"field";s:10:"is_receive";s:4:"type";s:10:"tinyint(1)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:59:"退款及其换货时是否收到货【0未收到1收到】";}i:10;a:9:{s:5:"field";s:6:"status";s:4:"type";s:10:"tinyint(1)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:89:"审核状态【0审核中1审核失败2审核通过3退货中4退货完成 5已撤销 】";}i:11;a:9:{s:5:"field";s:7:"user_id";s:4:"type";s:7:"int(11)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"用户编号";}i:12;a:9:{s:5:"field";s:6:"number";s:4:"type";s:7:"int(11)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"申请数量";}i:13;a:9:{s:5:"field";s:9:"apply_img";s:4:"type";s:12:"varchar(255)";s:9:"collation";s:15:"utf8_general_ci";s:4:"null";s:3:"YES";s:3:"key";s:0:"";s:7:"default";N;s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"申请图片";}i:14;a:9:{s:5:"field";s:7:"content";s:4:"type";s:12:"varchar(255)";s:9:"collation";s:15:"utf8_general_ci";s:4:"null";s:3:"YES";s:3:"key";s:0:"";s:7:"default";N;s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:12:"审核内容";}i:15;a:9:{s:5:"field";s:6:"is_own";s:4:"type";s:10:"tinyint(1)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"0";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:27:"是否自营【0否 1是】";}i:16;a:9:{s:5:"field";s:10:"express_id";s:4:"type";s:15:"int(3) unsigned";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";s:1:"1";s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:18:"快递【编号】";}i:17;a:9:{s:5:"field";s:10:"waybill_id";s:4:"type";s:19:"bigint(15) unsigned";s:9:"collation";N;s:4:"null";s:3:"YES";s:3:"key";s:0:"";s:7:"default";N;s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:9:"运单号";}i:18;a:9:{s:5:"field";s:6:"remark";s:4:"type";s:11:"varchar(80)";s:9:"collation";s:15:"utf8_general_ci";s:4:"null";s:3:"YES";s:3:"key";s:0:"";s:7:"default";N;s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:6:"备注";}i:19;a:9:{s:5:"field";s:8:"store_id";s:4:"type";s:7:"int(11)";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";N;s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:18:"店铺【编号】";}i:20;a:9:{s:5:"field";s:8:"apply_id";s:4:"type";s:16:"int(10) unsigned";s:9:"collation";N;s:4:"null";s:2:"NO";s:3:"key";s:0:"";s:7:"default";N;s:5:"extra";s:0:"";s:10:"privileges";s:31:"select,insert,update,references";s:7:"comment";s:9:"审核人";}}
?>