import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { Upload, FileText, CheckCircle2, Download, ArrowLeft, ArrowRight } from 'lucide-react';

const steps = ['选择方式', '上传数据', '预览校验', '发卡模式', '导入结果'];

const previewRows = [
  { content: 'XXXX-XXXX-XXXX-001', valid: true },
  { content: 'XXXX-XXXX-XXXX-002', valid: true },
  { content: 'XXXX-XXXX-XXXX-003', valid: true },
  { content: '', valid: false },
  { content: 'XXXX-XXXX-XXXX-005', valid: true },
];

export default function CardImport() {
  const { show } = useToast();
  const [step, setStep] = useState(1);
  const [method, setMethod] = useState<'single' | 'batch'>('batch');
  const [product, setProduct] = useState('');
  const [singleText, setSingleText] = useState('');
  const [deliverMode, setDeliverMode] = useState<'sequential' | 'random'>('sequential');

  const next = () => setStep((s) => Math.min(5, s + 1));
  const prev = () => setStep((s) => Math.max(1, s - 1));

  const confirmImport = () => {
    next();
    show('卡密导入完成', 'success');
  };

  return (
    <div>
      <PageHeader title="卡密导入" breadcrumb={['商品管理', '卡密导入']} />

      {/* Step indicator */}
      <div className="card p-5 mb-6">
        <div className="flex items-start">
          {steps.map((label, i) => {
            const idx = i + 1;
            const done = idx < step;
            const active = idx === step;
            const isLast = i === steps.length - 1;
            return (
              <div key={label} className={`flex ${isLast ? '' : 'flex-1'}`}>
                <div className="flex flex-col items-center">
                  <div
                    className={`w-9 h-9 rounded-full flex items-center justify-center text-sm font-medium border-2 ${
                      done || active ? 'border-primary bg-primary text-white' : 'border-border bg-card text-text-secondary'
                    }`}
                  >
                    {done ? <CheckCircle2 size={16} /> : idx}
                  </div>
                  <div className={`text-xs mt-2 whitespace-nowrap ${active || done ? 'text-primary font-medium' : 'text-text-secondary'}`}>
                    {label}
                  </div>
                </div>
                {!isLast && <div className={`flex-1 h-0.5 self-start mt-4 mx-2 ${done ? 'bg-primary' : 'bg-border'}`} />}
              </div>
            );
          })}
        </div>
      </div>

      {/* Step content */}
      <div className="card p-6">
        {step === 1 && (
          <div className="space-y-5 max-w-xl">
            <div>
              <label className="block text-sm mb-2 font-medium">导入方式</label>
              <div className="grid grid-cols-2 gap-3">
                <button
                  onClick={() => setMethod('single')}
                  className={`p-4 border rounded text-left ${method === 'single' ? 'border-primary bg-primary/5' : 'border-border'}`}
                >
                  <FileText size={20} className={method === 'single' ? 'text-primary' : 'text-text-secondary'} />
                  <div className="font-medium mt-2">单条添加</div>
                  <div className="text-xs text-text-secondary mt-1">手动输入卡密内容</div>
                </button>
                <button
                  onClick={() => setMethod('batch')}
                  className={`p-4 border rounded text-left ${method === 'batch' ? 'border-primary bg-primary/5' : 'border-border'}`}
                >
                  <Upload size={20} className={method === 'batch' ? 'text-primary' : 'text-text-secondary'} />
                  <div className="font-medium mt-2">批量导入</div>
                  <div className="text-xs text-text-secondary mt-1">上传 txt/xls/xlsx 文件</div>
                </button>
              </div>
            </div>
            <div>
              <label className="block text-sm mb-1 font-medium">选择商品</label>
              <select value={product} onChange={(e) => setProduct(e.target.value)} className="input">
                <option value="">请选择商品</option>
                <option value="CP001">VPN月卡</option>
                <option value="CP002">游戏点券100元</option>
                <option value="CP003">Steam充值卡</option>
                <option value="CP004">话费充值50元</option>
              </select>
            </div>
          </div>
        )}

        {step === 2 && (
          <div>
            {method === 'batch' ? (
              <div className="border-2 border-dashed border-border rounded-lg p-10 text-center hover:border-primary transition-colors cursor-pointer">
                <Upload size={32} className="mx-auto text-text-secondary mb-3" />
                <div className="font-medium">点击或拖拽文件到此处上传</div>
                <div className="text-xs text-text-secondary mt-1">支持 .txt / .xls / .xlsx 格式，单次最多 10000 条</div>
                <button onClick={() => show('请选择文件', 'info')} className="btn btn-default mt-4">选择文件</button>
              </div>
            ) : (
              <div>
                <label className="block text-sm mb-1 font-medium">输入卡密内容（每行一条）</label>
                <textarea
                  value={singleText}
                  onChange={(e) => setSingleText(e.target.value)}
                  rows={10}
                  className="input font-mono"
                  placeholder={'XXXX-XXXX-XXXX-001\nXXXX-XXXX-XXXX-002\n...'}
                />
                <div className="text-xs text-text-secondary mt-1">每行一条卡密，空行自动忽略</div>
              </div>
            )}
          </div>
        )}

        {step === 3 && (
          <div>
            <div className="flex items-center justify-between mb-3">
              <h3 className="font-medium">数据预览（前 5 行）</h3>
              <div className="flex items-center gap-3 text-xs">
                <span className="badge badge-success">有效 4 条</span>
                <span className="badge badge-danger">错误 1 条</span>
              </div>
            </div>
            {method === 'batch' && (
              <div className="grid grid-cols-2 gap-3 mb-4 max-w-md">
                <div>
                  <label className="block text-xs text-text-secondary mb-1">卡密内容列</label>
                  <select className="input">
                    <option>第 1 列</option>
                    <option>第 2 列</option>
                  </select>
                </div>
                <div>
                  <label className="block text-xs text-text-secondary mb-1">备注列</label>
                  <select className="input">
                    <option>不映射</option>
                    <option>第 2 列</option>
                  </select>
                </div>
              </div>
            )}
            <table className="table">
              <thead>
                <tr>
                  <th>行号</th>
                  <th>卡密内容</th>
                  <th>校验</th>
                </tr>
              </thead>
              <tbody>
                {previewRows.map((r, i) => (
                  <tr key={i} className={r.valid ? '' : 'bg-red-50'}>
                    <td className="text-text-secondary">{i + 1}</td>
                    <td className="font-mono">{r.content || <span className="text-text-secondary">（空）</span>}</td>
                    <td>
                      {r.valid ? (
                        <span className="badge badge-success">通过</span>
                      ) : (
                        <span className="badge badge-danger">内容为空</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {step === 4 && (
          <div className="space-y-5 max-w-xl">
            <div>
              <label className="block text-sm mb-2 font-medium">发卡模式</label>
              <div className="space-y-2">
                <label
                  className={`flex items-center gap-3 p-3 border rounded cursor-pointer ${
                    deliverMode === 'sequential' ? 'border-primary bg-primary/5' : 'border-border'
                  }`}
                >
                  <input type="radio" checked={deliverMode === 'sequential'} onChange={() => setDeliverMode('sequential')} />
                  <div>
                    <div className="font-medium">顺序发卡</div>
                    <div className="text-xs text-text-secondary">按导入顺序依次发放卡密</div>
                  </div>
                </label>
                <label
                  className={`flex items-center gap-3 p-3 border rounded cursor-pointer ${
                    deliverMode === 'random' ? 'border-primary bg-primary/5' : 'border-border'
                  }`}
                >
                  <input type="radio" checked={deliverMode === 'random'} onChange={() => setDeliverMode('random')} />
                  <div>
                    <div className="font-medium">随机发卡</div>
                    <div className="text-xs text-text-secondary">从库存中随机抽取卡密发放</div>
                  </div>
                </label>
              </div>
            </div>
            <div className="p-3 bg-gray-50 rounded text-sm text-text-secondary">
              将导入 <span className="text-text font-medium">4</span> 条卡密到商品{' '}
              <span className="text-text font-medium">{product ? '已选商品' : '未选择商品'}</span>，发卡模式：
              <span className="text-text font-medium">{deliverMode === 'sequential' ? '顺序发卡' : '随机发卡'}</span>
            </div>
          </div>
        )}

        {step === 5 && (
          <div className="text-center py-6">
            <div className="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
              <CheckCircle2 size={32} className="text-success" />
            </div>
            <h3 className="text-lg font-semibold mb-2">导入完成</h3>
            <div className="flex items-center justify-center gap-6 mb-6">
              <div>
                <div className="text-2xl font-bold text-success">4</div>
                <div className="text-xs text-text-secondary">成功</div>
              </div>
              <div>
                <div className="text-2xl font-bold text-danger">1</div>
                <div className="text-xs text-text-secondary">失败</div>
              </div>
            </div>
            <button
              onClick={() => show('错误文件下载已开始', 'info')}
              className="text-primary text-sm flex items-center gap-1 mx-auto"
            >
              <Download size={14} /> 下载错误文件
            </button>
          </div>
        )}

        {/* Footer nav */}
        {step < 5 && (
          <div className="flex justify-between mt-6 pt-4 border-t border-border">
            <button onClick={prev} disabled={step === 1} className="btn btn-default flex items-center gap-1 disabled:opacity-50">
              <ArrowLeft size={16} /> 上一步
            </button>
            {step < 4 ? (
              <button onClick={next} className="btn btn-primary flex items-center gap-1">
                下一步 <ArrowRight size={16} />
              </button>
            ) : (
              <button onClick={confirmImport} className="btn btn-primary flex items-center gap-1">
                确认导入 <ArrowRight size={16} />
              </button>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
