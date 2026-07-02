import { createContext, useContext } from 'react';

type ToastType = 'success' | 'error' | 'warning' | 'info';

interface ToastContextValue {
  show: (message: string, type?: ToastType) => void;
}

export const ToastContext = createContext<ToastContextValue | null>(null);

export function useToast() {
  const ctx = useContext(ToastContext);
  if (!ctx) throw new Error('useToast must be used within ToastProvider');
  return ctx;
}
