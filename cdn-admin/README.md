# CDN 防护加速平台后台（ThinkPHP 版）

基于 React 19 + TypeScript + Vite 8 + Tailwind CSS v4 的企业级 CDN 防护加速平台后台界面，后端采用 **ThinkPHP 8.1**，可直接部署到支持 PHP 的 Apache/Nginx 虚拟主机。

## 技术栈

- 前端：React 19 + React Router v7 + TypeScript 6 + Vite 8 + Tailwind CSS v4
- 后端：ThinkPHP 8.1 + PHP ≥ 8.0
- 数据：JSON 文件持久化（`data/data.json`）
- 部署：Apache/Nginx 虚拟主机，无需 Node.js 运行环境

## 环境要求

| 项目 | 要求 | 说明 |
|------|------|------|
| PHP 版本 | ≥ 8.0 | 推荐 8.1 或 8.2 |
| PHP 扩展 | json | 必须启用 |
| Web 服务器 | Apache / Nginx | Apache 已自带 `.htaccess` 配置 |
| 目录权限 | data/、runtime/ 可写 | 安装时会自动创建 |
| 数据库 | 不需要 | 使用 JSON 文件存储 |
| Node.js | 不需要 | 构建产物已包含在 `public/` 中 |
| Composer | 不需要 | 依赖 `vendor/` 已包含 |

## 本地开发（可选）

如果你想修改前端源码，才需要执行以下命令：

```bash
npm install
npm run dev
```

开发模式下使用浏览器 localStorage 持久化 mock 数据，刷新页面后数据不丢失。

修改源码后重新构建：

```bash
npm run build
```

构建会自动把产物复制到 `public/`。

## 部署方式（无需构建，直接上传安装）

本项目已包含完整的 ThinkPHP 框架、依赖和前端构建产物，**无需执行 npm install / npm run build / composer install**。

### 方式一：推荐（文档根目录指向 public/）

1. 将整个项目通过 FTP/文件管理器上传到虚拟主机，例如 `public_html/cdn-admin/`
2. 在主机控制面板中将域名**文档根目录（Document Root）**指向项目内的 `public/` 目录
3. 确保 `data/` 和 `runtime/` 目录可写（如不存在则手动创建并设为 755 或 777）
4. 浏览器访问 `https://你的域名/install`
5. 按向导完成环境检测 → 设置管理员账号密码 → 安装完成

### 方式二：无法修改文档根目录（根目录即 web 根目录）

1. 将整个项目上传到站点根目录
2. 确保根目录的 `.htaccess` 和 `index.php` 存在（已包含）
3. 确保 `data/` 和 `runtime/` 目录可写
4. 浏览器访问 `https://你的域名/install`
5. 完成安装向导

根目录的 `.htaccess` 会自动将请求转发到 `public/index.php`，并把 `/assets/`、`favicon.svg` 等静态资源重写到 `public/` 下，同时禁止直接访问敏感目录。

### 安装向导详细步骤

访问：

```
https://你的域名/install
```

#### 步骤 1：环境检测

系统自动检查：

- PHP 版本是否 ≥ 8.0
- JSON 扩展是否启用
- `data/` 目录是否可写

若提示 `data/ 不可写`，请在 FTP/文件管理器中将 `data/` 目录权限设置为 `755` 或 `777`。

#### 步骤 2：初始化配置

填写：

- **管理员账号**：用于登录 S 端总站长后台，默认 `admin`
- **管理员密码**：长度至少 6 位
- **确认密码**：与管理员密码一致
- **导入演示数据**：首次安装建议勾选，系统会预置商品、订单、商户、站点等演示数据

点击「开始安装」后，系统会生成：

- `data/config.php` — 站点配置文件（包含管理员账号密码）
- `data/install.lock` — 安装锁文件
- `data/data.json` — 运行时数据文件

#### 步骤 3：安装完成

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
| B 端商户 | merchant | 123456 | 演示商户账号 |

## 安全设置

安装完成后，强烈建议通过 FTP/文件管理器删除或重命名以下文件/目录，防止安装程序被重复执行：

```text
app/controller/Install.php
install/
```

如需重新安装，删除以下文件后刷新安装页面：

```text
data/config.php
data/install.lock
data/data.json
```

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
│   │   ├── Api.php          # REST API
│   │   ├── Auth.php         # 登录
│   │   ├── Health.php       # 健康检查
│   │   └── Install.php      # 安装向导
│   └── service/
│       └── DataService.php  # JSON 数据读写服务
├── config/                  # ThinkPHP 配置
├── data/                    # 运行时 JSON 数据（安装后生成）
├── frontend-public/         # 前端公共资源（favicon、icons）
├── install/
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
| `GET/POST/PUT/DELETE /api/:resource` | `api/index` | RESTful CRUD |
| `GET/POST /install` | `install/index` | 安装向导 |
| `GET /` | `index/index` | 返回 React SPA |
| `GET /:path` | `index/spa` | 兜底返回 React SPA |

## 数据持久化

部署到 PHP 主机后，所有 CRUD 请求由 ThinkPHP 控制器处理，数据保存到 `data/data.json`。未部署 PHP 时（本地 `npm run dev`），前端自动回退到浏览器 localStorage。

## 常见问题

### 1. 访问 `/install` 报 404

- Apache：确认已开启 `mod_rewrite` 模块
- Nginx：参考上方 Nginx 配置
- 无法修改文档根目录时：确认根目录 `.htaccess` 已上传

### 2. 安装时提示 `data/ 不可写`

将 `data/` 和 `runtime/` 目录权限设置为 `755` 或 `777`：

```bash
chmod -R 755 data runtime
# 或
chmod -R 777 data runtime
```

### 3. 登录时提示账号或密码错误

- S 端：使用安装向导中设置的管理员账号密码
- B 端：使用 `merchant / 123456`

### 4. 忘记管理员密码

删除 `data/config.php`，重新访问 `/install` 进行安装（注意：`data/data.json` 中的业务数据会保留，除非删除）。

## 许可证

MIT
