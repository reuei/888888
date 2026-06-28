import { X } from 'lucide-react';

interface ModalProps {
  open: boolean;
  title: string;
  onClose: () => void;
  children: React.ReactNode;
  footer?: React.ReactNode;
}

export default function Modal({ open, title, onClose, children, footer }: ModalProps) {
  if (!open) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
      <div className="bg-card rounded border border-border w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
        <div className="flex items-center justify-between px-4 h-12 border-b border-border">
          <h3 className="font-semibold">{title}</h3>
          <button onClick={onClose} className="p-1 rounded hover:bg-gray-100 text-text-secondary">
            <X size={18} />
          </button>
        </div>
        <div className="p-5 overflow-y-auto">{children}</div>
        {footer && <div className="px-4 py-3 border-t border-border flex justify-end gap-2">{footer}</div>}
      </div>
    </div>
  );
}
