import { useState, useCallback } from 'react'
import DataManager from '../../components/DataManager.js'
import { getTestimonials, createTestimonial, updateTestimonial, deleteTestimonial } from '../../api/index.js'
import type { Testimonial } from '../../types/index.js'

const fields = [
  { key: 'author', label: '评价人', type: 'text' as const, required: true },
  { key: 'company', label: '所属公司', type: 'text' as const },
  { key: 'content', label: '评价内容', type: 'textarea' as const, required: true },
  { key: 'avatar', label: '头像', type: 'image' as const },
  { key: 'orderIndex', label: '排序', type: 'number' as const },
  { key: 'enabled', label: '状态', type: 'checkbox' as const },
]

export default function Testimonials() {
  const [data, setData] = useState<Testimonial[]>([])

  const load = useCallback(async () => {
    const res = await getTestimonials()
    if (res.success) setData(res.data as Testimonial[])
  }, [])

  return (
    <DataManager<Testimonial>
      title="用户评价管理"
      fields={fields}
      data={data}
      onLoad={load}
      onCreate={async (item) => { await createTestimonial(item) }}
      onUpdate={async (id, item) => { await updateTestimonial(id, item) }}
      onDelete={async (id) => { await deleteTestimonial(id) }}
    />
  )
}
