import { Loader2 } from 'lucide-react';

interface LoadingProps {
  text?: string;
}

export default function Loading({ text = '加载中...' }: LoadingProps) {
  return (
    <div className="min-h-[200px] flex flex-col items-center justify-center text-text-secondary">
      <Loader2 size={28} className="animate-spin mb-3" />
      <span className="text-sm">{text}</span>
    </div>
  );
}
