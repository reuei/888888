# QEEFG授权站系统 v3.0

## 项目简介

QEEFG授权站是一个专业的软件授权与许可证服务平台，提供授权发放、在线验证、设备管理与授权服务支持。

**版本**: v3.0（稳定版）
**技术栈**: PHP 8.1+ / MySQL 5.7 / PDO

## ✨ 特性

- ✅ **稳定可靠** - 修复所有500错误和路由问题
- ✅ **目录绑定检测** - 自动检测运行目录是否正确
- ✅ **安装程序** - 一键安装，无需手动配置
- ✅ **完整功能** - 用户中心、后台管理、授权查询
- ✅ **简洁设计** - 白色背景，方框UI，整洁布局
- ✅ **响应式设计** - 适配PC和移动端
- ✅ **安全可靠** - PDO预处理、密码哈希、Session管理

## 🚀 快速开始

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

2. **配置Web服务器**
   - 将网站根目录指向 `/public` 目录
   - 开启URL重写

3. **运行安装程序**
   - 访问 `http://your-domain.com/install.php`
   - 按提示配置数据库信息
   - 点击安装完成

4. **访问系统**
   - 前台: `http://your-domain.com/`
   - 后台: `http://your-domain.com/admin/login`

### 默认账号

**管理员**
- 用户名: `admin`
- 密码: `admin123`

## 📱 功能列表

### 前台功能
- 首页展示（产品展示、统计数据、平台优势）
- 授权查询（公开查询授权信息）
- 文档中心（API文档、使用教程、常见问题）

### 用户中心
- 仪表盘（数据概览）
- 工作台（最近授权、最近订单）
- 产品中心（购买产品）
- 我的产品（授权列表）
- 余额管理（余额明细）
- 账户设置（个人信息、修改密码）
- 意见反馈

### 后台功能
- 管理员登录
- 数据统计（用户数、产品数、授权数、订单数、收入）
- 用户管理（列表、状态管理）
- 产品管理（添加、编辑、删除、上下架）
- 授权管理（查看、禁用、删除）
- 订单管理（查看、状态管理）
- 系统设置（网站配置）

## 📁 项目结构

```
qeefg-auth/
├── app/
│   ├── controller/        # 控制器（4个）
│   │   ├── BaseController.php
│   │   ├── Index.php
│   │   ├── User.php
│   │   └── Admin.php
│   └── view/              # 视图模板（21个）
│       ├── index/         # 首页相关
│       ├── user/          # 用户中心
│       └── admin/         # 后台管理
├── config/
│   └── database.php       # 数据库配置
├── public/
│   ├── index.php          # 入口文件（含路由系统）
│   ├── static/            # 静态资源
│   │   ├── css/style.css  # 样式文件
│   │   └── js/main.js     # JavaScript文件
│   └── .htaccess          # Apache重写规则
├── install.php            # 安装程序
├── install.sql            # 数据库安装文件
└── README.md              # 项目文档
```

## 🔧 Web服务器配置

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

    location ~ /\.ht {
        deny all;
    }
}
```

### Apache配置

确保网站根目录指向 `public` 目录，`.htaccess` 文件已包含：

```apache
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```

## 📊 数据库表

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

## 🎨 设计特点

### 视觉设计
- 整洁白色背景
- 蓝色主题（#667eea）
- 方框形状UI元素
- 清晰的视觉层次

### 交互功能
- 汉堡菜单（二级结构）
- 中英文切换
- 昼夜模式切换
- 平滑滚动
- 表单验证

### 响应式设计
- 移动端适配
- 平板端适配
- 桌面端适配

## 💡 技术特点

### 架构设计
- **MVC模式**: 控制器-视图分离
- **PDO数据库**: 预处理防SQL注入
- **Session管理**: 安全的用户认证
- **简单路由**: 轻量级路由系统

### 安全措施
- 密码哈希存储（password_hash）
- PDO预处理语句
- Session安全配置（httponly、samesite）
- 输入验证过滤
- 错误信息脱敏

### 错误处理
- 目录绑定错误检测
- 数据库连接失败提示
- 404页面不存在提示
- 500服务器错误提示
- 友好的错误界面

## 📝 更新日志

### v3.0 (2026-07-27)
- ✅ 修复目录绑定错误（public目录）
- ✅ 修复控制器不存在问题
- ✅ 修复页面不存在问题
- ✅ 添加安装程序（install.php）
- ✅ 添加错误页面（404、500）
- ✅ 完善功能和页面
- ✅ 优化代码结构
- ✅ 修复所有已知问题

### v2.0 (2026-07-27)
- 重构为独立版，不依赖框架
- 移除ThinkPHP框架依赖
- 使用原生PHP + PDO实现

### v1.0 (2026-07-27)
- 初始版本发布

## 🔗 设计参考

- https://entropy.slmsns.com/
- https://auth.nwovo.com

## 📄 许可证

MIT License

## 📞 联系方式

- QQ群: 123456789
- 邮箱: admin@qeefg.com
- 网站: https://qeefg.com

---

**感谢使用QEEFG授权站系统！** 🎊