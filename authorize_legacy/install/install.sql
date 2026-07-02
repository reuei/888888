-- QEEFG 授权站 / 插件寄售系统数据库结构 v1.0.0

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `qef_admin`;
CREATE TABLE `qef_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(30) NOT NULL DEFAULT 'admin' COMMENT 'super/admin',
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

DROP TABLE IF EXISTS `qef_user`;
CREATE TABLE `qef_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

DROP TABLE IF EXISTS `qef_config`;
CREATE TABLE `qef_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cfg_key` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` text,
  `cfg_group` varchar(50) NOT NULL DEFAULT 'base',
  `description` varchar(255) NOT NULL DEFAULT '',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`cfg_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

DROP TABLE IF EXISTS `qef_admin_log`;
CREATE TABLE `qef_admin_log` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员操作日志表';

DROP TABLE IF EXISTS `qef_article`;
CREATE TABLE `qef_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `category` varchar(50) NOT NULL DEFAULT 'notice' COMMENT 'notice-公告 help-帮助',
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章公告表';

DROP TABLE IF EXISTS `qef_product`;
CREATE TABLE `qef_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `license_type` varchar(20) NOT NULL DEFAULT 'code' COMMENT 'code-授权码 domain-域名授权',
  `valid_days` int(11) NOT NULL DEFAULT 0 COMMENT '0-永久 其他-有效期天数',
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0下架 1上架',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`license_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='授权码产品表';

DROP TABLE IF EXISTS `qef_license`;
CREATE TABLE `qef_license` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auth_code` varchar(64) NOT NULL DEFAULT '',
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `license_type` varchar(20) NOT NULL DEFAULT 'code' COMMENT 'code-授权码 domain-域名授权',
  `auth_domain` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1正常',
  `expire_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_auth_code` (`auth_code`),
  KEY `idx_product` (`product_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_domain` (`auth_domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='授权码表';

DROP TABLE IF EXISTS `qef_plugin`;
CREATE TABLE `qef_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '发布者ID',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `version` varchar(50) NOT NULL DEFAULT '',
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `file_md5` varchar(32) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待审核 1上架 2下架',
  `download_count` int(11) unsigned NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='插件表';

DROP TABLE IF EXISTS `qef_order`;
CREATE TABLE `qef_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `item_type` varchar(20) NOT NULL DEFAULT 'product' COMMENT 'product-授权产品 plugin-插件',
  `item_id` int(11) unsigned NOT NULL DEFAULT 0,
  `item_name` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pay_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pay_channel` varchar(30) NOT NULL DEFAULT 'balance' COMMENT 'balance-余额 offline-线下',
  `pay_time` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待支付 1已支付 2已取消',
  `license_id` int(11) unsigned NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_item` (`item_type`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';

DROP TABLE IF EXISTS `qef_user_plugin`;
CREATE TABLE `qef_user_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `plugin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_plugin` (`user_id`, `plugin_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户已购插件表';

DROP TABLE IF EXISTS `qef_version`;
CREATE TABLE `qef_version` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(50) NOT NULL DEFAULT '',
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `file_md5` varchar(32) NOT NULL DEFAULT '',
  `file_size` int(11) unsigned NOT NULL DEFAULT 0,
  `release_date` date DEFAULT NULL,
  `update_desc` text,
  `force_update` tinyint(1) NOT NULL DEFAULT 0,
  `is_latest` tinyint(1) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_version` (`version`),
  KEY `idx_latest` (`is_latest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='版本更新包表';

DROP TABLE IF EXISTS `qef_recharge`;
CREATE TABLE `qef_recharge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pay_channel` varchar(30) NOT NULL DEFAULT 'offline',
  `pay_remark` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待处理 1已到账 2已拒绝',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='充值申请表';

INSERT INTO `qef_config` (`cfg_key`, `cfg_value`, `cfg_group`, `description`) VALUES
('site_name', 'QEEFG 寄售系统售卖网站', 'base', '站点名称'),
('currency_unit', '¥', 'base', '货币单位'),
('copyright', 'QEEFG v1.0.0', 'base', '版权信息'),
('api_key', '', 'base', '主站 API 通信密钥');

INSERT INTO `qef_article` (`title`, `content`, `category`, `sort`, `status`) VALUES
('欢迎使用 QEEFG 寄售系统售卖网站', '<p>本系统提供授权码销售、域名授权管理及插件市场功能。</p>', 'notice', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
