-- 玄武发卡网 v1.0.4 数据库结构

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 管理员表
-- ----------------------------
DROP TABLE IF EXISTS `xw_admin`;
CREATE TABLE `xw_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `role` varchar(30) NOT NULL DEFAULT 'admin' COMMENT 'super/admin/operator',
  `real_name` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- ----------------------------
-- 用户表
-- ----------------------------
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
  UNIQUE KEY `uk_username` (`username`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- ----------------------------
-- 商品分类表
-- ----------------------------
DROP TABLE IF EXISTS `xw_category`;
CREATE TABLE `xw_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类表';

-- ----------------------------
-- 商品表
-- ----------------------------
DROP TABLE IF EXISTS `xw_goods`;
CREATE TABLE `xw_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `cover` varchar(255) NOT NULL DEFAULT '',
  `images` text COMMENT '商品图片JSON',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `original_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int(11) NOT NULL DEFAULT 0,
  `sold` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1卡密 2人工 3自动',
  `description` text COMMENT '商品说明',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0下架 1上架',
  `sort` int(11) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品表';

-- ----------------------------
-- 卡密表
-- ----------------------------
DROP TABLE IF EXISTS `xw_card`;
CREATE TABLE `xw_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned NOT NULL DEFAULT 0,
  `content` text NOT NULL COMMENT '卡密内容',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未售 1已售',
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
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
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待支付 1已支付 2已发货 3已完成 4已关闭',
  `contact` varchar(100) NOT NULL DEFAULT '',
  `card_info` text COMMENT '发货卡密',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';

-- ----------------------------
-- 公告表
-- ----------------------------
DROP TABLE IF EXISTS `xw_article`;
CREATE TABLE `xw_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text COMMENT '文章内容',
  `category` varchar(50) NOT NULL DEFAULT 'notice' COMMENT 'notice-公告 help-帮助',
  `sort` int(11) NOT NULL DEFAULT 0,
  `views` int(11) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公告文章表';

-- ----------------------------
-- 系统配置表
-- ----------------------------
DROP TABLE IF EXISTS `xw_config`;
CREATE TABLE `xw_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cfg_key` varchar(100) NOT NULL DEFAULT '' COMMENT '配置键',
  `cfg_value` text COMMENT '配置值',
  `cfg_group` varchar(50) NOT NULL DEFAULT 'base' COMMENT '配置分组',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '配置说明',
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`cfg_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

-- ----------------------------
-- 消息表
-- ----------------------------
DROP TABLE IF EXISTS `xw_message`;
CREATE TABLE `xw_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '接收用户ID，0为全体',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text COMMENT '消息内容',
  `type` varchar(30) NOT NULL DEFAULT 'system' COMMENT 'system-系统 order-订单 finance-财务 activity-活动',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息表';

-- ----------------------------
-- 登录日志表
-- ----------------------------
DROP TABLE IF EXISTS `xw_login_log`;
CREATE TABLE `xw_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '登录账号',
  `type` varchar(20) NOT NULL DEFAULT 'user' COMMENT '登录类型 admin/user',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT '登录IP',
  `location` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0失败 1成功',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_ip` (`ip`),
  KEY `idx_status` (`status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登录日志表';

-- ----------------------------
-- 操作日志表
-- ----------------------------
DROP TABLE IF EXISTS `xw_admin_log`;
CREATE TABLE `xw_admin_log` (
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
-- 支付通道配置表
-- ----------------------------
DROP TABLE IF EXISTS `xw_payment_channel`;
CREATE TABLE `xw_payment_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL DEFAULT '' COMMENT '通道编码',
  `name` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT 'pay' COMMENT 'pay-支付 sms-短信',
  `config` text COMMENT '配置JSON',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort` int(11) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_code` (`code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付通道配置表';

-- ----------------------------
-- 提现记录表
-- ----------------------------
DROP TABLE IF EXISTS `xw_withdraw`;
CREATE TABLE `xw_withdraw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `withdraw_no` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `real_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待处理 1处理中 2成功 3失败',
  `channel` varchar(30) NOT NULL DEFAULT '',
  `account` varchar(255) NOT NULL DEFAULT '',
  `account_name` varchar(100) NOT NULL DEFAULT '',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_withdraw_no` (`withdraw_no`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='提现记录表';

-- ----------------------------
-- 资金流水表
-- ----------------------------
DROP TABLE IF EXISTS `xw_finance_flow`;
CREATE TABLE `xw_finance_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT 'income-收入 withdraw-提现 fee-手续费 recharge-充值',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资金流水表';

-- ----------------------------
-- 商户/店铺表
-- ----------------------------
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
  `realname_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '实名状态 0未认证 1待审核 2已认证 3驳回',
  `company` varchar(100) NOT NULL DEFAULT '',
  `license_no` varchar(50) NOT NULL DEFAULT '',
  `license_image` varchar(255) NOT NULL DEFAULT '',
  `qualification_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '资质状态 0未认证 1待审核 2已认证 3驳回',
  `cert_type` varchar(30) NOT NULL DEFAULT '',
  `cert_no` varchar(50) NOT NULL DEFAULT '',
  `cert_image` varchar(255) NOT NULL DEFAULT '',
  `cert_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '认证状态 0未认证 1待审核 2已认证 3驳回',
  `risk_level` tinyint(1) NOT NULL DEFAULT 0 COMMENT '风险等级 0低 1中 2高',
  `risk_score` int(11) NOT NULL DEFAULT 0,
  `warn_count` int(11) NOT NULL DEFAULT 0,
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `frozen_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `service_qq` varchar(20) NOT NULL DEFAULT '',
  `service_phone` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0待审核 1正常 2封禁',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='店铺表';

-- ----------------------------
-- 初始化默认数据
-- ----------------------------
INSERT INTO `xw_category` (`name`, `icon`, `sort`, `status`) VALUES
('游戏点卡', 'game', 1, 1),
('视频会员', 'video', 2, 1),
('音乐会员', 'music', 3, 1),
('软件激活', 'software', 4, 1),
('社交账号', 'social', 5, 1),
('学习教育', 'education', 6, 1),
('生活服务', 'life', 7, 1);

INSERT INTO `xw_goods` (`category_id`, `name`, `price`, `original_price`, `stock`, `sold`, `sort`, `status`) VALUES
(2, '腾讯视频VIP会员月卡', 19.90, 30.00, 9999, 2345, 1, 1),
(2, '爱奇艺黄金会员季卡', 45.00, 68.00, 9999, 1876, 2, 1),
(3, '网易云音乐黑胶年卡', 88.00, 158.00, 9999, 3421, 3, 1),
(3, 'QQ音乐绿钻豪华版月卡', 12.80, 18.00, 9999, 2156, 4, 1),
(1, 'Steam充值卡100元', 95.00, 100.00, 9999, 987, 5, 1),
(1, '王者荣耀点券1000', 98.00, 100.00, 9999, 4532, 6, 1);

INSERT INTO `xw_article` (`title`, `content`, `category`, `sort`, `status`) VALUES
('欢迎使用玄武发卡网', '欢迎使用玄武发卡网，这是一个专业的数字点卡交易平台。', 'notice', 1, 1),
('新用户首单立减5元', '新用户首单立减5元，限时优惠活动进行中！', 'notice', 2, 1),
('系统维护通知', '系统将于每周日凌晨2-4点进行例行维护。', 'notice', 3, 1);

INSERT INTO `xw_payment_channel` (`code`, `name`, `type`, `status`, `sort`) VALUES
('alipay', '支付宝', 'pay', 1, 1),
('wxpay', '微信支付', 'pay', 1, 2),
('qqpay', 'QQ钱包', 'pay', 0, 3),
('epay', '易支付', 'pay', 0, 4);

SET FOREIGN_KEY_CHECKS = 1;
