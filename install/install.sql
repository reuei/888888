-- 玄武发卡 v1.0.5 数据库结构

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `xw_admin`;
CREATE TABLE `xw_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(30) NOT NULL DEFAULT 'admin',
  `real_name` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_user`;
CREATE TABLE `xw_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `level` int(11) unsigned NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `register_ip` varchar(50) NOT NULL DEFAULT '',
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(50) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_category`;
CREATE TABLE `xw_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_goods`;
CREATE TABLE `xw_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `cover` varchar(255) NOT NULL DEFAULT '',
  `images` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int(11) NOT NULL DEFAULT 0,
  `sold` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 1,
  `description` text,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(11) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_card`;
CREATE TABLE `xw_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned NOT NULL DEFAULT 0,
  `content` text NOT NULL,
  `remark` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `sale_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_goods` (`goods_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_order`;
CREATE TABLE `xw_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `goods_id` int(11) unsigned NOT NULL DEFAULT 0,
  `goods_name` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pay_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pay_channel` varchar(30) NOT NULL DEFAULT '',
  `pay_time` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `contact` varchar(100) NOT NULL DEFAULT '',
  `card_info` text,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_article`;
CREATE TABLE `xw_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `category` varchar(50) NOT NULL DEFAULT 'notice',
  `sort` int(11) NOT NULL DEFAULT 0,
  `views` int(11) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_config`;
CREATE TABLE `xw_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cfg_key` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` text,
  `cfg_group` varchar(50) NOT NULL DEFAULT 'base',
  `description` varchar(255) NOT NULL DEFAULT '',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`cfg_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_message`;
CREATE TABLE `xw_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `type` varchar(30) NOT NULL DEFAULT 'system',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_login_log`;
CREATE TABLE `xw_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT 'user',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `location` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_admin_log`;
CREATE TABLE `xw_admin_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_name` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL DEFAULT '',
  `content` text,
  `ip` varchar(50) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_payment_channel`;
CREATE TABLE `xw_payment_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT 'pay',
  `config` text,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(11) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_withdraw`;
CREATE TABLE `xw_withdraw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `withdraw_no` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `real_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `channel` varchar(30) NOT NULL DEFAULT '',
  `account` varchar(255) NOT NULL DEFAULT '',
  `account_name` varchar(100) NOT NULL DEFAULT '',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_withdraw_no` (`withdraw_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_finance_flow`;
CREATE TABLE `xw_finance_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(30) NOT NULL DEFAULT '',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_shop`;
CREATE TABLE `xw_shop` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `shop_name` varchar(100) NOT NULL DEFAULT '',
  `shop_logo` varchar(255) NOT NULL DEFAULT '',
  `shop_desc` varchar(500) NOT NULL DEFAULT '',
  `real_name` varchar(50) NOT NULL DEFAULT '',
  `id_card_no` varchar(20) NOT NULL DEFAULT '',
  `id_card_front` varchar(255) NOT NULL DEFAULT '',
  `id_card_back` varchar(255) NOT NULL DEFAULT '',
  `realname_status` tinyint(1) NOT NULL DEFAULT 0,
  `company` varchar(100) NOT NULL DEFAULT '',
  `license_no` varchar(50) NOT NULL DEFAULT '',
  `license_image` varchar(255) NOT NULL DEFAULT '',
  `qualification_status` tinyint(1) NOT NULL DEFAULT 0,
  `cert_type` varchar(30) NOT NULL DEFAULT '',
  `cert_no` varchar(50) NOT NULL DEFAULT '',
  `cert_image` varchar(255) NOT NULL DEFAULT '',
  `cert_status` tinyint(1) NOT NULL DEFAULT 0,
  `risk_level` tinyint(1) NOT NULL DEFAULT 0,
  `risk_score` int(11) NOT NULL DEFAULT 0,
  `warn_count` int(11) NOT NULL DEFAULT 0,
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `frozen_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `service_qq` varchar(20) NOT NULL DEFAULT '',
  `service_phone` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_license`;
CREATE TABLE `xw_license` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '',
  `product` varchar(50) NOT NULL DEFAULT '',
  `version` varchar(20) NOT NULL DEFAULT '1.0.5',
  `max_domains` int(11) NOT NULL DEFAULT 3,
  `expire_time` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `owner` varchar(100) NOT NULL DEFAULT '',
  `contact` varchar(100) NOT NULL DEFAULT '',
  `remark` varchar(500) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_license_domain`;
CREATE TABLE `xw_license_domain` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `license_id` int(11) unsigned NOT NULL DEFAULT 0,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_license` (`license_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `xw_license_log`;
CREATE TABLE `xw_license_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `license` varchar(50) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `message` varchar(500) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `xw_category` (`name`, `icon`, `sort`, `status`) VALUES
('游戏点卡', 'game', 1, 1),
('视频会员', 'video', 2, 1),
('音乐会员', 'music', 3, 1),
('软件激活', 'software', 4, 1),
('学习教育', 'edu', 5, 1),
('生活服务', 'life', 6, 1);

INSERT INTO `xw_config` (`cfg_key`, `cfg_value`, `cfg_group`, `description`) VALUES
('site_name', '玄武发卡', 'base', '站点名称'),
('site_title', '玄武发卡网 - 专业的数字点卡交易平台', 'base', '站点标题'),
('site_keywords', '发卡网,点卡,游戏点卡,视频会员', 'base', '关键词'),
('site_description', '数字商品交易平台', 'base', '描述'),
('site_icp', '', 'base', '备案号'),
('site_copyright', '© 2026 玄武发卡', 'base', '版权');

INSERT INTO `xw_goods` (`category_id`, `name`, `price`, `original_price`, `stock`, `sold`, `sort`, `status`) VALUES
(2, '腾讯视频VIP会员月卡', 19.90, 30.00, 9999, 2345, 1, 1),
(2, '爱奇艺黄金会员季卡', 45.00, 68.00, 9999, 1876, 2, 1),
(3, '网易云音乐黑胶年卡', 88.00, 158.00, 9999, 3421, 3, 1),
(3, 'QQ音乐绿钻豪华月卡', 12.80, 18.00, 9999, 2156, 4, 1),
(1, 'Steam充值卡100元', 95.00, 100.00, 9999, 987, 5, 1),
(1, '王者荣耀点券1000', 98.00, 100.00, 9999, 4532, 6, 1),
(4, 'WPS超级会员年卡', 69.00, 179.00, 9999, 1543, 7, 1),
(4, '百度网盘超级会员月卡', 25.00, 30.00, 9999, 2876, 8, 1);

INSERT INTO `xw_article` (`title`, `content`, `category`, `sort`, `status`) VALUES
('欢迎使用玄武发卡 v1.0.5', '全新版本采用自研轻量框架，提供更流畅的体验。', 'notice', 1, 1),
('新用户首单立减5元', '新用户首单立减5元，限时活动。', 'notice', 2, 1),
('系统维护通知', '系统将于每周日凌晨2-4点进行例行维护。', 'notice', 3, 1);

INSERT INTO `xw_payment_channel` (`code`, `name`, `type`, `status`, `sort`) VALUES
('alipay', '支付宝', 'pay', 1, 1),
('wxpay', '微信支付', 'pay', 1, 2),
('qqpay', 'QQ钱包', 'pay', 0, 3);

INSERT INTO `xw_license` (`code`, `product`, `version`, `max_domains`, `expire_time`, `status`, `owner`) VALUES
('XUANWU-DEMO-2026', 'xuanwu_card', '1.0.5', 5, '2099-12-31 23:59:59', 1, '演示授权');

SET FOREIGN_KEY_CHECKS = 1;
