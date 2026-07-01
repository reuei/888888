<?php
/**
 * 后台版本更新包管理
 */
class Admin_Version extends Controller
{
    public function __construct()
    {
        parent::__construct();
        require_admin_login();
        $this->setLayout('layout/admin');
    }

    public function index()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;
        $list = Db::query("SELECT * FROM qef_version ORDER BY id DESC LIMIT {$offset}, {$pageSize}");
        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_version");
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));

        $this->assign('title', '版本更新包');
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/version/index');
    }

    public function add()
    {
        $this->assign('title', '新增版本');
        $this->assign('version', []);
        $this->fetch('admin/version/edit');
    }

    public function edit()
    {
        $id = (int) input('id', 0);
        $version = Db::fetch("SELECT * FROM qef_version WHERE id = ?", [$id]);
        if (!$version) {
            throw new Exception('版本不存在');
        }
        $this->assign('title', '编辑版本');
        $this->assign('version', $version);
        $this->fetch('admin/version/edit');
    }

    public function save()
    {
        $id = (int) input('id', 0);
        $version = trim(input('version', ''));
        $releaseDate = input('release_date', '');
        $updateDesc = trim(input('update_desc', ''));
        $forceUpdate = (int) input('force_update', 0);
        $isLatest = (int) input('is_latest', 0);

        if (!$version) {
            json_error('请输入版本号');
        }

        if (!$id || !empty($_FILES['file']['name'])) {
            $result = upload_zip('file', 'update');
            if ($result['code'] !== 0) {
                json_error($result['msg']);
            }
            $filePath = $result['path'];
            $fullPath = ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
            $fileMd5 = md5_file($fullPath);
            $fileSize = filesize($fullPath);
        } else {
            $filePath = null;
        }

        if ($isLatest) {
            Db::execute("UPDATE qef_version SET is_latest = 0");
        }

        $data = [
            'version' => $version,
            'release_date' => $releaseDate,
            'update_desc' => $updateDesc,
            'force_update' => $forceUpdate,
            'is_latest' => $isLatest,
        ];
        if ($filePath) {
            $data['file_path'] = $filePath;
            $data['file_md5'] = $fileMd5;
            $data['file_size'] = $fileSize;
        }

        if ($id) {
            Db::update('qef_version', $data, 'id = ?', [$id]);
            admin_log('编辑版本', ['id' => $id]);
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::insert('qef_version', $data);
            admin_log('新增版本', ['version' => $version]);
        }

        json_success('保存成功', ['redirect' => url('admin/version')]);
    }

    public function delete()
    {
        $id = (int) input('id', 0);
        $version = Db::fetch("SELECT file_path FROM qef_version WHERE id = ?", [$id]);
        if ($version && $version['file_path']) {
            $fullPath = ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $version['file_path']);
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
        Db::delete('qef_version', 'id = ?', [$id]);
        admin_log('删除版本', ['id' => $id]);
        json_success('删除成功');
    }
}
