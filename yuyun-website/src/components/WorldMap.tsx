import { useState } from 'react'
import { motion } from 'framer-motion'

interface MapNode {
  id: string
  name: string
  x: number
  y: number
  region: string
}

const nodes: MapNode[] = [
  { id: 'beijing', name: '北京', x: 77, y: 35, region: '中国' },
  { id: 'qingdao', name: '青岛', x: 79, y: 38, region: '中国' },
  { id: 'seoul', name: '首尔', x: 82, y: 37, region: '韩国' },
  { id: 'singapore', name: '新加坡', x: 76, y: 58, region: '东南亚' },
  { id: 'dubai', name: '迪拜', x: 56, y: 45, region: '中东' },
  { id: 'moscow', name: '莫斯科', x: 60, y: 28, region: '俄罗斯' },
  { id: 'stpetersburg', name: '圣彼得堡', x: 57, y: 25, region: '俄罗斯' },
  { id: 'london', name: '伦敦', x: 45, y: 32, region: '欧洲' },
  { id: 'frankfurt', name: '法兰克福', x: 48, y: 34, region: '欧洲' },
  { id: 'sydney', name: '悉尼', x: 88, y: 72, region: '澳大利亚' },
  { id: 'newyork', name: '纽约', x: 25, y: 37, region: '美国' },
  { id: 'washington', name: '华盛顿', x: 24, y: 39, region: '美国' },
  { id: 'sanfrancisco', name: '旧金山', x: 15, y: 40, region: '美国' },
]

export default function WorldMap() {
  const [activeNode, setActiveNode] = useState<MapNode | null>(null)

  return (
    <section className="py-20 gradient-dark relative overflow-hidden">
      <div className="absolute inset-0 bg-grid-pattern opacity-20" />
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="text-center mb-14">
          <h2 className="text-2xl md:text-3xl font-bold text-white mb-3">公司分布</h2>
          <p className="text-gray-400 text-sm max-w-2xl mx-auto">
            语云科技业务覆盖中东、欧洲、亚太、北美及澳洲等地区，为全球客户提供本地化服务支持
          </p>
        </div>

        <div className="relative aspect-[2/1] w-full max-w-5xl mx-auto">
          <svg viewBox="0 0 100 60" className="w-full h-full" preserveAspectRatio="xMidYMid meet">
            <defs>
              <radialGradient id="glow" cx="50%" cy="50%" r="50%">
                <stop offset="0%" stopColor="#00A4E4" stopOpacity="0.3" />
                <stop offset="100%" stopColor="#00A4E4" stopOpacity="0" />
              </radialGradient>
            </defs>

            <path
              d="M18,15 Q22,12 26,15 T34,16 T42,14 T50,16 T58,15 T66,17 T74,15 T82,17 T90,15"
              fill="none"
              stroke="#00A4E4"
              strokeWidth="0.15"
              strokeDasharray="1,1"
              opacity="0.4"
            />

            {[
              'M20,18 Q25,16 30,18 Q35,20 40,18 Q45,16 50,18',
              'M55,18 Q60,16 65,18 Q70,20 75,18 Q80,16 85,18',
              'M22,42 Q28,40 34,42 Q40,44 46,42',
              'M55,42 Q62,40 70,42 Q78,44 86,42',
            ].map((d, i) => (
              <path
                key={i}
                d={d}
                fill="none"
                stroke="#1a3a5c"
                strokeWidth="0.2"
              />
            ))}

            <ellipse cx="50" cy="30" rx="45" ry="25" fill="url(#glow)" />

            {nodes.map((node) => (
              <g key={node.id} className="cursor-pointer" onClick={() => setActiveNode(node)}>
                <circle
                  cx={node.x}
                  cy={node.y}
                  r="1.8"
                  fill="#00A4E4"
                  className="map-node"
                />
                <circle cx={node.x} cy={node.y} r="0.8" fill="#ffffff" />
                <text
                  x={node.x}
                  y={node.y - 3}
                  textAnchor="middle"
                  fill="white"
                  fontSize="1.8"
                  fontWeight="500"
                  className="pointer-events-none"
                >
                  {node.name}
                </text>
              </g>
            ))}
          </svg>

          {activeNode && (
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              className="absolute bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-72 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-5 text-white"
            >
              <div className="flex items-center justify-between mb-2">
                <h4 className="font-bold text-lg">{activeNode.name}</h4>
                <button
                  onClick={() => setActiveNode(null)}
                  className="text-white/70 hover:text-white text-sm"
                >
                  关闭
                </button>
              </div>
              <p className="text-sm text-gray-300">所属区域：{activeNode.region}</p>
              <p className="text-xs text-gray-400 mt-2">语云科技在该地区提供全天候技术支持与本地化服务</p>
            </motion.div>
          )}
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-12">
          {['中国北京', '中国青岛', '俄罗斯莫斯科', '俄罗斯圣彼得堡', '韩国首尔', '新加坡', '中东迪拜', '欧洲伦敦', '欧洲法兰克福', '澳大利亚悉尼', '美国纽约', '美国旧金山'].map((city) => (
            <div
              key={city}
              className="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-sm text-gray-300"
            >
              <span className="w-2 h-2 rounded-full bg-[#00A4E4] map-node" />
              {city}
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
