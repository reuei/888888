import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import EmptyState from '../../components/EmptyState';
import Pagination from '../../components/Pagination';
import { usePagination } from '../../hooks/usePagination';
import { useDebounce } from '../../hooks/useDebounce';
import { fetchPcTemplates, fetchMobileTemplates, fetchCardTemplates } from '../../services/api';
import type { TemplateItem } from '../../types';
import { Monitor, Smartphone, ShoppingBag, Music, Smartphone as PhoneIcon, Download, Search, Layout } from 'lucide-react';

export default function STemplates() {
  const [pcTemplates, setPcTemplates] = useState<TemplateItem[]>([]);
  const [mobileTemplates, setMobileTemplates] = useState<TemplateItem[]>([]);
  const [cardTemplates, setCardTemplates] = useState<TemplateItem[]>([]);
  const [loading, setLoading] = useState(false);
  const [activePc, setActivePc] = useState('PC-01');
  const [activeMobile, setActiveMobile] = useState('M-01');
  const [activeCard, setActiveCard] = useState('CARD-01');
  const [autoPlay, setAutoPlay] = useState(false);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const loadTemplates = useCallback(async () => {
    setLoading(true);
    try {
      const [pc, mobile, card] = await Promise.all([
        fetchPcTemplates(),
        fetchMobileTemplates(),
        fetchCardTemplates(),
      ]);
      setPcTemplates(pc);
      setMobileTemplates(mobile);
      setCardTemplates(card);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadTemplates();
  }, [loadTemplates]);

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

  function TemplateSection({
    title,
    icon,
    templates,
    activeId,
    onSelect,
    pageSize,
    gridCols,
  }: {
    title: string;
    icon: React.ReactNode;
    templates: TemplateItem[];
    activeId: string;
    onSelect: (id: string) => void;
    pageSize: number;
    gridCols: string;
  }) {
    const filtered = templates.filter((t) => {
      const q = debouncedKeyword.trim().toLowerCase();
      if (!q) return true;
      return t.name.toLowerCase().includes(q) || t.id.toLowerCase().includes(q);
    });

    const { page, pageSize: ps, totalPages, slice, setPage } = usePagination({ total: filtered.length, pageSize });
    const paged = slice(filtered);

    return (
      <div className="card p-5">
        <h3 className="font-semibold mb-4 flex items-center gap-2">{icon} {title}（{filtered.length} 套）</h3>
        {filtered.length > 0 ? (
          <>
            <div className={`grid ${gridCols} gap-4`}>
              {paged.map((t) => (
                <TemplateCard key={t.id} id={t.id} name={t.name} active={activeId === t.id} onClick={() => onSelect(t.id)} />
              ))}
            </div>
            <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={ps} onChange={setPage} />
          </>
        ) : (
          <EmptyState title="暂无模板" description="没有符合搜索条件的模板" icon={<Layout size={24} />} />
        )}
      </div>
    );
  }

  return (
    <div>
      <PageHeader
        title="模板与前端管理"
        breadcrumb={['模板与前端管理', '首页模板']}
      />

      <div className="card p-5 mb-6">
        <div className="relative max-w-md">
          <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
          <input
            type="text"
            placeholder="搜索模板名称 / ID"
            className="input pl-9 w-full"
            value={keyword}
            onChange={(e) => setKeyword(e.target.value)}
          />
        </div>
      </div>

      {loading && <div className="text-sm text-text-secondary mb-6">加载中...</div>}

      {!loading && (
        <div className="space-y-6">
          <TemplateSection
            title="电脑端首页模板"
            icon={<Monitor size={18} className="text-primary" />}
            templates={pcTemplates}
            activeId={activePc}
            onSelect={setActivePc}
            pageSize={3}
            gridCols="grid-cols-1 sm:grid-cols-2 lg:grid-cols-3"
          />

          <TemplateSection
            title="手机端首页模板"
            icon={<Smartphone size={18} className="text-primary" />}
            templates={mobileTemplates}
            activeId={activeMobile}
            onSelect={setActiveMobile}
            pageSize={4}
            gridCols="grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
          />

          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2"><ShoppingBag size={18} className="text-primary" /> 购卡页模板（{cardTemplates.length} 套 + DIY）</h3>
            {(() => {
              const filtered = cardTemplates.filter((t) => {
                const q = debouncedKeyword.trim().toLowerCase();
                if (!q) return true;
                return t.name.toLowerCase().includes(q) || t.id.toLowerCase().includes(q);
              });
              const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: filtered.length, pageSize: 6 });
              const paged = slice(filtered);
              return filtered.length > 0 ? (
                <>
                  <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    {paged.map((t) => (
                      <TemplateCard key={t.id} id={t.id} name={t.name} active={activeCard === t.id} onClick={() => setActiveCard(t.id)} />
                    ))}
                  </div>
                  <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
                </>
              ) : (
                <EmptyState title="暂无模板" description="没有符合搜索条件的模板" icon={<Layout size={24} />} />
              );
            })()}
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
      )}
    </div>
  );
}
