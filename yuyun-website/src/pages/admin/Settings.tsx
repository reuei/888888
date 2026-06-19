import { useEffect, useState } from 'react'
import { Upload, X, Save, RotateCcw, Globe } from 'lucide-react'
import { motion } from 'framer-motion'
import { getSiteConfig, updateSiteConfig, uploadFile } from '../../api/index.js'
import type { SiteConfig } from '../../types/index.js'

interface FieldGroup {
  title: string
  fields: Field[]
}

interface Field {
  key: keyof SiteConfig
  label: string
  type: 'text' | 'textarea' | 'image' | 'checkbox'
  required?: boolean
  placeholder?: string
  help?: string
}

const fieldGroups: FieldGroup[] = [
  {
    title: '品牌信息',
    fields: [
      { key: 'siteName', label: '网站名称', type: 'text', required: true },
      { key: 'slogan', label: '网站标语', type: 'text' },
      { key: 'logo', label: '网站 Logo', type: 'image', help: '建议尺寸 180×40，透明背景 PNG 最佳' },
      { key: 'copyright', label: '版权说明', type: 'text', placeholder: '语云科技® 是语云科技美国有限公司在中国的注册授权' },
    ],
  },
  {
    title: '联系信息',
    fields: [
      { key: 'salesPhone', label: '销售电话', type: 'text', required: true, placeholder: '400-800-8451' },
      { key: 'marketingPhone', label: '营销电话', type: 'text', placeholder: '400-800-8541' },
      { key: 'address', label: '公司地址', type: 'textarea' },
      { key: 'email', label: '商务邮箱', type: 'text' },
      { key: 'qqGroup', label: '官方 QQ 群', type: 'text' },
      { key: 'wechatQr', label: '微信群/客服二维码', type: 'image' },
    ],
  },
  {
    title: '备案与资质',
    fields: [
      { key: 'icp', label: 'ICP 备案号', type: 'text', placeholder: '京ICP备XXXXXXXX号' },
      { key: 'publicSecurityRecord', label: '公安网备案号', type: 'text', placeholder: '京公网安备XXXXXXXXXXX号' },
      { key: 'businessLicense', label: '营业执照图片', type: 'image', help: '首页资质展示区域将显示该图片' },
    ],
  },
  {
    title: '首页弹窗',
    fields: [
      { key: 'popupEnabled', label: '启用弹窗', type: 'checkbox' },
      { key: 'popupTitle', label: '弹窗标题', type: 'text' },
      { key: 'popupContent', label: '弹窗内容', type: 'textarea' },
      { key: 'popupButtonText', label: '按钮文字', type: 'text' },
      { key: 'popupButtonLink', label: '按钮链接', type: 'text', placeholder: '/contact' },
    ],
  },
]

export default function Settings() {
  const [config, setConfig] = useState<SiteConfig | null>(null)
  const [form, setForm] = useState<Partial<SiteConfig>>({})
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [uploading, setUploading] = useState<Record<string, boolean>>({})
  const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null)

  const load = async () => {
    setLoading(true)
    const res = await getSiteConfig()
    if (res.success && res.config) {
      setConfig(res.config as SiteConfig)
      setForm(res.config as SiteConfig)
    }
    setLoading(false)
  }

  useEffect(() => {
    load()
  }, [])

  const handleChange = (key: keyof SiteConfig, value: string | number | boolean) => {
    setForm((prev) => ({ ...prev, [key]: value }))
  }

  const handleImageUpload = async (key: keyof SiteConfig, file: File) => {
    setUploading((prev) => ({ ...prev, [key]: true }))
    const res = await uploadFile(file)
    setUploading((prev) => ({ ...prev, [key]: false }))
    if (res.success && res.url) {
      handleChange(key, res.url as string)
      setMessage({ type: 'success', text: '图片上传成功' })
      setTimeout(() => setMessage(null), 2000)
    } else {
      setMessage({ type: 'error', text: '图片上传失败' })
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!form.siteName) {
      setMessage({ type: 'error', text: '网站名称不能为空' })
      return
    }
    setSaving(true)
    const res = await updateSiteConfig(form)
    setSaving(false)
    if (res.success) {
      setConfig((res.config as SiteConfig) || (form as SiteConfig))
      setMessage({ type: 'success', text: '站点配置保存成功' })
    } else {
      setMessage({ type: 'error', text: res.error || '保存失败' })
    }
    setTimeout(() => setMessage(null), 3000)
  }

  const handleReset = () => {
    if (config) {
      setForm(config)
      setMessage({ type: 'success', text: '已恢复为当前保存的配置' })
      setTimeout(() => setMessage(null), 2000)
    }
  }

  const renderField = (field: Field) => {
    const value = form[field.key]

    if (field.type === 'textarea') {
      return (
        <textarea
          value={(value as string) || ''}
          onChange={(e) => handleChange(field.key, e.target.value)}
          rows={4}
          className="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all resize-none"
          placeholder={field.placeholder}
        />
      )
    }

    if (field.type === 'image') {
      return (
        <div className="space-y-3">
          <label className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#F6F9FC] text-sm text-[#0A2540] hover:bg-[#00A4E4]/10 cursor-pointer transition-colors">
            <Upload className="w-4 h-4" />
            {uploading[field.key as string] ? '上传中...' : '上传图片'}
            <input
              type="file"
              accept="image/*"
              className="hidden"
              onChange={(e) => e.target.files?.[0] && handleImageUpload(field.key, e.target.files[0])}
            />
          </label>
          {value && (
            <div className="relative w-40 h-24 rounded-lg border border-gray-200 overflow-hidden bg-white">
              <img src={value as string} alt="" className="w-full h-full object-contain" />
              <button
                type="button"
                onClick={() => handleChange(field.key, '')}
                className="absolute top-1 right-1 p-1 rounded-full bg-red-500 text-white"
              >
                <X className="w-3 h-3" />
              </button>
            </div>
          )}
        </div>
      )
    }

    if (field.type === 'checkbox') {
      return (
        <label className="flex items-center gap-2 cursor-pointer">
          <input
            type="checkbox"
            checked={Boolean(value)}
            onChange={(e) => handleChange(field.key, e.target.checked ? 1 : 0)}
            className="w-4 h-4 rounded border-gray-300 text-[#00A4E4] focus:ring-[#00A4E4]"
          />
          <span className="text-sm text-gray-600">启用该功能</span>
        </label>
      )
    }

    return (
      <input
        type="text"
        value={(value as string) || ''}
        onChange={(e) => handleChange(field.key, e.target.value)}
        className="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all"
        placeholder={field.placeholder}
      />
    )
  }

  if (loading || !config) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="w-8 h-8 border-4 border-[#00A4E4] border-t-transparent rounded-full animate-spin" />
      </div>
    )
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-2xl font-bold text-[#0A2540]">站点配置</h1>
          <p className="text-sm text-gray-500 mt-1">配置网站全局信息，所有更改将立即生效</p>
        </div>
        <div className="flex items-center gap-3">
          <button
            type="button"
            onClick={handleReset}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors text-sm font-medium"
          >
            <RotateCcw className="w-4 h-4" />
            重置
          </button>
          <button
            type="submit"
            form="site-config-form"
            disabled={saving}
            className="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-[#00A4E4] text-white text-sm font-bold hover:bg-[#0093cd] transition-colors disabled:opacity-60"
          >
            <Save className="w-4 h-4" />
            {saving ? '保存中...' : '保存配置'}
          </button>
        </div>
      </div>

      {message && (
        <motion.div
          initial={{ opacity: 0, y: -10 }}
          animate={{ opacity: 1, y: 0 }}
          className={`mb-6 p-4 rounded-xl text-sm ${
            message.type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'
          }`}
        >
          {message.text}
        </motion.div>
      )}

      <form id="site-config-form" onSubmit={handleSubmit} className="space-y-6">
        {fieldGroups.map((group) => (
          <div key={group.title} className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-100 bg-[#F6F9FC]/50">
              <h2 className="font-bold text-[#0A2540]">{group.title}</h2>
            </div>
            <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              {group.fields.map((field) => (
                <div key={field.key as string} className={field.type === 'textarea' || field.type === 'image' ? 'md:col-span-2' : ''}>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    {field.label}
                    {field.required && <span className="text-red-500 ml-1">*</span>}
                  </label>
                  {renderField(field)}
                  {field.help && <p className="mt-1.5 text-xs text-gray-400">{field.help}</p>}
                </div>
              ))}
            </div>
          </div>
        ))}

        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-100 bg-[#F6F9FC]/50">
            <h2 className="font-bold text-[#0A2540]">国际版官网</h2>
          </div>
          <div className="p-6">
            <div className="flex items-center gap-3 p-4 rounded-xl bg-[#F6F9FC]">
              <Globe className="w-5 h-5 text-[#00A4E4]" />
              <div className="flex-1">
                <p className="text-sm font-medium text-[#0A2540]">国际版跳转链接</p>
                <p className="text-sm text-gray-500">https://cloud.loveym.cloud</p>
              </div>
              <span className="text-xs px-2 py-1 rounded bg-gray-200 text-gray-600">固定链接</span>
            </div>
          </div>
        </div>

        <div className="flex items-center justify-end gap-3 pt-2">
          <button
            type="button"
            onClick={handleReset}
            className="px-5 py-2.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors font-medium"
          >
            重置
          </button>
          <button
            type="submit"
            disabled={saving}
            className="px-6 py-2.5 rounded-lg bg-[#00A4E4] text-white font-bold hover:bg-[#0093cd] transition-colors disabled:opacity-60"
          >
            {saving ? '保存中...' : '保存配置'}
          </button>
        </div>
      </form>
    </div>
  )
}
