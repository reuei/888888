import { useNavigate } from 'react-router-dom';
import { Home, ArrowLeft } from 'lucide-react';

export default function NotFound() {
  const navigate = useNavigate();

  return (
    <div className="h-full flex items-center justify-center p-4">
      <div className="card p-10 w-full max-w-md text-center">
        <div className="text-6xl font-bold text-primary mb-4">404</div>
        <h1 className="text-xl font-bold mb-2">页面不存在</h1>
        <p className="text-sm text-text-secondary mb-6">
          您访问的页面已被移除、更名或暂时不可用。
        </p>
        <div className="flex justify-center gap-3">
          <button onClick={() => navigate(-1)} className="btn btn-default flex items-center gap-1">
            <ArrowLeft size={16} /> 返回上一页
          </button>
          <button onClick={() => navigate('/')} className="btn btn-primary flex items-center gap-1">
            <Home size={16} /> 回到首页
          </button>
        </div>
      </div>
    </div>
  );
}
