interface PageHeaderProps {
  title: string;
  breadcrumb?: string[];
  actions?: React.ReactNode;
}

export default function PageHeader({ title, breadcrumb, actions }: PageHeaderProps) {
  return (
    <div className="mb-6">
      {breadcrumb && (
        <div className="text-xs text-text-secondary mb-2">
          {breadcrumb.join(' / ')}
        </div>
      )}
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold">{title}</h1>
        {actions && <div className="flex items-center gap-2">{actions}</div>}
      </div>
    </div>
  );
}
