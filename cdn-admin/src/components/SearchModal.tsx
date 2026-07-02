import { useState, useMemo, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import type { Role } from '../types';
import { sMenu, bMenu, type MenuItem } from './menu';
import { Search, X, FileText } from 'lucide-react';

interface SearchModalProps {
  open: boolean;
  role: Role;
  onClose: () => void;
}

interface SearchItem {
  key: string;
  label: string;
  path: string;
  keywords: string;
}

function flattenMenu(menu: MenuItem[]): SearchItem[] {
  const items: SearchItem[] = [];
  menu.forEach((m) => {
    items.push({ key: m.key, label: m.label, path: m.key, keywords: m.label });
    m.children?.forEach((c) => {
      items.push({
        key: c.key,
        label: c.label,
        path: c.key,
        keywords: `${m.label} ${c.label}`,
      });
    });
  });
  return items;
}

export default function SearchModal({ open, role, onClose }: SearchModalProps) {
  const navigate = useNavigate();
  const [query, setQuery] = useState('');
  const [activeIndex, setActiveIndex] = useState(0);
  const menu = role === 's' ? sMenu : bMenu;
  const items = useMemo(() => flattenMenu(menu), [menu]);

  const results = useMemo(() => {
    if (!query.trim()) return items.slice(0, 8);
    const q = query.toLowerCase();
    return items.filter(
      (i) =>
        i.label.toLowerCase().includes(q) || i.keywords.toLowerCase().includes(q)
    );
  }, [query, items]);

  useEffect(() => {
    setActiveIndex(0);
  }, [query, results.length]);

  const handleSelect = useCallback((path: string) => {
    navigate(path);
    setQuery('');
    onClose();
  }, [navigate, onClose]);

  useEffect(() => {
    if (!open) {
      setQuery('');
      setActiveIndex(0);
    }
  }, [open]);

  useEffect(() => {
    if (!open) return;
    const handleKey = (e: KeyboardEvent) => {
      if (results.length === 0) return;
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        setActiveIndex((prev) => (prev + 1) % results.length);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        setActiveIndex((prev) => (prev - 1 + results.length) % results.length);
      } else if (e.key === 'Enter') {
        e.preventDefault();
        handleSelect(results[activeIndex].path);
      }
    };
    window.addEventListener('keydown', handleKey);
    return () => window.removeEventListener('keydown', handleKey);
  }, [open, results, activeIndex, handleSelect]);

  if (!open) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-start justify-center pt-24 bg-black/30 modal-overlay" onClick={onClose}>
      <div
        className="bg-card rounded border border-border w-full max-w-lg mx-4 flex flex-col shadow-lg modal-content"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="flex items-center gap-3 px-4 h-12 border-b border-border">
          <Search size={18} className="text-text-secondary" />
          <input
            autoFocus
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="搜索菜单、页面..."
            className="flex-1 outline-none text-sm bg-transparent"
          />
          <button onClick={onClose} className="p-1 rounded hover:bg-gray-100 text-text-secondary">
            <X size={18} />
          </button>
        </div>
        <div className="max-h-[60vh] overflow-y-auto py-2">
          {results.length === 0 ? (
            <div className="px-4 py-8 text-center text-sm text-text-secondary">
              未找到匹配的页面
            </div>
          ) : (
            results.map((item, index) => (
              <button
                key={item.key}
                onClick={() => handleSelect(item.path)}
                onMouseMove={() => setActiveIndex(index)}
                className={`w-full flex items-center gap-3 px-4 py-2.5 text-left ${
                  index === activeIndex ? 'bg-primary/10' : 'hover:bg-black/5 dark:hover:bg-white/5'
                }`}
              >
                <FileText size={16} className="text-text-secondary" />
                <div>
                  <div className="text-sm font-medium">{item.label}</div>
                  <div className="text-xs text-text-secondary">{item.keywords}</div>
                </div>
              </button>
            ))
          )}
        </div>
        <div className="px-4 py-2 border-t border-border text-xs text-text-secondary flex justify-between">
          <span>↑↓ 选择</span>
          <span>Enter 跳转</span>
        </div>
      </div>
    </div>
  );
}
