# CDN 防护加速平台后台

基于 React 19 + TypeScript + Vite 8 + Tailwind CSS v4 的企业级 CDN 防护加速平台后台界面，采用 S 端（总站长）+ B 端（入驻商户）二层架构。

## 技术栈

- React 19 + React Router v7
- TypeScript 6
- Vite 8
- Tailwind CSS v4
- lucide-react 图标库
- 纯前端 mock / PHP JSON 双模式数据持久化

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

构建产物输出到 `dist/` 目录。

## 部署到 PHP 虚拟主机（带安装程序）

本项目已改造为可直接部署到支持 PHP 的 Apache/Nginx 虚拟主机，**无需 Node.js 运行环境，也无需命令行操作**。

### 1. 本地构建（仅需一次）

在本地或支持 Node.js 的环境中执行：

```bash
npm install
npm run build
```

### 2. 上传到虚拟主机

将 `dist/` 目录内的**所有文件**上传到虚拟主机站点根目录（或子目录）。

```text
dist/
├── install.php        # Web 安装向导
├── index.php          # PHP 入口
├── index.html         # 静态入口（备用）
├── .htaccess          # Apache 重写规则
├── api/
│   ├── index.php      # REST API 入口
│   ├── health.php     # 健康检查
│   └── data/
│       ├── default.json    # 初始空数据结构
│       └── data.json       # 安装后生成的数据文件
├── install/
│   └── data-demo.php     # 演示数据集
└── assets/            # 前端静态资源
```

### 3. 运行安装向导

在浏览器中访问：

```
https://你的域名/install.php
```

按向导完成三步：

1. **环境检测**：自动检查 PHP 版本、JSON 扩展、目录可写权限。
2. **初始化配置**：设置管理员账号密码，选择是否导入演示数据。
3. **安装完成**：点击"进入平台"即可开始使用。

安装完成后，系统会生成：

- `api/config.php` — 站点配置文件
- `api/install.lock` — 安装锁文件
- `api/data/data.json` — 运行时数据文件

### 4. 目录权限

如果环境检测提示目录不可写，请将以下目录权限设置为 `755` 或 `777`：

```text
api/
api/data/
```

### 5. 访问

- 首页：`https://你的域名/` 或 `https://你的域名/#/`
- S 端后台：`https://你的域名/#/s/dashboard`
- B 端后台：`https://你的域名/#/b/dashboard`

默认登录账号：

- S 端：安装时设置的管理员账号
- B 端：`merchant` / `123456`（演示数据中的商户账号）

### 6. 安全提示

安装完成后，建议通过 FTP/文件管理器删除或重命名：

```text
install.php
install/
```

以防止安装程序被重复执行。

### 7. 重新安装

如需重新安装，删除以下文件后刷新页面：

```text
api/config.php
api/install.lock
api/data/data.json
```

### 8. Nginx 配置（如使用 Nginx）

如果使用 Nginx 而非 Apache，请添加类似配置：

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;  # 根据实际 PHP-FPM 地址调整
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## 项目结构

```text
src/
├── components/        # 公共组件
├── data/              # mock 数据
├── hooks/             # 自定义 Hooks
├── pages/             # 页面
│   ├── s/             # S 端（总站长）页面
│   ├── b/             # B 端（商户）页面
│   └── *.tsx          # 公共页面（首页、登录等）
├── services/          # API 服务层（自动检测 PHP / localStorage 模式）
├── types/             # TypeScript 类型
└── utils/             # 工具函数
```

## 双模式数据持久化

`src/services/api.ts` 会自动检测当前运行环境：

- 如果存在 `window.__CDN_ADMIN_RUNTIME__ === 'php'`（由 `public/index.php` 注入），或 `/api/health.php` 可访问，则所有 CRUD 请求走 PHP REST API，数据保存到服务器 JSON 文件。
- 否则（本地开发或纯静态部署）继续使用浏览器 localStorage 持久化。

## 许可证

MIT
