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
  `points` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '积分余额',
  `growth_value` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '成长值',
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
  `is_source` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否货源商品 0否 1是',
  `source_goods_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '来源商品ID（货源对接）',
  `source_merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '货源提供商户ID',
  `source_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '货源成本价',
  `is_seckill` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否秒杀 0否 1是',
  `seckill_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '秒杀价',
  `seckill_start` datetime DEFAULT NULL COMMENT '秒杀开始时间',
  `seckill_end` datetime DEFAULT NULL COMMENT '秒杀结束时间',
  `seckill_stock` int(11) NOT NULL DEFAULT 0 COMMENT '秒杀库存',
  `seckill_sold` int(11) NOT NULL DEFAULT 0 COMMENT '秒杀已售',
  `is_discount` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否限时折扣 0否 1是',
  `discount_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '折扣价',
  `discount_start` datetime DEFAULT NULL COMMENT '折扣开始时间',
  `discount_end` datetime DEFAULT NULL COMMENT '折扣结束时间',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_subsite` (`subsite_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`),
  KEY `idx_source` (`is_source`, `source_goods_id`)
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
-- 积分规则表
-- ----------------------------
DROP TABLE IF EXISTS `jz_points_rule`;
CREATE TABLE `jz_points_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT 'register-注册 login-登录 order-下单 review-评价 invite-邀请',
  `points` int(11) NOT NULL DEFAULT 0 COMMENT '奖励积分（负数为扣减）',
  `growth_value` int(11) NOT NULL DEFAULT 0 COMMENT '奖励成长值',
  `limit_type` varchar(20) NOT NULL DEFAULT 'day' COMMENT '限制周期 day-每日 week-每周 month-每月 once-一次性 total-累计',
  `limit_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '周期内限制次数（0为不限）',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `sort` int(11) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='积分规则表';

-- ----------------------------
-- 积分流水表
-- ----------------------------
DROP TABLE IF EXISTS `jz_points_log`;
CREATE TABLE `jz_points_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT 'register login order review invite redeem system',
  `points` int(11) NOT NULL DEFAULT 0 COMMENT '变动积分（正为增加，负为扣减）',
  `before_points` int(11) unsigned NOT NULL DEFAULT 0,
  `after_points` int(11) unsigned NOT NULL DEFAULT 0,
  `remark` varchar(255) NOT NULL DEFAULT '',
  `related_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '关联订单/记录ID',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='积分流水表';

-- ----------------------------
-- 积分商品表
-- ----------------------------
DROP TABLE IF EXISTS `jz_points_goods`;
CREATE TABLE `jz_points_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '商品标题',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '商品图片',
  `description` text COMMENT '商品详情',
  `points` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '兑换积分',
  `stock` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '库存',
  `sold` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '已兑换数量',
  `sort` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0下架 1上架',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='积分商品表';

-- ----------------------------
-- 积分兑换订单表
-- ----------------------------
DROP TABLE IF EXISTS `jz_points_order`;
CREATE TABLE `jz_points_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '兑换单号',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `points_goods_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `points` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '消耗积分',
  `quantity` int(11) unsigned NOT NULL DEFAULT 1,
  `deliver_content` text COMMENT '发放内容（卡密/优惠券/实物地址）',
  `contact` varchar(100) NOT NULL DEFAULT '' COMMENT '联系方式/收货地址',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0待处理 1已发放 2已取消',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='积分兑换订单表';

-- ----------------------------
-- 初始化默认费率分组
-- ----------------------------
INSERT INTO `jz_rate_group` (`name`, `rate`, `max_fee`, `cost_rate`, `is_default`) VALUES
('默认分组', '0.0200', '50.00', '0.0060', 1),
('VIP 分组', '0.0100', '30.00', '0.0060', 0);

-- ----------------------------
-- 初始化默认积分规则
-- ----------------------------
INSERT INTO `jz_points_rule` (`name`, `type`, `points`, `growth_value`, `limit_type`, `limit_count`, `status`, `sort`) VALUES
('新用户注册', 'register', 100, 50, 'once', 1, 1, 1),
('每日登录', 'login', 5, 2, 'day', 1, 1, 2),
('下单奖励', 'order', 10, 10, 'day', 10, 1, 3),
('邀请好友', 'invite', 50, 30, 'day', 5, 1, 4);

-- ----------------------------
-- 数据备份表
-- ----------------------------
DROP TABLE IF EXISTS `jz_backup`;
CREATE TABLE `jz_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '备份名称',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '备份文件名',
  `file_size` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文件大小（字节）',
  `file_md5` varchar(32) NOT NULL DEFAULT '' COMMENT '文件MD5',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型 1手动备份 2自动备份',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 0正常 1已恢复 2已删除',
  `operator_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '操作人ID',
  `operator_name` varchar(50) NOT NULL DEFAULT '' COMMENT '操作人账号',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='数据备份表';

-- ----------------------------
-- 消息模板表
-- ----------------------------
DROP TABLE IF EXISTS `jz_message_template`;
CREATE TABLE `jz_message_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '模板类型 sms-短信 email-邮件',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '模板编码',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模板名称',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件主题（短信为空）',
  `content` text COMMENT '模板内容',
  `variables` varchar(500) NOT NULL DEFAULT '' COMMENT '可用变量，逗号分隔',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `sort` int(11) NOT NULL DEFAULT 0,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_type_code` (`type`, `code`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息模板表';

-- ----------------------------
-- 初始化默认消息模板
-- ----------------------------
INSERT INTO `jz_message_template` (`type`, `code`, `name`, `title`, `content`, `variables`, `status`, `description`, `sort`) VALUES
('email', 'register', '用户注册验证', '欢迎注册 {site_name}', '您好，{nickname}，欢迎注册 {site_name}，您的验证码是：{code}。', 'site_name,nickname,code', 1, '用户注册时发送', 1),
('email', 'order_paid', '订单支付成功', '订单支付成功通知', '您好，您的订单 {order_no} 已支付成功，商品：{goods_name}，金额：{amount}。', 'order_no,goods_name,amount', 1, '订单支付成功后发送', 2),
('email', 'withdraw_done', '提现处理通知', '提现处理结果', '您好，您的提现申请 {withdraw_no} 已处理，金额：{amount}，状态：{status}。', 'withdraw_no,amount,status', 1, '提现处理后发送', 3),
('sms', 'register', '用户注册验证', '', '您的验证码是：{code}，5分钟内有效。', 'code', 1, '用户注册时发送', 4),
('sms', 'order_paid', '订单支付成功', '', '您的订单 {order_no} 已支付成功，金额 {amount}，如有疑问请联系客服。', 'order_no,amount', 1, '订单支付成功后发送', 5);

-- ----------------------------
-- 短信发送日志表
-- ----------------------------
DROP TABLE IF EXISTS `jz_sms_log`;
CREATE TABLE `jz_sms_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `template_code` varchar(50) NOT NULL DEFAULT '' COMMENT '模板编码',
  `content` text COMMENT '短信内容',
  `gateway` varchar(50) NOT NULL DEFAULT '' COMMENT '短信网关',
  `result` varchar(500) NOT NULL DEFAULT '' COMMENT '发送结果',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0失败/调试 1成功',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mobile` (`mobile`),
  KEY `idx_template` (`template_code`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='短信发送日志表';

-- ----------------------------
-- 邮件发送日志表
-- ----------------------------
DROP TABLE IF EXISTS `jz_email_log`;
CREATE TABLE `jz_email_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL DEFAULT '' COMMENT '收件人',
  `template_code` varchar(50) NOT NULL DEFAULT '' COMMENT '模板编码',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件主题',
  `content` text COMMENT '邮件内容',
  `result` varchar(500) NOT NULL DEFAULT '' COMMENT '发送结果',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0失败 1成功',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipient` (`recipient`),
  KEY `idx_template` (`template_code`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='邮件发送日志表';

-- ----------------------------
-- 第三方登录绑定表
-- ----------------------------
DROP TABLE IF EXISTS `jz_oauth_bind`;
CREATE TABLE `jz_oauth_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT 'qq/weixin/github',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT '平台OpenID',
  `unionid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信UnionID',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '平台昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `access_token` varchar(255) NOT NULL DEFAULT '' COMMENT '访问令牌',
  `refresh_token` varchar(255) NOT NULL DEFAULT '' COMMENT '刷新令牌',
  `expire_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '令牌过期时间戳',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_type_openid` (`type`, `openid`),
  KEY `idx_user` (`user_id`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='第三方登录绑定表';

-- ----------------------------
-- API 密钥表
-- ----------------------------
DROP TABLE IF EXISTS `jz_api_key`;
CREATE TABLE `jz_api_key` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '密钥名称',
  `app_id` varchar(50) NOT NULL DEFAULT '' COMMENT '应用ID',
  `app_secret` varchar(255) NOT NULL DEFAULT '' COMMENT '应用密钥',
  `merchant_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '关联商户ID（0为总站）',
  `permissions` varchar(500) NOT NULL DEFAULT '' COMMENT '权限列表，逗号分隔',
  `ips` varchar(500) NOT NULL DEFAULT '' COMMENT '允许IP，逗号分隔',
  `request_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '请求次数',
  `last_request_time` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_app_id` (`app_id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API密钥表';

-- ----------------------------
-- API 请求日志表
-- ----------------------------
DROP TABLE IF EXISTS `jz_api_log`;
CREATE TABLE `jz_api_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(50) NOT NULL DEFAULT '' COMMENT '应用ID',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '接口动作',
  `params` text COMMENT '请求参数',
  `result` varchar(500) NOT NULL DEFAULT '' COMMENT '返回结果摘要',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT '请求IP',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0失败 1成功',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_app_id` (`app_id`),
  KEY `idx_action` (`action`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API请求日志表';

-- ----------------------------
-- 插件配置表
-- ----------------------------
DROP TABLE IF EXISTS `jz_plugin`;
CREATE TABLE `jz_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '插件编码',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '插件名称',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT 'webhook-回调 bot-机器人',
  `config` text COMMENT '插件配置JSON',
  `event_types` varchar(255) NOT NULL DEFAULT '' COMMENT '监听事件，逗号分隔',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='插件配置表';

-- ----------------------------
-- 插件执行日志表
-- ----------------------------
DROP TABLE IF EXISTS `jz_plugin_log`;
CREATE TABLE `jz_plugin_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `event_type` varchar(50) NOT NULL DEFAULT '' COMMENT '事件类型',
  `payload` text COMMENT '请求内容',
  `response` text COMMENT '响应内容',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0失败 1成功',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_plugin` (`plugin_id`),
  KEY `idx_event` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='插件执行日志表';

SET FOREIGN_KEY_CHECKS = 1;
