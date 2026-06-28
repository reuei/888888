import { useState, useCallback, createContext, useContext, useEffect } from 'react';
import { CheckCircle, AlertCircle, XCircle, Info, X } from 'lucide-react';

type ToastType = 'success' | 'error' | 'warning' | 'info';

interface ToastItem {
  id: string;
  message: string;
  type: ToastType;
}

interface ToastContextValue {
  show: (message: string, type?: ToastType) => void;
}

const ToastContext = createContext<ToastContextValue | null>(null);

export function useToast() {
  const ctx = useContext(ToastContext);
  if (!ctx) throw new Error('useToast must be used within ToastProvider');
  return ctx;
}

const icons: Record<ToastType, React.ReactNode> = {
  success: <CheckCircle size={16} className="text-success" />,
  error: <XCircle size={16} className="text-danger" />,
  warning: <AlertCircle size={16} className="text-warning" />,
  info: <Info size={16} className="text-primary" />,
};

export function ToastProvider({ children }: { children: React.ReactNode }) {
  const [toasts, setToasts] = useState<ToastItem[]>([]);

  const show = useCallback((message: string, type: ToastType = 'info') => {
    const id = `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    setToasts((prev) => [...prev, { id, message, type }]);
  }, []);

  const remove = useCallback((id: string) => {
    setToasts((prev) => prev.filter((t) => t.id !== id));
  }, []);

  return (
    <ToastContext.Provider value={{ show }}>
      {children}
      <div className="fixed top-16 right-4 z-[60] space-y-2">
        {toasts.map((t) => (
          <Toast key={t.id} item={t} onRemove={remove} />
        ))}
      </div>
    </ToastContext.Provider>
  );
}

function Toast({ item, onRemove }: { item: ToastItem; onRemove: (id: string) => void }) {
  useEffect(() => {
    const timer = setTimeout(() => onRemove(item.id), 3000);
    return () => clearTimeout(timer);
  }, [item.id, onRemove]);

  return (
    <div className="card px-4 py-3 flex items-center gap-3 min-w-[240px] max-w-sm shadow-lg animate-slideIn">
      {icons[item.type]}
      <span className="text-sm flex-1">{item.message}</span>
      <button onClick={() => onRemove(item.id)} className="text-text-secondary hover:text-text">
        <X size={14} />
      </button>
    </div>
  );
}
