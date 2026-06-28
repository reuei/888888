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
  `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `id_card_no` varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号',
  `id_card_front` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证正面',
  `id_card_back` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证反面',
  `auth_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '实名认证状态 0未认证 1待审核 2已认证 3驳回',
  `auth_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '实名认证审核备注',
  `auth_time` datetime DEFAULT NULL COMMENT '实名认证审核时间',
  `pay_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '收款方式 0平台代收 1个人收款码 2第三方接口',
  `pay_alipay_qr` varchar(255) NOT NULL DEFAULT '' COMMENT '支付宝收款二维码',
  `pay_wechat_qr` varchar(255) NOT NULL DEFAULT '' COMMENT '微信收款二维码',
  `pay_alipay_account` varchar(100) NOT NULL DEFAULT '' COMMENT '支付宝账号',
  `pay_wechat_account` varchar(100) NOT NULL DEFAULT '' COMMENT '微信账号',
  `pay_api_url` varchar(255) NOT NULL DEFAULT '' COMMENT '第三方支付接口地址',
  `pay_api_key` varchar(255) NOT NULL DEFAULT '' COMMENT '第三方支付接口KEY',
  `pay_api_secret` varchar(255) NOT NULL DEFAULT '' COMMENT '第三方支付接口SECRET',
  `guide_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '引导页开关 0关闭 1开启',
  `guide_title` varchar(100) NOT NULL DEFAULT '' COMMENT '引导页标题',
  `guide_content` text COMMENT '引导页内容（支持HTML）',
  `guide_bg_image` varchar(255) NOT NULL DEFAULT '' COMMENT '引导页背景图',
  `guide_button_text` varchar(50) NOT NULL DEFAULT '立即进入' COMMENT '引导页按钮文字',
  `guide_button_link` varchar(255) NOT NULL DEFAULT '' COMMENT '引导页按钮链接',
  `domain_prefix` varchar(50) NOT NULL DEFAULT '' COMMENT '子域名前缀',
  `domain_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '子域名状态 0未设置 1已启用 2审核中',
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
  `last_login_ip` varchar(50) NOT NULL DEFAULT '',
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
  `contact` varchar(100) NOT NULL DEFAULT '' COMMENT '买家联系方式',
  `deliver_content` text COMMENT '发货内容',
  `coupon_code` varchar(32) NOT NULL DEFAULT '' COMMENT '使用的优惠券码',
  `coupon_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券抵扣金额',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_subsite` (`subsite_id`),
  KEY `idx_status` (`status`),
  KEY `idx_user` (`user_id`),
  KEY `idx_pay_time` (`pay_time`),
  KEY `idx_contact` (`contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';

-- ----------------------------
-- 文章公告表
-- ----------------------------
DROP TABLE IF EXISTS `jz_article`;
CREATE TABLE `jz_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text COMMENT '文章内容',
  `category` varchar(50) NOT NULL DEFAULT 'notice' COMMENT 'notice-公告 help-帮助',
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文章公告表';

-- ----------------------------
-- 系统配置表
-- ----------------------------
DROP TABLE IF EXISTS `jz_config`;
CREATE TABLE `jz_config` (
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
-- 广告位表
-- ----------------------------
DROP TABLE IF EXISTS `jz_ad`;
CREATE TABLE `jz_ad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '广告标题',
  `image` varchar(500) NOT NULL DEFAULT '' COMMENT '图片URL',
  `link` varchar(500) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `position` varchar(50) NOT NULL DEFAULT 'home_banner' COMMENT 'home_banner-首页轮播 home_top-首页顶部 category_top-分类顶部',
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_position` (`position`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告位表';

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
  `channel` varchar(30) NOT NULL DEFAULT '' COMMENT '结算渠道',
  `account` varchar(255) NOT NULL DEFAULT '' COMMENT '收款账号',
  `account_name` varchar(100) NOT NULL DEFAULT '' COMMENT '收款人姓名',
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
-- 投诉表
-- ----------------------------
DROP TABLE IF EXISTS `jz_complaint`;
CREATE TABLE `jz_complaint` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '订单ID',
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '商户ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '投诉类型',
  `content` text COMMENT '投诉内容',
  `images` text COMMENT '图片凭证JSON',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待处理 1已处理',
  `result` varchar(50) NOT NULL DEFAULT '' COMMENT '处理结果',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '处理备注',
  `handle_time` datetime DEFAULT NULL COMMENT '处理时间',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投诉表';

-- ----------------------------
-- 用户等级分组表
-- ----------------------------
DROP TABLE IF EXISTS `jz_user_group`;
CREATE TABLE `jz_user_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '分组名称',
  `level` int(11) unsigned NOT NULL DEFAULT 1 COMMENT '等级',
  `discount` decimal(5,4) NOT NULL DEFAULT '1.0000' COMMENT '折扣率',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '等级图标',
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户等级分组表';

-- ----------------------------
-- 资金流水表
-- ----------------------------
DROP TABLE IF EXISTS `jz_finance_flow`;
CREATE TABLE `jz_finance_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '商户ID',
  `order_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '订单ID',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT 'income-收入 refund-退款 fee-手续费 freeze-冻结 unfreeze-解冻',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '变动后余额',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资金流水表';

-- ----------------------------
-- 代理商品表
-- ----------------------------
DROP TABLE IF EXISTS `jz_agent_goods`;
CREATE TABLE `jz_agent_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '关联商品ID',
  `commission_rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT '佣金比例',
  `commission_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '固定佣金金额',
  `commission_mode` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1按比例 2按固定金额',
  `multi_level` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否多级分销 0否 1是',
  `level2_rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT '二级佣金比例',
  `level3_rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT '三级佣金比例',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_goods` (`goods_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='代理商品表';

-- ----------------------------
-- 代理用户表
-- ----------------------------
DROP TABLE IF EXISTS `jz_agent_user`;
CREATE TABLE `jz_agent_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `parent_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '上级代理用户ID',
  `level` int(11) unsigned NOT NULL DEFAULT 1 COMMENT '代理层级',
  `path` varchar(500) NOT NULL DEFAULT '' COMMENT '代理路径 如: 0,1,2,',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1正常',
  `total_commission` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '累计佣金',
  `settled_commission` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '已结算佣金',
  `pending_commission` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '待结算佣金',
  `invite_code` varchar(32) NOT NULL DEFAULT '' COMMENT '邀请码',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user` (`user_id`),
  UNIQUE KEY `uk_invite` (`invite_code`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='代理用户表';

-- ----------------------------
-- 佣金记录表
-- ----------------------------
DROP TABLE IF EXISTS `jz_agent_commission`;
CREATE TABLE `jz_agent_commission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '来源订单ID',
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '订单编号',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '获得佣金用户ID',
  `from_user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '消费用户ID',
  `goods_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '商品ID',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `commission` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `level` int(11) unsigned NOT NULL DEFAULT 1 COMMENT '佣金层级',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待结算 1已结算 2已取消',
  `settle_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '结算单ID',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_settle` (`settle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='佣金记录表';

-- ----------------------------
-- 代理结算表
-- ----------------------------
DROP TABLE IF EXISTS `jz_agent_settlement`;
CREATE TABLE `jz_agent_settlement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `settle_no` varchar(50) NOT NULL DEFAULT '' COMMENT '结算单号',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '代理用户ID',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '结算金额',
  `fee` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `real_amount` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '实际到账',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待处理 1处理中 2成功 3失败',
  `channel` varchar(30) NOT NULL DEFAULT '' COMMENT '结算渠道',
  `account` varchar(255) NOT NULL DEFAULT '' COMMENT '收款账号',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `pay_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_settle_no` (`settle_no`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='代理结算表';

-- ----------------------------
-- 优惠券表
-- ----------------------------
DROP TABLE IF EXISTS `jz_coupon`;
CREATE TABLE `jz_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `code` varchar(32) NOT NULL DEFAULT '' COMMENT '优惠券码（留空为领取券）',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1满减 2折扣 3固定金额',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额/折扣率',
  `min_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最低使用金额',
  `total_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '发放总量（0为不限）',
  `used_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '已使用数量',
  `receive_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '已领取数量',
  `limit_per_user` int(11) unsigned NOT NULL DEFAULT 1 COMMENT '每人限领',
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `scope` varchar(20) NOT NULL DEFAULT 'all' COMMENT '适用范围 all/category/goods',
  `scope_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '范围ID',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='优惠券表';

-- ----------------------------
-- 用户优惠券表
-- ----------------------------
DROP TABLE IF EXISTS `jz_user_coupon`;
CREATE TABLE `jz_user_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `coupon_id` int(11) unsigned NOT NULL DEFAULT 0,
  `coupon_code` varchar(32) NOT NULL DEFAULT '',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `min_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `type` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未使用 1已使用 2已过期',
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `use_time` datetime DEFAULT NULL,
  `expire_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_coupon` (`coupon_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户优惠券表';

-- ----------------------------
-- 登录日志表
-- ----------------------------
DROP TABLE IF EXISTS `jz_login_log`;
CREATE TABLE `jz_login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '登录账号',
  `type` varchar(20) NOT NULL DEFAULT 'admin' COMMENT '登录类型 admin/merchant/user',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT '登录IP',
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
-- IP 黑名单表
-- ----------------------------
DROP TABLE IF EXISTS `jz_ip_blacklist`;
CREATE TABLE `jz_ip_blacklist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP 或 IP 段，如 192.168.1.1 或 192.168.1.%',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '封禁原因',
  `expire_time` datetime DEFAULT NULL COMMENT '过期时间（NULL 为永久）',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip` (`ip`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='IP 黑名单表';

-- ----------------------------
-- 客服会话表
-- ----------------------------
DROP TABLE IF EXISTS `jz_chat_session`;
CREATE TABLE `jz_chat_session` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '商户ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID（0为游客）',
  `user_fingerprint` varchar(32) NOT NULL DEFAULT '' COMMENT '浏览器指纹',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '访客昵称',
  `contact` varchar(100) NOT NULL DEFAULT '' COMMENT '联系方式',
  `last_message` varchar(500) NOT NULL DEFAULT '' COMMENT '最后一条消息摘要',
  `unread_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '商户未读消息数',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0已关闭 1进行中',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_update_time` (`update_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客服会话表';

-- ----------------------------
-- 客服消息表
-- ----------------------------
DROP TABLE IF EXISTS `jz_chat_message`;
CREATE TABLE `jz_chat_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '会话ID',
  `sender_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0游客 1商户 2系统',
  `content` text COMMENT '消息内容',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未读 1已读',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_sender` (`sender_type`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客服消息表';

-- ----------------------------
-- 初始化默认费率分组
-- ----------------------------
INSERT INTO `jz_rate_group` (`name`, `rate`, `max_fee`, `cost_rate`, `is_default`) VALUES
('默认分组', '0.0200', '50.00', '0.0060', 1),
('VIP 分组', '0.0100', '30.00', '0.0060', 0);

SET FOREIGN_KEY_CHECKS = 1;
