<?php
/**
 * 定时任务接口（无需登录，通过密钥访问）
 */
class Cron extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('');
        $this->disableLayout();
    }

    /**
     * 自动备份数据库
     * URL: /cron/backup?key=xxx
     */
    public function backup()
    {
        $key = input('key', '');
        $cronKey = backup_get_cron_key();
        if (!$cronKey || $key !== $cronKey) {
            json_error('接口密钥错误');
        }

        $result = backup_database(date('Y-m-d H:i:s 自动备份'), 2, '定时任务自动备份');
        if ($result['code'] === 0) {
            json_success('自动备份成功', $result);
        }
        json_error($result['msg']);
    }
}
