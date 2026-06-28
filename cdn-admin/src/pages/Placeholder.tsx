import PageHeader from '../components/PageHeader';
import { Construction } from 'lucide-react';

interface PlaceholderProps {
  title: string;
  breadcrumb?: string[];
}

export default function Placeholder({ title, breadcrumb }: PlaceholderProps) {
  return (
    <div>
      <PageHeader title={title} breadcrumb={breadcrumb} />
      <div className="card p-10 flex flex-col items-center justify-center text-text-secondary">
        <Construction size={48} className="mb-4 text-primary" />
        <p className="text-base">{title} 页面正在建设中</p>
        <p className="text-sm mt-2">本示例已覆盖核心模块，其余页面可按统一组件规范扩展。</p>
      </div>
    </div>
  );
}
