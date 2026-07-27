-- QEEFG授权站数据库结构
-- 支持MySQL 5.7
-- 字符集: utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 用户表
-- ----------------------------
DROP TABLE IF EXISTS `qf_users`;
CREATE TABLE `qf_users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `qq` varchar(20) DEFAULT NULL COMMENT 'QQ号',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '余额',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态:0禁用1正常',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- 产品表
-- ----------------------------
DROP TABLE IF EXISTS `qf_products`;
CREATE TABLE `qf_products` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '产品ID',
  `name` varchar(100) NOT NULL COMMENT '产品名称',
  `description` text COMMENT '产品描述',
  `type` varchar(20) DEFAULT 'software' COMMENT '类型:software软件,plugin插件',
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `duration` int(11) DEFAULT '0' COMMENT '有效期(天):0永久',
  `features` text COMMENT '产品特性JSON',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态:0下架1上架',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='产品表';

-- ----------------------------
-- 授权表
-- ----------------------------
DROP TABLE IF EXISTS `qf_licenses`;
CREATE TABLE `qf_licenses` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '授权ID',
  `license_key` varchar(64) NOT NULL COMMENT '授权密钥',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `product_id` int(11) UNSIGNED NOT NULL COMMENT '产品ID',
  `order_id` int(11) UNSIGNED DEFAULT NULL COMMENT '订单ID',
  `device_limit` int(11) DEFAULT '1' COMMENT '设备限制数',
  `devices` text COMMENT '已绑定设备JSON',
  `expires_at` datetime DEFAULT NULL COMMENT '过期时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态:0禁用1正常',
  `activated_at` datetime DEFAULT NULL COMMENT '激活时间',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `license_key` (`license_key`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='授权表';

-- ----------------------------
-- 订单表
-- ----------------------------
DROP TABLE IF EXISTS `qf_orders`;
CREATE TABLE `qf_orders` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `order_no` varchar(32) NOT NULL COMMENT '订单号',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `product_id` int(11) UNSIGNED NOT NULL COMMENT '产品ID',
  `amount` decimal(10,2) NOT NULL COMMENT '金额',
  `payment_method` varchar(20) DEFAULT NULL COMMENT '支付方式',
  `payment_status` tinyint(1) DEFAULT '0' COMMENT '支付状态:0未支付1已支付',
  `payment_time` datetime DEFAULT NULL COMMENT '支付时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '订单状态:0待支付1已完成2已取消',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
-- 余额日志表
-- ----------------------------
DROP TABLE IF EXISTS `qf_balance_logs`;
CREATE TABLE `qf_balance_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `type` varchar(20) NOT NULL COMMENT '类型:recharge充值,consume消费,refund退款',
  `amount` decimal(10,2) NOT NULL COMMENT '金额',
  `balance_before` decimal(10,2) NOT NULL COMMENT '变动前余额',
  `balance_after` decimal(10,2) NOT NULL COMMENT '变动后余额',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='余额日志表';

-- ----------------------------
-- 登录日志表
-- ----------------------------
DROP TABLE IF EXISTS `qf_login_logs`;
CREATE TABLE `qf_login_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT '用户ID',
  `username` varchar(50) DEFAULT NULL COMMENT '登录用户名',
  `ip` varchar(50) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '用户代理',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态:0失败1成功',
  `message` varchar(255) DEFAULT NULL COMMENT '消息',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='登录日志表';

-- ----------------------------
-- 操作日志表
-- ----------------------------
DROP TABLE IF EXISTS `qf_operation_logs`;
CREATE TABLE `qf_operation_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `action` varchar(50) NOT NULL COMMENT '操作',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `ip` varchar(50) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '用户代理',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作日志表';

-- ----------------------------
-- 管理员表
-- ----------------------------
DROP TABLE IF EXISTS `qf_admins`;
CREATE TABLE `qf_admins` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- ----------------------------
-- 意见反馈表
-- ----------------------------
DROP TABLE IF EXISTS `qf_feedback`;
CREATE TABLE `qf_feedback` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `content` text NOT NULL COMMENT '反馈内容',
  `contact` varchar(100) DEFAULT NULL COMMENT '联系方式',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态:0待处理1已处理',
  `reply` text COMMENT '回复',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='意见反馈表';

-- ----------------------------
-- 系统设置表
-- ----------------------------
DROP TABLE IF EXISTS `qf_settings`;
CREATE TABLE `qf_settings` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL COMMENT '配置键',
  `value` text COMMENT '配置值',
  `description` varchar(255) DEFAULT NULL COMMENT '说明',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统设置表';

-- 插入默认管理员
INSERT INTO `qf_admins` VALUES (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@qeefg.com', 1, NOW(), NOW());

-- 插入默认系统设置
INSERT INTO `qf_settings` VALUES
(1, 'site_name', 'QEEFG授权站', '网站名称'),
(2, 'site_url', 'https://auth.qeefg.com', '网站地址'),
(3, 'site_keywords', '授权站,软件授权,授权管理', 'SEO关键词'),
(4, 'site_description', '便捷快速的授权管理系统', '网站描述'),
(5, 'qq_group', '123456789', 'QQ群'),
(6, 'contact_qq', '123456789', '联系QQ');

SET FOREIGN_KEY_CHECKS = 1;