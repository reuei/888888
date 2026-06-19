import { motion } from 'framer-motion'
import type { Product } from '../types/index.js'

interface ServiceCardProps {
  product: Product
  index: number
}

export default function ServiceCard({ product, index }: ServiceCardProps) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.4, delay: index * 0.1 }}
      className="group relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden"
    >
      <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#00A4E4] to-[#0A2540] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left" />
      <div className="h-12 w-12 rounded-xl bg-[#00A4E4]/10 flex items-center justify-center mb-5 group-hover:bg-[#00A4E4] transition-colors duration-300">
        {product.image ? (
          <img src={product.image} alt={product.name} className="h-6 w-6 object-contain" />
        ) : (
          <span className="text-[#00A4E4] group-hover:text-white font-bold text-lg transition-colors">
            {product.name.charAt(0)}
          </span>
        )}
      </div>
      <h3 className="text-lg font-bold text-[#0A2540] mb-3 group-hover:text-[#00A4E4] transition-colors">
        {product.name}
      </h3>
      <p className="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
        {product.description}
      </p>
      {product.features && product.features.length > 0 && (
        <ul className="space-y-2">
          {product.features.slice(0, 3).map((feature, i) => (
            <li key={i} className="flex items-center gap-2 text-xs text-gray-500">
              <span className="w-1.5 h-1.5 rounded-full bg-[#00A4E4]" />
              {feature}
            </li>
          ))}
        </ul>
      )}
    </motion.div>
  )
}
