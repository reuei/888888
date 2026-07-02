# CDN 防护加速平台后台（ThinkPHP 版）

基于 React 19 + TypeScript + Vite 8 + Tailwind CSS v4 的企业级 CDN 防护加速平台后台界面，后端采用 **ThinkPHP 8.1**，可直接部署到支持 PHP 的 Apache/Nginx 虚拟主机。

## 技术栈

- 前端：React 19 + React Router v7 + TypeScript 6 + Vite 8 + Tailwind CSS v4
- 后端：ThinkPHP 8.1 + PHP ≥ 8.0
- 数据：JSON 文件持久化（`data/data.json`）
- 部署：Apache/Nginx 虚拟主机，无需 Node.js 运行环境

## 本地开发

```bash
npm install
npm run dev
```

开发模式下使用浏览器 localStorage 持久化 mock 数据，刷新页面后数据不丢失。

## 构建

```bash
npm run build
```

构建会：

1. 编译 TypeScript 并打包前端资源到 `dist/`
2. 自动将 `dist/` 中的 `index.html`、`assets/`、`favicon.svg`、`icons.svg` 复制到 ThinkPHP 的 `public/` 目录

## 部署到 PHP 虚拟主机

### 方式一：推荐（文档根目录指向 public/）

1. 在本地执行 `npm run build` 生成构建产物
2. 将整个项目上传到虚拟主机（例如 `public_html/cdn-admin/`）
3. 在主机控制面板中将域名绑定到项目下的 `public/` 目录
4. 访问 `https://你的域名/install` 完成安装

### 方式二：无法修改文档根目录（根目录即 web 根目录）

1. 在本地执行 `npm run build` 生成构建产物
2. 将整个项目上传到虚拟主机站点根目录
3. 确保根目录存在 `.htaccess` 和 `index.php`（已包含）
4. 访问 `https://你的域名/install` 完成安装

根目录的 `.htaccess` 会自动将请求转发到 `public/index.php`，并把 `/assets/` 等静态资源重写到 `public/` 下。

### 安装向导

访问：

```
https://你的域名/install
```

按向导完成三步：

1. **环境检测**：自动检查 PHP 版本、JSON 扩展、`data/` 目录可写权限。
2. **初始化配置**：设置管理员账号密码，选择是否导入演示数据。
3. **安装完成**：点击“进入平台”即可开始使用。

安装完成后，系统会生成：

- `data/config.php` — 站点配置文件
- `data/install.lock` — 安装锁文件
- `data/data.json` — 运行时数据文件

### 目录权限

如果环境检测提示目录不可写，请将以下目录权限设置为 `755` 或 `777`：

```text
data/
runtime/
```

### 访问地址

- 首页：`https://你的域名/`
- S 端后台：`https://你的域名/#/s/dashboard`
- B 端后台：`https://你的域名/#/b/dashboard`
- 安装向导：`https://你的域名/install`

默认登录账号：

- S 端：安装时设置的管理员账号
- B 端：`merchant` / `123456`

### 安全提示

安装完成后，建议通过 FTP/文件管理器删除或重命名：

```text
app/controller/Install.php
install/
```

以防止安装程序被重复执行。

### 重新安装

如需重新安装，删除以下文件后刷新安装页面：

```text
data/config.php
data/install.lock
data/data.json
```

### Nginx 配置（如使用 Nginx 且文档根目录指向 public/）

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

## 许可证

MIT
