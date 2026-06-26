# 鲸商城 Pro

基于 PHP + MySQL（ThinkPHP 风格轻量框架）的卡密商城系统骨架，包含 **S 端总站后台**、**B 端商户后台** 与 **安装向导**，适合虚拟主机部署。

> 当前为 v1.0.0 基础骨架版本，已包含：安装程序、登录鉴权、双端仪表盘、商品列表、卡密批量导入。其余模块可按相同 MVC 模式继续扩展。

---

## 目录结构

```
/workspace
├── application/           应用目录
│   ├── bootstrap.php      应用启动
│   ├── config.php         全局配置
│   ├── functions.php      公共函数
│   ├── Controller.php     控制器基类
│   ├── Model.php          模型基类
│   ├── Db.php             PDO 数据库类
│   ├── Route.php          路由解析
│   ├── config/            运行时配置（安装后生成 database.php）
│   ├── controller/        控制器
│   │   ├── Index.php
│   │   ├── Login.php
│   │   ├── admin/         S 端总站后台
│   │   └── merchant/      B 端商户后台
│   └── view/              视图模板
│       ├── layout/        布局文件
│       ├── login/
│       ├── admin/
│       └── merchant/
├── install/               安装向导
│   ├── index.php
│   └── install.sql
├── public/                Web 入口
│   ├── index.php
│   └── .htaccess
├── runtime/               运行时缓存（需可写）
├── .htaccess              根目录重写规则
├── nginx.conf             Nginx 参考配置
└── README.md
```

---

## 环境要求

- PHP >= 7.4
- PDO / PDO_MySQL
- GD、mbstring、JSON、openssl
- MySQL 5.7+ / MariaDB 10.2+

---

## 安装步骤

### 1. 上传代码

将项目文件上传至虚拟主机根目录（例如 `public_html`）。

### 2. 目录权限

确保以下目录/文件可写：

- `application/config/`
- `runtime/`
- `public/uploads/`

### 3. 运行安装向导

浏览器访问：

```
http://你的域名/install/
```

按向导填写数据库信息并设置总站管理员账号，安装程序会自动：

- 创建数据库
- 导入数据表结构
- 写入 `application/config/database.php`
- 创建管理员账号

### 4. 删除安装目录

安装完成后，删除服务器上的 `install/` 目录。

### 5. 访问系统

- 前台首页：`http://你的域名/`
- 总站后台：`http://你的域名/login?type=admin`
- 商户后台：`http://你的域名/login?type=merchant`

---

## Nginx 配置参考

如果使用 Nginx，请将配置中的 `root` 指向项目根目录，并引入 `nginx.conf` 中的重写规则。伪静态示例：

```nginx
location / {
    if (!-e $request_filename) {
        rewrite ^(.*)$ /public/index.php last;
    }
}

location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

---

## 本地测试

```bash
# 进入项目根目录
cd /workspace

# 启动 PHP 内置服务器（仅本地开发使用）
php -S 127.0.0.1:8080 -t .

# 访问
# http://127.0.0.1:8080/install/
```

---

## 默认数据

安装完成后，数据库已初始化：

- 管理员表 `jz_admin`：由安装时创建
- 费率分组 `jz_rate_group`：默认分组、VIP 分组
- 分站、商户、用户、商品、卡密、订单、结算、支付通道、日志等表结构

> 商户账号需手动在 `jz_merchant` 表插入，密码使用 `password_hash('你的密码', PASSWORD_DEFAULT)` 生成。

---

## 扩展开发

### 新增一个控制器

在 `application/controller/admin/` 下创建 `Merchant.php`：

```php
<?php
class Admin_Merchant extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
    }

    public function index()
    {
        $this->assign('title', '商户列表');
        $this->fetch('admin/merchant/index');
    }
}
```

### 新增视图

创建 `application/view/admin/merchant/index.php`，继承 `layout/admin.php` 的布局。

### 路由规则

URL 自动映射：`/模块/控制器/操作`

- `/admin/dashboard` → `Admin_Dashboard::index()`
- `/merchant/goods/import` → `Merchant_Goods::import()`
- `/login/doLogin` → `Login::doLogin()`

---

## 安全提示

- 生产环境关闭 `display_errors`。
- 定期修改管理员密码，开启二次认证。
- 删除或重命名 `install/` 目录。
- 数据库密码、支付密钥等敏感信息不要提交到版本库。

---

## 版本说明

- v1.0.0：项目骨架、安装向导、双端登录、仪表盘、商品列表、卡密批量导入示例。

后续可按需求继续实现：分站管理、商户入驻审核、订单投诉、代理分销、资金结算、支付通道、模板前端、优惠券、数据统计等完整模块。
