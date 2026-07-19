/**
 * 后台管理脚本
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initSidebarToggle();
        initDeleteConfirm();
        initImagePreview();
    });

    // 侧边栏切换
    function initSidebarToggle() {
        var toggle = document.getElementById('sidebarToggle');
        var sidebar = document.querySelector('.admin-sidebar');
        if (!toggle || !sidebar) return;

        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });

        // 点击内容区关闭侧边栏
        document.querySelector('.admin-main').addEventListener('click', function() {
            if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    // 删除确认
    function initDeleteConfirm() {
        document.querySelectorAll('.btn-danger[data-confirm]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (!confirm(btn.dataset.confirm || '确定要删除吗？此操作不可恢复！')) {
                    e.preventDefault();
                }
            });
        });
    }

    // 图片预览
    function initImagePreview() {
        document.querySelectorAll('input[type="file"][data-preview]').forEach(function(input) {
            input.addEventListener('change', function() {
                var previewId = input.dataset.preview;
                var preview = document.getElementById(previewId);
                if (!preview) return;

                var file = input.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = '<img src="' + e.target.result + '" alt="预览">';
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }

})();