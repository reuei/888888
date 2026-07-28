<?php
namespace app\controller;

/**
 * 首页控制器
 */
class Index extends BaseController
{
    /**
     * 首页
     */
    public function index()
    {
        $siteSettings = $this->getSiteSettings();

        $products = [];
        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_products WHERE status = 1 ORDER BY sort DESC LIMIT 6");
            $stmt->execute();
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        $stats = [
            'products' => 0,
            'users' => 0,
            'licenses' => 0,
        ];
        try {
            $stats['products'] = $this->db->query("SELECT COUNT(*) FROM qf_products WHERE status = 1")->fetchColumn();
            $stats['users'] = $this->db->query("SELECT COUNT(*) FROM qf_users")->fetchColumn();
            $stats['licenses'] = $this->db->query("SELECT COUNT(*) FROM qf_licenses")->fetchColumn();
        } catch (\PDOException $e) {
        }

        return $this->render('index/index', [
            'products' => $products,
            'stats' => $stats,
            'siteSettings' => $siteSettings,
        ]);
    }

    /**
     * 平台介绍
     */
    public function platform()
    {
        $siteSettings = $this->getSiteSettings();
        return $this->render('index/platform', [
            'siteSettings' => $siteSettings,
        ]);
    }

    /**
     * 授权查询
     */
    public function licenseQuery()
    {
        $siteSettings = $this->getSiteSettings();
        $result = null;
        $error = null;

        $licenseKey = $this->get('key', '');
        if (!empty($licenseKey)) {
            try {
                $stmt = $this->db->prepare("
                    SELECT l.*, p.name AS product_name, p.description AS product_description, u.username
                    FROM qf_licenses l
                    JOIN qf_products p ON l.product_id = p.id
                    JOIN qf_users u ON l.user_id = u.id
                    WHERE l.license_key = ?
                ");
                $stmt->execute([$licenseKey]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$result) {
                    $error = '未找到该授权码';
                }
            } catch (\PDOException $e) {
                $error = '查询失败，请稍后重试';
            }
        }

        return $this->render('index/license-query', [
            'siteSettings' => $siteSettings,
            'result' => $result,
            'error' => $error,
            'key' => $licenseKey,
        ]);
    }

    /**
     * 文档中心
     */
    public function documents()
    {
        $siteSettings = $this->getSiteSettings();
        $docId = intval($this->get('doc', 0));

        $categories = [];
        $currentDoc = null;

        try {
            $stmt = $this->db->query("
                SELECT * FROM qf_documents
                ORDER BY category ASC, sort_order ASC, id ASC
            ");
            $documents = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($documents as $doc) {
                $cat = $doc['category'] ?: '未分类';
                if (!isset($categories[$cat])) {
                    $categories[$cat] = [];
                }
                $categories[$cat][] = $doc;

                if ($docId > 0 && $doc['id'] == $docId) {
                    $currentDoc = $doc;
                }
            }

            if ($currentDoc === null && !empty($documents)) {
                $currentDoc = $documents[0];
            }
        } catch (\PDOException $e) {
        }

        return $this->render('index/documents', [
            'siteSettings' => $siteSettings,
            'categories' => $categories,
            'currentDoc' => $currentDoc,
        ]);
    }

    /**
     * 公告页面
     */
    public function announcement()
    {
        $siteSettings = $this->getSiteSettings();
        return $this->render('index/announcement', [
            'siteSettings' => $siteSettings,
        ]);
    }

    /**
     * 切换语言
     */
    public function switchLang()
    {
        $currentLang = $_COOKIE['lang'] ?? 'zh';
        $newLang = $currentLang === 'zh' ? 'en' : 'zh';
        setcookie('lang', $newLang, time() + 86400 * 30, '/');

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }
}