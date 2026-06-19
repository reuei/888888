import type { Partner } from '../types/index.js'

interface PartnerMarqueeProps {
  partners: Partner[]
}

export default function PartnerMarquee({ partners }: PartnerMarqueeProps) {
  const enabledPartners = partners.filter((p) => p.enabled)
  if (enabledPartners.length === 0) return null

  const doubled = [...enabledPartners, ...enabledPartners]

  return (
    <section className="py-16 bg-[#F6F9FC] overflow-hidden">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
        <div className="text-center">
          <h2 className="text-2xl md:text-3xl font-bold text-[#0A2540] mb-3">我们与以下企业/组织携手共进</h2>
          <p className="text-gray-500 text-sm">携手全球领先企业，共建数字化未来</p>
        </div>
      </div>

      <div className="relative">
        <div className="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-[#F6F9FC] to-transparent z-10" />
        <div className="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-[#F6F9FC] to-transparent z-10" />

        <div className="flex animate-marquee">
          {doubled.map((partner, index) => (
            <div
              key={`${partner.id}-${index}`}
              className="flex-shrink-0 mx-8 group"
            >
              <div className="h-16 px-8 rounded-xl bg-white border border-gray-100 flex items-center justify-center gap-3 grayscale hover:grayscale-0 transition-all duration-300 hover:shadow-md">
                {partner.logo ? (
                  <img
                    src={partner.logo}
                    alt={partner.name}
                    className="h-8 w-auto object-contain max-w-[120px]"
                  />
                ) : (
                  <span className="text-[#0A2540] font-bold text-sm">{partner.name}</span>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
