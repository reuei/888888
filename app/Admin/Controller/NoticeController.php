<?php
namespace Admin\Controller;

use Framework\Database\Database;

class NoticeController extends BaseAdminController
{
    protected $layout = 'admin';

    public function publish($request, $params = [])
    {
        $this->assign('pageTitle', '发布公告');
        $this->assign('activeMenu', 'notice_publish');
        $this->view('admin.notice_publish');
    }

    public function doPublish($request, $params = [])
    {
        $title = trim($request->post('title', ''));
        $content = $request->post('content', '');
        if (empty($title) || empty($content)) {
            return $this->error('请填写完整');
        }
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('article')->insert([
                    'title' => $title,
                    'content' => $content,
                    'category' => 'notice',
                    'status' => 1,
                    'create_time' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Exception $e) {
        }
        return $this->success('发布成功');
    }

    public function list($request, $params = [])
    {
        $items = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $items = $db->table('article')->where('category', 'notice')->orderBy('id', 'DESC')->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($items)) {
            $items = [
                ['id' => 1, 'title' => '欢迎使用玄武发卡 v1.0.5', 'content' => '全新版本上线', 'status' => 1, 'create_time' => date('Y-m-d H:i:s')],
                ['id' => 2, 'title' => '系统维护通知', 'content' => '本周日凌晨', 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ];
        }
        $this->assign('items', $items);
        $this->assign('pageTitle', '公告管理');
        $this->assign('activeMenu', 'notice_list');
        $this->view('admin.notice_list');
    }

    public function delete($request, $params = [])
    {
        $id = (int) $request->post('id', 0);
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('article')->where('id', $id)->delete();
            }
        } catch (\Exception $e) {
        }
        return $this->success('删除成功');
    }
}
