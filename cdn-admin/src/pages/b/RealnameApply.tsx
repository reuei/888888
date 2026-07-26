import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { Upload, ShieldCheck, Clock, XCircle } from 'lucide-react';

type AuthStatus = 'none' | 'pending' | 'approved' | 'rejected';

export default function RealnameApply() {
  const { show } = useToast();
  const [status, setStatus] = useState<AuthStatus>('none');
  const [form, setForm] = useState({ name: '', idCard: '', phone: '' });

  const submit = () => {
    if (!form.name || !form.idCard || !form.phone) {
      show('请填写完整信息', 'warning');
      return;
    }
    setStatus('pending');
    show('实名申请已提交，等待审核', 'success');
  };

  const statusUI = () => {
    switch (status) {
      case 'none':
        return (
          <div className="flex items-center gap-2 text-text-secondary">
            <XCircle size={16} /> 未认证
          </div>
        );
      case 'pending':
        return (
          <div className="flex items-center gap-2 text-warning">
            <Clock size={16} /> 审核中
          </div>
        );
      case 'approved':
        return (
          <div className="flex items-center gap-2 text-success">
            <ShieldCheck size={16} /> 已认证
          </div>
        );
      case 'rejected':
        return (
          <div className="flex items-center gap-2 text-danger">
            <XCircle size={16} /> 已驳回，请重新提交
          </div>
        );
    }
  };

  const locked = status === 'pending' || status === 'approved';

  return (
    <div>
      <PageHeader title="实名申请" breadcrumb={['店铺设置', '实名申请']} />

      <div className="card p-5 mb-5">
        <div className="flex items-center justify-between">
          <h3 className="font-medium">当前认证状态</h3>
          {statusUI()}
        </div>
      </div>

      <div className="card p-5 max-w-2xl">
        <h3 className="font-semibold mb-4">实名认证信息</h3>
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">真实姓名</label>
            <input
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
              className="input"
              placeholder="请输入真实姓名"
              disabled={locked}
            />
          </div>
          <div>
            <label className="block text-sm mb-1">身份证号</label>
            <input
              value={form.idCard}
              onChange={(e) => setForm({ ...form, idCard: e.target.value })}
              className="input"
              placeholder="请输入18位身份证号"
              disabled={locked}
            />
          </div>
          <div>
            <label className="block text-sm mb-1">联系电话</label>
            <input
              value={form.phone}
              onChange={(e) => setForm({ ...form, phone: e.target.value })}
              className="input"
              placeholder="请输入联系电话"
              disabled={locked}
            />
          </div>
          <div>
            <label className="block text-sm mb-1">身份证照片</label>
            <div className="grid grid-cols-2 gap-3">
              <div className="border-2 border-dashed border-border rounded-lg p-6 text-center hover:border-primary cursor-pointer">
                <Upload size={20} className="mx-auto text-text-secondary mb-2" />
                <div className="text-sm">身份证正面</div>
                <div className="text-xs text-text-secondary mt-1">点击上传</div>
              </div>
              <div className="border-2 border-dashed border-border rounded-lg p-6 text-center hover:border-primary cursor-pointer">
                <Upload size={20} className="mx-auto text-text-secondary mb-2" />
                <div className="text-sm">身份证反面</div>
                <div className="text-xs text-text-secondary mt-1">点击上传</div>
              </div>
            </div>
          </div>
          <button onClick={submit} disabled={locked} className="btn btn-primary disabled:opacity-50">
            提交申请
          </button>
        </div>
      </div>
    </div>
  );
}
