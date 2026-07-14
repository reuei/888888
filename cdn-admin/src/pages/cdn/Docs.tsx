import { useState } from 'react';
import { BookOpen, ChevronDown, Code, FileText, Settings, Globe, ArrowRight, Server } from 'lucide-react';
import CdnHeader from '../../components/cdn/CdnHeader';
import CdnFooter from '../../components/cdn/CdnFooter';

const docs = [
  {
    category: '快速开始',
    icon: FileText,
    items: [
      { title: '注册与登录', content: '如何注册6cdn账号并完成登录' },
      { title: '添加域名', content: '如何添加您的域名到6cdn' },
      { title: 'CNAME配置', content: '如何配置DNS解析指向6cdn' },
      { title: 'HTTPS配置', content: '如何启用HTTPS加密传输' },
    ],
  },
  {
    category: '配置指南',
    icon: Settings,
    items: [
      { title: '缓存策略', content: '如何配置缓存规则和过期时间' },
      { title: 'WAF规则', content: '如何配置WAF防火墙规则' },
      { title: 'DDoS防护', content: '如何配置DDoS防护策略' },
      { title: 'CC防护', content: '如何配置CC攻击防护' },
      { title: '页面优化', content: '如何启用页面优化功能' },
      { title: 'WebSocket', content: '如何开启WebSocket支持' },
    ],
  },
  {
    category: '节点介绍',
    icon: Server,
    items: [
      { title: '亚太-Lite节点', content: '亚太-Lite节点详细介绍' },
      { title: '亚太-Pro节点', content: '亚太-Pro节点详细介绍' },
      { title: '国内高防型节点', content: '国内高防型节点详细介绍' },
      { title: '美国高防型节点', content: '美国高防型节点详细介绍' },
    ],
  },
  {
    category: 'API文档',
    icon: Code,
    items: [
      { title: 'API概述', content: '6cdn API接口概述' },
      { title: '站点管理', content: '站点相关API接口' },
      { title: '流量统计', content: '流量统计API接口' },
      { title: '套餐管理', content: '套餐相关API接口' },
      { title: '域名管理', content: '域名相关API接口' },
    ],
  },
  {
    category: 'FAQ',
    icon: Globe,
    items: [
      { title: '常见问题', content: '使用过程中常见问题解答' },
      { title: '故障排查', content: '如何排查常见故障' },
      { title: '最佳实践', content: 'CDN使用最佳实践' },
      { title: '迁移指南', content: '从其他CDN迁移到6cdn' },
    ],
  },
];

const activeDoc = {
  title: '添加域名',
  category: '快速开始',
  steps: [
    {
      step: '1',
      title: '登录控制台',
      content: '首先登录6cdn控制台，进入站点管理页面。',
    },
    {
      step: '2',
      title: '点击添加站点',
      content: '在站点管理页面，点击"添加站点"按钮，进入添加站点页面。',
    },
    {
      step: '3',
      title: '输入域名',
      content: '在添加站点页面，输入您要加速的域名，如: example.com。支持输入顶级域名或子域名。',
    },
    {
      step: '4',
      title: '选择套餐',
      content: '选择适合您的套餐方案，系统会根据您的套餐自动分配资源。',
    },
    {
      step: '5',
      title: '确认添加',
      content: '确认域名和套餐信息无误后，点击"确认添加"按钮，系统会自动为您创建站点。',
    },
    {
      step: '6',
      title: '配置CNAME',
      content: '站点创建成功后，系统会生成一个CNAME地址，您需要在DNS服务商处配置CNAME解析。',
    },
    {
      step: '7',
      title: '等待生效',
      content: 'DNS解析生效时间通常为5-30分钟，解析生效后您的网站即可开始享受CDN加速服务。',
    },
  ],
  codeExample: `// 添加站点API示例
curl -X POST https://api.6cdn.com/v1/sites \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Content-Type: application/json" \\
  -d '{
    "domain": "example.com",
    "package_id": "pkg_xxx",
    "https_enabled": true,
    "node_type": "asia-pro"
  }'`,
};

export default function Docs() {
  const [activeCategory, setActiveCategory] = useState('快速开始');
  const [expandedCategories, setExpandedCategories] = useState<Record<string, boolean>>({ '快速开始': true });

  const toggleCategory = (category: string) => {
    setExpandedCategories({ ...expandedCategories, [category]: !expandedCategories[category] });
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <CdnHeader />

      <main className="pt-24 pb-16">
        <section className="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="text-3xl md:text-5xl font-bold text-white mb-6">接入文档</h1>
            <p className="text-xl text-blue-100 max-w-2xl mx-auto">
              详细的接入文档和API说明，帮助您快速上手6cdn服务
            </p>
          </div>
        </section>

        <section className="py-16">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <div className="lg:col-span-1">
                <div className="bg-white rounded-xl border border-gray-100 overflow-hidden sticky top-24">
                  <div className="p-4 border-b border-gray-100">
                    <h2 className="font-semibold text-gray-800 flex items-center gap-2">
                      <BookOpen size={18} />
                      文档目录
                    </h2>
                  </div>
                  <div className="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                    {docs.map((doc) => (
                      <div key={doc.category}>
                        <button
                          onClick={() => toggleCategory(doc.category)}
                          className="w-full flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors"
                        >
                          <div className="flex items-center gap-3">
                            <doc.icon size={16} className="text-blue-600" />
                            <span className="font-medium text-gray-800">{doc.category}</span>
                          </div>
                          <ChevronDown
                            className={`w-4 h-4 text-gray-400 transition-transform ${
                              expandedCategories[doc.category] ? 'rotate-180' : ''
                            }`}
                          />
                        </button>
                        {expandedCategories[doc.category] && (
                          <div className="bg-gray-50">
                            {doc.items.map((item, index) => (
                              <button
                                key={index}
                                onClick={() => setActiveCategory(doc.category)}
                                className={`w-full text-left px-4 py-2.5 pl-12 text-sm transition-colors ${
                                  activeCategory === doc.category && item.title === activeDoc.title
                                    ? 'text-blue-600 bg-blue-50'
                                    : 'text-gray-600 hover:bg-gray-100'
                                }`}
                              >
                                {item.title}
                              </button>
                            ))}
                          </div>
                        )}
                      </div>
                    ))}
                  </div>
                </div>
              </div>

              <div className="lg:col-span-2">
                <div className="bg-white rounded-xl border border-gray-100 overflow-hidden">
                  <div className="p-6 border-b border-gray-100">
                    <div className="flex items-center justify-between">
                      <div>
                        <span className="inline-block px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-600 mb-2">
                          {activeDoc.category}
                        </span>
                        <h2 className="text-2xl font-bold text-gray-800">{activeDoc.title}</h2>
                      </div>
                      <button className="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700">
                        编辑文档
                        <ArrowRight size={14} />
                      </button>
                    </div>
                  </div>

                  <div className="p-6">
                    <div className="space-y-6">
                      {activeDoc.steps.map((step) => (
                        <div key={step.step} className="flex gap-4">
                          <div className="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center flex-shrink-0 font-bold">
                            {step.step}
                          </div>
                          <div>
                            <h3 className="font-semibold text-gray-800 mb-2">{step.title}</h3>
                            <p className="text-gray-600">{step.content}</p>
                          </div>
                        </div>
                      ))}
                    </div>

                    <div className="mt-8 p-4 rounded-lg bg-gray-900">
                      <div className="flex items-center justify-between mb-3">
                        <span className="text-sm text-gray-400 font-medium">代码示例</span>
                        <button className="text-sm text-gray-400 hover:text-white">复制</button>
                      </div>
                      <pre className="text-sm text-gray-300 overflow-x-auto">
                        <code>{activeDoc.codeExample}</code>
                      </pre>
                    </div>

                    <div className="mt-8 p-6 rounded-xl bg-blue-50">
                      <h3 className="font-semibold text-blue-800 mb-4">注意事项</h3>
                      <ul className="space-y-2 text-blue-700">
                        <li className="flex items-start gap-2">
                          <span className="text-blue-600">-</span>
                          <span>国内高防型节点需要域名已完成工信部备案</span>
                        </li>
                        <li className="flex items-start gap-2">
                          <span className="text-blue-600">-</span>
                          <span>CNAME解析生效时间取决于您的DNS服务商</span>
                        </li>
                        <li className="flex items-start gap-2">
                          <span className="text-blue-600">-</span>
                          <span>建议开启HTTPS以提升网站安全性和SEO排名</span>
                        </li>
                        <li className="flex items-start gap-2">
                          <span className="text-blue-600">-</span>
                          <span>如果遇到问题，请联系客服获取帮助</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>

      <CdnFooter />
    </div>
  );
}