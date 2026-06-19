import { useState, useCallback } from 'react'
import DataManager from '../../components/DataManager.js'
import { getCertificates, createCertificate, updateCertificate, deleteCertificate } from '../../api/index.js'
import type { Certificate } from '../../types/index.js'

const fields = [
  { key: 'title', label: '证书名称', type: 'text' as const, required: true },
  { key: 'image', label: '证书图片', type: 'image' as const },
  { key: 'orderIndex', label: '排序', type: 'number' as const },
  { key: 'enabled', label: '状态', type: 'checkbox' as const },
]

export default function Certificates() {
  const [data, setData] = useState<Certificate[]>([])

  const load = useCallback(async () => {
    const res = await getCertificates()
    if (res.success) setData(res.data as Certificate[])
  }, [])

  return (
    <DataManager<Certificate>
      title="资质证书管理"
      fields={fields}
      data={data}
      onLoad={load}
      onCreate={async (item) => { await createCertificate(item) }}
      onUpdate={async (id, item) => { await updateCertificate(id, item) }}
      onDelete={async (id) => { await deleteCertificate(id) }}
    />
  )
}
