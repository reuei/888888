CREATE TABLE IF NOT EXISTS `qf_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `qq` varchar(20) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `balance` decimal(10,2) DEFAULT '0.00',
    `status` tinyint(1) DEFAULT '1',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `qf_admins` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `status` tinyint(1) DEFAULT '1',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `qf_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `price` decimal(10,2) NOT NULL,
    `icon` varchar(255) DEFAULT NULL,
    `status` tinyint(1) DEFAULT '1',
    `sort` int(11) DEFAULT '0',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `qf_licenses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `license_key` varchar(50) NOT NULL,
    `hardware_id` varchar(100) DEFAULT NULL,
    `expire_date` date DEFAULT NULL,
    `status` tinyint(1) DEFAULT '1',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `license_key` (`license_key`),
    KEY `product_id` (`product_id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `qf_orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_no` varchar(30) NOT NULL,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `status` tinyint(1) DEFAULT '0',
    `payment_method` varchar(20) DEFAULT NULL,
    `paid_at` datetime DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `order_no` (`order_no`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `qf_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `key` varchar(100) NOT NULL,
    `value` text DEFAULT NULL,
    `description` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `qf_feedbacks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `content` text NOT NULL,
    `status` tinyint(1) DEFAULT '0',
    `reply` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `replied_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `qf_admins` (`id`, `username`, `email`, `password`, `status`, `created_at`) VALUES
(1, 'admin', 'admin@qeefg.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2024-01-01 00:00:00');

INSERT INTO `qf_products` (`id`, `name`, `description`, `price`, `icon`, `status`, `sort`, `created_at`) VALUES
(1, 'QEEFG Core', 'QEEFG核心授权，提供基础功能支持', 99.00, 'https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt=software%20product%20icon%20blue%20modern&image_size=square', 1, 1, '2024-01-01 00:00:00'),
(2, 'QEEFG Pro', 'QEEFG专业版授权，提供完整功能支持', 199.00, 'https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt=professional%20software%20product%20icon%20blue%20premium&image_size=square', 1, 2, '2024-01-01 00:00:00'),
(3, 'QEEFG Enterprise', 'QEEFG企业版授权，提供高级功能和技术支持', 499.00, 'https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt=enterprise%20software%20product%20icon%20blue%20gold&image_size=square', 1, 3, '2024-01-01 00:00:00');

INSERT INTO `qf_settings` (`id`, `key`, `value`, `description`) VALUES
(1, 'site_name', 'QEEFG授权站', '网站名称'),
(2, 'site_title', 'QEEFG授权站 - 专业软件授权管理平台', '网站标题'),
(3, 'site_description', 'QEEFG授权站是一个专业的软件授权管理平台，提供软件授权、许可证管理、产品管理等服务。', '网站描述'),
(4, 'site_keywords', '授权,许可证,软件授权,授权管理', '网站关键词'),
(5, 'site_url', 'https://auth.qeefg.com', '网站URL'),
(6, 'site_email', 'support@qeefg.com', '联系邮箱'),
(7, 'site_qq', '123456789', '联系QQ'),
(8, 'theme_color', '#667eea', '主题颜色');
