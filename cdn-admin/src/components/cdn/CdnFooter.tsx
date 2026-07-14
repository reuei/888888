import { Shield, Mail, Phone, MapPin } from 'lucide-react';

const footerLinks = [
  {
    title: '产品服务',
    links: [
      { label: 'CDN加速', href: '/cdn' },
      { label: 'DDoS防护', href: '/cdn' },
      { label: 'WAF防火墙', href: '/cdn' },
      { label: '企业定制', href: '/cdn/contact' },
    ],
  },
  {
    title: '帮助支持',
    links: [
      { label: '接入文档', href: '/cdn/docs' },
      { label: '常见问题', href: '/cdn/faq' },
      { label: '价格方案', href: '/cdn/pricing' },
      { label: '服务协议', href: '/cdn' },
    ],
  },
  {
    title: '关于我们',
    links: [
      { label: '公司介绍', href: '/cdn' },
      { label: '联系我们', href: '/cdn/contact' },
      { label: '合作代理', href: '/cdn/agent' },
      { label: '站点举报', href: '/cdn/report' },
    ],
  },
];

export default function CdnFooter() {
  return (
    <footer className="bg-gray-900 text-gray-300">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
          <div className="lg:col-span-2">
            <div className="flex items-center gap-2 mb-6">
              <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center">
                <Shield size={22} />
              </div>
              <span className="text-2xl font-bold text-white">6cdn</span>
            </div>
            <p className="text-gray-400 text-sm leading-relaxed mb-6 max-w-md">
              一站式CDN集成系统，提供全球加速、DDoS防护、WAF防火墙等全方位服务。
              上海语云科技有限公司专注于为企业提供稳定可靠的网络安全与加速解决方案。
            </p>
            <div className="space-y-3">
              <div className="flex items-center gap-3 text-sm">
                <Mail size={16} className="text-blue-400" />
                <span>contact@6cdn.com</span>
              </div>
              <div className="flex items-center gap-3 text-sm">
                <Phone size={16} className="text-blue-400" />
                <span>400-888-6cdn</span>
              </div>
              <div className="flex items-center gap-3 text-sm">
                <MapPin size={16} className="text-blue-400" />
                <span>上海市浦东新区张江高科技园区</span>
              </div>
            </div>
          </div>

          {footerLinks.map((group, index) => (
            <div key={index}>
              <h4 className="font-semibold text-white mb-4">{group.title}</h4>
              <ul className="space-y-3">
                {group.links.map((link, idx) => (
                  <li key={idx}>
                    <a
                      href={link.href}
                      className="text-sm text-gray-400 hover:text-white transition-colors"
                    >
                      {link.label}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="mt-12 pt-8 border-t border-gray-800">
          <div className="flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <div>© 2026 6cdn - 上海语云科技有限公司 版权所有</div>
            <div className="flex flex-wrap items-center justify-center gap-4">
              <span>沪ICP备xxxxxxxx号</span>
              <span>沪公网安备xxxxxxxxxx号</span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}