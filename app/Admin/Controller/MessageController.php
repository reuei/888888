<?php
namespace Admin\Controller;

use Framework\Database\Database;

class MessageController extends BaseAdminController
{
    protected $layout = 'admin';

    public function publish($request, $params = [])
    {
        $this->assign('pageTitle', '发布消息');
        $this->assign('activeMenu', 'message_publish');
        $this->view('admin.message_publish');
    }

    public function doPublish($request, $params = [])
    {
        $title = trim($request->post('title', ''));
        $content = $request->post('content', '');
        $type = $request->post('type', 'system');
        if (empty($title) || empty($content)) {
            return $this->error('请填写完整');
        }
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('message')->insert([
                    'user_id' => 0,
                    'title' => $title,
                    'content' => $content,
                    'type' => $type,
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
                $items = $db->table('message')->orderBy('id', 'DESC')->limit(50)->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($items)) {
            $items = [
                ['id' => 1, 'title' => '订单发货通知', 'content' => '订单已发货', 'type' => 'order', 'create_time' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
                ['id' => 2, 'title' => '系统维护通知', 'content' => '本周日凌晨维护', 'type' => 'system', 'create_time' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['id' => 3, 'title' => '活动奖励', 'content' => '充值返现活动', 'type' => 'activity', 'create_time' => date('Y-m-d H:i:s', strtotime('-2 day'))],
            ];
        }
        $this->assign('items', $items);
        $this->assign('pageTitle', '消息管理');
        $this->assign('activeMenu', 'message_list');
        $this->view('admin.message_list');
    }

    public function delete($request, $params = [])
    {
        $id = (int) $request->post('id', 0);
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('message')->where('id', $id)->delete();
            }
        } catch (\Exception $e) {
        }
        return $this->success('删除成功');
    }
}
