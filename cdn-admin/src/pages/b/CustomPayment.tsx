import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { Copy, Code2 } from 'lucide-react';

export default function CustomPayment() {
  const { show } = useToast();
  const [form, setForm] = useState({ gateway: 'alipay', merchantId: '', secret: '', callback: '' });
  const [code, setCode] = useState('');

  const generate = () => {
    if (!form.merchantId || !form.secret || !form.callback) {
      show('请填写完整对接信息', 'warning');
      return;
    }
    const generated = `// ${form.gateway.toUpperCase()} 对接配置
const config = {
  gateway: '${form.gateway}',
  merchantId: '${form.merchantId}',
  secret: '${form.secret}',
  callbackUrl: '${form.callback}',
  signType: 'MD5',
};

// 调用示例
POST /api/v1/pay/create
{
  "merchantId": "${form.merchantId}",
  "amount": 1.00,
  "sign": "..."
}`;
    setCode(generated);
    show('对接码已生成', 'success');
  };

  return (
    <div>
      <PageHeader title="自定义支付对接" breadcrumb={['店铺设置', '自定义支付对接']} />

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div className="card p-5">
          <h3 className="font-semibold mb-4">支付配置</h3>
          <div className="space-y-4">
            <div>
              <label className="block text-sm mb-1">支付网关</label>
              <select value={form.gateway} onChange={(e) => setForm({ ...form, gateway: e.target.value })} className="input">
                <option value="alipay">支付宝</option>
                <option value="wxpay">微信支付</option>
                <option value="epay">易支付</option>
                <option value="usdt">USDT</option>
              </select>
            </div>
            <div>
              <label className="block text-sm mb-1">商户号</label>
              <input
                value={form.merchantId}
                onChange={(e) => setForm({ ...form, merchantId: e.target.value })}
                className="input"
                placeholder="请输入商户号"
              />
            </div>
            <div>
              <label className="block text-sm mb-1">密钥</label>
              <input
                value={form.secret}
                onChange={(e) => setForm({ ...form, secret: e.target.value })}
                className="input font-mono"
                placeholder="请输入商户密钥"
              />
            </div>
            <div>
              <label className="block text-sm mb-1">回调地址</label>
              <input
                value={form.callback}
                onChange={(e) => setForm({ ...form, callback: e.target.value })}
                className="input"
                placeholder="https://your-domain.com/api/callback"
              />
            </div>
            <button onClick={generate} className="btn btn-primary flex items-center gap-1">
              <Code2 size={16} /> 生成对接码
            </button>
          </div>
        </div>

        <div className="card p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold">对接码</h3>
            {code && (
              <button
                onClick={() => {
                  navigator.clipboard?.writeText(code);
                  show('已复制到剪贴板', 'success');
                }}
                className="text-primary text-xs flex items-center gap-1"
              >
                <Copy size={14} /> 复制
              </button>
            )}
          </div>
          {code ? (
            <pre className="bg-gray-50 p-4 rounded text-xs font-mono overflow-x-auto whitespace-pre-wrap">{code}</pre>
          ) : (
            <div className="text-sm text-text-secondary text-center py-12">填写配置后点击「生成对接码」</div>
          )}
        </div>
      </div>
    </div>
  );
}
