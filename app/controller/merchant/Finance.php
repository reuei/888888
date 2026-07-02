<?php
/**
 * Migrated from main_legacy/controller/app/controller/merchant/Finance.php
 */
namespace app\controller\merchant;

/**
 * 商户后台 - 资金管理
 */
class Finance extends Controller
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
     * 资金概览
     */
    public function index()
    {
        $merchantId = $this->getMerchantId();
        $merchant = Db::fetch("SELECT balance, frozen_balance FROM jz_merchant WHERE id = ?", [$merchantId]);

        $stat = Db::fetch(
            "SELECT
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
                COALESCE(SUM(CASE WHEN type = 'fee' THEN amount ELSE 0 END), 0) AS total_fee,
                COALESCE(SUM(CASE WHEN type = 'settle' THEN amount ELSE 0 END), 0) AS total_settle,
                COALESCE(SUM(CASE WHEN type = 'refund' THEN amount ELSE 0 END), 0) AS total_refund
             FROM jz_finance_flow WHERE merchant_id = ?",
            [$merchantId]
        );

        $pendingSettle = Db::fetch(
            "SELECT COALESCE(SUM(real_amount), 0) AS total FROM jz_settlement WHERE merchant_id = ? AND status IN (0, 1)",
            [$merchantId]
        );

        $recentFlow = Db::query(
            "SELECT * FROM jz_finance_flow WHERE merchant_id = ? ORDER BY id DESC LIMIT 10",
            [$merchantId]
        );

        $typeMap = [
            'income' => '收入',
            'refund' => '退款',
            'fee' => '手续费',
            'freeze' => '冻结',
            'unfreeze' => '解冻',
            'settle' => '结算提现',
        ];

        $this->assign('title', '资金概览');
        $this->assign('merchant', $merchant);
        $this->assign('stat', $stat);
        $this->assign('pendingSettle', $pendingSettle);
        $this->assign('recentFlow', $recentFlow);
        $this->assign('typeMap', $typeMap);
        $this->fetch('merchant/finance/index');
    }

    /**
     * 资金流水
     */
    public function flow()
    {
        $merchantId = $this->getMerchantId();
        $type = input('type', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'merchant_id = ?';
        $params = [$merchantId];
        if ($type) {
            $where .= ' AND type = ?';
            $params[] = $type;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_finance_flow WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_finance_flow WHERE {$where} ORDER BY id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $typeMap = [
            'income' => '收入',
            'refund' => '退款',
            'fee' => '手续费',
            'freeze' => '冻结',
            'unfreeze' => '解冻',
            'settle' => '结算提现',
        ];

        $this->assign('title', '资金流水');
        $this->assign('list', $list);
        $this->assign('typeMap', $typeMap);
        $this->assign('type', $type);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('merchant/finance/flow');
    }

    /**
     * 结算/提现记录
     */
    public function settle()
    {
        $merchantId = $this->getMerchantId();
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'merchant_id = ?';
        $params = [$merchantId];
        if ($status !== '') {
            $where .= ' AND status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_settlement WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_settlement WHERE {$where} ORDER BY id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $statusMap = [
            0 => '待处理',
            1 => '处理中',
            2 => '成功',
            3 => '失败',
        ];

        $this->assign('title', '结算提现');
        $this->assign('list', $list);
        $this->assign('statusMap', $statusMap);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('merchant/finance/settle');
    }

    /**
     * 申请提现
     */
    public function applyWithdraw()
    {
        $merchantId = $this->getMerchantId();
        $amount = (float) input('amount', 0);
        $channel = trim(input('channel', ''));
        $account = trim(input('account', ''));
        $accountName = trim(input('account_name', ''));

        if ($amount <= 0) {
            json_error('提现金额必须大于 0');
        }
        if (!$channel) {
            json_error('请选择结算渠道');
        }
        if (!$account) {
            json_error('请输入收款账号');
        }

        $merchant = Db::fetch("SELECT balance, frozen_balance FROM jz_merchant WHERE id = ? FOR UPDATE", [$merchantId]);
        if (!$merchant) {
            json_error('商户不存在');
        }
        $available = round((float) $merchant['balance'] - (float) $merchant['frozen_balance'], 2);
        if ($amount > $available) {
            json_error('提现金额超过可用余额（含冻结金额）');
        }

        $now = date('Y-m-d H:i:s');
        $newBalance = round((float) $merchant['balance'] - $amount, 2);

        Db::execute("UPDATE jz_merchant SET balance = ?, update_time = ? WHERE id = ?", [$newBalance, $now, $merchantId]);

        $settleNo = 'ST' . date('YmdHis') . mt_rand(1000, 9999);
        Db::insert('jz_settlement', [
            'settle_no' => $settleNo,
            'merchant_id' => $merchantId,
            'amount' => $amount,
            'fee' => 0,
            'real_amount' => $amount,
            'status' => 0,
            'channel' => $channel,
            'account' => $account,
            'account_name' => $accountName,
            'remark' => '商户申请提现',
            'create_time' => $now,
        ]);

        Db::insert('jz_finance_flow', [
            'merchant_id' => $merchantId,
            'order_id' => 0,
            'type' => 'settle',
            'amount' => $amount,
            'balance' => $newBalance,
            'remark' => '申请提现 ' . $settleNo,
            'create_time' => $now,
        ]);

        json_success('提现申请已提交，等待平台审核打款');
    }
}
