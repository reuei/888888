<?php
/**
 * 通用文件下载入口（更新包 / 插件）
 * GET /api/download?type=version&id=...
 * GET /api/download?type=plugin&token=...
 */

namespace app\controller\api;

use app\BaseController;
use app\Db;
use think\App;

class Download extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->layoutEnabled = false;
    }

    public function index()
    {
        $type = input('type', '');
        $token = input('token', '');
        $id = (int) input('id', 0);

        $filePath = '';
        if ($type === 'plugin') {
            $filePath = verify_download_token($token);
            if (!$filePath) {
                $this->error('下载链接无效或已过期');
            }
        } elseif ($type === 'version') {
            $version = Db::fetch("SELECT file_path FROM qef_version WHERE id = ?", [$id]);
            if (!$version) {
                $this->error('版本不存在');
            }
            $filePath = $version['file_path'];
        } else {
            $this->error('下载类型错误');
        }

        $fullPath = public_path() . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        if (!is_file($fullPath)) {
            $this->error('文件不存在');
        }

        $filename = basename($fullPath);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }

    private function error($msg = 'error', $code = 1)
    {
        json_error($msg, $code);
    }
}
