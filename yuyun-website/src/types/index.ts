export interface SiteConfig {
  id: number
  logo: string
  siteName: string
  slogan: string
  salesPhone: string
  marketingPhone: string
  address: string
  email: string
  icp: string
  publicSecurityRecord: string
  businessLicense: string
  copyright: string
  qqGroup: string
  wechatQr: string
  popupEnabled: boolean
  popupTitle: string
  popupContent: string
  popupButtonText: string
  popupButtonLink: string
}

export interface Slide {
  id: number
  title: string
  subtitle: string
  buttonText: string
  buttonLink: string
  image: string
  orderIndex: number
  enabled: boolean
}

export interface Product {
  id: number
  name: string
  category: string
  description: string
  features: string[]
  icon: string
  image: string
  orderIndex: number
  enabled: boolean
}

export interface Partner {
  id: number
  name: string
  logo: string
  website: string
  orderIndex: number
  enabled: boolean
}

export interface FriendLink {
  id: number
  name: string
  url: string
  orderIndex: number
  enabled: boolean
}

export interface Certificate {
  id: number
  title: string
  image: string
  orderIndex: number
  enabled: boolean
}

export interface Testimonial {
  id: number
  author: string
  company: string
  content: string
  avatar: string
  orderIndex: number
  enabled: boolean
}

export interface ContactForm {
  name: string
  phone: string
  email: string
  message: string
}

export interface Admin {
  id: number
  username: string
}
