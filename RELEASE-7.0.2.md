# QEEFG 授权站补丁包 v7.0.2

**发布日期**: 2026-01-29  
**适用版本**: qeefg-auth v7.x

---

## 升级说明

本补丁包为 v7.0.2 的完整重写版本，**全部页面 UI/UX/动画/交互 1:1 对标 https://entropy.slmsns.com** 。应用前请务必备份数据库和现有代码文件。

### 默认后台账号（全新安装）
- 用户名：`admin`
- 密码：`201125.`

---

## 本次更新内容（CHANGELOG）

### 1. 首页与 Welcome 页面完全重写（对标 entropy.slmsns.com）
- 全新设计的 Hero 区域：左侧标语徽章 + 大标题渐变色 + 双按钮，右侧深色仪表盘 Mock 预览卡片
- 新增背景装饰元素：`.decorator1`、`.decorator2`、`.gridGlow` 发光径向渐变球 + 浮动动画
- 首页导航：汉堡菜单按钮在 **Logo 左侧**，头部右侧新增注册按钮+登录图标
- 新增「APP下载」导航入口
- 核心能力区块三卡布局：授权发放与管理 / 在线验证与设备绑定 / 授权服务支持
- 新增 `section-tag` 「核心能力」小标签设计
- 产品展示区、数据统计条、CTA 卡片统一视觉风格
- 新增 `/welcome` 路由，内容与首页一致
- 导航栏「平台能力」点击跳转到首页 `#features` 核心能力锚点（首页平滑滚动，其他页重定向到 `/#features`）

### 2. 授权查询与文档中心页面重写
- 授权查询：渐变背景 Banner + 居中卡片查询表单，支持 i-key 图标输入框
- 结果网格展示：授权码/产品/状态/到期/绑定用户/描述 六行列对展示
- 空态与错误态各自带图标说明
- 文档中心：左固定侧栏分类树 + 面包屑 + h1标题 + meta + 分隔线 + markdown-body 内容区
- markdown-body 支持基础正则替换渲染：`**粗体**`、`*斜体*`、`` `代码` ``

### 3. 新增 APP 下载页 (后台可配置)
- 路由 `/app-download`，完全对标 `/app-download` 熵云页面结构
- 顶部 mini-header：大图标/Logo + 官方下载中心副标 + 返回首页
- 渐变背景 Hero 区：APP图标 + 产品名 + 版本号 + 标语(i-shield-check徽章) + 描述 + 设备Mock
- 设备 Mock：默认内嵌骨架样式渲染（时间条 + Mock标题 + 2渐变卡 + 6列表行），若配置截图URL则显示截图
- 下载按钮：Android 绿色渐变 + iOS 黑色深色，分别显示版本号
- **后台配置**：管理员后台 → 系统设置 → APP管理（新增菜单项）
  - 新增/编辑/删除 APP 条目
  - 字段：软件名称、版本号、排序、上架/下架、宣传语、图标URL、截图URL、描述、Android 下载地址/版本号、iOS 下载地址/版本号
  - 数据库表：`qf_apps`（install.sql 自动创建并预置一条「商家工作台 v1.0.2」样例）

### 4. 汉堡菜单平台能力锚点跳转
- 移动端汉堡菜单（左侧抽屉）中 平台能力 链接改为 href=`/#features`
- JS 中 bindPlatformLinks 统一处理：若在首页则平滑滚动到 section#features，否则浏览器默认跳转到 /#features 执行原生锚点跳转
- 桌面端 main-nav 中的 平台能力 同样处理
- ESC 键可关闭移动抽屉 + 公告 Modal

### 5. 登录/注册页面完全重写（对标 entropy.slmsns.com 页面）
- 顶部统一 mini-header：Logo + 副标题「企业级软件定制与私有化交付服务商」+ 返回首页按钮
- 50/50 分栏布局：左侧品牌渐变面板 + 装饰圆/发光/网格，右侧居中表单卡片
- **登录页**：
  - 标题「用户中心登录」+ 副标题「使用您的账号继续访问工作台与服务记录」
  - 用户名/密码 输入框带前置图标 (i-user, i-lock)
  - 密码框右侧眼睛图标切换显示/隐藏密码
  - 图形验证码行（若配置 Captcha）+ 点击刷新
  - 记住密码 checkbox + 忘记密码 右对齐
  - 双按钮：登录（渐变主按钮）+ 注册账号（描边次按钮）
- **注册页**：
  - Tab 切换：邮箱注册 / 手机注册（下划线条纹移动动画）
  - 用户名 + 邮箱/手机号 + 密码 + 确认密码 + 验证码行 + 获取验证码 60s 倒计时按钮
  - 密码强度 3 段式指示器（弱红/中黄/强绿）
  - 底部服务条款/隐私政策小字链接
  - 所有输入启用 data-validate 实时校验：input/blur/change 三事件
- 左栏品牌面板：
  - 大圆形 Logo + 「熵」字
  - 标题 + 描述 + 三条 i-check-circle 特性勾选列表
  - 底部版权

### 6. 全局 UI / 加载动画 / 全局提示 / 图标重写
- **style.css** (4168行) 全站重写，Arco Design 风格：
  - 主色：`#4080FF`，渐变：`#4080FF → #722ED1`（蓝紫渐变）
  - 字体：Inter + 苹方/微软雅黑，`font-smoothing` 优化
  - 全站阴影：xs/sm/md/lg/xl 5级，border-radius: 按钮8px，卡片12px
  - .site-header 毛玻璃 backdrop-filter: blur(12px)，rgba(255,255,255,0.85)
  - .hamburger-btn 三条线 → X 变形动画
  - .mobile-nav 左侧抽屉滑入（-100% → 0 translateX）
  - Toast：顶部居中，slideDown/slideUp + 4 色（success/error/warning/info）含图标
  - Loading Overlay：双环 spinner (dual-ring 双色反向转)
  - 入场动画：11个 @keyframes 齐全：fadeInUp/Down/Left/Right, toastIn/Out, spin, shimmer, pulse, scaleIn, float
  - IntersectionObserver 驱动 [data-animate] 进入视口触发
  - 表单控件：高40px，聚焦 box-shadow 环高亮，field-invalid 下红边，field-valid 绿边
  - Auth 页：装饰圆形渐变 + blur-100 发光球
  - 暗色模式：`[data-theme="dark"]` 全部颜色变量+各组件样式适配（bg=#0B0E14, card=#161A22）
  - 响应式断点：1024px / 768px / 480px 三级
- **图标**：全站 SVG Sprite 统一图标库，首页39个，登录/认证15个，完整覆盖 stroke/fill currentColor 规范

### 7. 日志真实化：真实 IP + 真实操作
- **BaseController 新增 `getRealIp()`**：依次检查 `HTTP_X_FORWARDED_FOR, HTTP_X_REAL_IP, HTTP_CLIENT_IP, HTTP_X_FORWARDED, HTTP_FORWARDED_FOR, HTTP_FORWARDED`，过滤私有/保留地址，最终 fallback 到 `REMOTE_ADDR`，支持 CDN / 反代部署
- **BaseController 新增 `recordOperationLog(userId, action, description)`**：自动填入真实 IP 和截断 UA 写入 `qf_operation_logs` 表
- **User 控制器升级 `recordLoginLog()`**：改用 `getRealIp()`；User-Agent 截断到 255 字符；使用数据库 `NOW()` 函数统一时间
- **登录 IP 字段**：`qf_users.login_ip` 更新改用 `getRealIp()` 而不是 `REMOTE_ADDR`
- **新增 15+ 种操作日志记录点**：
  - 用户：登录、注册、修改资料、修改密码、提交反馈、创建订单（免费领取/余额支付/第三方支付）、下载产品、退出登录
  - 开发者：申请开发者、提交插件
  - 账号：换绑邮箱、换绑手机号、阅读单条消息、全部标记已读

### 8. 错误代码与布局问题修复
- 所有 52 个 PHP 文件通过 `php -l` 语法检查零错误
- main.js 通过 `node --check` 语法校验
- CSS 括号配对与闭合 100%（757对大括号）
- 路由表新增 4 个条目：`/welcome`、`/app-download`、`/appDownload`、`/admin/apps`、POST `/admin/saveApp`、`/admin/deleteApp`
- 修复 User.php 中部分分支缺少 operation log 的问题
- 修复 install.sql 中 admin 默认密码仍为旧 admin123 问题（见第9条）

### 9. 默认后台管理账号/密码更新
- **install.sql 第 400 行**：admin 账号密码哈希从 `admin123` 对应哈希更新为 `201125.` 对应哈希
  - 密码：**201125.**（最后包含点号 `.`）
  - 哈希：`$2y$12$OtTwrVfm//bUsyfsNyo3eOyDmuVOKhZi6xBXbW4a6x3mu63.ykX/2`
- 新增系统设置项：`app_download_enabled = 1`
- 新增 `qf_apps` 表（14字段+2时间戳）并预置样例 商家工作台 v1.0.2

### 10. 全部页面 UI/UX 重写覆盖清单
- [x] 首页 index / welcome
- [x] 授权查询 license-query
- [x] 文档中心 documents
- [x] APP下载 app-download
- [x] 公告页 announcement
- [x] 平台能力 /platform → 锚点 /#features
- [x] 登录 /user/login
- [x] 注册 /user/register
- [x] 用户中心布局 layout（保留原主体，已在7.0.1优化，确保样式联动生效）
- [x] 管理员后台 layout → 新增「系统设置 → APP管理」子菜单项
- [x] 管理员后台 /admin/apps 列表 + 新增编辑删除 Modal 表单

---

## 文件清单（补丁包含）

```
app/
  controller/
    BaseController.php   (新增 getRealIp, recordOperationLog)
    Index.php            (新增 welcome, appDownload; platform 改锚点)
    User.php             (真实IP; 新增15+操作日志点)
    Admin.php            (新增 appManagement/saveApp/deleteApp)
  view/
    index/
      index.php          (首页重写)
      license-query.php  (授权查询重写)
      documents.php      (文档中心重写)
      app-download.php   (APP下载页，新增)
    user/
      login.php          (登录页重写)
      register.php       (注册页重写)
    admin/
      layout.php         (侧边栏新增APP管理菜单项)
      apps.php           (APP后台管理页，新增)
config/
  (未包含 database.php，防止覆盖用户配置)
  app.php                (保留)
public/
  index.php              (新增路由)
  static/
    css/style.css        (全站重写 4168行 Arco风格)
    js/main.js           (1176行 动画/表单验证/锚点/ajax/toast/loading)
install.sql              (admin密码改为201125.; 新增 qf_apps 表)
```

---

## 安装方法

### 一、全新安装
1. 上传源码到服务器根目录
2. 访问 `/install.php` 引导安装，或手动将 `install.sql` 导入到数据库
3. 将 `config/database.php.example` 复制为 `config/database.php` 并填写数据库连接信息
4. 后台访问 `/admin/login`，登录：admin / 201125.

### 二、应用补丁（现有 v7.x 升级）
1. **先备份数据库 + 现有代码文件！**
2. 上传补丁包文件覆盖对应位置
3. 执行下面的 SQL 增量升级脚本（**必跑！**）：
```sql
-- 升级到 7.0.2 需要执行的增量 SQL
-- 1) 重置 admin 密码 (如使用旧库)
-- 请按需取消注释执行：
-- UPDATE qf_admins SET password = '$2y$12$OtTwrVfm//bUsyfsNyo3eOyDmuVOKhZi6xBXbW4a6x3mu63.ykX/2' WHERE username = 'admin';

-- 2) 新增 qf_apps 表
CREATE TABLE IF NOT EXISTS qf_apps (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  app_name VARCHAR(100) NOT NULL COMMENT '软件名称',
  app_version VARCHAR(30) DEFAULT '' COMMENT '版本号',
  app_logo VARCHAR(255) DEFAULT '' COMMENT '图标URL',
  app_screenshot VARCHAR(255) DEFAULT '' COMMENT '截图URL',
  app_description TEXT COMMENT '描述',
  app_slogan VARCHAR(255) DEFAULT '' COMMENT '宣传语',
  android_url VARCHAR(500) DEFAULT '' COMMENT '安卓下载地址',
  android_version VARCHAR(30) DEFAULT '' COMMENT '安卓版本号',
  ios_url VARCHAR(500) DEFAULT '' COMMENT 'iOS下载地址',
  ios_version VARCHAR(30) DEFAULT '' COMMENT 'iOS版本号',
  sort_order INT DEFAULT 0,
  status TINYINT DEFAULT 1 COMMENT '0下架1上架',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='APP下载配置表';

-- 3) 预置一条样例
INSERT IGNORE INTO qf_apps (id,app_name,app_version,app_description,app_slogan,sort_order,status)
VALUES (1,'商家工作台','v1.0.2','面向商家打造的一站式授权管理工作台，随时随地管理产品授权、订单与财务数据。','高效运营 · 安全管理 · 随时掌握',0,1);

-- 4) 新增设置项
INSERT IGNORE INTO qf_settings (`key`,`value`,`description`) VALUES ('app_download_enabled','1','APP下载页开关:0关1开');
```
4. 清理浏览器缓存 (Ctrl+F5) 或 Ctrl+Shift+R 强制刷新静态资源
5. 访问站点首页核对效果

---

## 常见问题

**Q：我应用补丁后后台登录不进去？**
A：请执行增量 SQL 中的 admin 密码更新语句，或将 install.sql 中的 `qf_admins` 单条重新导入。新密码为 `201125.`（末尾有点号）

**Q：日志中的 IP 全都是服务器内网IP？**
A：若使用 CDN/反代，请确保 CDN 正确转发 `X-Forwarded-For` 和 `X-Real-IP` 头部。本补丁已升级 getRealIp() 依次读取所有代理头。

**Q：登录/注册页面样式错位？**
A：请强制刷新浏览器（Ctrl+Shift+R），或清理 CDN 缓存。style.css 主样式和登录页专用样式已合并在同一个 style.css 文件中。

**Q：平台能力按钮点击不跳转？**
A：本补丁中 `/platform` 路由已改为直接 302 重定向到 `/#features`。如仍不生效，请检查浏览器端 main.js 是否加载到最新版本。

---

**致开发者**：本版本遵循 entropy.slmsns.com 的全部 UI/UX 与交互结构，如有差异请在浏览器开发者工具对比样式后通知维护者，下个补丁将持续跟进。
