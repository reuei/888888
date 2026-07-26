import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { Globe, ExternalLink } from 'lucide-react';

const DOMAIN = 'card.shop';

export default function Subdomain() {
  const { show } = useToast();
  const [subdomain, setSubdomain] = useState('jisu');
  const [saved, setSaved] = useState('jisu');

  const save = () => {
    if (!/^[a-z0-9-]{3,20}$/.test(subdomain)) {
      show('子域名只能包含小写字母、数字、短横线，3-20 位', 'warning');
      return;
    }
    setSaved(subdomain);
    show('子域名保存成功', 'success');
  };

  return (
    <div>
      <PageHeader title="引导页子域名" breadcrumb={['店铺设置', '引导页子域名']} />

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div className="card p-5">
          <h3 className="font-semibold mb-4">域名设置</h3>
          <div className="space-y-4">
            <div>
              <label className="block text-sm mb-1">子域名</label>
              <div className="flex items-center gap-2">
                <input
                  value={subdomain}
                  onChange={(e) => setSubdomain(e.target.value.toLowerCase())}
                  className="input"
                  placeholder="例如 jisu"
                />
                <span className="text-text-secondary text-sm whitespace-nowrap">.{DOMAIN}</span>
              </div>
              <div className="text-xs text-text-secondary mt-1">只能包含小写字母、数字、短横线，3-20 位</div>
            </div>
            <div className="p-3 bg-gray-50 rounded text-sm">
              <div className="text-text-secondary mb-1">当前访问地址：</div>
              <a
                href={`https://${saved}.${DOMAIN}`}
                target="_blank"
                rel="noreferrer"
                className="text-primary flex items-center gap-1"
              >
                <Globe size={14} /> https://{saved}.{DOMAIN} <ExternalLink size={12} />
              </a>
            </div>
            <button onClick={save} className="btn btn-primary">
              保存
            </button>
          </div>
        </div>

        <div className="card p-5">
          <h3 className="font-semibold mb-4">引导页预览</h3>
          <div className="border border-border rounded overflow-hidden">
            <div className="bg-gray-100 px-3 py-2 flex items-center gap-2 border-b border-border">
              <div className="flex gap-1.5">
                <span className="w-2.5 h-2.5 rounded-full bg-danger" />
                <span className="w-2.5 h-2.5 rounded-full bg-warning" />
                <span className="w-2.5 h-2.5 rounded-full bg-success" />
              </div>
              <div className="flex-1 bg-card border border-border rounded px-2 py-0.5 text-xs text-text-secondary ml-2">
                https://{saved}.{DOMAIN}
              </div>
            </div>
            <div className="p-6 bg-card">
              <div className="text-center">
                <div className="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center mx-auto mb-3 text-primary text-xl font-bold">
                  极
                </div>
                <h2 className="text-lg font-bold mb-1">极速发卡</h2>
                <p className="text-xs text-text-secondary mb-4">自动发卡 · 即买即发 · 7×24 小时</p>
                <div className="grid grid-cols-2 gap-2 max-w-xs mx-auto">
                  <div className="border border-border rounded p-2 text-xs">VPN月卡 ¥9.9</div>
                  <div className="border border-border rounded p-2 text-xs">游戏点券 ¥95</div>
                  <div className="border border-border rounded p-2 text-xs">Steam卡 ¥299</div>
                  <div className="border border-border rounded p-2 text-xs">话费充值 ¥49</div>
                </div>
                <button className="btn btn-primary mt-4 text-xs">立即购卡</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
