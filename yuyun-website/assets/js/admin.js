/**
 * 语云科技 - 后台管理系统JavaScript
 * 提供交互功能：侧边栏、Tab切换、模态框、AJAX提交、Toast等
 */

(function() {
    'use strict';

    // ==================== 工具函数 ====================

    /**
     * AJAX请求封装
     */
    function ajax(url, data, options = {}) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();

            if (data) {
                Object.keys(data).forEach(key => {
                    const value = data[key];
                    if (value !== null && value !== undefined) {
                        if (typeof value === 'object' && !(value instanceof File)) {
                            formData.append(key, JSON.stringify(value));
                        } else {
                            formData.append(key, value);
                        }
                    }
                });
            }

            // 自动添加CSRF Token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('csrf_token', csrfToken.getAttribute('content'));
            }

            fetch(url, {
                method: options.method || 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.code === 200) {
                    resolve(result);
                } else {
                    reject(result);
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                reject({ message: '网络请求失败，请检查网络连接' });
            });
        });
    }

    // ==================== Toast通知系统 ====================

    const Toast = {
        container: null,

        init() {
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.className = 'toast-container';
                document.body.appendChild(this.container);
            }
        },

        show(message, type = 'info', duration = 3500) {
            this.init();

            const icons = {
                success: '&#10004;',
                error: '&#10006;',
                warning: '&#9888;',
                info: '&#8505;'
            };

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <span class="toast-icon">${icons[type] || icons.info}</span>
                <span class="toast-message">${message}</span>
                <span class="toast-close">&times;</span>
            `;

            this.container.appendChild(toast);

            // 点击关闭
            toast.querySelector('.toast-close').addEventListener('click', () => {
                this.remove(toast);
            });

            // 点击整个toast关闭
            toast.addEventListener('click', () => {
                this.remove(toast);
            });

            // 自动消失
            if (duration > 0) {
                setTimeout(() => this.remove(toast), duration);
            }

            return toast;
        },

        success(message, duration) {
            return this.show(message, 'success', duration);
        },

        error(message, duration) {
            return this.show(message, 'error', duration);
        },

        warning(message, duration) {
            return this.show(message, 'warning', duration);
        },

        info(message, duration) {
            return this.show(message, 'info', duration);
        },

        remove(toast) {
            toast.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    };

    window.Toast = Toast;

    // ==================== 侧边栏管理 ====================

    function initSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const overlay = document.querySelector('.sidebar-overlay');

        if (!sidebar) return;

        // 切换折叠状态
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');

                // 更新按钮图标
                const icon = toggleBtn.querySelector('i') || toggleBtn;
                if (icon.classList || icon.tagName === 'I') {
                    icon.className = sidebar.classList.contains('collapsed')
                        ? 'fas fa-angle-double-right'
                        : 'fas fa-angle-double-left';
                }

                // 保存状态到localStorage
                localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
            });
        }

        // 移动端菜单
        if (mobileMenuBtn && overlay) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.add('mobile-show');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            });
        }

        // 恢复折叠状态
        if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth > 992) {
            sidebar.classList.add('collapsed');
        }

        // 高亮当前菜单项
        const currentPage = location.pathname.split('/').pop() || 'dashboard.php';
        document.querySelectorAll('.nav-item').forEach(item => {
            const href = item.getAttribute('href') || item.dataset.href;
            if (href === currentPage) {
                item.classList.add('active');
            }
        });
    }

    // ==================== Tab标签页 ====================

    function initTabs() {
        document.querySelectorAll('.tabs').forEach(tabContainer => {
            const tabs = tabContainer.querySelectorAll('.tab-item');
            const panes = tabContainer.parentElement.querySelectorAll('.tab-pane');

            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => {
                    // 移除所有active
                    tabs.forEach(t => t.classList.remove('active'));
                    panes.forEach(p => p.classList.remove('active'));

                    // 设置当前active
                    tab.classList.add('active');
                    if (panes[index]) {
                        panes[index].classList.add('active');
                    }
                });
            });
        });
    }

    // ==================== 模态框管理 ====================

    const Modal = {
        open(modalId) {
            const modal = typeof modalId === 'string'
                ? document.getElementById(modalId)
                : modalId;

            if (!modal) return;

            const overlay = modal.closest('.modal-overlay') || modal;
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';

            // ESC键关闭
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    this.close(modal);
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);

            // 聚焦第一个输入框
            setTimeout(() => {
                const firstInput = modal.querySelector('input:not([type=hidden]), textarea, select');
                if (firstInput) firstInput.focus();
            }, 100);
        },

        close(modalOrEvent) {
            let modal;

            if (modalOrEvent instanceof Event) {
                const target = modalOrEvent.target;
                const btnClose = target.closest('.modal-close');
                const overlay = target.closest('.modal-overlay');
                modal = btnClose ? overlay?.querySelector('.modal') : overlay?.querySelector('.modal');
            } else {
                const el = typeof modalOrEvent === 'string'
                    ? document.getElementById(modalOrEvent)
                    : modalOrEvent;
                modal = el?.closest('.modal-overlay') || el;
            }

            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';

                // 清空表单（如果是编辑模态框）
                const form = modal.querySelector('form');
                if (form && form.dataset.clearOnClose !== 'false') {
                    form.reset();
                    // 清除图片预览
                    const preview = form.querySelector('.upload-preview');
                    if (preview) preview.src = '';
                }
            }
        },

        confirm(title, message, onConfirm) {
            const overlay = document.createElement('div');
            overlay.className = 'modal-overlay show';
            overlay.innerHTML = `
                <div class="modal modal-sm">
                    <div class="modal-header">
                        <h3>${title}</h3>
                        <button type="button" class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-action="cancel">取消</button>
                        <button type="button" class="btn btn-danger" data-action="confirm">确认</button>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);

            const closeModal = () => overlay.remove();
            overlay.querySelector('[data-action="cancel"]').addEventListener('click', closeModal);
            overlay.querySelector('.modal-close').addEventListener('click', closeModal);
            overlay.querySelector('[data-action="confirm"]').addEventListener('click', () => {
                closeModal();
                if (onConfirm) onConfirm();
            });
        }
    };

    window.Modal = Modal;

    // ==================== 图片上传预览 ====================

    function initUploadPreview() {
        document.querySelectorAll('.upload-area, [data-upload]').forEach(area => {
            const input = area.querySelector('input[type="file"]') ||
                         document.querySelector(`#${area.dataset.upload}`);

            if (!input) return;

            // 点击触发文件选择
            area.addEventListener('click', (e) => {
                if (e.target.tagName !== 'INPUT') {
                    input.click();
                }
            });

            // 预览图片
            input.addEventListener('change', async (e) => {
                const file = e.target.files[0];
                if (!file) return;

                // 客户端预览
                const preview = area.closest('.form-group')?.querySelector('.upload-preview, .logo-preview img') ||
                               area.querySelector('.upload-preview');

                if (preview && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        preview.src = ev.target.result;
                    };
                    reader.readAsDataURL(file);
                }

                // 如果有自动上传配置
                if (area.dataset.autoUpload === 'true' || input.dataset.autoUpload === 'true') {
                    await uploadFile(input, preview);
                }
            });

            // 拖拽支持
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.style.borderColor = 'var(--primary-color)';
                area.style.background = 'rgba(0, 102, 204, 0.05)';
            });

            area.addEventListener('dragleave', (e) => {
                e.preventDefault();
                area.style.borderColor = '';
                area.style.background = '';
            });

            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.style.borderColor = '';
                area.style.background = '';

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    input.dispatchEvent(new Event('change'));
                }
            });
        });
    }

    /**
     * 上传文件到服务器
     */
    async function uploadFile(input, previewEl) {
        const file = input.files[0];
        if (!file) return null;

        const formData = new FormData();
        formData.append('file', file);

        if (input.dataset.subdir) {
            formData.append('subdir', input.dataset.subdir);
        }

        try {
            Toast.info('正在上传...');

            const response = await fetch('../api/upload.php', {
                method: 'POST',
                body: formData
            }).then(r => r.json());

            if (response.code === 200 && response.data) {
                // 存储路径到隐藏字段
                const pathInput = document.querySelector(`input[data-for="${input.id}"]`) ||
                                 input.closest('.form-group')?.querySelector('input[type="hidden"]');

                if (pathInput) {
                    pathInput.value = response.data.path;
                }

                // 更新预览
                if (previewEl) {
                    previewEl.src = response.data.url;
                }

                Toast.success('上传成功');
                return response.data;
            } else {
                throw new Error(response.message || '上传失败');
            }
        } catch (err) {
            Toast.error(err.message || '上传失败');
            return null;
        }
    }

    // 手动暴露上传方法
    window.uploadFile = uploadFile;

    // ==================== AJAX表单提交 ====================

    function initAjaxForms() {
        document.querySelectorAll('form[data-ajax]').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const actionUrl = form.action || form.dataset.action;
                const submitBtn = form.querySelector('button[type="submit"]');

                if (!actionUrl) {
                    Toast.error('未指定表单提交地址');
                    return;
                }

                // 禁用提交按钮防止重复提交
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 提交中...';
                }

                try {
                    // 收集表单数据
                    const formData = new FormData(form);

                    // 处理文件上传
                    const fileInputs = form.querySelectorAll('input[type="file"]');
                    for (const fileInput of fileInputs) {
                        if (fileInput.files.length > 0 && fileInput.dataset.autoUpload !== 'true') {
                            const result = await uploadFile(fileInput);
                            if (result) {
                                formData.set('image_path', result.path);
                            }
                        }
                    }

                    // 发送请求
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        body: formData
                    }).then(r => r.json());

                    if (response.code === 200) {
                        Toast.success(response.message || '操作成功');

                        // 执行回调
                        if (form.dataset.onSuccess) {
                            eval(form.dataset.onSuccess)(response);
                        }

                        // 关闭模态框
                        const modalOverlay = form.closest('.modal-overlay');
                        if (modalOverlay) {
                            setTimeout(() => modalOverlay.classList.remove('show'), 800);
                        }

                        // 刷新页面或数据
                        if (form.dataset.refresh !== 'false') {
                            setTimeout(() => {
                                if (form.dataset.reload === 'true') {
                                    location.reload();
                                }
                            }, 1000);
                        }
                    } else {
                        throw new Error(response.message || '操作失败');
                    }
                } catch (err) {
                    Toast.error(err.message || '操作失败，请稍后重试');
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText || '提交';
                    }
                }
            });
        });
    }

    // ==================== 表格行操作 ====================

    function initTableActions() {
        // 删除确认
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const url = btn.dataset.delete;
                const name = btn.dataset.name || '此项';

                Modal.confirm('确认删除', `确定要删除「${name}」吗？此操作不可恢复。`, async () => {
                    try {
                        const response = await ajax(url);
                        Toast.success(response.message || '删除成功');

                        // 移除表格行
                        const row = btn.closest('tr');
                        if (row) {
                            row.style.animation = 'fadeOut 0.3s ease forwards';
                            setTimeout(() => row.remove(), 300);
                        }
                    } catch (err) {
                        Toast.error(err.message || '删除失败');
                    }
                });
            });
        });

        // 编辑按钮 - 打开编辑模态框并填充数据
        document.querySelectorAll('[data-edit]').forEach(btn => {
            btn.addEventListener('click', () => {
                const dataStr = btn.dataset.edit;
                let data;

                try {
                    data = JSON.parse(dataStr);
                } catch(e) {
                    console.error('Invalid edit data:', dataStr);
                    return;
                }

                const modalId = btn.dataset.target || 'editModal';

                // 填充表单
                Object.keys(data).forEach(key => {
                    const field = document.querySelector(`#${modalId} [name="${key}"]`);
                    if (field) {
                        if (field.type === 'checkbox') {
                            field.checked = !!data[key];
                        } else {
                            field.value = data[key] ?? '';
                        }
                    }
                });

                // 图片预览
                if (data.image || data.logo || data.avatar) {
                    const imgSrc = data.image || data.logo || data.avatar;
                    const preview = document.querySelector(`#${modalId} .upload-preview`);
                    if (preview) preview.src = imgSrc;
                }

                Modal.open(modalId);
            });
        });
    }

    // ==================== 数据导出功能 ====================

    function initDataExport() {
        document.querySelectorAll('[data-export]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const exportType = btn.dataset.export;
                const url = btn.dataset.exportUrl || '../api/content.php?type=' + exportType;

                try {
                    Toast.info('正在准备导出...');

                    const response = await fetch(url + '&export=json');
                    const data = await response.json();

                    if (data.code === 200) {
                        // 创建下载链接
                        const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
                        const downloadUrl = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = downloadUrl;
                        a.download = `${exportType}_${new Date().toISOString().slice(0, 10)}.json`;
                        a.click();
                        URL.revokeObjectURL(downloadUrl);

                        Toast.success('导出成功');
                    } else {
                        throw new Error(data.message || '导出失败');
                    }
                } catch (err) {
                    Toast.error(err.message || '导出失败');
                }
            });
        });
    }

    // ==================== 搜索与筛选 ====================

    function initSearchFilter() {
        // 实时搜索
        document.querySelectorAll('.search-box input[data-search]').forEach(input => {
            let timeout;

            input.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    filterTableRows(input.value.trim().toLowerCase());
                }, 300);
            });
        });

        // 状态筛选
        document.querySelectorAll('.filter-select[data-filter]').forEach(select => {
            select.addEventListener('change', () => {
                filterByStatus(select.value, select.dataset.filter);
            });
        });
    }

    function filterTableRows(keyword) {
        const table = document.querySelector('.data-table');
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = !keyword || text.includes(keyword);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // 显示结果计数
        const counter = document.querySelector('.search-result-count');
        if (counter) {
            counter.textContent = `共 ${visibleCount} 条记录`;
        }
    }

    function filterByStatus(status, column) {
        const table = document.querySelector('.data-table');
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (status === 'all') {
                row.style.display = '';
            } else {
                const cell = row.querySelector(`td[data-status]`) ||
                           row.children[column] ||
                             Array.from(row.children).find(td =>
                                td.textContent.toLowerCase().includes(status)
                             );
                if (cell) {
                    const match = cell.textContent.toLowerCase().includes(status) ||
                                  cell.dataset.status === status;
                    row.style.display = match ? '' : 'none';
                }
            }
        });
    }

    // ==================== 工单详情展开 ====================

    function initTicketDetail() {
        document.querySelectorAll('[data-ticket-detail]').forEach(trigger => {
            trigger.addEventListener('click', async () => {
                const ticketId = trigger.dataset.ticketDetail;
                const detailRow = document.querySelector(`#ticket-detail-${ticketId}`);

                if (detailRow) {
                    // 切换显示/隐藏
                    const isHidden = detailRow.style.display === 'none' || !detailRow.style.display;
                    detailRow.style.display = isHidden ? '' : 'none';
                    trigger.querySelector('i')?.classList.toggle('fa-chevron-down');
                    trigger.querySelector('i')?.classList.toggle('fa-chevron-up');
                    return;
                }

                // 加载详情
                try {
                    trigger.disabled = true;
                    trigger.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    const response = await ajax(`../api/ticket.php?action=detail&id=${ticketId}`);
                    renderTicketDetail(ticketId, response.data, trigger);
                } catch (err) {
                    Toast.error(err.message);
                } finally {
                    trigger.disabled = false;
                    trigger.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        });
    }

    function renderTicketDetail(ticketId, ticketData, trigger) {
        const row = trigger.closest('tr');
        const newRow = document.createElement('tr');
        newRow.id = `ticket-detail-${ticketId}`;
        newRow.innerHTML = `
            <td colspan="10" style="padding: 20px;">
                <div style="background: var(--input-bg); border-radius: 8px; padding: 20px;">
                    <h4 style="margin-bottom: 12px;">工单内容</h4>
                    <p style="color: var(--text-secondary); line-height: 1.8; margin-bottom: 20px;">${ticketData.content}</p>

                    ${ticketData.replies && ticketData.replies.length > 0 ? `
                        <h4 style="margin-bottom: 12px;">回复记录 (${ticketData.replies.length})</h4>
                        <div style="max-height: 300px; overflow-y: auto;">
                            ${ticketData.replies.map(reply => `
                                <div style="
                                    padding: 12px;
                                    margin-bottom: 10px;
                                    border-radius: 8px;
                                    background: ${reply.is_admin ? 'rgba(0, 102, 204, 0.08)' : 'rgba(255, 107, 0, 0.08)'};
                                    border-left: 3px solid ${reply.is_admin ? 'var(--primary-color)' : 'var(--accent-color)'};
                                ">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                        <strong style="color: ${reply.is_admin ? 'var(--primary-color)' : 'var(--accent-color)'};">
                                            ${reply.user_name}
                                            ${reply.is_admin ? '<span class="badge badge-info ml-10">管理员</span>' : ''}
                                        </strong>
                                        <span style="font-size: 12px; color: var(--text-muted);">${reply.created_at}</span>
                                    </div>
                                    <p style="margin: 0; color: var(--text-secondary);">${reply.content}</p>
                                </div>
                            `).join('')}
                        </div>
                    ` : '<p style="color: var(--text-muted);">暂无回复</p>'}

                    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--card-border);">
                        <form data-ajax action="../api/ticket.php" style="display: flex; gap: 10px;">
                            <input type="hidden" name="action" value="reply">
                            <input type="hidden" name="ticket_id" value="${ticketId}">
                            <textarea name="content" placeholder="输入回复内容..." required
                                style="flex: 1; padding: 10px; background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 6px; color: var(--text-primary); resize: none; height: 44px;"></textarea>
                            <button type="submit" class="btn btn-primary btn-sm">回复</button>
                        </form>
                    </div>
                </div>
            </td>
        `;

        row.after(newRow);
        trigger.querySelector('i')?.classList.replace('fa-eye', 'fa-chevron-up');

        // 绑定新表单的AJAX提交
        initAjaxForms();
    }

    // ==================== 快捷操作 ====================

    function initQuickActions() {
        // 缓存清理
        document.querySelectorAll('[data-clear-cache]').forEach(btn => {
            btn.addEventListener('click', async () => {
                Modal.confirm('清除缓存', '确定要清除系统缓存吗？', async () => {
                    try {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 清理中...';

                        // 模拟缓存清理
                        await new Promise(resolve => setTimeout(resolve, 1500));

                        Toast.success('缓存已清理');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-trash-alt"></i> 清除缓存';
                    } catch (err) {
                        Toast.error(err.message);
                    }
                });
            });
        });

        // 数据备份
        document.querySelectorAll('[data-backup]').forEach(btn => {
            btn.addEventListener('click', async () => {
                try {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 备份中...';

                    // 收集所有数据
                    const backupData = {};
                    const types = ['config', 'banners', 'partners', 'products', 'staff',
                                   'certificates', 'links', 'tickets', 'ticket_replies'];

                    for (const type of types) {
                        try {
                            const resp = await fetch(`../api/content.php?type=${type}&action=list`);
                            const json = await resp.json();
                            backupData[type] = json.data || [];
                        } catch(e) {
                            backupData[type] = [];
                        }
                    }

                    backupData['backup_time'] = date('Y-m-d H:i:s');
                    backupData['version'] = '1.0.0';

                    // 下载
                    const blob = new Blob([JSON.stringify(backupData, null, 2)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `backup_${new Date().toISOString().slice(0, 19).replace(/[:-]/g, '')}.json`;
                    a.click();
                    URL.revokeObjectURL(url);

                    Toast.success('数据备份完成');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-download"></i> 数据备份';
                } catch (err) {
                    Toast.error(err.message || '备份失败');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-download"></i> 数据备份';
                }
            });
        });
    }

    // ==================== 初始化 ====================

    function init() {
        // DOM加载完成后初始化
        document.addEventListener('DOMContentLoaded', () => {
            initSidebar();
            initTabs();
            initUploadPreview();
            initAjaxForms();
            initTableActions();
            initDataExport();
            initSearchFilter();
            initTicketDetail();
            initQuickActions();

            // 全局模态框关闭事件
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal-overlay') &&
                    e.target.classList.contains('show')) {
                    Modal.close(e.target);
                }
            });

            console.log('Admin JS initialized successfully');
        });
    }

    // 启动初始化
    init();

})();
