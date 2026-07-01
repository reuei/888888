import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import { fetchApiDocs } from '../../services/api';
import { Copy, Check } from 'lucide-react';
import type { ApiDoc } from '../../types';

const methodClass: Record<ApiDoc['method'], string> = {
  GET: 'badge bg-primary/10 text-primary',
  POST: 'badge badge-success',
  PUT: 'badge badge-warning',
  DELETE: 'badge badge-danger',
};

export default function ApiDocs() {
  const [apiDocs, setApiDocs] = useState<ApiDoc[]>([]);
  const [loading, setLoading] = useState(false);
  const [copiedId, setCopiedId] = useState<string | null>(null);

  const loadApiDocs = useCallback(async () => {
    setLoading(true);
    try {
      const data = await fetchApiDocs();
      setApiDocs(data);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadApiDocs();
  }, [loadApiDocs]);

  const grouped = useMemo(() => {
    return apiDocs.reduce<Record<string, ApiDoc[]>>((acc, doc) => {
      if (!acc[doc.group]) acc[doc.group] = [];
      acc[doc.group].push(doc);
      return acc;
    }, {});
  }, [apiDocs]);

  const handleCopy = (doc: ApiDoc) => {
    // eslint-disable-next-line no-console
    console.log('Copied path:', doc.path);
    setCopiedId(doc.id);
    setTimeout(() => setCopiedId(null), 1500);
  };

  return (
    <div>
      <PageHeader title="API 文档" breadcrumb={['系统运维', 'API 文档']} />

      <div className="space-y-6">
        {loading && <div className="text-sm text-text-secondary">加载中...</div>}

        {!loading && Object.entries(grouped).map(([group, docs]) => (
          <div key={group} className="card p-5">
            <h3 className="text-base font-semibold mb-4">{group}</h3>
            <table className="table">
              <thead>
                <tr>
                  <th className="w-24">方法</th>
                  <th>接口路径</th>
                  <th>接口名称</th>
                  <th>说明</th>
                  <th className="w-24">操作</th>
                </tr>
              </thead>
              <tbody>
                {docs.map((doc) => (
                  <tr key={doc.id}>
                    <td>
                      <span className={methodClass[doc.method]}>{doc.method}</span>
                    </td>
                    <td className="font-mono text-text-secondary">{doc.path}</td>
                    <td className="font-medium">{doc.name}</td>
                    <td className="text-text-secondary">{doc.desc}</td>
                    <td>
                      <button
                        onClick={() => handleCopy(doc)}
                        className="inline-flex items-center gap-1 text-xs text-primary hover:underline"
                      >
                        {copiedId === doc.id ? (
                          <>
                            <Check size={14} /> 已复制
                          </>
                        ) : (
                          <>
                            <Copy size={14} /> 复制
                          </>
                        )}
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ))}
      </div>
    </div>
  );
}
