<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/Backup.php
 */
namespace app\controller\admin;

/**
 * 总站后台 - 数据备份与一键恢复
 */
class Backup extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
        check_admin_role(['super', 'admin']);
    }

    /**
     * 备份列表
     */
    public function index()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;
        $type = (int) input('type', 0);

        $where = '1=1';
        $params = [];
        if (in_array($type, [1, 2], true)) {
            $where .= ' AND type = ?';
            $params[] = $type;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_backup WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_backup WHERE {$where} ORDER BY id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '数据备份');
        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/backup/index');
    }

    /**
     * 创建手动备份（AJAX）
     */
    public function create()
    {
        $name = trim(input('name', ''));
        $remark = trim(input('remark', ''));

        $result = backup_database($name, 1, $remark);
        if ($result['code'] === 0) {
            admin_log('backup_create', [
                'id' => $result['id'],
                'size' => $result['size'],
                'md5' => $result['md5'],
            ]);
        }

        json_success($result['msg'], $result);
    }

    /**
     * 下载备份文件
     */
    public function download()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            throw new \Exception('参数错误');
        }

        $record = Db::fetch("SELECT * FROM jz_backup WHERE id = ? AND status = 0", [$id]);
        if (!$record) {
            throw new \Exception('备份记录不存在或已失效');
        }

        $backupPath = backup_storage_path();
        $filename = basename($record['filename']);
        $filepath = $backupPath . $filename;

        if (!is_file($filepath)) {
            throw new \Exception('备份文件不存在');
        }

        $md5 = md5_file($filepath);
        if ($md5 !== $record['file_md5']) {
            throw new \Exception('备份文件校验失败');
        }

        admin_log('backup_download', ['id' => $id, 'filename' => $filename]);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        readfile($filepath);
        exit;
    }

    /**
     * 恢复备份（AJAX）
     */
    public function restore()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('参数错误');
        }

        $result = backup_restore($id);
        if ($result['code'] === 0) {
            json_success($result['msg']);
        }
        json_error($result['msg']);
    }

    /**
     * 删除备份（AJAX）
     */
    public function delete()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('参数错误');
        }

        $result = backup_delete($id);
        if ($result['code'] === 0) {
            json_success($result['msg']);
        }
        json_error($result['msg']);
    }
}
