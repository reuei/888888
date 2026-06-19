import { useEffect, useState } from 'react'
import { motion } from 'framer-motion'
import { getProducts } from '../api/index.js'
import type { Product } from '../types/index.js'
import ServiceCard from '../components/ServiceCard.js'

export default function Products() {
  const [products, setProducts] = useState<Product[]>([])
  const [categories, setCategories] = useState<string[]>([])
  const [activeCategory, setActiveCategory] = useState('全部')

  useEffect(() => {
    getProducts().then((res) => {
      if (res.success) {
        const enabled = (res.data as Product[]).filter((p) => p.enabled)
        setProducts(enabled)
        const cats = Array.from(new Set(enabled.map((p) => p.category).filter(Boolean)))
        setCategories(['全部', ...cats])
      }
    })
  }, [])

  const filtered = activeCategory === '全部'
    ? products
    : products.filter((p) => p.category === activeCategory)

  return (
    <main className="pt-24 pb-20">
      <section className="relative py-20 gradient-dark overflow-hidden">
        <div className="absolute inset-0 bg-grid-pattern opacity-20" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="text-center text-white"
          >
            <h1 className="text-3xl md:text-5xl font-black mb-4">产品介绍</h1>
            <p className="text-gray-300 max-w-2xl mx-auto">多元化的云服务与数字化产品，满足不同规模企业的业务需求</p>
          </motion.div>
        </div>
      </section>

      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-wrap items-center justify-center gap-3 mb-12">
            {categories.map((cat) => (
              <button
                key={cat}
                onClick={() => setActiveCategory(cat)}
                className={`px-5 py-2 rounded-full text-sm font-medium transition-all ${
                  activeCategory === cat
                    ? 'bg-[#00A4E4] text-white shadow-md'
                    : 'bg-[#F6F9FC] text-gray-600 hover:bg-[#00A4E4]/10 hover:text-[#00A4E4]'
                }`}
              >
                {cat}
              </button>
            ))}
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {filtered.map((product, index) => (
              <ServiceCard key={product.id} product={product} index={index} />
            ))}
          </div>

          {filtered.length === 0 && (
            <div className="text-center py-20 text-gray-400">
              <p>该分类下暂无产品</p>
            </div>
          )}
        </div>
      </section>
    </main>
  )
}
