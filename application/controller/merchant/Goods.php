<?php
/**
 * 商户后台 - 商品管理示例
 */
class Merchant_Goods extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/merchant');
        if (!session('merchant_user')) {
            redirect(url('login') . '?type=merchant');
        }
    }

    public function index()
    {
        $merchant = session('merchant_user');
        $keyword = input('keyword', '');
        $where = 'merchant_id = ?';
        $params = [$merchant['id']];
        if ($keyword) {
            $where .= ' AND name LIKE ?';
            $params[] = '%' . $keyword . '%';
        }

        $goods = Db::query("SELECT * FROM jz_goods WHERE {$where} ORDER BY id DESC LIMIT 20", $params);
        $this->assign('title', '商品列表');
        $this->assign('goods', $goods);
        $this->assign('keyword', $keyword);
        $this->fetch('merchant/goods/index');
    }

    public function import()
    {
        $merchant = session('merchant_user');

        // 读取当前商户的卡密商品（用于选择导入目标）
        $goodsList = Db::query("SELECT id, name FROM jz_goods WHERE merchant_id = ? AND type = 1 ORDER BY id DESC", [$merchant['id']]);

        $this->assign('title', '批量导入卡密');
        $this->assign('goodsList', $goodsList);
        $this->fetch('merchant/goods/import');
    }

    public function doImport()
    {
        $merchant = session('merchant_user');
        $goodsId = (int) input('goods_id');
        $content = input('content', '');
        $separator = input('separator', 'newline');
        $dedup = input('dedup', '1');

        if (!$goodsId) {
            json_error('请选择商品');
        }
        if (!$content) {
            json_error('请输入或粘贴卡密内容');
        }

        $goods = Db::fetch("SELECT * FROM jz_goods WHERE id = ? AND merchant_id = ?", [$goodsId, $merchant['id']]);
        if (!$goods) {
            json_error('商品不存在或无权限');
        }

        // 解析分隔符
        $delim = ["newline" => "\n", "comma" => ",", "tab" => "\t"];
        $split = $delim[$separator] ?? "\n";
        if ($separator === 'custom') {
            $split = input('custom_sep', "\n");
        }

        $lines = explode($split, $content);
        $success = 0;
        $fail = 0;
        $errors = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // 校验卡密长度（示例规则：至少 4 位）
            if (mb_strlen($line) < 4) {
                $fail++;
                $errors[] = ['line' => $index + 1, 'content' => $line, 'reason' => '卡密长度不足'];
                continue;
            }

            // 去重
            if ($dedup) {
                $exists = Db::fetch("SELECT id FROM jz_card WHERE goods_id = ? AND content = ?", [$goodsId, $line]);
                if ($exists) {
                    $fail++;
                    $errors[] = ['line' => $index + 1, 'content' => $line, 'reason' => '卡密已存在'];
                    continue;
                }
            }

            Db::insert('jz_card', [
                'goods_id' => $goodsId,
                'merchant_id' => $merchant['id'],
                'content' => $line,
                'status' => 0,
                'create_time' => date('Y-m-d H:i:s'),
            ]);
            $success++;
        }

        // 更新商品库存
        if ($success > 0) {
            Db::execute("UPDATE jz_goods SET stock = stock + ? WHERE id = ?", [$success, $goodsId]);
        }

        json_success('导入完成', [
            'success' => $success,
            'fail' => $fail,
            'errors' => $errors,
        ]);
    }
}
