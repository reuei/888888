/**
 * senqidl - Admin Panel JavaScript
 * 蓝主题 | 后台管理交互
 *
 * 功能模块:
 *   1. 侧边栏开关 (响应式折叠)
 *   2. 标签页切换
 *   3. 表单验证 (后台专用)
 *   4. 图片上传预览
 *   5. 列表拖拽排序
 *   6. 删除确认对话框
 *   7. 表格全选/取消全选
 *   8. AJAX 搜索
 *   9. 富文本编辑器占位 (SimpleMDE / TinyMCE 替换)
 */

(function () {
    'use strict';

    /* ================================================
     * 1. 侧边栏开关
     * ================================================ */
    var sidebar = document.getElementById('adminSidebar');
    var sidebarToggle = document.getElementById('adminSidebarToggle');
    var sidebarOverlay = document.getElementById('adminSidebarOverlay');
    var adminMain = document.querySelector('.admin-main');

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('open');
        if (sidebarOverlay) sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function toggleSidebar() {
        if (!sidebar) return;
        if (sidebar.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // ESC 关闭侧边栏
    document.addEventListener('keydown', function (e) {
        if ((e.key === 'Escape' || e.key === 'Esc') && sidebar && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    // 响应式: 桌面端默认展开, 移动端默认折叠
    function handleResize() {
        if (window.innerWidth > 1024) {
            // 桌面端: 如果之前打开过, 保持展开
            // 不做自动操作, 由用户手动控制
        } else {
            closeSidebar();
        }
    }

    window.addEventListener('resize', function () {
        clearTimeout(window._resizeTimer);
        window._resizeTimer = setTimeout(handleResize, 200);
    });

    /* ================================================
     * 2. 标签页切换
     * ================================================ */
    document.querySelectorAll('[data-tabs]').forEach(function (tabGroup) {
        var tabs = tabGroup.querySelectorAll('[data-tab]');
        var panels = tabGroup.querySelectorAll('[data-tab-panel]');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var target = tab.getAttribute('data-tab');

                // 切换标签激活状态
                tabs.forEach(function (t) {
                    t.classList.toggle('active', t === tab);
                    t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
                });

                // 切换面板显示
                panels.forEach(function (panel) {
                    var panelTarget = panel.getAttribute('data-tab-panel');
                    panel.classList.toggle('active', panelTarget === target);
                    panel.hidden = panelTarget !== target;
                });

                // 触发自定义事件
                tabGroup.dispatchEvent(new CustomEvent('tabchange', {
                    detail: { tab: target, element: tab }
                }));
            });
        });
    });

    /* ================================================
     * 3. 表单验证 (后台)
     * ================================================ */
    var adminForms = document.querySelectorAll('.admin-form, [data-admin-form]');

    adminForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var isValid = true;
            var firstError = null;

            form.querySelectorAll('[required], [data-rule], [minlength], [maxlength]').forEach(function (field) {
                var wrapper = field.closest('.form-group') || field.closest('.form-row') || field.parentElement;
                var errorEl = wrapper ? wrapper.querySelector('.form-error') : null;
                var value = field.value.trim();
                var fieldValid = true;
                var errorMessage = '';

                // 必填验证
                if (field.hasAttribute('required') || field.hasAttribute('data-required')) {
                    if (!value) {
                        fieldValid = false;
                        errorMessage = '此字段为必填项';
                    }
                }

                // 最小长度
                var minLength = field.getAttribute('minlength');
                if (minLength && value && value.length < parseInt(minLength, 10)) {
                    fieldValid = false;
                    errorMessage = '最少需要 ' + minLength + ' 个字符';
                }

                // 最大长度
                var maxLength = field.getAttribute('maxlength');
                if (maxLength && value && value.length > parseInt(maxLength, 10)) {
                    fieldValid = false;
                    errorMessage = '最多允许 ' + maxLength + ' 个字符';
                }

                // 自定义规则
                var rule = field.getAttribute('data-rule');
                if (rule && value) {
                    switch (rule) {
                        case 'email':
                            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                                fieldValid = false;
                                errorMessage = '请输入有效的邮箱地址';
                            }
                            break;
                        case 'url':
                            if (!/^https?:\/\/.+/i.test(value)) {
                                fieldValid = false;
                                errorMessage = '请输入有效的 URL (以 http:// 或 https:// 开头)';
                            }
                            break;
                        case 'number':
                            if (isNaN(parseFloat(value))) {
                                fieldValid = false;
                                errorMessage = '请输入有效的数字';
                            }
                            break;
                        case 'phone':
                            if (!/^[\d\s\-+()]{7,20}$/.test(value)) {
                                fieldValid = false;
                                errorMessage = '请输入有效的电话号码';
                            }
                            break;
                        case 'alpha':
                            if (!/^[a-zA-Z\u4e00-\u9fa5]+$/.test(value)) {
                                fieldValid = false;
                                errorMessage = '只能包含字母或中文';
                            }
                            break;
                        case 'slug':
                            if (!/^[a-z0-9-]+$/.test(value)) {
                                fieldValid = false;
                                errorMessage = '只能包含小写字母、数字和连字符';
                            }
                            break;
                        case 'password':
                            if (value.length < 8) {
                                fieldValid = false;
                                errorMessage = '密码至少需要 8 个字符';
                            } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(value)) {
                                fieldValid = false;
                                errorMessage = '密码需包含大小写字母和数字';
                            }
                            break;
                    }
                }

                // 文件类型验证
                var accept = field.getAttribute('accept');
                if (field.type === 'file' && accept && field.files.length > 0) {
                    var allowedTypes = accept.split(',').map(function (t) { return t.trim(); });
                    var file = field.files[0];
                    var fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                    var mimeOk = allowedTypes.some(function (type) {
                        if (type.startsWith('.')) {
                            return fileExtension === type.toLowerCase();
                        }
                        return file.type === type || file.type.indexOf(type.replace('/*', '')) === 0;
                    });
                    if (!mimeOk) {
                        fieldValid = false;
                        errorMessage = '不支持的文件类型';
                    }

                    // 文件大小验证 (默认 5MB)
                    var maxSize = field.getAttribute('data-max-size');
                    if (maxSize && file.size > parseInt(maxSize, 10)) {
                        fieldValid = false;
                        errorMessage = '文件大小超过限制';
                    }
                }

                // 确认密码
                var confirmField = field.getAttribute('data-confirm');
                if (confirmField) {
                    var target = document.querySelector('[name="' + confirmField + '"]');
                    if (target && value !== target.value) {
                        fieldValid = false;
                        errorMessage = '两次输入不一致';
                    }
                }

                if (!fieldValid) {
                    isValid = false;
                    if (wrapper) wrapper.classList.add('has-error');
                    if (errorEl) errorEl.textContent = errorMessage;
                    if (!firstError) firstError = field;
                } else {
                    if (wrapper) wrapper.classList.remove('has-error');
                    if (errorEl) errorEl.textContent = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                // 使用全局 toast (如果可用)
                if (typeof window.showToast === 'function') {
                    window.showToast('请检查表单填写是否正确', 'error');
                } else {
                    alert('请检查表单填写是否正确');
                }
            }
        });

        // 实时验证
        form.querySelectorAll('.form-control, [data-rule]').forEach(function (field) {
            field.addEventListener('blur', function () {
                var wrapper = field.closest('.form-group') || field.parentElement;
                if (wrapper) wrapper.classList.remove('has-error');
                var errorEl = wrapper ? wrapper.querySelector('.form-error') : null;
                if (errorEl) errorEl.textContent = '';
            });
        });
    });

    /* ================================================
     * 4. 图片上传预览
     * ================================================ */
    document.querySelectorAll('input[type="file"][data-preview], [data-image-upload]').forEach(function (input) {
        input.addEventListener('change', function (e) {
            var files = e.target.files;
            if (!files || files.length === 0) return;

            var wrapper = input.closest('[data-upload-wrapper]') || input.parentElement;
            var previewContainer = wrapper ? wrapper.querySelector('[data-preview-container]') : null;

            // 如果没有预览容器, 创建一个
            if (!previewContainer && wrapper) {
                previewContainer = document.createElement('div');
                previewContainer.setAttribute('data-preview-container', '');
                previewContainer.style.cssText = 'display:flex;gap:1rem;flex-wrap:wrap;margin-top:1rem;';
                wrapper.appendChild(previewContainer);
            }

            if (!previewContainer) return;

            // 支持多选
            var existingPreviews = previewContainer.querySelectorAll('[data-preview-item]');
            existingPreviews.forEach(function (p) { p.remove(); });

            Array.prototype.forEach.call(files, function (file, index) {
                if (!file.type.startsWith('image/')) return;

                var reader = new FileReader();
                var previewItem = document.createElement('div');
                previewItem.setAttribute('data-preview-item', '');
                previewItem.style.cssText = [
                    'position:relative',
                    'width:120px',
                    'height:120px',
                    'border-radius:8px',
                    'overflow:hidden',
                    'box-shadow:0 2px 10px rgba(0,0,0,0.1)',
                    'border:1px solid #e4e9f0'
                ].join(';');

                var img = document.createElement('img');
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                img.alt = file.name;

                // 删除按钮
                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.style.cssText = [
                    'position:absolute',
                    'top:4px',
                    'right:4px',
                    'width:24px',
                    'height:24px',
                    'border-radius:50%',
                    'background:rgba(0,0,0,0.6)',
                    'color:#fff',
                    'border:none',
                    'cursor:pointer',
                    'display:flex',
                    'align-items:center',
                    'justify-content:center',
                    'font-size:14px',
                    'line-height:1'
                ].join(';');
                removeBtn.textContent = '×';
                removeBtn.addEventListener('click', function () {
                    previewItem.remove();
                    // 从 input 中移除该文件
                    var dt = new DataTransfer();
                    Array.prototype.forEach.call(input.files, function (f, i) {
                        if (i !== index) dt.items.add(f);
                    });
                    input.files = dt.files;
                });

                previewItem.appendChild(img);
                previewItem.appendChild(removeBtn);

                reader.onload = function (e) {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);

                previewContainer.appendChild(previewItem);
            });
        });
    });

    /* ================================================
     * 5. 列表拖拽排序
     * ================================================ */
    document.querySelectorAll('[data-sortable]').forEach(function (container) {
        var items = container.querySelectorAll('[data-sortable-item]');
        var dragItem = null;
        var placeholder = document.createElement('div');

        placeholder.style.cssText = [
            'background:linear-gradient(135deg,rgba(26,95,255,0.08),rgba(0,212,255,0.08))',
            'border:2px dashed #1a5fff',
            'border-radius:8px',
            'margin:0.5rem 0',
            'transition:all 0.2s ease'
        ].join(';');

        items.forEach(function (item) {
            item.setAttribute('draggable', 'true');

            item.addEventListener('dragstart', function (e) {
                dragItem = item;
                item.style.opacity = '0.5';
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', '');
            });

            item.addEventListener('dragend', function () {
                item.style.opacity = '1';
                if (placeholder.parentNode) {
                    placeholder.parentNode.removeChild(placeholder);
                }
                // 触发排序变更事件
                container.dispatchEvent(new CustomEvent('sortchange', {
                    detail: getOrder()
                }));
            });

            item.addEventListener('dragover', function (e) {
                e.preventDefault();
                if (!dragItem || dragItem === item) return;

                var rect = item.getBoundingClientRect();
                var offset = e.clientY - rect.top;

                placeholder.style.height = item.offsetHeight + 'px';

                if (offset < rect.height / 2) {
                    container.insertBefore(placeholder, item);
                } else {
                    container.insertBefore(placeholder, item.nextSibling);
                }
            });

            item.addEventListener('drop', function (e) {
                e.preventDefault();
                if (!dragItem || dragItem === item) return;

                if (placeholder.parentNode) {
                    placeholder.parentNode.insertBefore(dragItem, placeholder);
                    placeholder.parentNode.removeChild(placeholder);
                }
            });
        });

        function getOrder() {
            var order = [];
            container.querySelectorAll('[data-sortable-item]').forEach(function (item, index) {
                var id = item.getAttribute('data-id') || item.getAttribute('data-sortable-item');
                order.push({ id: id, order: index });
            });
            return order;
        }

        // 暴露 API
        container._sortable = { getOrder: getOrder };
    });

    /* ================================================
     * 6. 删除确认对话框
     * ================================================ */
    function showConfirmDialog(options) {
        options = options || {};
        var title = options.title || '确认操作';
        var message = options.message || '确定要执行此操作吗?';
        var confirmText = options.confirmText || '确定';
        var cancelText = options.cancelText || '取消';
        var onConfirm = options.onConfirm || function () {};
        var onCancel = options.onCancel || function () {};
        var danger = options.danger !== false;

        var overlay = document.createElement('div');
        overlay.style.cssText = [
            'position:fixed',
            'inset:0',
            'background:rgba(0,0,0,0.5)',
            'display:flex',
            'align-items:center',
            'justify-content:center',
            'z-index:9999',
            'opacity:0',
            'transition:opacity 0.2s ease'
        ].join(';');

        var dialog = document.createElement('div');
        dialog.style.cssText = [
            'background:#fff',
            'border-radius:12px',
            'padding:2rem',
            'max-width:420px',
            'width:calc(100% - 3rem)',
            'box-shadow:0 20px 60px rgba(0,0,0,0.3)',
            'text-align:center',
            'transform:scale(0.9)',
            'transition:transform 0.25s cubic-bezier(0.4,0,0.2,1)'
        ].join(';');

        var iconColor = danger ? '#dc3545' : '#1a5fff';
        var iconBg = danger ? 'rgba(220,53,69,0.1)' : 'rgba(26,95,255,0.1)';

        dialog.innerHTML =
            '<div style="width:56px;height:56px;margin:0 auto 1rem;border-radius:50%;background:' + iconBg + ';display:flex;align-items:center;justify-content:center;font-size:1.75rem;color:' + iconColor + ';">' +
            (danger ? '⚠' : '?') +
            '</div>' +
            '<h3 style="margin:0 0 0.5rem;font-size:1.25rem;color:#1a1a2e;">' + title + '</h3>' +
            '<p style="margin:0 0 1.5rem;color:#666;font-size:0.95rem;line-height:1.6;">' + message + '</p>' +
            '<div style="display:flex;gap:0.75rem;justify-content:center;">' +
            '<button type="button" data-confirm-cancel style="padding:0.625rem 1.5rem;border:1px solid #e4e9f0;border-radius:8px;background:#fff;color:#666;cursor:pointer;font-size:0.9rem;font-weight:500;transition:all 0.15s ease;">' + cancelText + '</button>' +
            '<button type="button" data-confirm-ok style="padding:0.625rem 1.5rem;border:none;border-radius:8px;background:' + (danger ? 'linear-gradient(135deg,#dc3545,#b02a37)' : 'linear-gradient(135deg,#1a5fff,#0d47c2)') + ';color:#fff;cursor:pointer;font-size:0.9rem;font-weight:600;box-shadow:0 4px 15px ' + (danger ? 'rgba(220,53,69,0.35)' : 'rgba(26,95,255,0.35)') + ';">' + confirmText + '</button>' +
            '</div>';

        overlay.appendChild(dialog);
        document.body.appendChild(overlay);

        // 动画
        requestAnimationFrame(function () {
            overlay.style.opacity = '1';
            dialog.style.transform = 'scale(1)';
        });

        function close(result) {
            overlay.style.opacity = '0';
            dialog.style.transform = 'scale(0.9)';
            setTimeout(function () {
                if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
            }, 200);

            if (result) {
                onConfirm();
            } else {
                onCancel();
            }
        }

        dialog.querySelector('[data-confirm-ok]').addEventListener('click', function () { close(true); });
        dialog.querySelector('[data-confirm-cancel]').addEventListener('click', function () { close(false); });
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) close(false);
        });

        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                close(false);
                document.removeEventListener('keydown', escHandler);
            }
        });
    }

    // 暴露到全局
    window.showConfirmDialog = showConfirmDialog;

    // 自动绑定带 data-confirm-delete 的链接/按钮
    document.querySelectorAll('[data-confirm-delete], [data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            var message = el.getAttribute('data-confirm-message') || '确定要删除此项吗?此操作不可恢复。';
            var title = el.getAttribute('data-confirm-title') || '删除确认';

            e.preventDefault();

            showConfirmDialog({
                title: title,
                message: message,
                confirmText: '删除',
                cancelText: '取消',
                danger: true,
                onConfirm: function () {
                    var href = el.getAttribute('href');
                    var action = el.getAttribute('data-action');

                    if (action) {
                        // 触发自定义事件
                        document.dispatchEvent(new CustomEvent('confirm-delete', {
                            detail: { element: el, action: action }
                        }));
                    } else if (href && href !== '#') {
                        window.location.href = href;
                    } else {
                        // 提交关联表单
                        var formId = el.getAttribute('data-form');
                        if (formId) {
                            var form = document.getElementById(formId);
                            if (form) form.submit();
                        }
                    }
                }
            });
        });
    });

    /* ================================================
     * 7. 表格全选 / 取消全选
     * ================================================ */
    document.querySelectorAll('[data-check-all]').forEach(function (masterCheckbox) {
        var targetSelector = masterCheckbox.getAttribute('data-check-all');
        var table = masterCheckbox.closest('table') || masterCheckbox.closest('[data-table]');

        if (!table) return;

        masterCheckbox.addEventListener('change', function () {
            var checkboxes = table.querySelectorAll('tbody ' + targetSelector + ' input[type="checkbox"]');
            checkboxes.forEach(function (cb) {
                cb.checked = masterCheckbox.checked;
                cb.closest('tr').classList.toggle('selected', masterCheckbox.checked);
            });

            updateSelectionInfo();
        });

        // 监听子复选框变化
        table.addEventListener('change', function (e) {
            if (e.target.matches('tbody ' + targetSelector + ' input[type="checkbox"]')) {
                var checkboxes = table.querySelectorAll('tbody ' + targetSelector + ' input[type="checkbox"]');
                var checkedCount = 0;
                checkboxes.forEach(function (cb) {
                    if (cb.checked) {
                        checkedCount++;
                        cb.closest('tr').classList.add('selected');
                    } else {
                        cb.closest('tr').classList.remove('selected');
                    }
                });

                // 更新主复选框状态
                masterCheckbox.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
                masterCheckbox.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;

                updateSelectionInfo();
            }
        });

        function updateSelectionInfo() {
            var info = table.querySelector('[data-selection-info]');
            if (!info) return;

            var checkboxes = table.querySelectorAll('tbody ' + targetSelector + ' input[type="checkbox"]');
            var checkedCount = 0;
            checkboxes.forEach(function (cb) { if (cb.checked) checkedCount++; });

            if (checkedCount > 0) {
                info.style.display = 'flex';
                info.textContent = '已选择 ' + checkedCount + ' 项';
            } else {
                info.style.display = 'none';
            }
        }

        updateSelectionInfo();
    });

    /* ================================================
     * 8. AJAX 搜索
     * ================================================ */
    document.querySelectorAll('[data-ajax-search]').forEach(function (searchInput) {
        var searchUrl = searchInput.getAttribute('data-ajax-search');
        var searchTarget = searchInput.getAttribute('data-search-target');
        var debounceTime = parseInt(searchInput.getAttribute('data-debounce') || '300', 10);
        var resultsContainer = document.querySelector(searchTarget);

        if (!searchUrl) return;

        var debounceTimer = null;
        var minLength = parseInt(searchInput.getAttribute('data-min-length') || '2', 10);

        searchInput.addEventListener('input', function () {
            var value = searchInput.value.trim();

            clearTimeout(debounceTimer);

            if (value.length < minLength) {
                if (resultsContainer) resultsContainer.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(function () {
                performSearch(value);
            }, debounceTime);
        });

        function performSearch(query) {
            // 显示加载状态
            if (resultsContainer) {
                resultsContainer.innerHTML = '<div class="search-loading" style="padding:1rem;text-align:center;color:#999;">搜索中...</div>';
            }

            var url = searchUrl + (searchUrl.indexOf('?') === -1 ? '?' : '&') + 'q=' + encodeURIComponent(query);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('搜索失败');
                    return response.text();
                })
                .then(function (html) {
                    if (resultsContainer) {
                        resultsContainer.innerHTML = html;
                    }
                })
                .catch(function (err) {
                    if (resultsContainer) {
                        resultsContainer.innerHTML = '<div class="search-error" style="padding:1rem;text-align:center;color:#dc3545;">搜索出错,请重试</div>';
                    }
                    console.error('AJAX Search Error:', err);
                });
        }
    });

    /* ================================================
     * 9. 富文本编辑器占位
     * ================================================ */
    function initRichTextEditors() {
        document.querySelectorAll('[data-rich-editor], .rich-text-editor, textarea[data-editor]').forEach(function (textarea) {
            if (textarea.getAttribute('data-editor-initialized')) return;

            var height = textarea.getAttribute('data-height') || '300';

            // 创建工具栏
            var toolbar = document.createElement('div');
            toolbar.className = 'editor-toolbar';
            toolbar.style.cssText = [
                'display:flex',
                'flex-wrap:wrap',
                'gap:0.25rem',
                'padding:0.5rem',
                'background:#f5f8ff',
                'border:1px solid #e4e9f0',
                'border-bottom:none',
                'border-radius:8px 8px 0 0'
            ].join(';');

            var commands = [
                { cmd: 'bold', icon: 'B', title: '加粗', style: 'font-weight:bold;' },
                { cmd: 'italic', icon: 'I', title: '斜体', style: 'font-style:italic;' },
                { cmd: 'underline', icon: 'U', title: '下划线', style: 'text-decoration:underline;' },
                { cmd: 'strikeThrough', icon: 'S', title: '删除线', style: 'text-decoration:line-through;' },
                { cmd: 'formatBlock', value: 'H2', icon: 'H2', title: '标题', style: 'font-weight:bold;font-size:0.85rem;' },
                { cmd: 'formatBlock', value: 'P', icon: 'P', title: '段落' },
                { cmd: 'justifyLeft', icon: '⬅', title: '左对齐' },
                { cmd: 'justifyCenter', icon: '⬍', title: '居中' },
                { cmd: 'justifyRight', icon: '➡', title: '右对齐' },
                { cmd: 'insertUnorderedList', icon: '• 列表', title: '无序列表' },
                { cmd: 'insertOrderedList', icon: '1. 列表', title: '有序列表' },
                { cmd: 'createLink', icon: '🔗', title: '链接' },
                { cmd: 'insertImage', icon: '🖼', title: '图片' },
                { cmd: 'removeFormat', icon: '✕', title: '清除格式' }
            ];

            commands.forEach(function (cmd) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'editor-btn';
                btn.setAttribute('title', cmd.title);
                btn.style.cssText = [
                    'padding:0.375rem 0.625rem',
                    'border:1px solid transparent',
                    'border-radius:4px',
                    'background:transparent',
                    'cursor:pointer',
                    'font-size:0.85rem',
                    'color:#333',
                    'min-width:32px',
                    'transition:all 0.15s ease',
                    cmd.style || ''
                ].join(';');
                btn.textContent = cmd.icon;

                btn.addEventListener('mouseenter', function () {
                    btn.style.background = 'rgba(26,95,255,0.1)';
                    btn.style.borderColor = 'rgba(26,95,255,0.2)';
                    btn.style.color = '#1a5fff';
                });
                btn.addEventListener('mouseleave', function () {
                    btn.style.background = 'transparent';
                    btn.style.borderColor = 'transparent';
                    btn.style.color = '#333';
                });

                btn.addEventListener('click', function () {
                    var editor = textarea.nextElementSibling;
                    if (!editor || !editor.classList.contains('editor-content')) return;

                    editor.focus();

                    if (cmd.cmd === 'createLink') {
                        var url = prompt('请输入链接地址:');
                        if (url) document.execCommand('createLink', false, url);
                    } else if (cmd.cmd === 'insertImage') {
                        var imgUrl = prompt('请输入图片地址:');
                        if (imgUrl) document.execCommand('insertImage', false, imgUrl);
                    } else if (cmd.value) {
                        document.execCommand(cmd.cmd, false, cmd.value);
                    } else {
                        document.execCommand(cmd.cmd, false, null);
                    }

                    // 同步内容回 textarea
                    textarea.value = editor.innerHTML;
                });

                toolbar.appendChild(btn);
            });

            // 创建可编辑区域
            var editorContent = document.createElement('div');
            editorContent.className = 'editor-content';
            editorContent.contentEditable = 'true';
            editorContent.style.cssText = [
                'min-height:' + height + 'px',
                'max-height:' + (parseInt(height, 10) + 200) + 'px',
                'overflow-y:auto',
                'padding:1rem',
                'border:1px solid #e4e9f0',
                'border-top:none',
                'border-radius:0 0 8px 8px',
                'outline:none',
                'font-size:0.95rem',
                'line-height:1.7',
                'color:#333',
                'background:#fff',
                'transition:border-color 0.15s ease, box-shadow 0.15s ease'
            ].join(';');
            editorContent.innerHTML = textarea.value;

            editorContent.addEventListener('focus', function () {
                editorContent.style.borderColor = '#1a5fff';
                editorContent.style.boxShadow = '0 0 0 3px rgba(26,95,255,0.12)';
            });
            editorContent.addEventListener('blur', function () {
                editorContent.style.borderColor = '#e4e9f0';
                editorContent.style.boxShadow = 'none';
                textarea.value = editorContent.innerHTML;
            });
            editorContent.addEventListener('input', function () {
                textarea.value = editorContent.innerHTML;
            });

            // 隐藏原始 textarea
            textarea.style.display = 'none';
            textarea.setAttribute('data-editor-initialized', 'true');

            // 包装到容器
            var wrapper = document.createElement('div');
            wrapper.className = 'rich-editor-wrapper';
            wrapper.style.cssText = 'margin-bottom:1rem;';
            wrapper.appendChild(toolbar);
            wrapper.appendChild(editorContent);

            textarea.parentNode.insertBefore(wrapper, textarea);

            // 暴露同步方法
            textarea._syncEditor = function () {
                textarea.value = editorContent.innerHTML;
            };
        });
    }

    // 延迟初始化 (确保 DOM 就绪)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRichTextEditors);
    } else {
        initRichTextEditors();
    }

    /* ================================================
     * 10. 表格行全选样式同步 (补充)
     * ================================================ */
    document.querySelectorAll('.admin-table').forEach(function (table) {
        // 键盘快捷键: 全选 (Ctrl+A)
        table.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                var master = table.querySelector('[data-check-all] input[type="checkbox"]');
                if (master) {
                    e.preventDefault();
                    master.checked = !master.checked;
                    master.dispatchEvent(new Event('change'));
                }
            }
        });
    });

    /* ================================================
     * 11. 统计卡片数字动画 (后台)
     * ================================================ */
    function animateAdminStats() {
        document.querySelectorAll('.admin-stat-value[data-count]').forEach(function (el) {
            if (el.getAttribute('data-animated')) return;

            var target = parseFloat(el.getAttribute('data-count')) || 0;
            var duration = 1500;
            var startTime = null;

            function step(time) {
                if (!startTime) startTime = time;
                var elapsed = time - startTime;
                var progress = Math.min(elapsed / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                var current = target * eased;

                if (Number.isInteger(target)) {
                    el.textContent = Math.round(current).toLocaleString();
                } else {
                    el.textContent = current.toFixed(1);
                }

                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.setAttribute('data-animated', 'true');
                }
            }

            requestAnimationFrame(step);
        });
    }

    if ('IntersectionObserver' in window) {
        var statsObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateAdminStats();
                    statsObserver.disconnect();
                }
            });
        }, { threshold: 0.3 });

        var adminStats = document.querySelector('.admin-stats');
        if (adminStats) statsObserver.observe(adminStats);
    } else {
        animateAdminStats();
    }

    /* ================================================
     * 12. 状态切换开关 (toggle switch)
     * ================================================ */
    document.querySelectorAll('[data-toggle-status]').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            var id = toggle.getAttribute('data-toggle-status');
            var status = toggle.checked ? 1 : 0;
            var url = toggle.getAttribute('data-toggle-url');

            if (url) {
                var formData = new FormData();
                formData.append('id', id);
                formData.append('status', status);

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (data.success) {
                            if (typeof window.showToast === 'function') {
                                window.showToast('状态更新成功', 'success');
                            }
                        } else {
                            toggle.checked = !toggle.checked;
                            if (typeof window.showToast === 'function') {
                                window.showToast(data.message || '更新失败', 'error');
                            }
                        }
                    })
                    .catch(function () {
                        toggle.checked = !toggle.checked;
                        if (typeof window.showToast === 'function') {
                            window.showToast('网络错误', 'error');
                        }
                    });
            }
        });
    });

    /* ================================================
     * 13. 批量操作选中状态管理
     * ================================================ */
    var selectedIds = [];

    document.addEventListener('change', function (e) {
        if (e.target.matches('tbody [data-check-item] input[type="checkbox"]')) {
            var id = e.target.getAttribute('data-id') || e.target.value;
            if (e.target.checked) {
                if (selectedIds.indexOf(id) === -1) selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(function (i) { return i !== id; });
            }

            updateBulkActions();
        }
    });

    function updateBulkActions() {
        var bulkBar = document.querySelector('[data-bulk-actions]');
        if (!bulkBar) return;

        if (selectedIds.length > 0) {
            bulkBar.style.display = 'flex';
            bulkBar.querySelectorAll('[data-bulk-count]').forEach(function (el) {
                el.textContent = selectedIds.length;
            });
        } else {
            bulkBar.style.display = 'none';
        }
    }

    // 暴露到全局
    window._adminSelectedIds = selectedIds;

})();