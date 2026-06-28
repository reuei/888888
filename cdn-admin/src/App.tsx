import { useState } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import type { Role } from './types';
import Layout from './components/Layout';
import Login from './pages/Login';
import Placeholder from './pages/Placeholder';

// S-side pages
import SDashboard from './pages/s/Dashboard';
import SSites from './pages/s/Sites';
import SiteConfig from './pages/s/SiteConfig';
import SMerchants from './pages/s/Merchants';
import SMerchantAudit from './pages/s/MerchantAudit';
import SInvites from './pages/s/Invites';
import SProducts from './pages/s/Products';
import SCategories from './pages/s/Categories';
import SNodes from './pages/s/Nodes';
import SSkus from './pages/s/Skus';
import SOrders from './pages/s/Orders';
import SComplaints from './pages/s/Complaints';
import SAbnormalOrders from './pages/s/AbnormalOrders';
import SFinance from './pages/s/SFinance';
import SPayments from './pages/s/Payments';
import STemplates from './pages/s/Templates';
import SArticles from './pages/s/Articles';
import SAds from './pages/s/Ads';
import SCoupons from './pages/s/Coupons';
import SSystem from './pages/s/System';

// B-side pages
import BDashboard from './pages/b/Dashboard';
import BSites from './pages/b/Sites';
import BPackages from './pages/b/Packages';
import BWhitelist from './pages/b/Whitelist';
import BFinance from './pages/b/Finance';
import BSettings from './pages/b/Settings';

function App() {
  const [role, setRole] = useState<Role | null>(null);

  const handleLogin = (r: Role) => setRole(r);
  const handleLogout = () => setRole(null);
  const handleSwitchRole = () => setRole(role === 's' ? 'b' : 's');

  if (!role) {
    return <Login onLogin={handleLogin} />;
  }

  return (
    <BrowserRouter>
      <Layout role={role} onSwitchRole={handleSwitchRole} onLogout={handleLogout}>
        <Routes>
          {role === 's' ? (
            <>
              <Route path="/s/dashboard" element={<SDashboard />} />
              <Route path="/s/sites" element={<SSites />} />
              <Route path="/s/site-config" element={<SiteConfig />} />
              <Route path="/s/merchants" element={<SMerchants />} />
              <Route path="/s/merchant-audit" element={<SMerchantAudit />} />
              <Route path="/s/invites" element={<SInvites />} />
              <Route path="/s/products" element={<SProducts />} />
              <Route path="/s/categories" element={<SCategories />} />
              <Route path="/s/nodes" element={<SNodes />} />
              <Route path="/s/skus" element={<SSkus />} />
              <Route path="/s/orders" element={<SOrders />} />
              <Route path="/s/complaints" element={<SComplaints />} />
              <Route path="/s/abnormal-orders" element={<SAbnormalOrders />} />
              <Route path="/s/finance" element={<SFinance />} />
              <Route path="/s/payments" element={<SPayments />} />
              <Route path="/s/templates" element={<STemplates />} />
              <Route path="/s/articles" element={<SArticles />} />
              <Route path="/s/ads" element={<SAds />} />
              <Route path="/s/coupons" element={<SCoupons />} />
              <Route path="/s/system" element={<SSystem />} />
              <Route path="*" element={<Navigate to="/s/dashboard" replace />} />
            </>
          ) : (
            <>
              <Route path="/b/dashboard" element={<BDashboard />} />
              <Route path="/b/sites" element={<BSites />} />
              <Route path="/b/add-site" element={<Placeholder title="添加站点" breadcrumb={['站点管理', '添加站点']} />} />
              <Route path="/b/packages" element={<BPackages />} />
              <Route path="/b/my-packages" element={<BPackages />} />
              <Route path="/b/renew" element={<BPackages />} />
              <Route path="/b/whitelist" element={<BWhitelist />} />
              <Route path="/b/finance" element={<BFinance />} />
              <Route path="/b/settings" element={<BSettings />} />
              <Route path="*" element={<Navigate to="/b/dashboard" replace />} />
            </>
          )}
        </Routes>
      </Layout>
    </BrowserRouter>
  );
}

export default App;
