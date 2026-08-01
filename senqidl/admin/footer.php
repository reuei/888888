<?php
$adminUser = Auth::user();
?>
            </main>
            <footer class="admin-footer">
                <div>© <?php echo date('Y'); ?> <?php echo h(DB::getSettingValue('site_name', SITE_NAME)); ?> 管理后台</div>
                <div>当前用户: <?php echo h($adminUser['username'] ?? 'admin'); ?> | 登录时间: <?php echo h(date('Y-m-d H:i:s', $_SESSION['login_time'] ?? time())); ?></div>
            </footer>
        </div>
    </div>

    <script>
    (function() {
        const toggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (toggle && sidebar) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                backdrop.classList.toggle('active');
            });
        }
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                sidebar.classList.remove('open');
                backdrop.classList.remove('active');
            });
        }

        document.querySelectorAll('.toggle-status').forEach(function(cb) {
            cb.addEventListener('change', function() {
                var table = this.dataset.table;
                var id = this.dataset.id;
                var status = this.checked ? 1 : 0;
                var form = new FormData();
                form.append('action', 'toggle_status');
                form.append('table', table);
                form.append('id', id);
                form.append('status', status);
                fetch('<?php echo SITE_URL; ?>/admin/ajax.php', {
                    method: 'POST',
                    body: form
                }).then(function(r) { return r.json(); })
                .then(function(d) {
                    if (!d.success) { alert(d.message); }
                }).catch(function() { alert('网络错误'); });
            });
        });

        var sortableTable = document.getElementById('sortableTable');
        if (sortableTable) {
            var dragRow = null;
            sortableTable.querySelectorAll('.admin-sortable-item').forEach(function(row) {
                row.addEventListener('dragstart', function(e) {
                    dragRow = this;
                    this.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                });
                row.addEventListener('dragend', function() {
                    this.classList.remove('dragging');
                    sortableTable.querySelectorAll('.admin-sortable-item').forEach(function(r) {
                        r.classList.remove('drag-over');
                    });
                });
                row.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('drag-over');
                });
                row.addEventListener('dragleave', function() {
                    this.classList.remove('drag-over');
                });
                row.addEventListener('drop', function(e) {
                    e.preventDefault();
                    if (dragRow && dragRow !== this) {
                        var tbody = sortableTable.querySelector('tbody');
                        var rows = Array.from(tbody.querySelectorAll('.admin-sortable-item'));
                        var dragIdx = rows.indexOf(dragRow);
                        var dropIdx = rows.indexOf(this);
                        if (dragIdx < dropIdx) {
                            this.after(dragRow);
                        } else {
                            this.before(dragRow);
                        }
                        var newRows = Array.from(tbody.querySelectorAll('.admin-sortable-item'));
                        var items = newRows.map(function(r, i) {
                            return { id: parseInt(r.dataset.id), sort: i + 1 };
                        });
                        var form = new FormData();
                        form.append('action', 'save_sort');
                        form.append('table', 'slides');
                        form.append('items', JSON.stringify(items));
                        fetch('<?php echo SITE_URL; ?>/admin/ajax.php', {
                            method: 'POST',
                            body: form
                        }).then(function(r) { return r.json(); })
                        .then(function(d) {
                            if (!d.success) { alert(d.message); }
                        }).catch(function() { alert('排序保存失败'); });
                    }
                });
            });
        }
    })();
    </script>
</body>
</html>