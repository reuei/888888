import { motion } from 'framer-motion';

interface Props {
  data: { label: string; value: number }[];
  size?: number;
}

export default function RadarChart({ data, size = 280 }: Props) {
  const center = size / 2;
  const radius = size * 0.35;
  const angleStep = (Math.PI * 2) / data.length;

  const getPoint = (index: number, value: number) => {
    const angle = index * angleStep - Math.PI / 2;
    const r = (value / 100) * radius;
    return {
      x: center + r * Math.cos(angle),
      y: center + r * Math.sin(angle),
    };
  };

  const points = data.map((d, i) => getPoint(i, d.value));
  const pathD = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ') + ' Z';

  const gridLevels = [20, 40, 60, 80, 100];

  return (
    <svg width={size} height={size} className="overflow-visible">
      {/* Grid */}
      {gridLevels.map((level) => {
        const levelPoints = data.map((_, i) => getPoint(i, level));
        const d = levelPoints.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ') + ' Z';
        return (
          <path
            key={level}
            d={d}
            fill="none"
            stroke="#e7e5e4"
            strokeWidth={1}
            strokeDasharray={level === 100 ? 'none' : '4 4'}
          />
        );
      })}

      {/* Axes */}
      {data.map((_, i) => {
        const end = getPoint(i, 100);
        return (
          <line
            key={i}
            x1={center}
            y1={center}
            x2={end.x}
            y2={end.y}
            stroke="#e7e5e4"
            strokeWidth={1}
          />
        );
      })}

      {/* Data area */}
      <motion.path
        d={pathD}
        fill="url(#radarGradient)"
        stroke="#4f46e5"
        strokeWidth={2}
        initial={{ pathLength: 0, opacity: 0 }}
        animate={{ pathLength: 1, opacity: 1 }}
        transition={{ duration: 1.5, ease: 'easeOut' }}
      />

      {/* Data points */}
      {points.map((p, i) => (
        <motion.circle
          key={i}
          cx={p.x}
          cy={p.y}
          r={5}
          fill="#4f46e5"
          stroke="white"
          strokeWidth={2}
          initial={{ scale: 0 }}
          animate={{ scale: 1 }}
          transition={{ delay: 0.5 + i * 0.1, type: 'spring' }}
        />
      ))}

      {/* Labels */}
      {data.map((d, i) => {
        const angle = i * angleStep - Math.PI / 2;
        const labelRadius = radius + 28;
        const x = center + labelRadius * Math.cos(angle);
        const y = center + labelRadius * Math.sin(angle);
        return (
          <text
            key={`label-${i}`}
            x={x}
            y={y}
            textAnchor="middle"
            dominantBaseline="middle"
            className="text-xs fill-warm-600 font-medium"
          >
            {d.label}
          </text>
        );
      })}

      {/* Gradient definition */}
      <defs>
        <linearGradient id="radarGradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="#4f46e5" stopOpacity="0.3" />
          <stop offset="100%" stopColor="#84cc16" stopOpacity="0.2" />
        </linearGradient>
      </defs>
    </svg>
  );
}
