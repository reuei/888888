# 玄武发卡 v1.0.5

> 自研轻量MVC框架 · 数字商品交易平台

## 全新特性

- **全新架构**：从 v1.0.5 开始，采用自研轻量MVC框架，移除 ThinkPHP
- **全新 UI**：深色极简设计，区别于之前版本的明亮玻璃拟态
- **全新配色**：墨黑 + 翠绿 + 琥珀金
- **授权站升级**：v1.1.1，对标 entropy.slmsns.com

## 系统要求

- PHP >= 8.2
- MySQL 5.6 - 5.9
- 启用扩展：PDO、PDO_MySQL、mbstring、JSON、openssl、session、curl

## 部署步骤

1. 上传 zip 到虚拟主机并解压
2. 将站点运行目录（web根目录）设置为 `public`
3. 访问 `/install` 完成安装向导
4. 安装完成后删除 `install` 目录

## 默认账号

- 前台用户：`test` / `123456`
- 后台管理员：`admin` / `admin888`
- 授权站后台：`admin` / `license888`

## 目录结构

```
/
├── app/                # 应用代码
│   ├── Home/          # 前台总站
│   ├── User/          # 用户中心
│   ├── Admin/         # 后台管理
│   ├── License/       # 授权站 v1.1.1
│   └── Install/       # 安装向导
├── framework/          # 自研框架
│   ├── App.php        # 应用类
│   ├── Router.php     # 路由
│   ├── Controller.php # 控制器基类
│   ├── Request.php    # 请求
│   ├── Response.php   # 响应
│   ├── Session.php    # Session
│   ├── Database/      # 数据库层
│   └── Cache/         # 缓存
├── config/             # 配置文件
├── public/             # 公共目录（web根）
├── install/            # 安装程序
├── runtime/            # 运行时
├── index.php           # 入口文件
└── routes.php          # 路由定义
```

## 授权系统

授权站独立运行（`/license`），提供：
- 授权验证 API：`/license/api/check`
- 域名激活 API：`/license/api/activate`
- 心跳上报 API：`/license/api/heartbeat`

## 版本对应

| 客户端 | 授权协议 |
| --- | --- |
| v1.0.5 | v1.1.1 |
| v1.0.4 | v1.1.0 |
| v1.0.3 | v1.0.x (已停止) |
