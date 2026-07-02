<div class="breadcrumb">代理分销 / 代理商品</div>
<div class="page-header">
    <h2>代理商品</h2>
    <a href="javascript:;" class="btn" onclick="openModal()">+ 添加代理商品</a>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/agent/goods'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="商品名称 / ID">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>启用</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>禁用</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>商品</th>
            <th>售价</th>
            <th>佣金模式</th>
            <th>佣金</th>
            <th>多级分销</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无代理商品</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['goods_name'] ?? '-'); ?></div>
                <div style="color: #94A3B8; font-size: 12px;">商品ID: <?php echo $item['goods_id']; ?></div>
            </td>
            <td>¥ <?php echo $item['price'] ?? '0.00'; ?></td>
            <td><?php echo $item['commission_mode'] == 1 ? '按比例' : '固定金额'; ?></td>
            <td>
                <?php if ($item['commission_mode'] == 1): ?>
                <?php echo ($item['commission_rate'] * 100) . '%'; ?>
                <?php else: ?>
                ¥ <?php echo $item['commission_amount']; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item['multi_level']): ?>
                <span class="tag tag-blue">开启</span>
                <div style="color: #94A3B8; font-size: 12px;">二级 <?php echo ($item['level2_rate'] * 100) . '%'; ?> / 三级 <?php echo ($item['level3_rate'] * 100) . '%'; ?></div>
                <?php else: ?>
                <span class="tag">单级</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">启用</span>
                <?php else: ?>
                <span class="tag">禁用</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="javascript:;" class="btn btn-sm" onclick="openModal(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)">编辑</a>
                <?php if ($item['status'] == 1): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)">禁用</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)">启用</a>
                <?php endif; ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $item['id']; ?>, '<?php echo h($item['goods_name'] ?? ''); ?>')">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/agent/goods') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/agent/goods') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="modal" class="card" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 520px; z-index: 200; max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 id="modalTitle">添加代理商品</h3>
        <span style="cursor: pointer; font-size: 20px; color: #64748B;" onclick="closeModal()">×</span>
    </div>
    <form id="agentGoodsForm">
        <input type="hidden" name="id" id="editId" value="0">
        <div class="form-group">
            <label>选择商品</label>
            <select name="goods_id" id="goodsId" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                <option value="">请选择商品</option>
                <?php
                $goodsList = Db::query("SELECT id, name, price FROM jz_goods WHERE status = 1 ORDER BY id DESC LIMIT 200");
                foreach ($goodsList as $g):
                ?>
                <option value="<?php echo $g['id']; ?>"><?php echo h($g['name']); ?> (¥<?php echo $g['price']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>佣金模式</label>
            <select name="commission_mode" id="commissionMode" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;" onchange="toggleMode()">
                <option value="1">按比例</option>
                <option value="2">固定金额</option>
            </select>
        </div>
        <div class="form-group" id="rateBox">
            <label>佣金比例（0-1，如 0.1 表示 10%）</label>
            <input type="number" step="0.0001" max="1" min="0" name="commission_rate" id="commissionRate" value="0.1000">
        </div>
        <div class="form-group" id="amountBox" style="display: none;">
            <label>固定佣金金额（元）</label>
            <input type="number" step="0.01" min="0" name="commission_amount" id="commissionAmount" value="0.00">
        </div>
        <div class="form-group">
            <label>多级分销</label>
            <select name="multi_level" id="multiLevel" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;" onchange="toggleMultiLevel()">
                <option value="0">关闭</option>
                <option value="1">开启</option>
            </select>
        </div>
        <div id="multiLevelBox" style="display: none;">
            <div class="form-group">
                <label>二级佣金比例</label>
                <input type="number" step="0.0001" max="1" min="0" name="level2_rate" id="level2Rate" value="0.0500">
            </div>
            <div class="form-group">
                <label>三级佣金比例</label>
                <input type="number" step="0.0001" max="1" min="0" name="level3_rate" id="level3Rate" value="0.0200">
            </div>
        </div>
        <div class="form-group">
            <label>状态</label>
            <select name="status" id="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存</button>
    </form>
</div>
<div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); z-index: 199;" onclick="closeModal()"></div>

<script>
function toggleMode() {
    const mode = document.getElementById('commissionMode').value;
    document.getElementById('rateBox').style.display = mode == '1' ? 'block' : 'none';
    document.getElementById('amountBox').style.display = mode == '2' ? 'block' : 'none';
}

function toggleMultiLevel() {
    const enabled = document.getElementById('multiLevel').value == '1';
    document.getElementById('multiLevelBox').style.display = enabled ? 'block' : 'none';
}

function openModal(item) {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
    if (item) {
        document.getElementById('modalTitle').textContent = '编辑代理商品';
        document.getElementById('editId').value = item.id;
        document.getElementById('goodsId').value = item.goods_id;
        document.getElementById('commissionMode').value = item.commission_mode;
        document.getElementById('commissionRate').value = item.commission_rate;
        document.getElementById('commissionAmount').value = item.commission_amount;
        document.getElementById('multiLevel').value = item.multi_level;
        document.getElementById('level2Rate').value = item.level2_rate;
        document.getElementById('level3Rate').value = item.level3_rate;
        document.getElementById('status').value = item.status;
    } else {
        document.getElementById('modalTitle').textContent = '添加代理商品';
        document.getElementById('agentGoodsForm').reset();
        document.getElementById('editId').value = '0';
    }
    toggleMode();
    toggleMultiLevel();
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
}

document.getElementById('agentGoodsForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/agent/goodsSave'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存';
    }
});

async function toggleStatus(id, status) {
    if (!confirm(status == 1 ? '确认启用？' : '确认禁用？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/agent/goodsToggle'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function deleteItem(id, name) {
    if (!confirm('确认删除「' + name + '」的代理配置？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/agent/goodsDelete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
