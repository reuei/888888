<?php
/**
 * C 端客服聊天控制器
 */
class Chat extends Controller
{
    protected $subsite = null;

    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/main');
        $this->subsite = current_subsite();
        $this->assign('currentSubsite', $this->subsite);
    }

    /**
     * 聊天窗口
     */
    public function index()
    {
        $merchantId = (int) input('merchant_id', 0);
        $sessionId = (int) input('session_id', 0);
        $contact = trim(session('user_contact') ?? '');

        $merchant = null;
        if ($merchantId > 0) {
            $merchant = Db::fetch("SELECT id, shop_name, shop_id, status FROM jz_merchant WHERE id = ? AND status = 1", [$merchantId]);
        }

        $session = null;
        $messages = [];

        if ($sessionId > 0) {
            $session = Db::fetch("SELECT * FROM jz_chat_session WHERE id = ?", [$sessionId]);
            if ($session) {
                $merchantId = (int) $session['merchant_id'];
                $messages = Db::query("SELECT * FROM jz_chat_message WHERE session_id = ? ORDER BY create_time ASC", [$sessionId]);
                Db::execute("UPDATE jz_chat_message SET is_read = 1 WHERE session_id = ? AND sender_type = 1 AND is_read = 0", [$sessionId]);
            }
        } elseif ($merchantId > 0) {
            // 按浏览器指纹+商户查找已有会话
            $fingerprint = $this->getFingerprint();
            $session = Db::fetch(
                "SELECT * FROM jz_chat_session WHERE merchant_id = ? AND user_fingerprint = ? AND status = 1 ORDER BY id DESC LIMIT 1",
                [$merchantId, $fingerprint]
            );
            if ($session) {
                $sessionId = (int) $session['id'];
                $messages = Db::query("SELECT * FROM jz_chat_message WHERE session_id = ? ORDER BY create_time ASC", [$sessionId]);
                Db::execute("UPDATE jz_chat_message SET is_read = 1 WHERE session_id = ? AND sender_type = 1 AND is_read = 0", [$sessionId]);
            }
        }

        $this->assign('title', '在线客服');
        $this->assign('merchant', $merchant);
        $this->assign('merchantId', $merchantId);
        $this->assign('session', $session);
        $this->assign('sessionId', $sessionId);
        $this->assign('messages', $messages);
        $this->assign('contact', $contact);
        $this->fetch('chat/index');
    }

    /**
     * 发送消息
     */
    public function send()
    {
        $merchantId = (int) input('merchant_id', 0);
        $sessionId = (int) input('session_id', 0);
        $content = trim(input('content', ''));
        $userName = trim(input('user_name', ''));
        $contact = trim(input('contact', '') ?: (session('user_contact') ?? ''));

        if (!$merchantId) {
            json_error('请选择要咨询的商户');
        }
        if (!$content) {
            json_error('消息内容不能为空');
        }
        if (mb_strlen($content) > 500) {
            json_error('消息内容不能超过500字');
        }

        $merchant = Db::fetch("SELECT id FROM jz_merchant WHERE id = ? AND status = 1", [$merchantId]);
        if (!$merchant) {
            json_error('商户不存在或已下线');
        }

        $userId = 0;
        if ($contact) {
            $user = Db::fetch("SELECT id FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
            if ($user) {
                $userId = (int) $user['id'];
            }
        }

        $fingerprint = $this->getFingerprint();

        // 创建或复用会话
        if (!$sessionId) {
            $session = Db::fetch(
                "SELECT * FROM jz_chat_session WHERE merchant_id = ? AND user_fingerprint = ? AND status = 1 ORDER BY id DESC LIMIT 1",
                [$merchantId, $fingerprint]
            );
            if ($session) {
                $sessionId = (int) $session['id'];
            }
        }

        if (!$sessionId) {
            $sessionId = Db::insert('jz_chat_session', [
                'merchant_id' => $merchantId,
                'user_id' => $userId,
                'user_fingerprint' => $fingerprint,
                'user_name' => $userName ?: '游客',
                'contact' => $contact,
                'last_message' => $content,
                'unread_count' => 1,
                'status' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $session = Db::fetch("SELECT status FROM jz_chat_session WHERE id = ?", [$sessionId]);
            if (!$session) {
                json_error('会话不存在');
            }
            if ((int) $session['status'] === 0) {
                json_error('会话已关闭');
            }
            Db::execute(
                "UPDATE jz_chat_session SET last_message = ?, unread_count = unread_count + 1, update_time = ? WHERE id = ?",
                [$content, date('Y-m-d H:i:s'), $sessionId]
            );
        }

        Db::insert('jz_chat_message', [
            'session_id' => $sessionId,
            'sender_type' => 0,
            'content' => $content,
            'is_read' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        json_success('发送成功', ['session_id' => $sessionId]);
    }

    /**
     * 轮询新消息
     */
    public function poll()
    {
        $sessionId = (int) input('session_id', 0);
        $lastId = (int) input('last_id', 0);

        if (!$sessionId) {
            json_error('会话ID不能为空');
        }

        $session = Db::fetch("SELECT * FROM jz_chat_session WHERE id = ?", [$sessionId]);
        if (!$session) {
            json_error('会话不存在');
        }

        $messages = Db::query("SELECT * FROM jz_chat_message WHERE session_id = ? AND id > ? ORDER BY create_time ASC", [$sessionId, $lastId]);
        Db::execute("UPDATE jz_chat_message SET is_read = 1 WHERE session_id = ? AND sender_type = 1 AND is_read = 0", [$sessionId]);

        json_success('ok', ['messages' => $messages, 'status' => (int) $session['status']]);
    }

    /**
     * 获取浏览器指纹（简单实现）
     */
    private function getFingerprint()
    {
        $key = 'chat_fp';
        if (!empty($_COOKIE[$key])) {
            return preg_replace('/[^a-z0-9]/', '', $_COOKIE[$key]);
        }
        $fp = md5($_SERVER['HTTP_USER_AGENT'] ?? '' . ($_SERVER['REMOTE_ADDR'] ?? '') . uniqid());
        setcookie($key, $fp, time() + 86400 * 30, '/');
        return $fp;
    }
}
