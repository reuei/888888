/**
 * 语云科技企业官网 - 弹窗组件
 * 毛玻璃效果 Modal (魔方财务同款)
 */

(function() {
    'use strict';

    /**
     * Modal 类
     */
    function Modal(options) {
        this.options = Object.assign({
            title: '',
            content: '',
            footer: '',
            width: '520px',
            closable: true,
            maskClosable: true,
            onOpen: null,
            onClose: null,
            onConfirm: null
        }, options);

        this.overlay = null;
        this.modal = null;
        this.isOpen = false;

        this.create();
    }

    Modal.prototype.create = function() {
        // 创建遮罩层
        this.overlay = document.createElement('div');
        this.overlay.className = 'modal-overlay';

        // 创建弹窗容器
        this.modal = document.createElement('div');
        this.modal.className = 'modal';
        this.modal.style.maxWidth = this.options.width;

        var html = '';

        // 头部
        if (this.options.title || this.options.closable) {
            html += '<div class="modal-header">';
            if (this.options.title) {
                html += '<h3 class="modal-title">' + this.options.title + '</h3>';
            }
            if (this.options.closable) {
                html += '<button class="modal-close" aria-label="关闭">&times;</button>';
            }
            html += '</div>';
        }

        // 内容区
        html += '<div class="modal-body">';
        html += this.options.content;
        html += '</div>';

        // 底部
        if (this.options.footer) {
            html += '<div class="modal-footer">';
            html += this.options.footer;
            html += '</div>';
        }

        this.modal.innerHTML = html;
        this.overlay.appendChild(this.modal);
        document.body.appendChild(this.overlay);

        this.bindEvents();
    };

    Modal.prototype.bindEvents = function() {
        var self = this;

        // 关闭按钮
        var closeBtn = this.modal.querySelector('.modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                self.close();
            });
        }

        // 点击遮罩关闭
        if (this.options.maskClosable) {
            this.overlay.addEventListener('click', function(e) {
                if (e.target === self.overlay) {
                    self.close();
                }
            });
        }

        // ESC键关闭
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && self.isOpen) {
                self.close();
            }
        });
    };

    Modal.prototype.open = function() {
        this.isOpen = true;
        document.body.appendChild(this.overlay);
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(function() {
            self.overlay.classList.add('show');
        }.bind(this));

        if (this.options.onOpen) {
            this.options.onOpen();
        }
    };

    Modal.prototype.close = function() {
        var self = this;
        this.isOpen = false;
        this.overlay.classList.remove('show');

        setTimeout(function() {
            if (self.overlay.parentNode) {
                self.overlay.parentNode.removeChild(self.overlay);
            }
            document.body.style.overflow = '';
        }, 350);

        if (this.options.onClose) {
            this.options.onClose();
        }
    };

    Modal.prototype.setContent = function(content) {
        var body = this.modal.querySelector('.modal-body');
        if (body) {
            body.innerHTML = content;
        }
    };

    Modal.prototype.setTitle = function(title) {
        var titleEl = this.modal.querySelector('.modal-title');
        if (titleEl) {
            titleEl.textContent = title;
        }
    };

    // ============================================
    // 快捷方法
    // ============================================

    // Alert弹窗
    window.showAlert = function(message, title, callback) {
        var modal = new Modal({
            title: title || '提示',
            content: '<p style="font-size:15px;color:#374151;line-height:1.7;">' + message + '</p>',
            footer: '<button class="btn btn-primary" onclick="this.closest(\'.modal-overlay\')._modal.close()">确定</button>',
            onConfirm: callback
        });

        modal.overlay._modal = modal;
        modal.open();
        return modal;
    };

    // Confirm确认弹窗
    window.showConfirm = function(message, title, onConfirm, onCancel) {
        var modal = new Modal({
            title: title || '确认操作',
            content: '<p style="font-size:15px;color:#374151;line-height:1.7;">' + message + '</p>',
            footer: '' +
                '<button class="btn btn-outline-dark" id="modal-cancel">取消</button>' +
                '<button class="btn btn-primary" id="modal-confirm">确定</button>',
            onClose: onCancel
        });

        modal.overlay._modal = modal;
        modal.open();

        modal.modal.querySelector('#modal-cancel').addEventListener('click', function() {
            modal.close();
            if (onCancel) onCancel();
        });

        modal.modal.querySelector('#modal-confirm').addEventListener('click', function() {
            modal.close();
            if (onConfirm) onConfirm();
        });

        return modal;
    };

    // Prompt输入弹窗
    window.showPrompt = function(message, defaultValue, title, callback) {
        var inputId = 'prompt-input-' + Date.now();
        var modal = new Modal({
            title: title || '请输入',
            content: '' +
                '<p style="margin-bottom:16px;font-size:15px;color:#374151;">' + message + '</p>' +
                '<input type="text" class="form-input" id="' + inputId + '" value="' + (defaultValue || '') + '" placeholder="请输入...">',
            footer: '' +
                '<button class="btn btn-outline-dark" onclick="this.closest(\'.modal-overlay\')._modal.close()">取消</button>' +
                '<button class="btn btn-primary" id="prompt-ok">确定</button>'
        });

        modal.overlay._modal = modal;
        modal.open();

        var input = document.getElementById(inputId);
        input.focus();
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                modal.close();
                if (callback) callback(input.value);
            }
        });

        modal.modal.querySelector('#prompt-ok').addEventListener('click', function() {
            modal.close();
            if (callback) callback(input.value);
        });

        return modal;
    };

    // 客服联系弹窗
    window.showContactModal = function() {
        var config = window.siteConfig || {};

        var content = '' +
            '<div style="text-align:center;">' +
            '  <div style="width:80px;height:80px;background:linear-gradient(135deg,#0066CC,#00A8E8);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">' +
            '    <i class="fa-solid fa-headset" style="font-size:36px;color:#fff;"></i>' +
            '  </div>' +
            '  <h4 style="margin-bottom:20px;font-size:18px;color:#1f2937;">联系我们</h4>' +
            '  <div style="background:#f9fafb;border-radius:12px;padding:20px;text-align:left;">' +
            '    <div style="display:flex;align-items:center;padding:10px 0;border-bottom:1px solid #eee;">' +
            '      <i class="fa-solid fa-phone" style="color:#FF6B00;width:24px;"></i>' +
            '      <span style="margin-left:12px;"><strong>销售电话:</strong> <span style="color:#FF6B00;font-weight:700;font-size:17px;">400-800-8541</span></span>' +
            '    </div>' +
            '    <div style="display:flex;align-items:center;padding:10px 0;border-bottom:1px solid #eee;">' +
            '      <i class="fa-solid fa-envelope" style="color:#0066CC;width:24px;"></i>' +
            '      <span style="margin-left:12px;"><strong>邮箱:</strong> ' + (config.admin_email || 'support@yuyun.com') + '</span>' +
            '    </div>' +
            '    <div style="display:flex;align-items:center;padding:10px 0;">' +
            '      <i class="fa-brands fa-qq" style="color:#12B7F5;width:24px;"></i>' +
            '      <span style="margin-left:12px;"><strong>QQ群:</strong> ' + (config.qq_group || '123456789') + '</span>' +
            '    </div>' +
            '  </div>' +
            '  <p style="margin-top:16px;font-size:13px;color:#9ca3af;">工作时间: 周一至周日 9:00-21:00</p>' +
            '</div>';

        var modal = new Modal({
            title: '客服中心',
            content: content,
            width: '420px'
        });

        modal.open();
        return modal;
    };

    // 暴露Modal类
    window.Modal = Modal;

})();
