import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { Monitor, Smartphone, ShoppingBag, Music, Smartphone as PhoneIcon, Download } from 'lucide-react';

export default function STemplates() {
  const [activePc, setActivePc] = useState('PC-01');
  const [activeMobile, setActiveMobile] = useState('M-01');
  const [activeCard, setActiveCard] = useState('CARD-01');
  const [autoPlay, setAutoPlay] = useState(false);

  const pcTemplates = [
    { id: 'PC-01', name: '企业官网风格' },
    { id: 'PC-02', name: '科技深蓝风格' },
    { id: 'PC-03', name: '极简白风格' },
  ];

  const mobileTemplates = [
    { id: 'M-01', name: '移动端标准版' },
    { id: 'M-02', name: '移动端深色版' },
    { id: 'M-03', name: '移动端渐变版' },
    { id: 'M-04', name: '移动端卡片版' },
  ];

  const cardTemplates = [
    { id: 'CARD-01', name: '经典购卡页' },
    { id: 'CARD-02', name: '暗色游戏风' },
    { id: 'CARD-03', name: '清新电商风' },
    { id: 'CARD-04', name: '极简科技风' },
    { id: 'CARD-05', name: '促销节日风' },
    { id: 'CARD-06', name: '会员专享风' },
    { id: 'CARD-07', name: '直播带货风' },
    { id: 'CARD-08', name: '企业采购风' },
    { id: 'CARD-09', name: '教育课程风' },
    { id: 'CARD-10', name: '云服务风' },
    { id: 'CARD-11', name: 'DIY 自定义' },
  ];

  const TemplateCard = ({ id, name, active, onClick }: { id: string; name: string; active: boolean; onClick: () => void }) => (
    <div
      onClick={onClick}
      className={`card p-3 cursor-pointer border-2 ${active ? 'border-primary' : 'border-border'}`}
    >
      <div className="h-20 bg-gray-100 rounded mb-2 flex items-center justify-center text-xs text-text-secondary">
        {name}
      </div>
      <div className="text-sm font-medium">{name}</div>
      <div className="text-xs text-text-secondary">{id}</div>
    </div>
  );

  return (
    <div>
      <PageHeader title="模板与前端管理" breadcrumb={['模板与前端管理', '首页模板']} />

      <div className="space-y-6">
        <div className="card p-5">
          <h3 className="font-semibold mb-4 flex items-center gap-2"><Monitor size={18} className="text-primary" /> 电脑端首页模板（3 套）</h3>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {pcTemplates.map((t) => (
              <TemplateCard key={t.id} {...t} active={activePc === t.id} onClick={() => setActivePc(t.id)} />
            ))}
          </div>
        </div>

        <div className="card p-5">
          <h3 className="font-semibold mb-4 flex items-center gap-2"><Smartphone size={18} className="text-primary" /> 手机端首页模板（4 套）</h3>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {mobileTemplates.map((t) => (
              <TemplateCard key={t.id} {...t} active={activeMobile === t.id} onClick={() => setActiveMobile(t.id)} />
            ))}
          </div>
        </div>

        <div className="card p-5">
          <h3 className="font-semibold mb-4 flex items-center gap-2"><ShoppingBag size={18} className="text-primary" /> 购卡页模板（11 套 + DIY）</h3>
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            {cardTemplates.map((t) => (
              <TemplateCard key={t.id} {...t} active={activeCard === t.id} onClick={() => setActiveCard(t.id)} />
            ))}
          </div>
          {activeCard === 'CARD-11' && (
            <div className="mt-4 p-4 border border-border rounded bg-gray-50">
              <h4 className="font-medium mb-3">DIY 自定义</h4>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm mb-1">背景图片 / 视频</label>
                  <button className="btn btn-default text-xs">上传文件</button>
                </div>
                <div>
                  <label className="block text-sm mb-1">主色</label>
                  <input type="color" defaultValue="#2196F3" className="input h-8 p-1" />
                </div>
                <div>
                  <label className="block text-sm mb-1">按钮样式</label>
                  <select className="input">
                    <option>圆角矩形</option>
                    <option>直角矩形</option>
                    <option>胶囊形</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm mb-1">页面文案</label>
                  <input className="input" placeholder="例如 立即购买" />
                </div>
              </div>
            </div>
          )}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2"><Music size={18} className="text-primary" /> 售卡页音乐播放器</h3>
            <div className="space-y-3">
              <div>
                <label className="block text-sm mb-1">背景音乐 MP3</label>
                <button className="btn btn-default text-xs">上传音乐</button>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-sm">自动播放</span>
                <input type="checkbox" checked={autoPlay} onChange={(e) => setAutoPlay(e.target.checked)} className="w-4 h-4" />
              </div>
              <div>
                <label className="block text-sm mb-1">音量</label>
                <input type="range" min="0" max="100" defaultValue="50" className="w-full" />
              </div>
              <button className="btn btn-primary">保存</button>
            </div>
          </div>

          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2"><PhoneIcon size={18} className="text-primary" /> APP 打包</h3>
            <div className="space-y-3">
              <div>
                <label className="block text-sm mb-1">应用图标</label>
                <button className="btn btn-default text-xs">上传图标</button>
              </div>
              <div>
                <label className="block text-sm mb-1">启动图</label>
                <button className="btn btn-default text-xs">上传启动图</button>
              </div>
              <div>
                <label className="block text-sm mb-1">包名</label>
                <input className="input" defaultValue="com.example.cdnapp" />
              </div>
              <button className="btn btn-primary flex items-center gap-1"><Download size={16} /> 生成安装包</button>
            </div>
          </div>

          <div className="card p-5">
            <h3 className="font-semibold mb-4">店铺引导页 / 子域名</h3>
            <div className="space-y-3">
              <div>
                <label className="block text-sm mb-1">默认引导页</label>
                <select className="input">
                  <option>引导页 A</option>
                  <option>引导页 B</option>
                </select>
              </div>
              <div>
                <label className="block text-sm mb-1">子域名规则</label>
                <input className="input" defaultValue="*.cdn.example.com" />
              </div>
              <button className="btn btn-primary">保存</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
