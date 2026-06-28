import { useState } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import type { Role } from './types';
import Layout from './components/Layout';
import Login from './pages/Login';
import Placeholder from './pages/Placeholder';

// S-side pages
import SDashboard from './pages/s/Dashboard';
import SSites from './pages/s/Sites';
import SMerchants from './pages/s/Merchants';
import SProducts from './pages/s/Products';
import SOrders from './pages/s/Orders';

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
              <Route path="/s/site-config" element={<Placeholder title="站点配置" breadcrumb={['站点管理', '站点配置']} />} />
              <Route path="/s/merchants" element={<SMerchants />} />
              <Route path="/s/merchant-audit" element={<Placeholder title="商户审核" breadcrumb={['商户管理', '商户审核']} />} />
              <Route path="/s/invites" element={<Placeholder title="邀请码管理" breadcrumb={['商户管理', '邀请码管理']} />} />
              <Route path="/s/products" element={<SProducts />} />
              <Route path="/s/categories" element={<Placeholder title="产品分类" breadcrumb={['商品管理', '产品分类']} />} />
              <Route path="/s/nodes" element={<Placeholder title="CDN节点管理" breadcrumb={['商品管理', 'CDN节点管理']} />} />
              <Route path="/s/skus" element={<Placeholder title="套餐规格管理" breadcrumb={['商品管理', '套餐规格管理']} />} />
              <Route path="/s/orders" element={<SOrders />} />
              <Route path="/s/complaints" element={<Placeholder title="投诉管理" breadcrumb={['订单管理', '投诉管理']} />} />
              <Route path="/s/abnormal-orders" element={<Placeholder title="异常订单处理" breadcrumb={['订单管理', '异常订单处理']} />} />
              <Route path="/s/finance" element={<Placeholder title="财务管理" breadcrumb={['财务管理', '资金流水总览']} />} />
              <Route path="/s/payments" element={<Placeholder title="支付网关管理" breadcrumb={['支付网关管理', '渠道对接']} />} />
              <Route path="/s/templates" element={<Placeholder title="模板与前端管理" breadcrumb={['模板与前端管理', '首页模板']} />} />
              <Route path="/s/articles" element={<Placeholder title="文章/公告管理" breadcrumb={['文章/公告管理', '平台公告']} />} />
              <Route path="/s/ads" element={<Placeholder title="广告位管理" breadcrumb={['广告位管理', '广告位列表']} />} />
              <Route path="/s/coupons" element={<Placeholder title="优惠券/营销管理" breadcrumb={['优惠券/营销管理', '优惠券生成']} />} />
              <Route path="/s/system" element={<Placeholder title="系统设置" breadcrumb={['系统设置', '邮件系统']} />} />
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
