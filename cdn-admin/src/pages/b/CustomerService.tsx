import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { Send, Headset, ArrowUpRight } from 'lucide-react';

interface Conv {
  id: string;
  name: string;
  avatar: string;
  lastMessage: string;
  unread: number;
  online: boolean;
}

interface Msg {
  id: string;
  from: 'me' | 'user';
  content: string;
  time: string;
}

const mockConvs: Conv[] = [
  { id: 'C1', name: 'user_9527', avatar: '9', lastMessage: '卡密用不了，麻烦处理下', unread: 2, online: true },
  { id: 'C2', name: 'user_3344', avatar: '3', lastMessage: '好的，谢谢', unread: 0, online: false },
  { id: 'C3', name: 'user_7788', avatar: '7', lastMessage: '订单什么时候发货？', unread: 1, online: true },
];

const mockMsgs: Record<string, Msg[]> = {
  C1: [
    { id: 'M1', from: 'user', content: '你好，我买的VPN月卡卡密用不了', time: '10:20' },
    { id: 'M2', from: 'me', content: '您好，请提供下订单号', time: '10:21' },
    { id: 'M3', from: 'user', content: 'O202607100001', time: '10:22' },
    { id: 'M4', from: 'user', content: '卡密用不了，麻烦处理下', time: '10:23' },
  ],
  C2: [
    { id: 'M1', from: 'me', content: '问题已处理', time: '09:30' },
    { id: 'M2', from: 'user', content: '好的，谢谢', time: '09:31' },
  ],
  C3: [{ id: 'M1', from: 'user', content: '订单什么时候发货？', time: '10:00' }],
};

export default function CustomerService() {
  const { show } = useToast();
  const [convs, setConvs] = useState(mockConvs);
  const [activeId, setActiveId] = useState(mockConvs[0].id);
  const [msgs, setMsgs] = useState<Record<string, Msg[]>>(mockMsgs);
  const [input, setInput] = useState('');

  const send = () => {
    if (!input.trim()) return;
    const newMsg: Msg = {
      id: `M${Date.now()}`,
      from: 'me',
      content: input.trim(),
      time: new Date().toLocaleTimeString('zh-CN', { hour: '2-digit', minute: '2-digit' }),
    };
    setMsgs((prev) => ({ ...prev, [activeId]: [...(prev[activeId] || []), newMsg] }));
    setConvs((prev) =>
      prev.map((c) => (c.id === activeId ? { ...c, lastMessage: newMsg.content, unread: 0 } : c))
    );
    setInput('');
  };

  const selectConv = (id: string) => {
    setActiveId(id);
    setConvs((prev) => prev.map((c) => (c.id === id ? { ...c, unread: 0 } : c)));
  };

  return (
    <div>
      <PageHeader title="客服管理" breadcrumb={['客服管理']} />

      <div
        className="bg-card border border-border rounded-lg overflow-hidden flex"
        style={{ height: 'calc(100vh - 180px)' }}
      >
        {/* Conversation list */}
        <div className="w-64 border-r border-border flex flex-col">
          <div className="p-3 border-b border-border text-sm font-medium flex items-center gap-2">
            <Headset size={16} className="text-primary" /> 会话列表
          </div>
          <div className="flex-1 overflow-y-auto">
            {convs.map((c) => (
              <button
                key={c.id}
                onClick={() => selectConv(c.id)}
                className={`w-full flex items-start gap-2.5 p-3 border-b border-border text-left hover:bg-gray-50 ${
                  activeId === c.id ? 'bg-primary/5' : ''
                }`}
              >
                <div className="relative">
                  <div className="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-medium">
                    {c.avatar}
                  </div>
                  {c.online && (
                    <span className="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-success rounded-full border-2 border-card" />
                  )}
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center justify-between">
                    <span className="font-medium text-sm truncate">{c.name}</span>
                    {c.unread > 0 && (
                      <span className="badge badge-danger text-xs px-1.5">{c.unread}</span>
                    )}
                  </div>
                  <div className="text-xs text-text-secondary truncate mt-0.5">{c.lastMessage}</div>
                </div>
              </button>
            ))}
          </div>
        </div>

        {/* Chat window */}
        <div className="flex-1 flex flex-col">
          <div className="p-3 border-b border-border flex items-center justify-between">
            <span className="font-medium text-sm">{convs.find((c) => c.id === activeId)?.name}</span>
            <button
              onClick={() => show('已转交 S端 客服处理', 'success')}
              className="btn btn-default text-xs flex items-center gap-1"
            >
              <ArrowUpRight size={14} /> 转S端
            </button>
          </div>
          <div className="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            {(msgs[activeId] || []).map((m) => (
              <div key={m.id} className={`flex ${m.from === 'me' ? 'justify-end' : 'justify-start'}`}>
                <div
                  className={`max-w-[70%] px-3 py-2 rounded-lg text-sm ${
                    m.from === 'me' ? 'bg-primary text-white' : 'bg-card border border-border'
                  }`}
                >
                  <div>{m.content}</div>
                  <div className={`text-xs mt-1 ${m.from === 'me' ? 'text-white/70' : 'text-text-secondary'}`}>
                    {m.time}
                  </div>
                </div>
              </div>
            ))}
          </div>
          <div className="p-3 border-t border-border flex gap-2">
            <input
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && send()}
              placeholder="输入消息..."
              className="input"
            />
            <button onClick={send} className="btn btn-primary flex items-center gap-1">
              <Send size={14} /> 发送
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
