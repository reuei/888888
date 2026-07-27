# QEEFG授权站系统 v2.0

## 项目简介

QEEFG授权站是一个专业的软件授权与许可证服务平台，提供授权发放、在线验证、设备管理与授权服务支持。

**版本**: v2.0 (独立版)
**技术栈**: PHP 8.1+ / MySQL 5.7 / PDO

## 特性

- ✅ **轻量级架构** - 不依赖框架，直接使用原生PHP+PDO
- ✅ **简洁设计** - 白色背景，方框UI，整洁布局
- ✅ **完整功能** - 用户中心、后台管理、授权查询
- ✅ **易于部署** - 无需composer install，解压即用
- ✅ **响应式设计** - 适配PC和移动端
- ✅ **安全可靠** - PDO预处理、密码哈希、Session管理

## 快速开始

### 环境要求

- PHP >= 8.1
- MySQL = 5.7
- PDO扩展
- Session扩展

### 安装步骤

1. **下载项目**
   ```bash
   git clone https://github.com/your-username/qeefg-auth.git
   ```

2. **配置数据库**
   - 复制 `config/database.php.example` 为 `config/database.php`
   - 修改数据库连接信息

3. **导入数据库**
   ```bash
   mysql -u root -p your_database < install.sql
   ```

4. **配置Web服务器**
   - 网站根目录指向 `/public`
   - 开启URL重写

5. **访问系统**
   - 前台: `http://your-domain.com/`
   - 后台: `http://your-domain.com/admin/login`

### 默认账号

**管理员**
- 用户名: `admin`
- 密码: `admin123`

## 功能列表

### 前台功能
- 首页展示（产品、统计）
- 授权查询（公开）
- 文档中心
- 用户注册/登录
- 用户中心
  - 仪表盘
  - 工作台
  - 产品中心
  - 我的产品
  - 余额管理
  - 账户设置

### 后台功能
- 管理员登录
- 数据统计
- 用户管理
- 产品管理
- 系统设置

## 项目结构

```
qeefg-auth/
├── app/
│   ├── controller/        # 控制器
│   │   ├── BaseController.php
│   │   ├── Index.php
│   │   ├── User.php
│   │   └── Admin.php
│   └── view/              # 视图模板
│       ├── index/
│       ├── user/
│       └── admin/
├── config/
│   └── database.php       # 数据库配置
├── public/
│   ├── index.php          # 入口文件
│   ├── static/            # 静态资源
│   └── .htaccess          # Apache重写
├── install.sql            # 数据库安装
└── README.md              # 说明文档
```

## Web服务器配置

### Nginx配置

```nginx
server {
    listen 80;
    server_name auth.qeefg.com;
    root /var/www/qeefg-auth/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### Apache配置

确保 `public/.htaccess` 存在：

```apache
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```

## 数据库表

- `qf_users` - 用户表
- `qf_products` - 产品表
- `qf_licenses` - 授权表
- `qf_orders` - 订单表
- `qf_admins` - 管理员表
- `qf_settings` - 系统设置表
- `qf_balance_logs` - 余额日志表
- `qf_login_logs` - 登录日志表
- `qf_operation_logs` - 操作日志表
- `qf_feedback` - 意见反馈表

## 技术特点

### 架构设计
- **MVC模式**: 控制器-视图分离
- **PDO数据库**: 预处理防SQL注入
- **Session管理**: 安全的用户认证
- **简单路由**: 轻量级路由系统

### 安全措施
- 密码哈希存储
- PDO预处理语句
- Session安全配置
- 输入验证过滤

### 前端设计
- 整洁白色背景
- 方框形状UI
- 响应式布局
- 现代CSS样式

## 开发说明

### 添加新页面

1. 在 `app/controller/` 创建控制器方法
2. 在 `public/index.php` 添加路由
3. 在 `app/view/` 创建视图模板

### 数据库操作

```php
// 查询
$stmt = $this->db->prepare("SELECT * FROM table WHERE id = ?");
$stmt->execute([$id]);
$result = $stmt->fetch();

// 插入
$stmt = $this->db->prepare("INSERT INTO table (field) VALUES (?)");
$stmt->execute([$value]);
```

## 更新日志

### v2.0 (2026-07-27)
- 重构为独立版，不依赖框架
- 修复500错误问题
- 添加目录绑定检查
- 优化控制器和视图
- 改进错误提示页面

### v1.0 (2026-07-27)
- 初始版本发布

## 许可证

MIT License

## 联系方式

- QQ群: 123456789
- 邮箱: admin@qeefg.com
- 网站: https://qeefg.com