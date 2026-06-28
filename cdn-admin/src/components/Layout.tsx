import { useState } from 'react';
import type { Role } from '../types';
import Header from './Header';
import Sidebar from './Sidebar';

interface LayoutProps {
  role: Role;
  children: React.ReactNode;
  onSwitchRole: () => void;
  onLogout: () => void;
}

export default function Layout({ role, children, onSwitchRole, onLogout }: LayoutProps) {
  const [collapsed, setCollapsed] = useState(false);

  return (
    <div className="flex h-screen bg-bg">
      <Sidebar role={role} collapsed={collapsed} />
      <div className="flex flex-col flex-1 min-w-0">
        <Header
          role={role}
          collapsed={collapsed}
          onToggle={() => setCollapsed(!collapsed)}
          onSwitchRole={onSwitchRole}
          onLogout={onLogout}
        />
        <main className="flex-1 overflow-auto p-6">
          {children}
          <footer className="mt-8 text-xs text-text-secondary text-center py-2 border-t border-border">
            CDN 防护加速平台 v1.0.0 · © 2026 企业级CDN · 帮助文档
          </footer>
        </main>
      </div>
    </div>
  );
}
