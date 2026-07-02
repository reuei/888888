<div class="breadcrumb">商品管理 / 批量导入卡密</div>
<div class="page-header">
    <h2>批量导入卡密</h2>
    <div>
        <a href="<?php echo url('merchant/goods'); ?>" class="btn btn-outline">返回商品列表</a>
    </div>
</div>

<div class="card">
    <form id="importForm">
        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">选择商品</label>
            <select name="goods_id" style="width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
                <option value="">请选择卡密商品</option>
                <?php foreach ($goodsList as $g): ?>
                <option value="<?php echo $g['id']; ?>"><?php echo h($g['name']); ?> (ID: <?php echo $g['id']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">分隔符</label>
            <label style="margin-right: 16px; font-weight: normal;"><input type="radio" name="separator" value="newline" checked> 换行符</label>
            <label style="margin-right: 16px; font-weight: normal;"><input type="radio" name="separator" value="comma"> 逗号</label>
            <label style="margin-right: 16px; font-weight: normal;"><input type="radio" name="separator" value="tab"> 制表符</label>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">
                <input type="checkbox" name="dedup" value="1" checked> 跳过已存在卡密
            </label>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">卡密内容</label>
            <textarea name="content" rows="12" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; font-family: monospace;" placeholder="请粘贴卡密内容，每行一条"></textarea>
            <p style="color: #64748B; font-size: 12px; margin-top: 6px;">支持直接粘贴，系统会自动过滤空行并校验长度。</p>
        </div>

        <button type="submit" class="btn" id="submitBtn">开始导入</button>
    </form>

    <div id="resultBox" style="display: none; margin-top: 24px; padding: 16px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px;">
        <h3 style="font-size: 16px; margin-bottom: 12px;">导入结果</h3>
        <div id="resultSummary" style="margin-bottom: 12px;"></div>
        <div id="resultErrors" style="display: none;">
            <table>
                <tr><th>行号</th><th>内容</th><th>原因</th></tr>
                <tbody id="errorList"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('importForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = '导入中...';

        const formData = new FormData(e.target);
        try {
            const res = await fetch('<?php echo url('merchant/goods/doImport'); ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            const box = document.getElementById('resultBox');
            box.style.display = 'block';

            const summary = `<span style="color: #10B981; font-weight: 600;">成功 ${data.data.success} 条</span>，<span style="color: #EF4444; font-weight: 600;">失败 ${data.data.fail} 条</span>`;
            document.getElementById('resultSummary').innerHTML = summary;

            const errors = data.data.errors || [];
            if (errors.length > 0) {
                document.getElementById('resultErrors').style.display = 'block';
                document.getElementById('errorList').innerHTML = errors.map(item =>
                    `<tr><td>${item.line}</td><td>${item.content}</td><td>${item.reason}</td></tr>`
                ).join('');
            } else {
                document.getElementById('resultErrors').style.display = 'none';
            }
        } catch (err) {
            alert('请求失败，请重试');
        } finally {
            btn.disabled = false;
            btn.textContent = '开始导入';
        }
    });
</script>
