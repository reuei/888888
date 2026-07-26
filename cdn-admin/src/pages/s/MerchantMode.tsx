import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';

type EntryMode = 'invite' | 'self' | 'closed';

const MODE_OPTIONS: { value: EntryMode; title: string; desc: string }[] = [
  {
    value: 'invite',
    title: '邀请码入驻',
    desc: '商户通过邀请码申请入驻，平台可控邀请码数量与有效期。',
  },
  {
    value: 'self',
    title: '自助注册',
    desc: '商户可直接在注册页填写信息提交申请，需平台人工审核。',
  },
  {
    value: 'closed',
    title: '关闭入驻',
    desc: '暂停新商户入驻申请，已注册商户不受影响。',
  },
];

export default function SMerchantMode() {
  const { show } = useToast();
  const [mode, setMode] = useState<EntryMode>('invite');
  const [wxLogin, setWxLogin] = useState(true);
  const [qqLogin, setQqLogin] = useState(false);

  const handleSave = () => {
    const modeText = MODE_OPTIONS.find((m) => m.value === mode)?.title || '';
    show(`已保存：${modeText} | 微信登录${wxLogin ? '开' : '关'} | QQ登录${qqLogin ? '开' : '关'}`, 'success');
  };

  return (
    <div>
      <PageHeader title="入驻模式设置" breadcrumb={['商户管理', '入驻模式设置']} />

      <div className="card p-6 max-w-3xl">
        <h3 className="font-semibold mb-4">商户入驻模式</h3>
        <div className="space-y-3 mb-8">
          {MODE_OPTIONS.map((opt) => {
            const selected = mode === opt.value;
            return (
              <label
                key={opt.value}
                className={`flex items-start gap-3 p-4 border rounded cursor-pointer hover:bg-gray-50 ${
                  selected ? 'border-primary bg-blue-50' : 'border-border'
                }`}
              >
                <input
                  type="radio"
                  name="entryMode"
                  value={opt.value}
                  checked={selected}
                  onChange={() => setMode(opt.value)}
                  className="mt-1 w-4 h-4"
                />
                <div className="flex-1">
                  <div className="font-medium">{opt.title}</div>
                  <div className="text-sm text-text-secondary mt-1">{opt.desc}</div>
                </div>
              </label>
            );
          })}
        </div>

        <h3 className="font-semibold mb-4">第三方登录</h3>
        <div className="space-y-3 mb-8">
          <div className="flex items-center justify-between p-4 border border-border rounded">
            <div>
              <div className="font-medium">开启微信登录</div>
              <div className="text-sm text-text-secondary mt-1">
                用户/商户可使用微信扫码登录平台
              </div>
            </div>
            <button
              type="button"
              onClick={() => setWxLogin(!wxLogin)}
              className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                wxLogin ? 'bg-primary' : 'bg-gray-300'
              }`}
            >
              <span
                className={`inline-block h-5 w-5 transform rounded-full bg-white transition-transform ${
                  wxLogin ? 'translate-x-5' : 'translate-x-0.5'
                }`}
              />
            </button>
          </div>

          <div className="flex items-center justify-between p-4 border border-border rounded">
            <div>
              <div className="font-medium">开启QQ登录</div>
              <div className="text-sm text-text-secondary mt-1">
                用户/商户可使用QQ授权登录平台
              </div>
            </div>
            <button
              type="button"
              onClick={() => setQqLogin(!qqLogin)}
              className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                qqLogin ? 'bg-primary' : 'bg-gray-300'
              }`}
            >
              <span
                className={`inline-block h-5 w-5 transform rounded-full bg-white transition-transform ${
                  qqLogin ? 'translate-x-5' : 'translate-x-0.5'
                }`}
              />
            </button>
          </div>
        </div>

        <div className="flex gap-2">
          <button onClick={handleSave} className="btn btn-primary">
            保存
          </button>
        </div>
      </div>
    </div>
  );
}
