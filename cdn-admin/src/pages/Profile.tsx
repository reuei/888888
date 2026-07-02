import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import PageHeader from '../components/PageHeader';
import Modal from '../components/Modal';
import { useToast } from '../hooks/useToast';
import { sProfile, bProfile } from '../data/mock';
import { User, Mail, Phone, Building2, Save, Camera, Lock, Shield, Smartphone } from 'lucide-react';

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

  const [twoFactorEnabled, setTwoFactorEnabled] = useState(false);

  // 修改密码弹窗
  const [pwdOpen, setPwdOpen] = useState(false);
  const [pwdForm, setPwdForm] = useState({ current: '', new: '', confirm: '' });
  const [pwdError, setPwdError] = useState('');

  // 更换手机弹窗
  const [phoneOpen, setPhoneOpen] = useState(false);
  const [phoneForm, setPhoneForm] = useState({ newPhone: '', code: '' });
  const [codeSending, setCodeSending] = useState(false);
  const [codeSent, setCodeSent] = useState(false);

  const handleSave = () => {
    show('个人资料保存成功', 'success');
  };

  const openPwd = () => {
    setPwdForm({ current: '', new: '', confirm: '' });
    setPwdError('');
    setPwdOpen(true);
  };

  const handlePwdSubmit = () => {
    if (!pwdForm.current || !pwdForm.new || !pwdForm.confirm) {
      setPwdError('请填写完整密码信息');
      return;
    }
    if (pwdForm.current.length < 6 || pwdForm.new.length < 6) {
      setPwdError('密码长度不能少于 6 位');
      return;
    }
    if (pwdForm.new !== pwdForm.confirm) {
      setPwdError('两次输入的新密码不一致');
      return;
    }
    setPwdOpen(false);
    show('登录密码修改成功，请使用新密码登录', 'success');
  };

  const sendCode = () => {
    if (!/^1[3-9]\d{9}$/.test(phoneForm.newPhone)) {
      show('请输入正确的手机号', 'error');
      return;
    }
    setCodeSending(true);
    setTimeout(() => {
      setCodeSending(false);
      setCodeSent(true);
      show(`验证码已发送至 ${phoneForm.newPhone.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2')}`, 'success');
    }, 1000);
  };

  const handlePhoneSubmit = () => {
    if (!/^1[3-9]\d{9}$/.test(phoneForm.newPhone)) {
      show('请输入正确的手机号', 'error');
      return;
    }
    if (!/^\d{6}$/.test(phoneForm.code)) {
      show('请输入 6 位短信验证码', 'error');
      return;
    }
    setForm((prev) => ({ ...prev, phone: phoneForm.newPhone.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2') }));
    setPhoneOpen(false);
    setPhoneForm({ newPhone: '', code: '' });
    setCodeSent(false);
    show('安全手机更换成功', 'success');
  };

  const toggleTwoFactor = () => {
    setTwoFactorEnabled((prev) => {
      const next = !prev;
      show(next ? '两步验证已开启' : '两步验证已关闭', next ? 'success' : 'info');
      return next;
    });
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
              <button onClick={openPwd} className="btn btn-default text-xs flex items-center gap-1">
                <Lock size={14} /> 修改密码
              </button>
            </div>
            <div className="flex items-center justify-between py-3 border-b border-border">
              <div>
                <div className="font-medium">手机绑定</div>
                <div className="text-sm text-text-secondary">已绑定：{form.phone}</div>
              </div>
              <button onClick={() => setPhoneOpen(true)} className="btn btn-default text-xs flex items-center gap-1">
                <Smartphone size={14} /> 更换手机
              </button>
            </div>
            <div className="flex items-center justify-between py-3">
              <div>
                <div className="font-medium">两步验证</div>
                <div className="text-sm text-text-secondary">开启后登录需二次验证，提升安全性</div>
              </div>
              <button
                onClick={toggleTwoFactor}
                className={`btn text-xs flex items-center gap-1 ${twoFactorEnabled ? 'btn-success' : 'btn-default'}`}
              >
                <Shield size={14} /> {twoFactorEnabled ? '已开启' : '去开启'}
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

      {/* 修改密码弹窗 */}
      <Modal
        open={pwdOpen}
        title="修改登录密码"
        onClose={() => setPwdOpen(false)}
        footer={
          <>
            <button onClick={() => setPwdOpen(false)} className="btn btn-default text-xs">取消</button>
            <button onClick={handlePwdSubmit} className="btn btn-primary text-xs">确认修改</button>
          </>
        }
      >
        <div className="space-y-4">
          {pwdError && (
            <div className="text-sm text-danger bg-danger/5 px-3 py-2 rounded">{pwdError}</div>
          )}
          <div>
            <label className="block text-sm mb-1">当前密码</label>
            <input
              type="password"
              className="input"
              placeholder="请输入当前密码"
              value={pwdForm.current}
              onChange={(e) => setPwdForm({ ...pwdForm, current: e.target.value })}
            />
          </div>
          <div>
            <label className="block text-sm mb-1">新密码</label>
            <input
              type="password"
              className="input"
              placeholder="请输入新密码（至少 6 位）"
              value={pwdForm.new}
              onChange={(e) => setPwdForm({ ...pwdForm, new: e.target.value })}
            />
          </div>
          <div>
            <label className="block text-sm mb-1">确认新密码</label>
            <input
              type="password"
              className="input"
              placeholder="请再次输入新密码"
              value={pwdForm.confirm}
              onChange={(e) => setPwdForm({ ...pwdForm, confirm: e.target.value })}
            />
          </div>
        </div>
      </Modal>

      {/* 更换手机弹窗 */}
      <Modal
        open={phoneOpen}
        title="更换安全手机"
        onClose={() => {
          setPhoneOpen(false);
          setPhoneForm({ newPhone: '', code: '' });
          setCodeSent(false);
        }}
        footer={
          <>
            <button
              onClick={() => {
                setPhoneOpen(false);
                setPhoneForm({ newPhone: '', code: '' });
                setCodeSent(false);
              }}
              className="btn btn-default text-xs"
            >
              取消
            </button>
            <button onClick={handlePhoneSubmit} className="btn btn-primary text-xs">确认更换</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">当前手机号</label>
            <input className="input bg-black/5 dark:bg-white/5" value={form.phone} disabled />
          </div>
          <div>
            <label className="block text-sm mb-1">新手机号</label>
            <div className="flex gap-2">
              <input
                className="input flex-1"
                placeholder="请输入新手机号"
                value={phoneForm.newPhone}
                onChange={(e) => {
                  setPhoneForm({ ...phoneForm, newPhone: e.target.value });
                  setCodeSent(false);
                }}
              />
              <button
                onClick={sendCode}
                disabled={codeSending || codeSent}
                className="btn btn-default text-xs whitespace-nowrap disabled:opacity-60"
              >
                {codeSending ? '发送中...' : codeSent ? '已发送' : '获取验证码'}
              </button>
            </div>
          </div>
          <div>
            <label className="block text-sm mb-1">短信验证码</label>
            <input
              className="input"
              placeholder="请输入 6 位验证码"
              maxLength={6}
              value={phoneForm.code}
              onChange={(e) => setPhoneForm({ ...phoneForm, code: e.target.value.replace(/\D/g, '') })}
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
