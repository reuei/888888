import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';

const COLORS = [
  { value: '#2F6BFF', label: '科技蓝' },
  { value: '#06B6D4', label: '青色' },
  { value: '#F97316', label: '橙色' },
];

const SETTLE_MODES = [
  { value: 't0', label: 'T+0（实时结算）' },
  { value: 't1', label: 'T+1（次日结算）' },
  { value: 't7', label: 'T+7（周结算）' },
];

export default function SStationCreate() {
  const { show } = useToast();
  const [form, setForm] = useState({
    name: '',
    domain: '',
    themeColor: '#2F6BFF',
    settleMode: 't1',
    adminUsername: '',
    adminPassword: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});

  const validate = () => {
    const e: Record<string, string> = {};
    if (!form.name.trim()) e.name = '请输入分站名称';
    if (!form.domain.trim()) e.domain = '请输入域名';
    else if (!/^[a-z0-9.-]+\.[a-z]{2,}$/.test(form.domain.trim())) e.domain = '域名格式不正确';
    if (!form.adminUsername.trim()) e.adminUsername = '请输入超管用户名';
    if (form.adminPassword.length < 6) e.adminPassword = '密码至少 6 位';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = () => {
    if (!validate()) {
      show('表单填写有误，请检查', 'error');
      return;
    }
    show(`分站「${form.name}」创建成功`, 'success');
    setForm({
      name: '',
      domain: '',
      themeColor: '#2F6BFF',
      settleMode: 't1',
      adminUsername: '',
      adminPassword: '',
    });
    setErrors({});
  };

  return (
    <div>
      <PageHeader title="新建分站" breadcrumb={['分站管理', '新建分站']} />

      <div className="card p-6 max-w-2xl">
        <div className="space-y-5">
          <div>
            <label className="block text-sm mb-1">
              分站名称 <span className="text-danger">*</span>
            </label>
            <input
              className="input"
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
              placeholder="例如：华东分站"
            />
            {errors.name && <div className="text-xs text-danger mt-1">{errors.name}</div>}
          </div>

          <div>
            <label className="block text-sm mb-1">
              域名 <span className="text-danger">*</span>
            </label>
            <input
              className="input"
              value={form.domain}
              onChange={(e) => setForm({ ...form, domain: e.target.value })}
              placeholder="例如：east.card.com"
            />
            {errors.domain && <div className="text-xs text-danger mt-1">{errors.domain}</div>}
          </div>

          <div>
            <label className="block text-sm mb-1">主题色</label>
            <div className="flex gap-3">
              {COLORS.map((c) => (
                <label
                  key={c.value}
                  className={`flex items-center gap-2 px-3 py-2 border rounded cursor-pointer hover:bg-gray-50 ${
                    form.themeColor === c.value ? 'border-primary bg-blue-50' : 'border-border'
                  }`}
                >
                  <input
                    type="radio"
                    name="themeColor"
                    value={c.value}
                    checked={form.themeColor === c.value}
                    onChange={(e) => setForm({ ...form, themeColor: e.target.value })}
                    className="sr-only"
                  />
                  <span
                    className="w-5 h-5 rounded-full border border-border"
                    style={{ backgroundColor: c.value }}
                  />
                  <span className="text-sm">{c.label}</span>
                </label>
              ))}
            </div>
          </div>

          <div>
            <label className="block text-sm mb-1">结算模式</label>
            <select
              className="input"
              value={form.settleMode}
              onChange={(e) => setForm({ ...form, settleMode: e.target.value })}
            >
              {SETTLE_MODES.map((m) => (
                <option key={m.value} value={m.value}>
                  {m.label}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm mb-1">
              超管用户名 <span className="text-danger">*</span>
            </label>
            <input
              className="input"
              value={form.adminUsername}
              onChange={(e) => setForm({ ...form, adminUsername: e.target.value })}
              placeholder="登录用户名"
            />
            {errors.adminUsername && (
              <div className="text-xs text-danger mt-1">{errors.adminUsername}</div>
            )}
          </div>

          <div>
            <label className="block text-sm mb-1">
              超管密码 <span className="text-danger">*</span>
            </label>
            <input
              type="password"
              className="input"
              value={form.adminPassword}
              onChange={(e) => setForm({ ...form, adminPassword: e.target.value })}
              placeholder="至少 6 位"
            />
            {errors.adminPassword && (
              <div className="text-xs text-danger mt-1">{errors.adminPassword}</div>
            )}
          </div>

          <div className="flex gap-2 pt-2">
            <button onClick={handleSubmit} className="btn btn-primary">
              创建分站
            </button>
            <button
              onClick={() => {
                setForm({
                  name: '',
                  domain: '',
                  themeColor: '#2F6BFF',
                  settleMode: 't1',
                  adminUsername: '',
                  adminPassword: '',
                });
                setErrors({});
              }}
              className="btn btn-default"
            >
              重置
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
