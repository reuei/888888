# QEEFG授权站系统

## 项目简介

QEEFG授权站是一个专业的软件授权与许可证服务平台，提供授权发放、在线验证、设备管理与授权服务支持。

## 技术栈

- **PHP**: >= 8.1
- **MySQL**: = 5.7
- **框架**: ThinkPHP 8.0
- **运行目录**: /public
- **伪静态**: ThinkPHP

## 功能特性

### 前台功能
- ✅ 首页展示（产品展示、统计数据）
- ✅ 授权查询（公开查询授权信息）
- ✅ 文档中心（使用帮助和文档）
- ✅ 用户注册/登录
- ✅ 用户中心
  - 仪表盘（数据概览）
  - 工作台（最近授权和订单）
  - 余额管理（充值、明细）
  - 产品中心（购买产品、我的产品）
  - 插件中心（购买插件、我的插件）
  - 日志查询（余额明细、登录日志、操作日志）
  - 账户设置（个人信息、修改密码）
  - 意见反馈

### 后台功能
- ✅ 管理员登录
- ✅ 数据统计（用户数、产品数、授权数、订单数、收入）
- ✅ 用户管理（列表、状态管理）
- ✅ 产品管理（添加、编辑、删除、上下架）
- ✅ 授权管理（查看、禁用、删除）
- ✅ 订单管理（查看、状态管理）
- ✅ 系统设置（网站配置）

## 安装步骤

### 1. 环境要求
- PHP >= 8.1
- MySQL = 5.7
- Apache/Nginx web服务器
- 开启ThinkPHP伪静态

### 2. 安装流程

```bash
# 1. 克隆项目
git clone https://github.com/your-username/qeefg-auth.git

# 2. 进入项目目录
cd qeefg-auth

# 3. 安装依赖
composer install

# 4. 复制环境配置文件
cp .env.example .env

# 5. 配置数据库信息
# 编辑 .env 文件，修改数据库连接信息

# 6. 导入数据库
mysql -u root -p your_database < install.sql

# 7. 配置Web服务器
# 将网站根目录指向 public 目录
# 开启伪静态（参考下方配置）

# 8. 设置目录权限
chmod -R 755 runtime
chmod -R 755 storage
```

### 3. Nginx配置示例

```nginx
server {
    listen 80;
    server_name auth.qeefg.com;
    root /var/www/qeefg-auth/public;
    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 4. Apache配置

确保 `public/.htaccess` 文件存在，内容如下：

```apache
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```

### 5. 默认账号

**后台管理员**
- 用户名: `admin`
- 密码: `password`

## 项目结构

```
qeefg-auth/
├── app/                 # 应用目录
│   ├── controller/      # 控制器
│   ├── model/           # 模型
│   ├── view/            # 视图模板
│   ├── middleware/      # 中间件
│   └── validate/        # 验证器
├── config/              # 配置文件
├── public/              # 公共目录（Web根目录）
│   ├── static/          # 静态资源
│   │   ├── css/         # 样式文件
│   │   ├── js/          # JavaScript文件
│   │   └── images/      # 图片资源
│   ├── index.php        # 入口文件
│   └── .htaccess        # Apache伪静态
├── route/               # 路由定义
├── runtime/             # 运行时目录
├── storage/             # 存储目录
├── vendor/              # 第三方依赖
├── .env.example         # 环境配置示例
├── composer.json        # Composer配置
├── install.sql          # 数据库安装文件
└── README.md            # 说明文档
```

## 数据库表结构

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

## 开发说明

### 控制器开发
所有控制器继承自 `app\controller\BaseController`，基类提供了常用方法：
- `success()` - 返回成功响应
- `error()` - 返回失败响应
- `validate()` - 数据验证
- `fetch()` - 渲染模板

### 模型开发
所有模型继承自 `app\model\BaseModel`，基类配置了自动时间戳。

### 视图开发
使用ThinkPHP模板语法：
- 变量输出: `{$variable}`
- 条件判断: `{if} {else} {/if}`
- 循环: `{foreach} {/foreach}`
- 模板继承: `{extend} {block}`

## 安全建议

1. 修改默认管理员密码
2. 设置 `APP_DEBUG = false`（生产环境）
3. 定期备份数据库
4. 使用HTTPS协议
5. 配置防火墙规则
6. 定期更新依赖包

## 许可证

MIT License

## 联系方式

- QQ群: 123456789
- 联系QQ: 123456789
- 邮箱: admin@qeefg.com

## 更新日志

### v1.0.0 (2026-07-27)
- 初始版本发布
- 完成基础授权管理功能
- 实现用户中心和后台管理