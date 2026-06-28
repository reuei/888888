import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { gateways } from '../../data/mock';
import { Plus, Trash2, CheckCircle } from 'lucide-react';

export default function GatewayConfig() {
  const [list, setList] = useState(gateways);
  const [open, setOpen] = useState(false);
  const [name, setName] = useState('');
  const [channel, setChannel] = useState('alipay');
  const [fee, setFee] = useState('');

  const toggleEnabled = (id: string) => {
    setList((prev) =>
      prev.map((g) => (g.id === id ? { ...g, enabled: !g.enabled } : g))
    );
  };

  const setDefault = (id: string) => {
    setList((prev) =>
      prev.map((g) => ({ ...g, isDefault: g.id === id }))
    );
  };

  const remove = (id: string) => {
    setList((prev) => prev.filter((g) => g.id !== id));
  };

  const handleAdd = () => {
    const feeValue = parseFloat(fee);
    if (!name || Number.isNaN(feeValue)) return;
    const newGateway = {
      id: `GW${String(list.length + 1).padStart(3, '0')}`,
      name,
      channel,
      fee: feeValue,
      enabled: true,
      isDefault: false,
    };
    setList([...list, newGateway]);
    setName('');
    setChannel('alipay');
    setFee('');
    setOpen(false);
  };

  return (
    <div>
      <PageHeader
        title="网关配置"
        breadcrumb={['财务管理', '网关配置']}
        actions={
          <button onClick={() => setOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加网关
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>网关ID</th>
              <th>网关名称</th>
              <th>通道编码</th>
              <th>费率(%)</th>
              <th>启用状态</th>
              <th>默认</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((g) => (
              <tr key={g.id}>
                <td className="font-medium">{g.id}</td>
                <td>{g.name}</td>
                <td>{g.channel}</td>
                <td>{g.fee}%</td>
                <td>
                  <label className="flex items-center gap-2 text-sm cursor-pointer">
                    <input
                      type="checkbox"
                      checked={g.enabled}
                      onChange={() => toggleEnabled(g.id)}
                    />
                    <span className={g.enabled ? 'text-success' : 'text-text-secondary'}>
                      {g.enabled ? '已启用' : '已停用'}
                    </span>
                  </label>
                </td>
                <td>
                  <input
                    type="radio"
                    name="defaultGateway"
                    checked={g.isDefault}
                    onChange={() => setDefault(g.id)}
                  />
                </td>
                <td>
                  <button
                    onClick={() => remove(g.id)}
                    className="btn btn-default text-xs flex items-center gap-1"
                  >
                    <Trash2 size={14} /> 删除
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={open}
        title="添加网关"
        onClose={() => setOpen(false)}
        footer={
          <>
            <button onClick={() => setOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-success flex items-center gap-1">
              <CheckCircle size={16} /> 确认添加
            </button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">网关名称</label>
            <input
              className="input"
              placeholder="例如：支付宝官方"
              value={name}
              onChange={(e) => setName(e.target.value)}
            />
          </div>
          <div>
            <label className="block text-sm mb-1">通道编码</label>
            <select className="input" value={channel} onChange={(e) => setChannel(e.target.value)}>
              <option value="alipay">alipay</option>
              <option value="wxpay">wxpay</option>
              <option value="epay">epay</option>
              <option value="usdt">usdt</option>
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">费率（%）</label>
            <input
              type="number"
              step="0.01"
              className="input"
              placeholder="例如：0.6"
              value={fee}
              onChange={(e) => setFee(e.target.value)}
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
