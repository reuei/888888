-- 鲸商城 Pro 数据库结构 v1.0.0

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 分站表
-- ----------------------------
DROP TABLE IF EXISTS `jz_subsite`;
CREATE TABLE `jz_subsite` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '分站名称',
  `domain_prefix` varchar(50) NOT NULL DEFAULT '' COMMENT '域名前缀',
  `admin_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分站超管ID',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 0关闭 1正常 2冻结',
  `rate_group_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '默认费率分组',
  `settle_template` varchar(50) NOT NULL DEFAULT 'T+1' COMMENT '结算周期模板',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分站表';

-- ----------------------------
-- 管理员表
-- ----------------------------
DROP TABLE IF EXISTS `jz_admin`;
CREATE TABLE `jz_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(30) NOT NULL DEFAULT 'admin' COMMENT 'super/admin/operator',
  `subsite_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分站ID，总站为0',
  `real_name` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `two_factor` tinyint(1) NOT NULL DEFAULT 0 COMMENT '二次认证开关',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  KEY `idx_subsite` (`subsite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- ----------------------------
-- 商户表
-- ----------------------------
DROP TABLE IF EXISTS `jz_merchant`;
CREATE TABLE `jz_merchant` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `shop_name` varchar(100) NOT NULL DEFAULT '',
  `shop_id` varchar(30) NOT NULL DEFAULT '' COMMENT '店铺ID',
  `subsite_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '所属分站',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `deposit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '保证金',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '可用余额',
  `frozen_balance` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '冻结余额',
  `rate_group_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0待审核 1正常 2封禁 3冻结',
  `audit_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '审核备注/驳回原因',
  `audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `invite_code_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '使用的邀请码ID（0为自助注册）',
  `open_time` datetime DEFAULT NULL COMMENT '开店时间',
  `last_login_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_shop_id` (`shop_id`),
  KEY `idx_subsite` (`subsite_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商户表';

-- ----------------------------
-- 邀请码表
-- ----------------------------
DROP TABLE IF EXISTS `jz_invite_code`;
CREATE TABLE `jz_invite_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL DEFAULT '' COMMENT '邀请码',
  `subsite_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分站归属（0为总站全局）',
  `rate_group_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '默认费率分组',
  `max_uses` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '最大使用次数（0为不限）',
  `used_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '已使用次数',
  `expire_time` datetime DEFAULT NULL COMMENT '过期时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0失效 1有效',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`),
  KEY `idx_subsite` (`subsite_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='邀请码表';

-- ----------------------------
-- 用户表
-- ----------------------------
DROP TABLE IF EXISTS `jz_user`;
CREATE TABLE `jz_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `subsite_id` int(11) unsigned NOT NULL DEFAULT 0,
  `level` int(11) unsigned NOT NULL DEFAULT 1,
  `group_id` int(11) unsigned NOT NULL DEFAULT 0,
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_subsite` (`subsite_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- ----------------------------
-- 商品分类表
-- ----------------------------
DROP TABLE IF EXISTS `jz_category`;
CREATE TABLE `jz_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  `is_nav` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'C端导航显示',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类表';

-- ----------------------------
-- 商品表
-- ----------------------------
DROP TABLE IF EXISTS `jz_goods`;
CREATE TABLE `jz_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `subsite_id` int(11) unsigned NOT NULL DEFAULT 0,
  `category_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `cover` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int(11) NOT NULL DEFAULT 0,
  `sold` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1卡密 2人工 3自动',
  `content` text COMMENT '商品说明富文本',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0下架 1上架 2违规下架',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '下架原因',
  `low_stock` int(11) NOT NULL DEFAULT 10 COMMENT '库存预警阈值',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_subsite` (`subsite_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品表';

-- ----------------------------
-- 卡密表
-- ----------------------------
DROP TABLE IF EXISTS `jz_card`;
CREATE TABLE `jz_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned NOT NULL DEFAULT 0,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `content` text NOT NULL COMMENT '卡密内容',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未售 1已售',
  `order_id` varchar(50) NOT NULL DEFAULT '',
  `sale_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_goods` (`goods_id`),
  KEY `idx_status` (`status`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='卡密表';

-- ----------------------------
-- 订单表
-- ----------------------------
DROP TABLE IF EXISTS `jz_order`;
CREATE TABLE `jz_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `subsite_id` int(11) unsigned NOT NULL DEFAULT 0,
  `goods_id` int(11) unsigned NOT NULL DEFAULT 0,
  `goods_name` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pay_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
  `pay_channel` varchar(30) NOT NULL DEFAULT '' COMMENT '支付渠道',
  `pay_time` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待支付 1已支付 2已发货 3已完成 4退款中 5已关闭',
  `risk_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT '风控标记',
  `client_ip` varchar(50) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_subsite` (`subsite_id`),
  KEY `idx_status` (`status`),
  KEY `idx_user` (`user_id`),
  KEY `idx_pay_time` (`pay_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';

-- ----------------------------
-- 费率分组表
-- ----------------------------
DROP TABLE IF EXISTS `jz_rate_group`;
CREATE TABLE `jz_rate_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT '默认手续费率',
  `max_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '封顶费率',
  `cost_rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT '成本费率',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='费率分组表';

-- ----------------------------
-- 支付通道配置表
-- ----------------------------
DROP TABLE IF EXISTS `jz_payment_channel`;
CREATE TABLE `jz_payment_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL DEFAULT '' COMMENT '通道编码',
  `name` varchar(50) NOT NULL DEFAULT '',
  `config` text COMMENT '配置JSON',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(11) NOT NULL DEFAULT 0,
  `scope` varchar(20) NOT NULL DEFAULT 'global' COMMENT 'global/subsite/merchant',
  `scope_id` int(11) unsigned NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_code` (`code`),
  KEY `idx_scope` (`scope`,`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付通道配置表';

-- ----------------------------
-- 结算记录表
-- ----------------------------
DROP TABLE IF EXISTS `jz_settlement`;
CREATE TABLE `jz_settlement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `settle_no` varchar(50) NOT NULL DEFAULT '',
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `real_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待处理 1处理中 2成功 3失败',
  `channel` varchar(30) NOT NULL DEFAULT '',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_settle_no` (`settle_no`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='结算记录表';

-- ----------------------------
-- 操作日志表
-- ----------------------------
DROP TABLE IF EXISTS `jz_admin_log`;
CREATE TABLE `jz_admin_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_name` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL DEFAULT '',
  `content` text,
  `ip` varchar(50) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin` (`admin_id`),
  KEY `idx_create` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表';

-- ----------------------------
-- 禁售关键词表
-- ----------------------------
DROP TABLE IF EXISTS `jz_banned_keyword`;
CREATE TABLE `jz_banned_keyword` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(100) NOT NULL DEFAULT '' COMMENT '禁售关键词',
  `type` varchar(20) NOT NULL DEFAULT 'goods' COMMENT 'goods-商品名 category-类目',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_keyword` (`keyword`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='禁售关键词表';

-- ----------------------------
-- 初始化默认费率分组
-- ----------------------------
INSERT INTO `jz_rate_group` (`name`, `rate`, `max_fee`, `cost_rate`, `is_default`) VALUES
('默认分组', '0.0200', '50.00', '0.0060', 1),
('VIP 分组', '0.0100', '30.00', '0.0060', 0);

SET FOREIGN_KEY_CHECKS = 1;
