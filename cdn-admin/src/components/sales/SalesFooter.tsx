import { Shield } from 'lucide-react';

const footerLinks = [
  {
    title: '产品',
    links: ['边缘加速', 'DDoS 防护', 'WAF 防火墙', '对象存储', '开放平台'],
  },
  {
    title: '解决方案',
    links: ['电商加速', '游戏出海', '政企安全', '视频直播', '金融风控'],
  },
  {
    title: '支持与服务',
    links: ['快速开始', '帮助文档', 'API 文档', '工单支持', '联系我们'],
  },
  {
    title: '关于',
    links: ['公司介绍', '新闻动态', '合作伙伴', '加入我们', '服务协议'],
  },
];

export default function SalesFooter() {
  return (
    <footer className="bg-[#1d1d1d] text-white/80">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div className="grid grid-cols-2 md:grid-cols-5 gap-8">
          <div className="col-span-2 md:col-span-1">
            <div className="flex items-center gap-2 mb-4">
              <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-[#0052d9] to-[#4656ff] text-white flex items-center justify-center">
                <Shield size={16} />
              </div>
              <span className="text-lg font-bold">EdgeOne</span>
            </div>
            <p className="text-sm text-white/50 leading-relaxed">
              统一边缘加速与安全防护平台，为企业提供全球化的内容分发与 Web 应用防护能力。
            </p>
          </div>

          {footerLinks.map((group, index) => (
            <div key={index}>
              <h4 className="font-semibold text-white mb-4 text-sm">{group.title}</h4>
              <ul className="space-y-2.5 text-sm text-white/50">
                {group.links.map((link, idx) => (
                  <li key={idx}>
                    <button className="hover:text-white transition-colors">{link}</button>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="mt-12 pt-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-white/40">
          <div>© 2026 EdgeOne. All rights reserved.</div>
          <div className="flex flex-wrap items-center justify-center gap-4">
            <span>京 ICP 备 12345678 号</span>
            <span>京公网安备 11010802020202 号</span>
            <button className="hover:text-white transition-colors">隐私政策</button>
            <button className="hover:text-white transition-colors">服务条款</button>
          </div>
        </div>
      </div>
    </footer>
  );
}
