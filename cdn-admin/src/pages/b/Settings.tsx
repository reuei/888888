import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { User, Lock, Phone, Mail, CreditCard, MessageCircle } from 'lucide-react';

export default function BSettings() {
  const [tab, setTab] = useState('profile');
  const { show } = useToast();

  return (
    <div>
      <PageHeader title="个人设置" breadcrumb={['个人设置', '个人信息']} />

      <div className="card p-5">
        <div className="flex gap-2 mb-6 border-b border-border">
          {[
            { key: 'profile', label: '个人信息', icon: User },
            { key: 'third', label: '第三方登录', icon: MessageCircle },
            { key: 'realname', label: '实名认证', icon: CreditCard },
            { key: 'payment', label: '自定义支付', icon: CreditCard },
          ].map((t) => (
            <button
              key={t.key}
              onClick={() => setTab(t.key)}
              className={`flex items-center gap-1.5 px-4 py-2 text-sm border-b-2 ${
                tab === t.key ? 'border-primary text-primary' : 'border-transparent text-text-secondary'
              }`}
            >
              <t.icon size={14} /> {t.label}
            </button>
          ))}
        </div>

        {tab === 'profile' && (
          <div className="max-w-lg space-y-4">
            <div className="flex items-center gap-4">
              <div className="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center text-2xl">B</div>
              <button className="btn btn-default text-xs">更换头像</button>
            </div>
            <div>
              <label className="block text-sm mb-1">昵称</label>
              <input className="input" defaultValue="商户_阿明" />
            </div>
            <div>
              <label className="block text-sm mb-1">登录密码</label>
              <div className="flex gap-2">
                <input type="password" className="input" defaultValue="********" />
                <button className="btn btn-default text-xs"><Lock size={14} /> 修改</button>
              </div>
            </div>
            <div>
              <label className="block text-sm mb-1">安全手机</label>
              <div className="flex gap-2">
                <input className="input" defaultValue="138****1234" />
                <button className="btn btn-default text-xs"><Phone size={14} /> 修改</button>
              </div>
            </div>
            <div>
              <label className="block text-sm mb-1">安全邮箱</label>
              <div className="flex gap-2">
                <input className="input" defaultValue="amin@example.com" />
                <button className="btn btn-default text-xs"><Mail size={14} /> 修改</button>
              </div>
            </div>
            <button onClick={() => show('个人信息保存成功', 'success')} className="btn btn-primary">保存</button>
          </div>
        )}

        {tab === 'third' && (
          <div className="max-w-lg space-y-4">
            {[
              { name: '微信', bound: true },
              { name: 'QQ', bound: false },
            ].map((item) => (
              <div key={item.name} className="flex items-center justify-between p-4 border border-border rounded">
                <div>
                  <div className="font-medium">{item.name}登录</div>
                  <div className="text-xs text-text-secondary">{item.bound ? '已绑定' : '未绑定'}</div>
                </div>
                <button onClick={() => show(`${item.name}${item.bound ? '解绑' : '绑定'}成功`, item.bound ? 'warning' : 'success')} className={`btn text-xs ${item.bound ? 'btn-default' : 'btn-primary'}`}>
                  {item.bound ? '解绑' : '绑定'}
                </button>
              </div>
            ))}
          </div>
        )}

        {tab === 'realname' && (
          <div className="max-w-lg space-y-4">
            <div>
              <label className="block text-sm mb-1">真实姓名</label>
              <input className="input" placeholder="请输入真实姓名" />
            </div>
            <div>
              <label className="block text-sm mb-1">身份证号</label>
              <input className="input" placeholder="请输入身份证号" />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div className="h-32 bg-gray-100 rounded flex items-center justify-center text-xs text-text-secondary">身份证正面</div>
              <div className="h-32 bg-gray-100 rounded flex items-center justify-center text-xs text-text-secondary">身份证反面</div>
            </div>
            <div className="text-xs text-text-secondary">当前状态：<span className="text-warning">待审核</span></div>
            <button onClick={() => show('实名认证提交成功，等待审核', 'success')} className="btn btn-primary">提交实名认证</button>
          </div>
        )}

        {tab === 'payment' && (
          <div className="max-w-lg space-y-4">
            <div>
              <label className="block text-sm mb-1">支付接口地址</label>
              <input className="input" placeholder="https://api.example.com/pay" />
            </div>
            <div>
              <label className="block text-sm mb-1">商户号</label>
              <input className="input" placeholder="请输入商户号" />
            </div>
            <div>
              <label className="block text-sm mb-1">密钥</label>
              <input type="password" className="input" placeholder="请输入密钥" />
            </div>
            <div>
              <label className="block text-sm mb-1">回调地址</label>
              <input className="input" placeholder="https://yourdomain.com/callback" />
            </div>
            <div className="flex gap-2">
              <button onClick={() => show('连通性测试通过', 'success')} className="btn btn-default">测试连通性</button>
              <button onClick={() => show('自定义支付配置保存成功', 'success')} className="btn btn-primary">保存</button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
