import { create } from 'zustand'
import type { SiteConfig } from '../types/index.js'
import { getSiteConfig } from '../api/index.js'

interface SiteState {
  config: SiteConfig | null
  loading: boolean
  fetchConfig: () => Promise<void>
}

const defaultConfig: SiteConfig = {
  id: 1,
  logo: '',
  siteName: '语云科技',
  slogan: '全球领先的云服务与数字化解决方案提供商',
  salesPhone: '400-800-8451',
  marketingPhone: '400-800-8541',
  address: '中国北京市朝阳区建国路88号SOHO现代城A座1208室',
  email: 'contact@yuyun.com',
  icp: '京ICP备XXXXXXXX号',
  publicSecurityRecord: '京公网安备XXXXXXXXXXX号',
  businessLicense: '',
  copyright: '语云科技® 是语云科技美国有限公司在中国的注册授权',
  qqGroup: '123456789',
  wechatQr: '',
  popupEnabled: true,
  popupTitle: '欢迎访问语云科技',
  popupContent: '我们提供全球领先的云服务与解决方案，立即咨询获取专属优惠。',
  popupButtonText: '立即咨询',
  popupButtonLink: '/contact',
}

export const useSiteStore = create<SiteState>((set) => ({
  config: null,
  loading: false,
  fetchConfig: async () => {
    set({ loading: true })
    try {
      const res = await getSiteConfig()
      if (res.success && res.config) {
        set({ config: res.config as SiteConfig })
      } else {
        set({ config: defaultConfig })
      }
    } catch {
      set({ config: defaultConfig })
    } finally {
      set({ loading: false })
    }
  },
}))
