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

## 部署到 PHP 虚拟主机

本项目已改造为可直接部署到支持 PHP 的 Apache/Nginx 虚拟主机，无需 Node.js 运行环境。

### 1. 构建

在本地或支持 Node.js 的环境中执行：

```bash
npm install
npm run build
```

### 2. 上传

将 `dist/` 目录内的所有文件上传到虚拟主机站点根目录（或子目录）。

```text
dist/
├── index.php          # PHP 入口
├── index.html         # 静态入口（备用）
├── .htaccess          # Apache 重写规则
├── api/
│   ├── index.php      # REST API 入口
│   ├── health.php     # 健康检查
│   └── data/
│       ├── default.json   # 初始空数据结构
│       └── data.json      # 运行时自动生成的数据文件（首次请求后创建）
└── assets/            # 前端静态资源
```

### 3. 目录权限

确保 `dist/api/data/` 目录可写，PHP 需要在该目录创建 `data.json`：

```bash
chmod -R 755 dist/api/data
# 如需要，可设置为 775 或 777（请根据主机安全策略调整）
```

### 4. 访问

- 首页：`https://你的域名/` 或 `https://你的域名/#/`
- S 端后台：`https://你的域名/#/s/dashboard`
- B 端后台：`https://你的域名/#/b/dashboard`

由于使用 HashRouter，所有前端路由都通过 URL hash 实现，因此无需服务器端路由配置即可在普通 PHP 虚拟主机上运行。

### 5. 数据说明

- 部署后首次访问时，`api/data/data.json` 会自动从 `api/data/default.json` 初始化。
- 默认 `default.json` 为空数据，您可以通过界面进行增删改查操作，数据会持久化到服务器 JSON 文件。
- 如需预置演示数据，可将包含完整数据的 JSON 文件替换 `dist/api/data/default.json`，然后删除已生成的 `dist/api/data/data.json` 使其重新初始化。

### 6. Nginx 配置（如使用 Nginx）

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
