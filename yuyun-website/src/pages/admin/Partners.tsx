import { useState, useCallback } from 'react'
import DataManager from '../../components/DataManager.js'
import { getPartners, createPartner, updatePartner, deletePartner } from '../../api/index.js'
import type { Partner } from '../../types/index.js'

const fields = [
  { key: 'name', label: '合作伙伴名称', type: 'text' as const, required: true },
  { key: 'logo', label: 'Logo', type: 'image' as const },
  { key: 'website', label: '官网链接', type: 'text' as const },
  { key: 'orderIndex', label: '排序', type: 'number' as const },
  { key: 'enabled', label: '状态', type: 'checkbox' as const },
]

export default function Partners() {
  const [data, setData] = useState<Partner[]>([])

  const load = useCallback(async () => {
    const res = await getPartners()
    if (res.success) setData(res.data as Partner[])
  }, [])

  return (
    <DataManager<Partner>
      title="合作伙伴管理"
      fields={fields}
      data={data}
      onLoad={load}
      onCreate={async (item) => { await createPartner(item) }}
      onUpdate={async (id, item) => { await updatePartner(id, item) }}
      onDelete={async (id) => { await deletePartner(id) }}
    />
  )
}
