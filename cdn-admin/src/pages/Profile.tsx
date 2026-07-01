import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import PageHeader from '../components/PageHeader';
import { useToast } from '../components/Toast';
import { sProfile, bProfile } from '../data/mock';
import { User, Mail, Phone, Building2, Save, Camera } from 'lucide-react';

interface ProfileProps {
  role: 's' | 'b';
}

export default function Profile({ role }: ProfileProps) {
  const { show } = useToast();
  const navigate = useNavigate();
  const base = role === 's' ? sProfile : bProfile;

  const [form, setForm] = useState({
    name: base.name,
    email: role === 's' ? 'admin@cloudshield.cn' : 'merchant@example.com',
    phone: role === 's' ? '138****0000' : '139****8888',
    shopName: base.shopName ?? '',
  });

  const handleSave = () => {
    show('个人资料保存成功', 'success');
  };

  return (
    <div>
      <PageHeader title="个人资料" breadcrumb={['账号设置', '个人资料']} />

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="card p-6 flex flex-col items-center text-center">
          <div className="relative mb-4">
            <div className="w-24 h-24 rounded-full bg-primary text-white flex items-center justify-center text-3xl font-bold">
              {base.avatar}
            </div>
            <button className="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-card border border-border flex items-center justify-center text-text-secondary hover:text-primary">
              <Camera size={14} />
            </button>
          </div>
          <h3 className="text-lg font-semibold">{form.name}</h3>
          <p className="text-sm text-text-secondary mt-1">{role === 's' ? 'S 端总站长' : 'B 端商户'}</p>
          <div className="w-full border-t border-border my-5" />
          <div className="w-full text-left space-y-3 text-sm">
            <div className="flex justify-between">
              <span className="text-text-secondary">账号角色</span>
              <span>{role === 's' ? '总站长' : '商户'}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-text-secondary">注册时间</span>
              <span>2026-01-01</span>
            </div>
            {role === 'b' && (
              <div className="flex justify-between">
                <span className="text-text-secondary">账户余额</span>
                <span className="text-danger font-medium">¥{base.balance.toLocaleString('zh-CN')}</span>
              </div>
            )}
          </div>
        </div>

        <div className="card p-6 lg:col-span-2">
          <h3 className="text-lg font-semibold mb-5">基础信息</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label className="block text-sm mb-1.5 flex items-center gap-1.5 text-text-secondary">
                <User size={14} /> 昵称 / 姓名
              </label>
              <input
                className="input"
                value={form.name}
                onChange={(e) => setForm({ ...form, name: e.target.value })}
              />
            </div>
            <div>
              <label className="block text-sm mb-1.5 flex items-center gap-1.5 text-text-secondary">
                <Mail size={14} /> 邮箱
              </label>
              <input
                className="input"
                value={form.email}
                onChange={(e) => setForm({ ...form, email: e.target.value })}
              />
            </div>
            <div>
              <label className="block text-sm mb-1.5 flex items-center gap-1.5 text-text-secondary">
                <Phone size={14} /> 手机号
              </label>
              <input
                className="input"
                value={form.phone}
                onChange={(e) => setForm({ ...form, phone: e.target.value })}
              />
            </div>
            {role === 'b' && (
              <div>
                <label className="block text-sm mb-1.5 flex items-center gap-1.5 text-text-secondary">
                  <Building2 size={14} /> 店铺名称
                </label>
                <input
                  className="input"
                  value={form.shopName}
                  onChange={(e) => setForm({ ...form, shopName: e.target.value })}
                />
              </div>
            )}
          </div>

          <h3 className="text-lg font-semibold mb-5 mt-8">安全设置</h3>
          <div className="space-y-4">
            <div className="flex items-center justify-between py-3 border-b border-border">
              <div>
                <div className="font-medium">登录密码</div>
                <div className="text-sm text-text-secondary">建议定期更换密码以保障账号安全</div>
              </div>
              <button onClick={() => show('修改密码功能开发中', 'info')} className="btn btn-default text-xs">
                修改密码
              </button>
            </div>
            <div className="flex items-center justify-between py-3 border-b border-border">
              <div>
                <div className="font-medium">手机绑定</div>
                <div className="text-sm text-text-secondary">已绑定：{form.phone}</div>
              </div>
              <button onClick={() => show('换绑功能开发中', 'info')} className="btn btn-default text-xs">
                更换手机
              </button>
            </div>
            <div className="flex items-center justify-between py-3">
              <div>
                <div className="font-medium">两步验证</div>
                <div className="text-sm text-text-secondary">开启后登录需二次验证，提升安全性</div>
              </div>
              <button onClick={() => show('两步验证功能开发中', 'info')} className="btn btn-default text-xs">
                去开启
              </button>
            </div>
          </div>

          <div className="flex items-center justify-end gap-3 mt-8">
            <button onClick={() => navigate(-1)} className="btn btn-default">返回</button>
            <button onClick={handleSave} className="btn btn-primary flex items-center gap-1">
              <Save size={16} /> 保存修改
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
