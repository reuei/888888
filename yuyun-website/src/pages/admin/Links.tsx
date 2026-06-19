import { useState, useCallback } from 'react'
import DataManager from '../../components/DataManager.js'
import { getLinks, createLink, updateLink, deleteLink } from '../../api/index.js'
import type { FriendLink } from '../../types/index.js'

const fields = [
  { key: 'name', label: '链接名称', type: 'text' as const, required: true },
  { key: 'url', label: '链接地址', type: 'text' as const, required: true },
  { key: 'orderIndex', label: '排序', type: 'number' as const },
  { key: 'enabled', label: '状态', type: 'checkbox' as const },
]

export default function Links() {
  const [data, setData] = useState<FriendLink[]>([])

  const load = useCallback(async () => {
    const res = await getLinks()
    if (res.success) setData(res.data as FriendLink[])
  }, [])

  return (
    <DataManager<FriendLink>
      title="友情链接管理"
      fields={fields}
      data={data}
      onLoad={load}
      onCreate={async (item) => { await createLink(item) }}
      onUpdate={async (id, item) => { await updateLink(id, item) }}
      onDelete={async (id) => { await deleteLink(id) }}
    />
  )
}
