-- ============================================
-- 玄武发卡 · 企业级多商户发卡平台
-- 数据库结构 v1.0.0
-- 架构: S端(总站长) + B端(商户) + C端(用户)
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------
-- 1. 分站表 (station)
-- ------------------------------
DROP TABLE IF EXISTS `station`;
CREATE TABLE `station` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '分站名称',
  `domain` varchar(255) NOT NULL DEFAULT '' COMMENT '域名',
  `theme_color` varchar(20) NOT NULL DEFAULT '#2F6BFF' COMMENT '主题色',
  `super_admin_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分站超管ID',
  `settle_mode` enum('t0','t1','t7') NOT NULL DEFAULT 't1' COMMENT '结算模式',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 0停用',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分站表';

-- ------------------------------
-- 2. 管理员表 (admin)
-- ------------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分站ID(0=总站)',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码hash',
  `role` enum('super','station_admin','merchant') NOT NULL DEFAULT 'merchant' COMMENT '角色',
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '关联商户ID(role=merchant时)',
  `last_login_time` datetime DEFAULT NULL,
  `last_login_ip` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- ------------------------------
-- 3. 商户表 (merchant)
-- ------------------------------
DROP TABLE IF EXISTS `merchant`;
CREATE TABLE `merchant` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分站ID',
  `shop_name` varchar(100) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `deposit` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '保证金',
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '余额',
  `frozen_balance` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '冻结余额',
  `fee_group_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '费率分组ID',
  `status` enum('normal','pending','banned') NOT NULL DEFAULT 'pending' COMMENT '状态',
  `invite_code` varchar(50) NOT NULL DEFAULT '' COMMENT '邀请码',
  `realname_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '实名状态 0未认证 1已认证',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `id_card` varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号',
  `subdomain` varchar(100) NOT NULL DEFAULT '' COMMENT '引导页子域名',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_station` (`station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商户表';

-- ------------------------------
-- 4. 商品分类 (category)
-- ------------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名',
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '父分类ID',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类';

-- ------------------------------
-- 5. 商品表 (product)
-- ------------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int(11) unsigned NOT NULL DEFAULT 0,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '商户ID',
  `category_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分类ID',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '商品名',
  `description` text COMMENT '商品描述(富文本)',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '价格',
  `stock` int(11) NOT NULL DEFAULT 0 COMMENT '库存',
  `sold_count` int(11) NOT NULL DEFAULT 0 COMMENT '已售数量',
  `delivery_mode` enum('auto','manual') NOT NULL DEFAULT 'auto' COMMENT '发卡模式',
  `template` varchar(50) NOT NULL DEFAULT 'default' COMMENT '购卡页模板',
  `status` enum('on','off') NOT NULL DEFAULT 'on' COMMENT '上下架',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_station` (`station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品表';

-- ------------------------------
-- 6. 卡密表 (card_secret)
-- ------------------------------
DROP TABLE IF EXISTS `card_secret`;
CREATE TABLE `card_secret` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `station_id` int(11) unsigned NOT NULL DEFAULT 0,
  `content` text NOT NULL COMMENT '卡密明文(加密存储)',
  `status` enum('unsold','sold','locked','voided') NOT NULL DEFAULT 'unsold',
  `order_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '售出关联订单',
  `import_batch_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '导入批次',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_batch` (`import_batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='卡密表';

-- ------------------------------
-- 7. 导入批次 (import_batch)
-- ------------------------------
DROP TABLE IF EXISTS `import_batch`;
CREATE TABLE `import_batch` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `operator_id` int(11) unsigned NOT NULL DEFAULT 0,
  `operator_role` enum('s','b') NOT NULL DEFAULT 'b',
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL DEFAULT 0 COMMENT '总数',
  `success` int(11) NOT NULL DEFAULT 0 COMMENT '成功数',
  `fail` int(11) NOT NULL DEFAULT 0 COMMENT '失败数',
  `deliver_mode` enum('sequential','random') NOT NULL DEFAULT 'sequential' COMMENT '发卡模式',
  `error_file` varchar(255) NOT NULL DEFAULT '' COMMENT '错误行清单',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='导入批次';

-- ------------------------------
-- 8. 禁售目录 (banned_product)
-- ------------------------------
DROP TABLE IF EXISTS `banned_product`;
CREATE TABLE `banned_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(200) NOT NULL DEFAULT '',
  `category` varchar(100) NOT NULL DEFAULT '',
  `match_type` enum('keyword','category') NOT NULL DEFAULT 'keyword',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='禁售目录';

-- ------------------------------
-- 9. 库存预警 (stock_alert)
-- ------------------------------
DROP TABLE IF EXISTS `stock_alert`;
CREATE TABLE `stock_alert` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `threshold` int(11) NOT NULL DEFAULT 10 COMMENT '预警阈值',
  `status` enum('alerting','resolved') NOT NULL DEFAULT 'alerting',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='库存预警';

-- ------------------------------
-- 10. 订单表 (order)
-- ------------------------------
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `station_id` int(11) unsigned NOT NULL DEFAULT 0,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `product_name` varchar(200) NOT NULL DEFAULT '' COMMENT '商品快照',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '订单金额',
  `real_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '实际支付金额(含随机尾数)',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT '数量',
  `status` enum('pending','paid','refunded','closed') NOT NULL DEFAULT 'pending',
  `pay_method` varchar(50) NOT NULL DEFAULT '' COMMENT '支付方式',
  `pay_time` datetime DEFAULT NULL,
  `remark` varchar(500) NOT NULL DEFAULT '',
  `is_abnormal` tinyint(1) NOT NULL DEFAULT 0 COMMENT '异常标记',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_station` (`station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';

-- ------------------------------
-- 11. 用户表 (user)
-- ------------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int(11) unsigned NOT NULL DEFAULT 0,
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `level_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户等级',
  `group_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户分组',
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `realname_status` tinyint(1) NOT NULL DEFAULT 0,
  `realname` varchar(50) NOT NULL DEFAULT '',
  `id_card` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_station` (`station_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- ------------------------------
-- 12. 代理关系树 (agent_relation)
-- ------------------------------
DROP TABLE IF EXISTS `agent_relation`;
CREATE TABLE `agent_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '上级商户',
  `child_merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '下级代理',
  `level` int(11) NOT NULL DEFAULT 1 COMMENT '层级深度',
  `path` varchar(500) NOT NULL DEFAULT '' COMMENT '关系链 如 1/8/23',
  `commission_rate` decimal(5,4) NOT NULL DEFAULT 0.1000 COMMENT '默认佣金比例',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_merchant_id`),
  KEY `idx_child` (`child_merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='代理关系树';

-- ------------------------------
-- 13. 代理对接商品 (agent_product)
-- ------------------------------
DROP TABLE IF EXISTS `agent_product`;
CREATE TABLE `agent_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `source_product_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '源商品ID',
  `agent_merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '代理商户ID',
  `cost_price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '上级设成本价',
  `sale_price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '代理售价',
  `audit_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `status` enum('on','off') NOT NULL DEFAULT 'on',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_agent` (`agent_merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='代理对接商品';

-- ------------------------------
-- 14. 分润明细 (profit_share)
-- ------------------------------
DROP TABLE IF EXISTS `profit_share`;
CREATE TABLE `profit_share` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `role` enum('platform','parent','agent') NOT NULL DEFAULT 'platform',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rate` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `settle_status` enum('settled','pending') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分润明细';

-- ------------------------------
-- 15. 费率分组 (fee_group)
-- ------------------------------
DROP TABLE IF EXISTS `fee_group`;
CREATE TABLE `fee_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `rate` decimal(5,4) NOT NULL DEFAULT 0.0200 COMMENT '费率',
  `description` varchar(500) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='费率分组';

-- ------------------------------
-- 16. 支付通道 (pay_channel)
-- ------------------------------
DROP TABLE IF EXISTS `pay_channel`;
CREATE TABLE `pay_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gateway_code` varchar(50) NOT NULL DEFAULT '' COMMENT '网关编码 微信/支付宝/易支付/码支付/USDT/信汇',
  `gateway_name` varchar(100) NOT NULL DEFAULT '' COMMENT '网关名称',
  `scope_type` enum('global','group','merchant') NOT NULL DEFAULT 'global',
  `scope_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分组ID或商户ID',
  `enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '通道开关',
  `rate` decimal(5,4) NOT NULL DEFAULT 0.0060 COMMENT '费率',
  `cap_fee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '封顶费率',
  `cost_fee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '成本费率',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '网关排序',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_scope` (`scope_type`, `scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付通道';

-- ------------------------------
-- 17. 风控策略 (pay_risk_rule)
-- ------------------------------
DROP TABLE IF EXISTS `pay_risk_rule`;
CREATE TABLE `pay_risk_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scope_type` enum('group','merchant') NOT NULL DEFAULT 'merchant',
  `scope_id` int(11) unsigned NOT NULL DEFAULT 0,
  `gateway_code` varchar(50) NOT NULL DEFAULT '',
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.01,
  `max_amount` decimal(10,2) NOT NULL DEFAULT 99999.00,
  `random_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '金额随机化',
  `random_range` varchar(50) NOT NULL DEFAULT '0.01-0.99' COMMENT '随机区间',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='风控策略';

-- ------------------------------
-- 18. 随机金额占用 (payment_lock)
-- ------------------------------
DROP TABLE IF EXISTS `payment_lock`;
CREATE TABLE `payment_lock` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `real_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `gateway_code` varchar(50) NOT NULL DEFAULT '',
  `expire_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_amount` (`real_amount`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='随机金额占用';

-- ------------------------------
-- 19. 结算记录 (settlement)
-- ------------------------------
DROP TABLE IF EXISTS `settlement`;
CREATE TABLE `settlement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `station_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cycle` varchar(20) NOT NULL DEFAULT '' COMMENT '结算周期 T+0/T+1/T+7',
  `gross_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '总金额',
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '手续费',
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '到账金额',
  `status` enum('settled','pending') NOT NULL DEFAULT 'pending',
  `settled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_merchant` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='结算记录';

-- ------------------------------
-- 20. 投诉 (complaint)
-- ------------------------------
DROP TABLE IF EXISTS `complaint`;
CREATE TABLE `complaint` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `reason` text NOT NULL,
  `evidence` text COMMENT '图片证据JSON',
  `status` enum('pending','resolved','rejected') NOT NULL DEFAULT 'pending',
  `arbitration` text COMMENT '仲裁结果(S端)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投诉';

-- ------------------------------
-- 21. 公告/文章 (announcement)
-- ------------------------------
DROP TABLE IF EXISTS `announcement`;
CREATE TABLE `announcement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(200) NOT NULL DEFAULT '',
  `content` text COMMENT 'HTML富文本',
  `type` enum('popup','notice','article') NOT NULL DEFAULT 'notice',
  `is_popup` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否弹窗',
  `countdown` int(11) NOT NULL DEFAULT 0 COMMENT '倒计时秒数',
  `is_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '置顶',
  `publish_at` datetime DEFAULT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'draft',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公告/文章';

-- ------------------------------
-- 22. 广告位 (ad_slot)
-- ------------------------------
DROP TABLE IF EXISTS `ad_slot`;
CREATE TABLE `ad_slot` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `position` varchar(100) NOT NULL DEFAULT '',
  `content` text COMMENT '广告内容HTML',
  `status` enum('on','off') NOT NULL DEFAULT 'on',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告位';

-- ------------------------------
-- 23. 优惠券 (coupon)
-- ------------------------------
DROP TABLE IF EXISTS `coupon`;
CREATE TABLE `coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `batch` varchar(50) NOT NULL DEFAULT '' COMMENT '批次',
  `type` enum('fixed','percent') NOT NULL DEFAULT 'fixed',
  `value` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '减免或折扣',
  `threshold` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '满减门槛',
  `total` int(11) NOT NULL DEFAULT 0 COMMENT '发行总量',
  `received` int(11) NOT NULL DEFAULT 0 COMMENT '已领取',
  `used` int(11) NOT NULL DEFAULT 0 COMMENT '已使用',
  `status` enum('active','expired') NOT NULL DEFAULT 'active',
  `expire_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='优惠券';

-- ------------------------------
-- 24. 模板 (template)
-- ------------------------------
DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `type` enum('pc','mobile','card') NOT NULL DEFAULT 'pc',
  `preview` varchar(255) NOT NULL DEFAULT '',
  `status` enum('on','off') NOT NULL DEFAULT 'on',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='模板';

-- ------------------------------
-- 25. 操作日志 (operation_log)
-- ------------------------------
DROP TABLE IF EXISTS `operation_log`;
CREATE TABLE `operation_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `operator_id` int(11) unsigned NOT NULL DEFAULT 0,
  `operator_role` enum('s','b','c') NOT NULL DEFAULT 's',
  `operator_name` varchar(50) NOT NULL DEFAULT '',
  `module` varchar(50) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL DEFAULT '',
  `detail` text,
  `ip` varchar(50) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_operator` (`operator_id`, `operator_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志';

-- ------------------------------
-- 26. 客服消息 (cs_message)
-- ------------------------------
DROP TABLE IF EXISTS `cs_message`;
CREATE TABLE `cs_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0,
  `content` text NOT NULL,
  `direction` enum('in','out') NOT NULL DEFAULT 'in',
  `status` enum('read','unread') NOT NULL DEFAULT 'unread',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_merchant_user` (`merchant_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客服消息';

-- ------------------------------
-- 27. 用户等级 (user_level)
-- ------------------------------
DROP TABLE IF EXISTS `user_level`;
CREATE TABLE `user_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '最低消费',
  `discount` decimal(3,2) NOT NULL DEFAULT 1.00 COMMENT '折扣',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户等级';

-- ------------------------------
-- 28. 用户分组 (user_group)
-- ------------------------------
DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `discount` decimal(3,2) NOT NULL DEFAULT 1.00,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户分组';

-- ------------------------------
-- 29. 邀请码 (invite_code)
-- ------------------------------
DROP TABLE IF EXISTS `invite_code`;
CREATE TABLE `invite_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '',
  `max_uses` int(11) NOT NULL DEFAULT 1,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `expire_at` datetime DEFAULT NULL,
  `status` enum('active','expired','disabled') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='邀请码';

-- ============================================
-- 初始数据
-- ============================================

INSERT INTO `admin` (`username`, `password`, `role`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super', 1);

INSERT INTO `fee_group` (`name`, `rate`, `description`) VALUES
('标准费率', 0.0200, '默认2%费率'),
('VIP费率', 0.0100, 'VIP商户1%费率'),
('大客户费率', 0.0050, '大客户0.5%费率');

INSERT INTO `pay_channel` (`gateway_code`, `gateway_name`, `scope_type`, `enabled`, `rate`, `sort`) VALUES
('wechat', '微信支付', 'global', 1, 0.0060, 1),
('alipay', '支付宝', 'global', 1, 0.0060, 2),
('epay', '易支付', 'global', 1, 0.0050, 3),
('codepay', '码支付', 'global', 0, 0.0040, 4),
('usdt', 'USDT', 'global', 1, 0.0030, 5),
('xinhui', '信汇', 'global', 0, 0.0050, 6);

INSERT INTO `category` (`name`, `sort`, `status`) VALUES
('虚拟商品', 1, 1),
('充值卡', 2, 1),
('礼品卡', 3, 1),
('优惠券', 4, 1);

INSERT INTO `template` (`name`, `type`, `status`) VALUES
('PC经典版', 'pc', 'on'),
('PC简约版', 'pc', 'on'),
('PC商务版', 'pc', 'on'),
('M极简版', 'mobile', 'on'),
('M卡片版', 'mobile', 'on'),
('M列表版', 'mobile', 'on'),
('M瀑布版', 'mobile', 'on');

SET FOREIGN_KEY_CHECKS = 1;
