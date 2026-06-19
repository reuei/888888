import { useState, useCallback } from 'react'
import DataManager from '../../components/DataManager.js'
import { getProducts, createProduct, updateProduct, deleteProduct } from '../../api/index.js'
import type { Product } from '../../types/index.js'

const fields = [
  { key: 'name', label: '产品名称', type: 'text' as const, required: true },
  { key: 'category', label: '分类', type: 'text' as const },
  { key: 'description', label: '产品描述', type: 'textarea' as const },
  { key: 'features', label: '产品特性', type: 'array' as const },
  { key: 'icon', label: '图标', type: 'image' as const },
  { key: 'image', label: '产品图', type: 'image' as const },
  { key: 'orderIndex', label: '排序', type: 'number' as const },
  { key: 'enabled', label: '状态', type: 'checkbox' as const },
]

export default function Products() {
  const [data, setData] = useState<Product[]>([])

  const load = useCallback(async () => {
    const res = await getProducts()
    if (res.success) setData(res.data as Product[])
  }, [])

  return (
    <DataManager<Product>
      title="产品管理"
      fields={fields}
      data={data}
      onLoad={load}
      onCreate={async (item) => { await createProduct(item) }}
      onUpdate={async (id, item) => { await updateProduct(id, item) }}
      onDelete={async (id) => { await deleteProduct(id) }}
    />
  )
}
