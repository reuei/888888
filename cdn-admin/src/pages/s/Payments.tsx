import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { useToast } from '../../components/Toast';
import { CreditCard, RefreshCw, Shield } from 'lucide-react';

export default function SPayments() {
  const { show } = useToast();
  const [tab, setTab] = useState<'channels' | 'risk'>('channels');
  const [testOpen, setTestOpen] = useState(false);
  const [currentChannel, setCurrentChannel] = useState('');

  const channels = [
    { key: 'wechat', name: '微信支付', enabled: true },
    { key: 'alipay', name: '支付宝', enabled: true },
    { key: 'epay', name: '易支付', enabled: false },
    { key: 'codepay', name: '码支付', enabled: false },
    { key: 'usdt', name: 'USDT 支付', enabled: true },
    { key: 'xinpay', name: '信汇', enabled: false },
  ];

  const openTest = (name: string) => {
    setCurrentChannel(name);
    setTestOpen(true);
    show(`${name} 连接测试成功`, 'success');
  };

  return (
    <div>
      <PageHeader title="支付网关管理" breadcrumb={['支付网关管理', '渠道对接']} />

      <div className="flex gap-2 mb-6">
        {[
          { key: 'channels', label: '渠道对接' },
          { key: 'risk', label: '支付风控' },
        ].map((t) => (
          <button
            key={t.key}
            onClick={() => setTab(t.key as any)}
            className={`btn text-xs ${tab === t.key ? 'btn-primary' : 'btn-default'}`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {tab === 'channels' && (
        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          {channels.map((c) => (
            <div key={c.key} className="card p-5">
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                  <CreditCard size={18} className="text-primary" />
                  <h3 className="font-semibold">{c.name}</h3>
                </div>
                <div className={`w-2 h-2 rounded-full ${c.enabled ? 'bg-success' : 'bg-gray-300'}`}></div>
              </div>
              <div className="space-y-3 text-sm">
                <div>
                  <label className="block text-text-secondary mb-1">商户号 / AppID</label>
                  <input className="input" placeholder="请输入商户号" defaultValue={c.enabled ? 'M' + Math.random().toString().slice(2, 10) : ''} />
                </div>
                <div>
                  <label className="block text-text-secondary mb-1">密钥</label>
                  <input type="password" className="input" placeholder="请输入密钥" defaultValue={c.enabled ? '************************' : ''} />
                </div>
                <div>
                  <label className="block text-text-secondary mb-1">回调地址</label>
                  <input className="input" placeholder="https://..." defaultValue={c.enabled ? `https://cdn.example.com/callback/${c.key}` : ''} />
                </div>
              </div>
              <div className="flex items-center justify-between mt-4">
                <label className="flex items-center gap-2 text-sm">
                  <input type="checkbox" defaultChecked={c.enabled} />
                  启用
                </label>
                <button onClick={() => openTest(c.name)} className="btn btn-default text-xs flex items-center gap-1">
                  <RefreshCw size={14} /> 测试连接
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {tab === 'risk' && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2">
              <Shield size={18} className="text-warning" /> 单笔限额
            </h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm mb-1">单笔最小金额（元）</label>
                <input type="number" className="input" defaultValue="0.01" />
              </div>
              <div>
                <label className="block text-sm mb-1">单笔最大金额（元）</label>
                <input type="number" className="input" defaultValue="50000" />
              </div>
              <button onClick={() => show('单笔限额配置保存成功', 'success')} className="btn btn-primary">保存</button>
            </div>
          </div>

          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2">
              <RefreshCw size={18} className="text-primary" /> 金额随机化
            </h3>
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-sm">开启金额随机化</span>
                <input type="checkbox" defaultChecked className="w-4 h-4" />
              </div>
              <div>
                <label className="block text-sm mb-1">随机范围（元）</label>
                <div className="flex items-center gap-2">
                  <input type="number" className="input" defaultValue="0.01" />
                  <span>~</span>
                  <input type="number" className="input" defaultValue="0.99" />
                </div>
              </div>
              <button onClick={() => show('金额随机化配置保存成功', 'success')} className="btn btn-primary">保存</button>
            </div>
          </div>

          <div className="card p-5 lg:col-span-2">
            <h3 className="font-semibold mb-4">风控日志</h3>
            <table className="table">
              <thead>
                <tr>
                  <th>时间</th>
                  <th>订单号</th>
                  <th>触发规则</th>
                  <th>处理结果</th>
                </tr>
              </thead>
              <tbody>
                {[
                  { time: '2026-06-28 09:12', order: 'O202606280099', rule: '金额超过单日上限', result: '拦截' },
                  { time: '2026-06-27 22:30', order: 'O202606270088', rule: '高频下单', result: '人工复核' },
                ].map((r, i) => (
                  <tr key={i}>
                    <td className="text-text-secondary">{r.time}</td>
                    <td>{r.order}</td>
                    <td>{r.rule}</td>
                    <td><span className="badge badge-warning">{r.result}</span></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      <Modal
        open={testOpen}
        title="测试连接"
        onClose={() => setTestOpen(false)}
        footer={<button onClick={() => setTestOpen(false)} className="btn btn-default">关闭</button>}
      >
        <div className="text-center py-6">
          <div className="w-12 h-12 rounded-full bg-green-50 text-success flex items-center justify-center mx-auto mb-3">
            <RefreshCw size={24} />
          </div>
          <p className="font-medium">{currentChannel} 连接成功</p>
          <p className="text-sm text-text-secondary mt-1">响应延迟 32ms</p>
        </div>
      </Modal>
    </div>
  );
}
