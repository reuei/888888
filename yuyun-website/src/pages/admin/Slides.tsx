import { useState, useCallback } from 'react'
import DataManager from '../../components/DataManager.js'
import { getSlides, createSlide, updateSlide, deleteSlide } from '../../api/index.js'
import type { Slide } from '../../types/index.js'

const fields = [
  { key: 'title', label: '标题', type: 'text' as const, required: true },
  { key: 'subtitle', label: '副标题', type: 'textarea' as const },
  { key: 'buttonText', label: '按钮文字', type: 'text' as const },
  { key: 'buttonLink', label: '按钮链接', type: 'text' as const },
  { key: 'image', label: '背景图', type: 'image' as const },
  { key: 'orderIndex', label: '排序', type: 'number' as const },
  { key: 'enabled', label: '状态', type: 'checkbox' as const },
]

export default function Slides() {
  const [data, setData] = useState<Slide[]>([])

  const load = useCallback(async () => {
    const res = await getSlides()
    if (res.success) setData(res.data as Slide[])
  }, [])

  return (
    <DataManager<Slide>
      title="轮播图管理"
      fields={fields}
      data={data}
      onLoad={load}
      onCreate={async (item) => {
        await createSlide(item)
      }}
      onUpdate={async (id, item) => {
        await updateSlide(id, item)
      }}
      onDelete={async (id) => {
        await deleteSlide(id)
      }}
    />
  )
}
