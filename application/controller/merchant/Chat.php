<?php
/**
 * 商户后台 - 客服管理
 */
class Merchant_Chat extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/merchant');
        if (!session('merchant_user')) {
            redirect(url('login') . '?type=merchant');
        }
    }

    private function getMerchantId()
    {
        return (int) (session('merchant_user')['id'] ?? 0);
    }

    /**
     * 咨询列表
     */
    public function index()
    {
        $merchantId = $this->getMerchantId();
        $status = input('status', '');
        $keyword = trim(input('keyword', ''));

        $where = 'merchant_id = ?';
        $params = [$merchantId];

        if ($status !== '' && in_array((int) $status, [0, 1], true)) {
            $where .= ' AND status = ?';
            $params[] = (int) $status;
        }
        if ($keyword) {
            $where .= ' AND (user_name LIKE ? OR contact LIKE ? OR last_message LIKE ?)';
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }

        $page = max(1, (int) input('page', 1));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $total = Db::fetch("SELECT COUNT(*) AS total FROM jz_chat_session WHERE {$where}", $params);
        $sessions = Db::query("SELECT * FROM jz_chat_session WHERE {$where} ORDER BY update_time DESC LIMIT {$offset}, {$pageSize}", $params);

        $this->assign('title', '咨询列表');
        $this->assign('sessions', $sessions);
        $this->assign('status', $status);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('pageSize', $pageSize);
        $this->assign('total', $total['total'] ?? 0);
        $this->fetch('merchant/chat/index');
    }

    /**
     * 回复会话
     */
    public function session()
    {
        $merchantId = $this->getMerchantId();
        $sessionId = (int) input('id', 0);

        $session = Db::fetch("SELECT * FROM jz_chat_session WHERE id = ? AND merchant_id = ?", [$sessionId, $merchantId]);
        if (!$session) {
            $this->assign('title', '会话不存在');
            $this->fetch('merchant/chat/session');
            return;
        }

        $messages = Db::query("SELECT * FROM jz_chat_message WHERE session_id = ? ORDER BY create_time ASC", [$sessionId]);

        // 标记商户未读消息为已读
        Db::execute("UPDATE jz_chat_message SET is_read = 1 WHERE session_id = ? AND sender_type = 0 AND is_read = 0", [$sessionId]);
        Db::execute("UPDATE jz_chat_session SET unread_count = 0 WHERE id = ?", [$sessionId]);

        $this->assign('title', '回复会话');
        $this->assign('session', $session);
        $this->assign('messages', $messages);
        $this->fetch('merchant/chat/session');
    }

    /**
     * 发送消息
     */
    public function send()
    {
        $merchantId = $this->getMerchantId();
        $sessionId = (int) input('session_id', 0);
        $content = trim(input('content', ''));

        if (!$sessionId) {
            json_error('会话ID不能为空');
        }
        if (!$content) {
            json_error('消息内容不能为空');
        }
        if (mb_strlen($content) > 500) {
            json_error('消息内容不能超过500字');
        }

        $session = Db::fetch("SELECT * FROM jz_chat_session WHERE id = ? AND merchant_id = ?", [$sessionId, $merchantId]);
        if (!$session) {
            json_error('会话不存在');
        }
        if ((int) $session['status'] === 0) {
            json_error('会话已关闭，无法回复');
        }

        Db::insert('jz_chat_message', [
            'session_id' => $sessionId,
            'sender_type' => 1,
            'content' => $content,
            'is_read' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        Db::execute("UPDATE jz_chat_session SET last_message = ?, update_time = ? WHERE id = ?", [$content, date('Y-m-d H:i:s'), $sessionId]);

        json_success('发送成功');
    }

    /**
     * 关闭会话
     */
    public function close()
    {
        $merchantId = $this->getMerchantId();
        $sessionId = (int) input('id', 0);

        $session = Db::fetch("SELECT * FROM jz_chat_session WHERE id = ? AND merchant_id = ?", [$sessionId, $merchantId]);
        if (!$session) {
            json_error('会话不存在');
        }

        Db::execute("UPDATE jz_chat_session SET status = 0, update_time = ? WHERE id = ?", [date('Y-m-d H:i:s'), $sessionId]);
        Db::insert('jz_chat_message', [
            'session_id' => $sessionId,
            'sender_type' => 2,
            'content' => '商户已结束本次会话',
            'is_read' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        json_success('会话已关闭');
    }

    /**
     * 获取新消息（轮询）
     */
    public function poll()
    {
        $merchantId = $this->getMerchantId();
        $sessionId = (int) input('session_id', 0);
        $lastId = (int) input('last_id', 0);

        if (!$sessionId) {
            json_error('会话ID不能为空');
        }

        $session = Db::fetch("SELECT * FROM jz_chat_session WHERE id = ? AND merchant_id = ?", [$sessionId, $merchantId]);
        if (!$session) {
            json_error('会话不存在');
        }

        $messages = Db::query("SELECT * FROM jz_chat_message WHERE session_id = ? AND id > ? ORDER BY create_time ASC", [$sessionId, $lastId]);

        // 标记为已读
        Db::execute("UPDATE jz_chat_message SET is_read = 1 WHERE session_id = ? AND sender_type = 0 AND is_read = 0", [$sessionId]);
        Db::execute("UPDATE jz_chat_session SET unread_count = 0 WHERE id = ?", [$sessionId]);

        json_success('ok', ['messages' => $messages]);
    }
}
