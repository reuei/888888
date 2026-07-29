-- QEEFG授权站数据库结构
-- 支持MySQL 5.7+
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
  `login_ip` varchar(50) DEFAULT NULL COMMENT '最后登录IP',
  `login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `is_developer` tinyint(1) DEFAULT '0' COMMENT '是否开发者:0否1是',
  `developer_status` varchar(20) DEFAULT NULL COMMENT '开发者状态:pending审核中,approved已通过,rejected已拒绝',
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
  `type` varchar(20) DEFAULT 'software' COMMENT '类型:software软件,service服务,other其他',
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `duration` int(11) DEFAULT '0' COMMENT '有效期(天):0永久',
  `download_file` varchar(255) DEFAULT NULL COMMENT '下载文件名',
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
  `type` varchar(20) DEFAULT 'feedback' COMMENT '类型:feedback反馈,report举报',
  `content` text NOT NULL COMMENT '反馈内容',
  `contact` varchar(100) DEFAULT NULL COMMENT '联系方式',
  `status` varchar(20) DEFAULT 'pending' COMMENT '状态:pending待处理,processing处理中,approved已通过,rejected已拒绝',
  `reply` text COMMENT '回复',
  `replied_at` datetime DEFAULT NULL COMMENT '回复时间',
  `reject_reason` varchar(255) DEFAULT NULL COMMENT '拒绝原因',
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

-- ----------------------------
-- 文档表
-- ----------------------------
DROP TABLE IF EXISTS `qf_documents`;
CREATE TABLE `qf_documents` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '文档标题',
  `content` text COMMENT '文档内容',
  `category` varchar(50) DEFAULT '未分类' COMMENT '分类',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文档表';

-- ----------------------------
-- 功能码表
-- ----------------------------
DROP TABLE IF EXISTS `qf_feature_codes`;
CREATE TABLE `qf_feature_codes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '名称',
  `code` varchar(100) NOT NULL COMMENT '功能码',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态:0禁用1正常',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='功能码表';

-- ----------------------------
-- 消息通知表
-- ----------------------------
DROP TABLE IF EXISTS `qf_messages`;
CREATE TABLE `qf_messages` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID:0表示全部用户',
  `title` varchar(255) NOT NULL COMMENT '消息标题',
  `content` text NOT NULL COMMENT '消息内容',
  `type` varchar(20) DEFAULT 'system' COMMENT '类型:system系统,plugin插件,developer开发者,feedback反馈',
  `is_read` tinyint(1) DEFAULT '0' COMMENT '是否已读:0未读1已读',
  `is_email_sent` tinyint(1) DEFAULT '0' COMMENT '是否已发送邮件:0未发送1已发送',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态:0隐藏1显示',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='消息通知表';

-- ----------------------------
-- 开发者申请表
-- ----------------------------
DROP TABLE IF EXISTS `qf_developer_applications`;
CREATE TABLE `qf_developer_applications` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `real_name` varchar(50) NOT NULL COMMENT '真实姓名',
  `reason` text NOT NULL COMMENT '申请理由',
  `status` enum('pending','approved','rejected') DEFAULT 'pending' COMMENT '状态:pending审核中,approved已通过,rejected已拒绝',
  `reject_reason` varchar(255) DEFAULT NULL COMMENT '拒绝原因',
  `reviewed_at` datetime DEFAULT NULL COMMENT '审核时间',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='开发者申请表';

-- ----------------------------
-- 插件表(用户提交)
-- ----------------------------
DROP TABLE IF EXISTS `qf_plugins`;
CREATE TABLE `qf_plugins` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `name` varchar(100) NOT NULL COMMENT '插件名称',
  `description` text COMMENT '插件描述',
  `version` varchar(20) DEFAULT NULL COMMENT '版本号',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `status` enum('pending','approved','rejected') DEFAULT 'pending' COMMENT '状态:pending审核中,approved已通过,rejected已拒绝',
  `reject_reason` varchar(255) DEFAULT NULL COMMENT '拒绝原因',
  `file_path` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `download_count` int(11) DEFAULT '0' COMMENT '下载次数',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='插件表';

-- ----------------------------
-- 邮箱池
-- ----------------------------
DROP TABLE IF EXISTS `qf_email_pool`;
CREATE TABLE `qf_email_pool` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL COMMENT '邮箱地址',
  `smtp_user` varchar(100) DEFAULT NULL COMMENT 'SMTP用户名',
  `smtp_pass` varchar(255) DEFAULT NULL COMMENT 'SMTP密码',
  `smtp_host` varchar(100) NOT NULL COMMENT 'SMTP主机',
  `smtp_port` int(11) DEFAULT '587' COMMENT 'SMTP端口',
  `smtp_encryption` varchar(10) DEFAULT 'tls' COMMENT '加密方式:tls/ssl',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态:0禁用1启用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮箱池';

-- ----------------------------
-- 支付通道
-- ----------------------------
DROP TABLE IF EXISTS `qf_payment_channels`;
CREATE TABLE `qf_payment_channels` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '通道名称',
  `channel_code` varchar(50) NOT NULL COMMENT '通道代码',
  `api_url` varchar(255) DEFAULT NULL COMMENT 'API地址',
  `merchant_id` varchar(100) DEFAULT NULL COMMENT '商户ID',
  `merchant_key` varchar(255) DEFAULT NULL COMMENT '商户密钥',
  `fee_rate` decimal(10,4) DEFAULT '0.0000' COMMENT '费率',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态:0禁用1启用',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付通道';

-- ----------------------------
-- 邮件模板
-- ----------------------------
DROP TABLE IF EXISTS `qf_email_templates`;
CREATE TABLE `qf_email_templates` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '模板名称',
  `code` varchar(50) NOT NULL COMMENT '唯一标识',
  `subject` varchar(255) NOT NULL COMMENT '邮件主题',
  `content` text COMMENT '邮件内容(HTML)',
  `description` varchar(255) DEFAULT NULL COMMENT '模板描述',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态:0禁用1启用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮件模板';

-- ----------------------------
-- 上传文件表
-- ----------------------------
DROP TABLE IF EXISTS `qf_upload_files`;
CREATE TABLE `qf_upload_files` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_type` varchar(20) NOT NULL COMMENT '文件类型:logo/favicon/product',
  `file_name` varchar(255) NOT NULL COMMENT '存储文件名',
  `file_path` varchar(255) NOT NULL COMMENT '文件路径',
  `original_name` varchar(255) NOT NULL COMMENT '原始文件名',
  `file_size` int(11) DEFAULT '0' COMMENT '文件大小(字节)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `file_type` (`file_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='上传文件表';

-- ----------------------------
-- 验证码表
-- ----------------------------
DROP TABLE IF EXISTS `qf_verify_codes`;
CREATE TABLE `qf_verify_codes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `target` varchar(100) NOT NULL COMMENT '目标(邮箱/手机号)',
  `type` varchar(10) NOT NULL COMMENT '类型:email/phone',
  `code` varchar(10) NOT NULL COMMENT '验证码',
  `expires_at` datetime NOT NULL COMMENT '过期时间',
  `used` tinyint(1) DEFAULT '0' COMMENT '是否已使用:0未使用1已使用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target` (`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='验证码表';

-- 插入默认管理员 (密码: admin123)
INSERT INTO `qf_admins` VALUES (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@qeefg.com', 1, NOW(), NOW());

-- 插入默认系统设置
INSERT INTO `qf_settings` VALUES
(1, 'site_name', '熵云', '网站名称'),
(2, 'site_url', 'https://auth.qeefg.com', '网站地址'),
(3, 'site_keywords', '授权站,软件授权,授权管理', 'SEO关键词'),
(4, 'site_description', '便捷快速的授权管理系统', '网站描述'),
(5, 'qq_group', '123456789', 'QQ群'),
(6, 'contact_qq', '123456789', '联系QQ'),
(7, 'require_email_register', '1', '注册是否需要邮箱验证:0否1是'),
(8, 'require_phone_verify', '0', '是否需要手机验证:0否1是'),
(9, 'site_logo', '', '网站LOGO路径'),
(10, 'site_favicon', '', '网站Favicon路径');

-- 插入默认邮件模板
INSERT INTO `qf_email_templates` VALUES
(1, '注册验证码', 'register_verify', '邮箱验证码', '<p>您的验证码是：<strong>{code}</strong>，有效期{expire}分钟，请勿泄露给他人。</p>', '用户注册时发送的邮箱验证码模板', 1, NOW(), NOW()),
(2, '登录验证码', 'login_verify', '登录验证码', '<p>您的登录验证码是：<strong>{code}</strong>，有效期{expire}分钟，请勿泄露给他人。</p>', '用户登录时发送的邮箱验证码模板', 1, NOW(), NOW()),
(3, '插件审核通过', 'plugin_approved', '插件审核通过通知', '<p>您好！您提交的插件 <strong>{plugin_name}</strong> 已审核通过，现已上架。</p>', '插件审核通过时发送的通知模板', 1, NOW(), NOW()),
(4, '插件审核被拒', 'plugin_rejected', '插件审核结果通知', '<p>您好！您提交的插件 <strong>{plugin_name}</strong> 未通过审核，原因：{reason}</p>', '插件审核被拒时发送的通知模板', 1, NOW(), NOW()),
(5, '开发者申请通过', 'developer_approved', '开发者申请通过通知', '<p>恭喜！您的开发者申请已通过审核，现在可以提交插件了。</p>', '开发者申请通过时发送的通知模板', 1, NOW(), NOW()),
(6, '开发者申请被拒', 'developer_rejected', '开发者申请结果通知', '<p>很遗憾，您的开发者申请未通过审核，原因：{reason}</p>', '开发者申请被拒时发送的通知模板', 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;