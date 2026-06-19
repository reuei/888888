import axios from 'axios'
import type {
  SiteConfig,
  Slide,
  Product,
  Partner,
  FriendLink,
  Certificate,
  Testimonial,
  ContactForm,
  Admin,
} from '../types/index.js'

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:3001/api'

const api = axios.create({
  baseURL: API_BASE,
  headers: {
    'Content-Type': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('yuyun_admin_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export interface ApiResponse<T = unknown> {
  success: boolean
  data?: T
  error?: string
  message?: string
  [key: string]: unknown
}

export const login = async (username: string, password: string) => {
  const res = await api.post<ApiResponse<{ token: string; admin: Admin }>>('/auth/login', {
    username,
    password,
  })
  return res.data
}

export const getMe = async () => {
  const res = await api.get<ApiResponse<{ admin: Admin }>>('/auth/me')
  return res.data
}

export const changePassword = async (oldPassword: string, newPassword: string) => {
  const res = await api.put<ApiResponse<unknown>>('/auth/password', { oldPassword, newPassword })
  return res.data
}

export const getSiteConfig = async () => {
  const res = await api.get<ApiResponse<{ config: SiteConfig }>>('/site')
  return res.data
}

export const updateSiteConfig = async (config: Partial<SiteConfig>) => {
  const res = await api.put<ApiResponse<{ config: SiteConfig }>>('/site', config)
  return res.data
}

export const getSlides = async () => {
  const res = await api.get<ApiResponse<Slide[]>>('/slides')
  return res.data
}

export const createSlide = async (data: Partial<Slide>) => {
  const res = await api.post<ApiResponse<Slide>>('/slides', data)
  return res.data
}

export const updateSlide = async (id: number, data: Partial<Slide>) => {
  const res = await api.put<ApiResponse<Slide>>(`/slides/${id}`, data)
  return res.data
}

export const deleteSlide = async (id: number) => {
  const res = await api.delete<ApiResponse<unknown>>(`/slides/${id}`)
  return res.data
}

export const getProducts = async () => {
  const res = await api.get<ApiResponse<Product[]>>('/products')
  return res.data
}

export const createProduct = async (data: Partial<Product>) => {
  const res = await api.post<ApiResponse<Product>>('/products', data)
  return res.data
}

export const updateProduct = async (id: number, data: Partial<Product>) => {
  const res = await api.put<ApiResponse<Product>>(`/products/${id}`, data)
  return res.data
}

export const deleteProduct = async (id: number) => {
  const res = await api.delete<ApiResponse<unknown>>(`/products/${id}`)
  return res.data
}

export const getPartners = async () => {
  const res = await api.get<ApiResponse<Partner[]>>('/partners')
  return res.data
}

export const createPartner = async (data: Partial<Partner>) => {
  const res = await api.post<ApiResponse<Partner>>('/partners', data)
  return res.data
}

export const updatePartner = async (id: number, data: Partial<Partner>) => {
  const res = await api.put<ApiResponse<Partner>>(`/partners/${id}`, data)
  return res.data
}

export const deletePartner = async (id: number) => {
  const res = await api.delete<ApiResponse<unknown>>(`/partners/${id}`)
  return res.data
}

export const getLinks = async () => {
  const res = await api.get<ApiResponse<FriendLink[]>>('/links')
  return res.data
}

export const createLink = async (data: Partial<FriendLink>) => {
  const res = await api.post<ApiResponse<FriendLink>>('/links', data)
  return res.data
}

export const updateLink = async (id: number, data: Partial<FriendLink>) => {
  const res = await api.put<ApiResponse<FriendLink>>(`/links/${id}`, data)
  return res.data
}

export const deleteLink = async (id: number) => {
  const res = await api.delete<ApiResponse<unknown>>(`/links/${id}`)
  return res.data
}

export const getCertificates = async () => {
  const res = await api.get<ApiResponse<Certificate[]>>('/certificates')
  return res.data
}

export const createCertificate = async (data: Partial<Certificate>) => {
  const res = await api.post<ApiResponse<Certificate>>('/certificates', data)
  return res.data
}

export const updateCertificate = async (id: number, data: Partial<Certificate>) => {
  const res = await api.put<ApiResponse<Certificate>>(`/certificates/${id}`, data)
  return res.data
}

export const deleteCertificate = async (id: number) => {
  const res = await api.delete<ApiResponse<unknown>>(`/certificates/${id}`)
  return res.data
}

export const getTestimonials = async () => {
  const res = await api.get<ApiResponse<Testimonial[]>>('/testimonials')
  return res.data
}

export const createTestimonial = async (data: Partial<Testimonial>) => {
  const res = await api.post<ApiResponse<Testimonial>>('/testimonials', data)
  return res.data
}

export const updateTestimonial = async (id: number, data: Partial<Testimonial>) => {
  const res = await api.put<ApiResponse<Testimonial>>(`/testimonials/${id}`, data)
  return res.data
}

export const deleteTestimonial = async (id: number) => {
  const res = await api.delete<ApiResponse<unknown>>(`/testimonials/${id}`)
  return res.data
}

export const uploadFile = async (file: File) => {
  const formData = new FormData()
  formData.append('file', file)
  const res = await api.post<ApiResponse<{ url: string }>>('/upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  })
  return res.data
}

export const submitContact = async (data: ContactForm) => {
  const res = await api.post<ApiResponse<unknown>>('/contact', data)
  return res.data
}

export default api
