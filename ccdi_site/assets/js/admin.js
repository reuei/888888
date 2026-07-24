/**
 * 后台管理脚本 v10.0.0
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initDeleteConfirm();
        initImagePreview();
    });

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