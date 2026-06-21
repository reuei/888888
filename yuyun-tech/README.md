# 语云科技企业官网系统 (YuYun Tech Enterprise Website)

一个完整的中国企业官网系统，包含多页面前台和功能完善的后台管理。
主题风格参考魔方财务、腾讯云、Cloudflare 等优秀企业官网。

---

## 目录结构

```
yuyun-tech/
├── index.php              首页
├── about.php              关于我们
├── company.php            公司简介
├── products.php           产品介绍
├── contact.php            联系我们
├── partners.php           合作伙伴
├── international.php      国际版（跳转页）
├── install.php            安装向导
├── config.php             系统配置
├── README.md              本文档
│
├── includes/
│   ├── header.php         公共头部
│   └── footer.php         公共页脚
│
├── admin/                 后台管理目录
│   ├── login.php          登录页
│   ├── index.php          仪表盘
│   ├── site.php           网站设置
│   ├── slides.php         轮播图管理
│   ├── products.php       产品管理
│   ├── partners.php       合作伙伴
│   ├── certificates.php   资质证书
│   ├── employees.php      员工卡片
│   ├── testimonials.php   用户评价
│   ├── news.php           新闻动态
│   ├── locations.php      公司分布
│   ├── contact.php        联系方式
│   ├── icp.php            备案信息
│   ├── friendlinks.php    友情链接
│   ├── account.php        账号管理
│   ├── header.php         后台公共头
│   ├── config.php         后台配置
│   └── helper_items.php   表单帮助脚本
│
├── assets/
│   ├── css/style.css      主样式
│   └── js/main.js         主脚本
│
└── data/                  数据存储目录（JSON）
    └── site_data.json
```

---

## 功能特性

### 前台功能
- ✅ 多页面：首页、关于我们、公司简介、产品介绍、联系我们、合作伙伴
- ✅ 首页轮播图（6屏、自动轮播、左右切换）
- ✅ 公司/产品数据展示、资质证书、员工卡片
- ✅ 用户评价、新闻动态
- ✅ 全球分布地图（含定位点）
- ✅ 横向滚动合作伙伴展示
- ✅ 右侧悬浮客服按钮（电话/QQ/微信/反馈）
- ✅ 悬浮按钮调用弹窗（魔方财务同款）
- ✅ Toast 消息提示（魔方财务/腾讯云同款）
- ✅ 顶部条、主导航、汉堡菜单（移动端）
- ✅ 深色页脚 + 橙色销售电话（Cloudflare 同款）
- ✅ 底部备案号、增值电信许可证、公安备案号
- ✅ 完全响应式，适配手机、平板、电脑

### 后台管理
- ✅ 登录验证（默认账号 admin / admin123）
- ✅ 仪表盘（系统信息、数据统计）
- ✅ 网站基础信息管理（名称、LOGO、主题、版权）
- ✅ 轮播图管理（标题、描述、图片、链接、主题色）
- ✅ 产品管理（名称、描述、价格、图标、颜色）
- ✅ 合作伙伴管理
- ✅ 资质证书管理
- ✅ 员工卡片管理
- ✅ 用户评价管理
- ✅ 新闻动态管理
- ✅ 公司分布管理
- ✅ 联系方式（电话、邮箱、QQ、微信、地址）
- ✅ 备案信息（ICP、公安备案、增值电信）
- ✅ 友情链接
- ✅ 管理员账号/密码修改
- ✅ 数据导出 / 一键恢复默认

### 技术特性
- 前端：HTML5 + CSS3 + 原生 JavaScript
- 图标：Font Awesome 6 CDN
- 后端：PHP 7.0+（无框架，轻量高效）
- 存储：JSON 文件（无需 MySQL，虚拟主机即开即用）
- 可选：如需切换到 MySQL，可在 `config.php` 中启用 `DB_ENABLED` 并在各 `save_*` 函数中写数据库逻辑
- 自适应 / 响应式设计，多主题可选

---

## 安装使用

### 方法一：一键安装（推荐）
1. 将整个 `yuyun-tech` 目录上传到您的虚拟主机或服务器
2. 确保 PHP 版本 >= 7.0（普通虚拟主机都支持）
3. 确保 `yuyun-tech/data/` 目录有写入权限（755）
4. 在浏览器访问：`http://您的域名/install.php`
5. 点击"立即安装"，系统将自动写入初始数据
6. 安装完成后进入后台 `admin/login.php` 登录（账号 `admin` 密码 `admin123`）

### 方法二：直接使用
1. 上传文件后直接访问 `index.php` 即可查看前台
2. 首次访问后台 `admin/` 时系统会自动使用默认数据
3. 通过后台管理功能修改网站内容

### 使用提示
- 安装后请立即登录后台 → 账号管理 → 修改默认账号密码
- 所有数据以 JSON 格式存储在 `data/site_data.json` 中（易于迁移备份）
- 图片可使用绝对路径（如 `https://example.com/img/logo.png`），或把图片放在 `assets/images/` 中，填写相对路径

---

## 后台登录地址

```
http://您的域名/admin/
默认账号：admin
默认密码：admin123
```

---

## 文件权限

```
chmod 755 data/          # 数据目录，需要可写
chmod 644 *.php           # PHP 文件，可读可执行
chmod 644 assets/css/*.css
chmod 644 assets/js/*.js
```

---

## 自定义开发

### 切换到 MySQL
编辑 `config.php`，将 `DB_ENABLED` 改为 `true`，并配置数据库参数：

```php
define('DB_ENABLED', true);
define('DB_HOST', 'localhost');
define('DB_NAME', 'yuyun_tech');
define('DB_USER', 'root');
define('DB_PASS', 'yourpassword');
```

然后将 `config.php` 中的 `save_site_data` / `load_site_data` 改为读写数据库即可。

### 添加自定义页面
复制 `products.php` 或 `about.php`，修改：
1. `$currentPage` 变量设置为页面标识
2. `$pageTitle` 设置页面标题
3. 内容区域的 HTML

### 主题样式
主要样式都在 `assets/css/style.css` 中，支持的变量：
- `--primary`：主色（默认 #1a73e8）
- `--secondary`：辅助色（默认 #ff6b35）
- `--orange`：橙色（用于电话高亮）
- `--dark`：深色（#1a1a2e）

顶部菜单中提供"深色模式"切换按钮，或在 CSS 中定义 `body.theme-dark`。

---

## 技术栈

- 前端：HTML5 / CSS3 / JavaScript (原生)
- 图标：Font Awesome 6.4 (CDN)
- 后端：PHP 7.0+ (零依赖、零框架)
- 数据库：可选，默认 JSON 文件

---

## 安全建议
1. 安装后务必修改默认管理员账号密码
2. 不要把 `data/site_data.json` 暴露到外网（通过 .htaccess 或 nginx 配置禁止访问）
3. 生产环境建议禁用错误显示
4. 如需更高安全性，建议启用 MySQL 并添加 CSRF 防护

---

## 许可证
© 语云科技 - 本系统供企业官网建设使用。
