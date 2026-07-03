import { ShoppingBag, Mail, MessageCircle } from 'lucide-react';

export default function SalesFooter() {
  return (
    <footer className="border-t border-[var(--sales-border)] bg-[var(--sales-card)]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div className="md:col-span-1">
            <div className="flex items-center gap-2 mb-4">
              <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-[var(--sales-primary)] to-[var(--sales-accent)] text-white flex items-center justify-center">
                <ShoppingBag size={16} />
              </div>
              <span className="font-bold text-[var(--sales-text)]">CloudShield Store</span>
            </div>
            <p className="text-sm text-[var(--sales-text-secondary)] leading-relaxed">
              官方授权销售平台，提供源码授权、CDN节点、在线更新等一站式服务。
            </p>
          </div>

          <div>
            <h4 className="font-semibold text-[var(--sales-text)] mb-4">产品服务</h4>
            <ul className="space-y-2 text-sm text-[var(--sales-text-secondary)]">
              <li>源码授权</li>
              <li>CDN 节点</li>
              <li>在线更新</li>
              <li>技术支持</li>
            </ul>
          </div>

          <div>
            <h4 className="font-semibold text-[var(--sales-text)] mb-4">帮助中心</h4>
            <ul className="space-y-2 text-sm text-[var(--sales-text-secondary)]">
              <li>购买指南</li>
              <li>部署文档</li>
              <li>常见问题</li>
              <li>服务协议</li>
            </ul>
          </div>

          <div>
            <h4 className="font-semibold text-[var(--sales-text)] mb-4">联系我们</h4>
            <ul className="space-y-2 text-sm text-[var(--sales-text-secondary)]">
              <li className="flex items-center gap-2">
                <Mail size={14} />
                sales@cloudshield.example
              </li>
              <li className="flex items-center gap-2">
                <MessageCircle size={14} />
                7×24 在线客服
              </li>
            </ul>
          </div>
        </div>

        <div className="mt-10 pt-6 border-t border-[var(--sales-border)] text-center text-xs text-[var(--sales-text-secondary)]">
          © 2026 CloudShield Store. 企业级 CDN 防护加速解决方案. All rights reserved.
        </div>
      </div>
    </footer>
  );
}
