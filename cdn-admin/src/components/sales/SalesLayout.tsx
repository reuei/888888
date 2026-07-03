import { useState, useEffect } from 'react';
import SalesHeader from './SalesHeader';
import SalesFooter from './SalesFooter';
import AnnouncementModal from './AnnouncementModal';

interface SalesLayoutProps {
  children: React.ReactNode;
  onLogout?: () => void;
}

export default function SalesLayout({ children, onLogout }: SalesLayoutProps) {
  const [showAnnouncement, setShowAnnouncement] = useState(false);

  useEffect(() => {
    const closed = sessionStorage.getItem('sales-announcement-closed');
    if (!closed) {
      const timer = setTimeout(() => setShowAnnouncement(true), 1200);
      return () => clearTimeout(timer);
    }
  }, []);

  return (
    <div className="min-h-screen flex flex-col bg-[var(--sales-bg)] text-[var(--sales-text)]">
      <SalesHeader loggedIn onLogout={onLogout} />
      <main className="flex-1">{children}</main>
      <SalesFooter />
      <AnnouncementModal isOpen={showAnnouncement} onClose={() => setShowAnnouncement(false)} />
    </div>
  );
}
