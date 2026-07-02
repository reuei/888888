# CDN 防护加速平台后台（ThinkPHP + MySQL 版）

基于 React 19 + TypeScript + Vite 8 + Tailwind CSS v4 的企业级 CDN 防护加速平台后台界面，后端采用 **ThinkPHP 8.1**，数据持久化使用 **MySQL 5.7+**，可直接部署到支持 PHP 的 Apache/Nginx 虚拟主机。

## 技术栈

- 前端：React 19 + React Router v7 + TypeScript 6 + Vite 8 + Tailwind CSS v4
- 后端：ThinkPHP 8.1 + PHP ≥ 8.0
- 数据：MySQL 5.7+ / MariaDB 10.2+，表前缀 `cdn_`
- 部署：Apache/Nginx 虚拟主机，无需 Node.js 运行环境

## 环境要求

| 项目 | 要求 | 说明 |
|------|------|------|
| PHP 版本 | ≥ 8.0 | 推荐 8.1 或 8.2 |
| PHP 扩展 | pdo_mysql | 必须启用 |
| PHP 扩展 | json | 必须启用 |
| PHP 扩展 | openssl | 推荐启用，用于安全密码哈希 |
| 数据库 | MySQL 5.7+ / MariaDB 10.2+ | 需要支持 JSON 类型 |
| 数据库账号 | 具备 CREATE DATABASE、CREATE TABLE 权限 | 安装向导会自动建库建表 |
| Web 服务器 | Apache / Nginx | Apache 已自带 `.htaccess` 配置 |
| 目录权限 | `config/`、`data/`、`runtime/` 可写 | 安装时会自动创建 |
| Node.js | 不需要 | 构建产物已包含在 `public/` 中 |
| Composer | 不需要 | 依赖 `vendor/` 已包含 |

## 本地开发（可选）

如果你想修改前端源码，才需要执行以下命令：

```bash
npm install
npm run dev
```

开发模式下前端使用浏览器 localStorage 持久化 mock 数据，刷新页面后数据不丢失。

修改源码后重新构建：

```bash
npm run build
```

构建会自动把产物复制到 `public/`。

## 部署方式（无需构建，直接上传安装）

本项目已包含完整的 ThinkPHP 框架、依赖、前端构建产物和 MySQL 建表脚本，**无需执行 npm install / npm run build / composer install**。

### 方式一：推荐（文档根目录指向 public/）

1. 将整个项目通过 FTP/文件管理器上传到虚拟主机，例如 `public_html/cdn-admin/`
2. 在主机控制面板中将域名**文档根目录（Document Root）**指向项目内的 `public/` 目录
3. 确保 `config/`、`data/` 和 `runtime/` 目录可写（如不存在则手动创建并设为 755 或 777）
4. 浏览器访问 `https://你的域名/install`
5. 按向导完成环境检测 → 填写 MySQL 数据库与管理账号 → 安装完成

### 方式二：无法修改文档根目录（根目录即 web 根目录）

1. 将整个项目上传到站点根目录或子目录（如 `public_html/cdn-admin/`）
2. 确保根目录的 `.htaccess` 和 `index.php` 存在（已包含）
3. 确保 `config/`、`data/` 和 `runtime/` 目录可写
4. 浏览器访问 `https://你的域名/install` 或 `https://你的域名/cdn-admin/install`
5. 完成安装向导

根目录的 `.htaccess` 会自动将请求转发到 `public/index.php`，并把 `/assets/`、`favicon.svg` 等静态资源重写到 `public/` 下，同时禁止直接访问敏感目录。程序会自动识别部署路径并注入 `<base href>`，因此支持子目录部署。

## 安装向导详细步骤

访问：

```
https://你的域名/install
```

### 步骤 1：环境检测

系统自动检查：

- PHP 版本是否 ≥ 8.0
- PDO_MySQL 扩展是否启用
- JSON 扩展是否启用
- `config/`、`data/`、`runtime/` 目录是否可写

若提示某目录不可写，请在 FTP/文件管理器中将该目录权限设置为 `755` 或 `777`。

### 步骤 2：数据库与管理员配置

填写：

- **数据库主机**：MySQL 服务器地址，通常为 `127.0.0.1` 或 `localhost`
- **端口**：MySQL 端口，默认 `3306`
- **数据库名**：目标数据库名，如 `cdn_admin`，不存在会自动创建
- **数据库账号**：具备建库建表权限的 MySQL 账号
- **数据库密码**：对应密码
- **表前缀**：建议使用 `cdn_`，安装后会自动创建 `cdn_articles`、`cdn_orders` 等 35 张数据表
- **管理员账号/密码**：用于登录 S 端总站长后台，密码至少 6 位
- **导入演示数据**：首次安装建议勾选，系统会预置商品、订单、商户、站点等演示数据

点击「开始安装」后，系统会：

1. 测试数据库连接并自动创建数据库
2. 生成 `config/database.php` 数据库配置文件
3. 自动创建 35 张数据表（表前缀 `cdn_`）
4. 生成 `data/config.php` 站点配置文件（包含管理员账号密码）
5. 可选导入 `install/data-demo.php` 演示数据
6. 创建 `data/install.lock` 安装锁文件

### 步骤 3：安装完成

安装完成后点击「进入平台」即可开始使用。

## 访问地址

| 入口 | 地址 |
|------|------|
| 首页 | `https://你的域名/` |
| S 端后台 | `https://你的域名/#/s/dashboard` |
| B 端后台 | `https://你的域名/#/b/dashboard` |
| 安装向导 | `https://你的域名/install` |

## 默认登录账号

| 角色 | 账号 | 密码 | 说明 |
|------|------|------|------|
| S 端总站长 | 安装时设置 | 安装时设置 | 示例：admin / 123456 |
| B 端商户 | merchant | 123456 | 演示商户账号；也可在 `cdn_merchants` 表中配置正式账号 |

## 安全设置

安装完成后，强烈建议通过 FTP/文件管理器删除或重命名以下文件/目录，防止安装程序被重复执行：

```text
app/controller/Install.php
install/
```

如需重新安装，删除以下文件后刷新安装页面：

```text
data/install.lock
data/config.php
config/database.php
```

## 数据库说明

- 所有业务数据统一以 `id + data(json) + created_at + updated_at` 形式存储
- 默认表前缀 `cdn_`，共 35 张数据表
- 每张表均为 `created_at` 和 `updated_at` 添加了索引，优化按时间排序与分页
- 数据库结构定义见 `install/database.sql`
- 演示数据见 `install/data-demo.php`

## 服务器配置参考

### Apache（文档根目录指向 public/）

`public/.htaccess` 已包含：

```apache
<IfModule mod_rewrite.c>
    Options +FollowSymlinks -Multiviews
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?s=$1 [QSA,PT,L]
</IfModule>
```

### Apache（无法修改文档根目录）

根目录 `.htaccess` 已包含静态资源重写与敏感目录保护：

```apache
RewriteRule ^assets/(.*)$ public/assets/$1 [L]
RewriteRule ^favicon\.svg$ public/favicon.svg [L]
RewriteRule ^icons\.svg$ public/icons.svg [L]
RewriteRule ^index\.html$ public/index.html [L]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/index.php?s=$1 [QSA,PT,L]

RewriteRule ^(app|config|route|runtime|vendor|data|install)(/|$) - [F,L]
```

### Nginx（文档根目录指向 public/）

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### Nginx（无法修改文档根目录）

```nginx
location /assets/ {
    alias /站点根目录/public/assets/;
}

location = /favicon.svg {
    alias /站点根目录/public/favicon.svg;
}

location = /icons.svg {
    alias /站点根目录/public/icons.svg;
}

location / {
    try_files $uri $uri/ /public/index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## 项目结构

```text
cdn-admin/
├── app/
│   ├── controller/          # ThinkPHP 控制器
│   │   ├── Index.php        # SPA 入口
│   │   ├── Api.php          # REST API（支持分页、搜索）
│   │   ├── Auth.php         # 登录认证（S 端管理员 + B 端商户）
│   │   ├── Health.php       # 健康检查
│   │   └── Install.php      # 安装向导
│   └── service/
│       └── DataService.php  # MySQL 数据读写服务
├── config/                  # ThinkPHP 配置
│   └── database.php         # 数据库配置（安装时会被覆盖）
├── data/                    # 站点配置与安装锁（安装后生成）
├── frontend-public/         # 前端公共资源（favicon、icons）
├── install/
│   ├── database.sql         # MySQL 建表脚本（表前缀 cdn_）
│   └── data-demo.php        # 演示数据集
├── public/                  # ThinkPHP 入口 + 构建后前端资源
│   ├── index.php            # ThinkPHP 入口
│   ├── index.html           # React SPA（构建后生成）
│   ├── assets/              # 前端构建资源（构建后生成）
│   └── .htaccess            # ThinkPHP 重写规则
├── route/
│   └── app.php              # 路由配置
├── runtime/                 # ThinkPHP 运行时缓存
├── src/                     # React 源码
├── vendor/                  # ThinkPHP 及依赖
├── .htaccess                # 根目录 fallback 重写规则
├── index.php                # 根目录 fallback 入口
├── composer.json
├── package.json
└── vite.config.ts
```

## ThinkPHP 路由说明

| 路由 | 控制器方法 | 说明 |
|------|-----------|------|
| `GET /api/health` | `health/index` | 健康检查，前端据此判断是否 PHP 运行 |
| `POST /api/login` | `auth/login` | 登录验证 |
| `GET /api/me` | `auth/profile` | 当前登录/安装信息 |
| `GET/POST/PUT/DELETE /api/:resource` | `api/index` | RESTful CRUD，支持 `?id=`、`?search=`、`?page=`、`?limit=` |
| `GET/POST /install` | `install/index` | 安装向导 |
| `GET /` | `index/index` | 返回 React SPA |
| `GET /:path` | `index/spa` | 兜底返回 React SPA |

## 数据持久化

部署到 PHP 主机后，所有 CRUD 请求由 ThinkPHP 控制器处理，数据保存到 MySQL。未部署 PHP 时（本地 `npm run dev`），前端自动回退到浏览器 localStorage。

## 常见问题

### 1. 访问 `/install` 报 404

- Apache：确认已开启 `mod_rewrite` 模块
- Nginx：参考上方 Nginx 配置
- 无法修改文档根目录时：确认根目录 `.htaccess` 已上传

### 2. 安装时提示 `config/ 不可写` 或 `data/ 不可写`

将 `config/`、`data/` 和 `runtime/` 目录权限设置为 `755` 或 `777`：

```bash
chmod -R 755 config data runtime
# 或
chmod -R 777 config data runtime
```

### 3. 安装时提示数据库连接失败

- 确认数据库主机、端口、账号、密码正确
- 确认数据库账号具备 `CREATE DATABASE` 权限
- 确认 MySQL 版本 ≥ 5.7 或 MariaDB ≥ 10.2
- 部分虚拟主机的 MySQL 主机不是 `127.0.0.1`，请查看主机商提供的数据库地址

### 4. 访问页面报 500 错误

- 检查 PHP 版本是否 ≥ 8.0，且已启用 `pdo_mysql`、`json` 扩展
- 检查 `config/`、`data/`、`runtime/` 目录是否可写
- 检查是否已上传 `vendor/` 目录与 `public/` 下的构建产物
- 将 `config/app.php` 中 `'show_error_msg' => false` 改为 `true`，或开启 `'app_debug' => true`，可查看具体错误信息（调试用，生产环境请关闭）

### 5. 登录时提示账号或密码错误

- S 端：使用安装向导中设置的管理员账号密码
- B 端：使用 `merchant / 123456`，或在 `cdn_merchants` 表中为商户配置 `account` 与 `password_hash`/`password`

### 6. 忘记管理员密码

删除 `data/config.php`，重新访问 `/install` 进行安装。数据库中的业务数据不会丢失。

### 7. 如何修改数据库配置

编辑 `config/database.php` 中的 `connections.mysql` 配置项，修改后刷新页面即可生效。

## 许可证

MIT
