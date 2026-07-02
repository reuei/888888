# CloudShield CDN 常见问题

## 1. 能否在 EasyPanel 虚拟主机上运行？

可以。项目已针对 EasyPanel 适配，提供 `.user.ini` 配置、相对路径路由、子目录部署支持，上传后即可浏览器安装。

## 2. 与鹿云盾、SCDN 有什么区别？

鹿云盾与 SCDN 是闭源 SaaS，按量计费，数据托管在平台。CloudShield CDN 是源码交付、自主部署、一次性授权，更适合需要数据私有、二次开发、代理分销的用户。

## 3. 是否支持自定义 CDN 节点？

支持。系统内置节点管理、套餐管理、商户管理，用户可对接自有或第三方节点资源。

## 4. 安装完成后如何重新安装？

删除以下文件后重新访问 `/install`：

- `data/install.lock`
- `data/config.php`
- `config/database.php`

## 5. 忘记管理员密码怎么办？

删除 `data/config.php`，重新运行安装向导设置新管理员。数据库中的业务数据不会丢失。

## 6. 如何修改数据库配置？

编辑 `config/database.php` 中的 `connections.mysql` 配置项，修改后刷新页面即可生效。

## 7. 为什么访问某些页面报 500？

- 检查 PHP 版本是否 ≥ 8.0
- 检查是否开启 `pdo_mysql`、`json`
- 检查 `config/`、`data/`、`runtime/` 是否可写
- 查看 `runtime/php_errors.log` 获取详细错误

## 8. 源码是否可以商用？

本项目采用 MIT 许可证，允许商用、修改、分发。详见 LICENSE.txt。
