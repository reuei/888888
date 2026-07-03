import { useState, Suspense, lazy } from 'react';
import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import type { SalesRole } from './types';
import SalesLayout from './components/sales/SalesLayout';
import ErrorBoundary from './components/ErrorBoundary';
import Loading from './components/Loading';
import SalesHome from './pages/sales/Home';
import SalesLogin from './pages/sales/Login';
import NotFound from './pages/NotFound';

const BuySource = lazy(() => import('./pages/sales/BuySource'));
const BuyNodes = lazy(() => import('./pages/sales/BuyNodes'));
const Updates = lazy(() => import('./pages/sales/Updates'));
const UserCenter = lazy(() => import('./pages/sales/UserCenter'));
const Orders = lazy(() => import('./pages/sales/Orders'));
const Announcements = lazy(() => import('./pages/sales/Announcements'));

function SalesApp() {
  const [role, setRole] = useState<SalesRole | null>(() => {
    if (typeof window === 'undefined') return null;
    const saved = localStorage.getItem('sales-role') as SalesRole | null;
    return saved === 'user' ? saved : null;
  });

  const handleLogin = (remember = true) => {
    if (remember) {
      localStorage.setItem('sales-role', 'user');
    } else {
      localStorage.removeItem('sales-role');
    }
    setRole('user');
  };

  const handleLogout = () => {
    localStorage.removeItem('sales-role');
    setRole(null);
  };

  if (!role) {
    return (
      <ErrorBoundary>
        <HashRouter>
          <Routes>
            <Route path="/" element={<SalesHome />} />
            <Route path="/login" element={<SalesLogin onLogin={handleLogin} />} />
            <Route path="/buy-source" element={<SalesHome />} />
            <Route path="/buy-nodes" element={<SalesHome />} />
            <Route path="/updates" element={<SalesHome />} />
            <Route path="/announcements" element={<SalesHome />} />
            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </HashRouter>
      </ErrorBoundary>
    );
  }

  return (
    <ErrorBoundary>
      <HashRouter>
        <SalesLayout onLogout={handleLogout}>
          <Suspense fallback={<Loading />}>
            <Routes>
              <Route path="/" element={<SalesHome />} />
              <Route path="/buy-source" element={<BuySource />} />
              <Route path="/buy-nodes" element={<BuyNodes />} />
              <Route path="/updates" element={<Updates />} />
              <Route path="/user" element={<UserCenter />} />
              <Route path="/orders" element={<Orders />} />
              <Route path="/announcements" element={<Announcements />} />
              <Route path="*" element={<NotFound />} />
            </Routes>
          </Suspense>
        </SalesLayout>
      </HashRouter>
    </ErrorBoundary>
  );
}

export default SalesApp;
