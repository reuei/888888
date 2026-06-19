import { useEffect, useState } from 'react'
import { Plus, Pencil, Trash2, X, Upload, GripVertical } from 'lucide-react'
import { motion, AnimatePresence } from 'framer-motion'
import { uploadFile } from '../api/index.js'

interface Field {
  key: string
  label: string
  type: 'text' | 'textarea' | 'image' | 'array' | 'number' | 'checkbox'
  required?: boolean
  placeholder?: string
}

interface DataManagerProps<T extends { id: number }> {
  title: string
  fields: Field[]
  data: T[]
  onLoad: () => Promise<void>
  onCreate: (item: Partial<T>) => Promise<void>
  onUpdate: (id: number, item: Partial<T>) => Promise<void>
  onDelete: (id: number) => Promise<void>
}

export default function DataManager<T extends { id: number }>({
  title,
  fields,
  data,
  onLoad,
  onCreate,
  onUpdate,
  onDelete,
}: DataManagerProps<T>) {
  const [isOpen, setIsOpen] = useState(false)
  const [editing, setEditing] = useState<T | null>(null)
  const [form, setForm] = useState<Partial<T>>({})
  const [uploading, setUploading] = useState(false)

  useEffect(() => {
    onLoad()
  }, [onLoad])

  const openCreate = () => {
    setEditing(null)
    setForm({ enabled: true } as unknown as Partial<T>)
    setIsOpen(true)
  }

  const openEdit = (item: T) => {
    setEditing(item)
    setForm({ ...item })
    setIsOpen(true)
  }

  const close = () => {
    setIsOpen(false)
    setEditing(null)
    setForm({})
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    for (const field of fields) {
      if (field.required && !(form as Record<string, unknown>)[field.key] && field.type !== 'checkbox') {
        return
      }
    }
    if (editing) {
      await onUpdate(editing.id, form)
    } else {
      await onCreate(form)
    }
    close()
    await onLoad()
  }

  const handleDelete = async (id: number) => {
    if (!confirm('确定要删除这条记录吗？')) return
    await onDelete(id)
    await onLoad()
  }

  const handleImageUpload = async (fieldKey: string, file: File) => {
    setUploading(true)
    const res = await uploadFile(file)
    setUploading(false)
    if (res.success && res.url) {
      setForm({ ...form, [fieldKey]: res.url })
    }
  }

  const renderField = (field: Field) => {
    const value = (form as Record<string, unknown>)[field.key]

    if (field.type === 'textarea') {
      return (
        <textarea
          value={(value as string) || ''}
          onChange={(e) => setForm({ ...form, [field.key]: e.target.value })}
          rows={4}
          className="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all resize-none"
          placeholder={field.placeholder}
        />
      )
    }

    if (field.type === 'image') {
      return (
        <div className="space-y-3">
          <label className="flex items-center gap-2 px-4 py-2 rounded-lg bg-[#F6F9FC] text-sm text-[#0A2540] hover:bg-[#00A4E4]/10 cursor-pointer transition-colors w-fit">
            <Upload className="w-4 h-4" />
            {uploading ? '上传中...' : '上传图片'}
            <input
              type="file"
              accept="image/*"
              className="hidden"
              onChange={(e) => e.target.files?.[0] && handleImageUpload(field.key, e.target.files[0])}
            />
          </label>
          {value && (
            <div className="relative w-32 h-32 rounded-lg border border-gray-200 overflow-hidden">
              <img src={value as string} alt="" className="w-full h-full object-contain bg-white" />
              <button
                type="button"
                onClick={() => setForm({ ...form, [field.key]: '' })}
                className="absolute top-1 right-1 p-1 rounded-full bg-red-500 text-white"
              >
                <X className="w-3 h-3" />
              </button>
            </div>
          )}
        </div>
      )
    }

    if (field.type === 'array') {
      const items = Array.isArray(value) ? value : []
      return (
        <div className="space-y-2">
          {items.map((item: string, index: number) => (
            <div key={index} className="flex items-center gap-2">
              <GripVertical className="w-4 h-4 text-gray-300" />
              <input
                type="text"
                value={item}
                onChange={(e) => {
                  const newItems = [...items]
                  newItems[index] = e.target.value
                  setForm({ ...form, [field.key]: newItems })
                }}
                className="flex-1 px-3 py-2 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all text-sm"
                placeholder="输入特性"
              />
              <button
                type="button"
                onClick={() => setForm({ ...form, [field.key]: items.filter((_, i) => i !== index) })}
                className="p-2 text-red-500 hover:bg-red-50 rounded-lg"
              >
                <X className="w-4 h-4" />
              </button>
            </div>
          ))}
          <button
            type="button"
            onClick={() => setForm({ ...form, [field.key]: [...items, ''] })}
            className="text-sm text-[#00A4E4] hover:underline"
          >
            + 添加一项
          </button>
        </div>
      )
    }

    if (field.type === 'checkbox') {
      return (
        <label className="flex items-center gap-2 cursor-pointer">
          <input
            type="checkbox"
            checked={Boolean(value)}
            onChange={(e) => setForm({ ...form, [field.key]: e.target.checked ? 1 : 0 })}
            className="w-4 h-4 rounded border-gray-300 text-[#00A4E4] focus:ring-[#00A4E4]"
          />
          <span className="text-sm text-gray-600">启用</span>
        </label>
      )
    }

    return (
      <input
        type={field.type === 'number' ? 'number' : 'text'}
        value={(value as string | number) || ''}
        onChange={(e) => setForm({ ...form, [field.key]: field.type === 'number' ? Number(e.target.value) : e.target.value })}
        className="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all"
        placeholder={field.placeholder}
      />
    )
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-[#0A2540]">{title}</h1>
        <button
          onClick={openCreate}
          className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#00A4E4] text-white text-sm font-bold hover:bg-[#0093cd] transition-colors"
        >
          <Plus className="w-4 h-4" />
          新增
        </button>
      </div>

      <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-[#F6F9FC]">
              <tr>
                {fields.map((field) => (
                  <th key={field.key} className="px-4 py-3 text-left font-semibold text-gray-700">
                    {field.label}
                  </th>
                ))}
                <th className="px-4 py-3 text-left font-semibold text-gray-700">操作</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {data.map((item) => (
                <tr key={item.id} className="hover:bg-gray-50">
                  {fields.map((field) => {
                    const value = (item as Record<string, unknown>)[field.key]
                    return (
                      <td key={field.key} className="px-4 py-3">
                        {field.type === 'image' ? (
                          value ? (
                            <img src={value as string} alt="" className="h-10 w-10 object-contain rounded" />
                          ) : (
                            <span className="text-gray-300">-</span>
                          )
                        ) : field.type === 'checkbox' ? (
                          <span className={value ? 'text-green-600' : 'text-gray-400'}>
                            {value ? '启用' : '禁用'}
                          </span>
                        ) : field.type === 'array' ? (
                          <span className="text-gray-500">{(value as string[]).length} 项</span>
                        ) : (
                          <span className="text-gray-700 line-clamp-2">{String(value || '-')}</span>
                        )}
                      </td>
                    )
                  })}
                  <td className="px-4 py-3">
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => openEdit(item)}
                        className="p-2 text-[#00A4E4] hover:bg-[#00A4E4]/10 rounded-lg transition-colors"
                      >
                        <Pencil className="w-4 h-4" />
                      </button>
                      <button
                        onClick={() => handleDelete(item.id)}
                        className="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {data.length === 0 && (
                <tr>
                  <td colSpan={fields.length + 1} className="px-4 py-12 text-center text-gray-400">
                    暂无数据，点击右上角新增
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      <AnimatePresence>
        {isOpen && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="absolute inset-0 bg-black/50 backdrop-blur-sm"
              onClick={close}
            />
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="relative z-10 w-full max-w-2xl max-h-[90vh] overflow-auto bg-white rounded-2xl shadow-2xl"
              onClick={(e) => e.stopPropagation()}
            >
              <div className="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <h2 className="text-lg font-bold text-[#0A2540]">{editing ? '编辑' : '新增'}{title.replace('管理', '')}</h2>
                <button onClick={close} className="p-2 hover:bg-gray-100 rounded-lg">
                  <X className="w-5 h-5" />
                </button>
              </div>
              <form onSubmit={handleSubmit} className="p-6 space-y-5">
                {fields.map((field) => (
                  <div key={field.key}>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      {field.label}
                      {field.required && <span className="text-red-500 ml-1">*</span>}
                    </label>
                    {renderField(field)}
                  </div>
                ))}
                <div className="pt-4 flex items-center justify-end gap-3">
                  <button
                    type="button"
                    onClick={close}
                    className="px-5 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors"
                  >
                    取消
                  </button>
                  <button
                    type="submit"
                    className="px-5 py-2 rounded-lg bg-[#00A4E4] text-white font-bold hover:bg-[#0093cd] transition-colors"
                  >
                    保存
                  </button>
                </div>
              </form>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  )
}
