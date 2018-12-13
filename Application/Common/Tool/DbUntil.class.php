<?php
namespace Common\Tool;

/**
 * 分表插件
 * @author 王强
 * @version 1.0
 */
class DbUntil
{
    /**
     * 用户数量
     * @var int
     */
    private $count = 0;
    
    const BASE_NUMBER = 10000000;
    
    const TABLE_STRING = <<<aaa
            DROP TABLE IF EXISTS `db_{tb_name}`;
            CREATE TABLE `db_order_goods` (
              `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
              `order_id` int(11) DEFAULT '0' COMMENT '商品id',
              `goods_id` int(11) DEFAULT '0' COMMENT '商品编号',
              `goods_num` int(11) DEFAULT NULL COMMENT '商品数量',
              `goods_price` float(11,2) DEFAULT NULL COMMENT '商品价格',
              `status` enum('8','7','6','5','4','3','2','1','9','-1','0') DEFAULT '0' COMMENT '-1：取消订单；0 未支付，1已支付，2，发货中，3已发货，4已收货，5退货审核中，6审核失败，7审核成功，8退款中，9退款成功',
              `space_id` int(11) DEFAULT NULL COMMENT '商品规格id',
              `user_id` int(11) unsigned DEFAULT '0' COMMENT '用户id',
              `comment` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已评价（0未评价1已评价）',
              `over` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已完成该单(0未完成 1已完成）',
              `ware_id` int(10) DEFAULT NULL COMMENT '所在仓库',
              PRIMARY KEY (`id`),
              KEY `goodsId` (`order_id`,`goods_id`) USING BTREE
            ) ENGINE=InnoDB AUTO_INCREMENT={auto} DEFAULT CHARSET=utf8 COMMENT='订单商品表';
aaa;
    
    private $createDbCmd = 'CREATE DATABASE {db_name} DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
    
    /**
     * 表前缀
     * @var string
     */
    const  tablePerfix = 'db_';
    
    private $dbName = '';
    
    
    private $tableName = '';
    
    private $newDbName = '';
    
    private $maxId = 0;
    
    /**
     *  * 架构方法
     * @param int $count
     * @param string $tableName
     * @param string $dbName
     */
    public function __construct($count, $tableName, $dbName)
    {
        $this->count = (int)$count;
        
        $this->tableName = $tableName;
        
        $this->dbName = $dbName;
    }
    
    /**
     * 创建数据库
     */
    public function buildDataBase($maxId)
    {
        if ($this->count >= $maxId) {
            return null;
        }
        $str = '';
        
        $source = $this->createDbCmd;
        
        $dbName = $this->dbName.'_'.substr(md5(time()), 0, 6);
        
        $source = str_replace('{db_name}', $dbName, $source);
        
        $str .= $source.";\r\n";
        
        $this->newDbName = $dbName;
        
        
        
        return $str;
    }
    
    /**
     * @return the $newDbName
     */
    public function getNewDbName()
    {
        return $this->newDbName;
    }

    public function createTable($auto)
    {
        if ($this->count >= $auto) {
            return null;
        }
        $cmd = 'use ' .$this->newDbName.";\r\n";
        
        $tableStr = self::TABLE_STRING;
        
        $tableStr = str_replace(['{tb_name}', '{auto}'], [$this->tableName, $this->count + 1], $tableStr);
        
        $cmd .= $tableStr;
        
        $this->maxId = $this->count + 1;
        
        return $cmd;
    }
    /**
     * @return the $maxId
     */
    public function getMaxId()
    {
        return $this->maxId;
    }

    
}