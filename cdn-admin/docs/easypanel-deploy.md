# EasyPanel 虚拟主机部署指南

CloudShield CDN 已针对 EasyPanel 虚拟主机进行适配，无需 SSH、无需命令行，上传后即可通过浏览器完成安装。

## 一、准备工作

1. 购买支持 PHP 的 Linux 虚拟主机（推荐香港/内地 CN2 线路）。
2. 在 EasyPanel 面板中创建 MySQL 数据库，记录：
   - 数据库主机（通常为 `127.0.0.1` 或面板提供的内网地址）
   - 数据库名
   - 数据库用户名
   - 数据库密码
3. 确认 PHP 版本 ≥ 8.2，并开启 `pdo_mysql`、`json` 扩展。
4. 建议将 PHP `memory_limit` 设置为 ≥ 64M，`max_execution_time` ≥ 30 秒，以便导入演示数据时不会超时。

## 二、上传文件

### 方式 A：文档根目录指向 public/（推荐）

1. 将源码包解压到本地。
2. 使用 FTP/文件管理器上传全部文件到虚拟主机，例如 `public_html/cdn-admin/`。
3. 在 EasyPanel 的“域名管理”或“子目录绑定”中，将域名文档根目录设置为项目内的 `public/`。
4. 访问 `https://你的域名/install`。

### 方式 B：无法修改文档根目录

1. 将源码包解压并上传全部文件到站点根目录或子目录，例如 `public_html/` 或 `public_html/cdn-admin/`。
2. 访问 `https://你的域名/install` 或 `https://你的域名/cdn-admin/install`。
3. 根目录的 `.htaccess` 会自动将请求转发到 `public/index.php`。

## 三、目录权限设置

在 EasyPanel 文件管理器中，将以下目录权限设为 `755` 或 `777`：

- `config/`
- `data/`
- `runtime/`

如果面板提供“修复权限”按钮，直接点击即可。

## 四、运行安装向导

1. 访问 `/install`。
2. 环境检测页面会检查 PHP 版本、扩展、目录可写性、`open_basedir` 与禁用函数。
3. 填写 MySQL 信息（表前缀建议使用 `cdn_`）。
4. 设置管理员账号密码。
5. 勾选“导入演示数据”以体验完整功能。
6. 安装完成后，根据提示删除或重命名 `app/controller/Install.php` 与 `install/` 目录。

## 五、常见问题

### 1. 访问 /install 报 500

- 检查 PHP 版本是否 ≥ 8.2
- 检查 `config/`、`data/`、`runtime/` 是否可写
- 查看 `runtime/php_errors.log` 中的详细错误
- 确认 `.user.ini` 中的 `error_log` 路径可写

### 2. 数据库连接失败

- 确认数据库主机不是 `localhost`（部分主机需用 `127.0.0.1`）
- 确认账号具备 `CREATE DATABASE` 权限，或提前在面板中手动建库
- 确认 MySQL 版本 ≥ 5.7 或 MariaDB ≥ 10.2

### 3. open_basedir 限制

如果环境检测提示 `open_basedir` 受限，请在 EasyPanel 中关闭 `open_basedir`，或确保其包含项目目录。

### 4. 静态资源 404

- 方式 A：确认文档根目录已指向 `public/`
- 方式 B：确认 `.htaccess` 已上传，且 Apache `mod_rewrite` 已开启

## 六、安全建议

1. 安装完成后删除 `app/controller/Install.php`。
2. 删除或重命名 `install/` 目录。
3. 将 `config/database.php` 权限设为 `644`。
4. 定期备份 `data/config.php` 与数据库。
