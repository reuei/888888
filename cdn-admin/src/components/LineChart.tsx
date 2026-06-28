interface LineChartProps {
  labels: string[];
  datasets: { label: string; values: number[]; color: string }[];
  height?: number;
}

export default function LineChart({ labels, datasets, height = 240 }: LineChartProps) {
  const all = datasets.flatMap((d) => d.values);
  const max = Math.max(...all, 1);
  const min = Math.min(...all, 0);
  const range = max - min || 1;
  const width = 100;
  const pad = 5;

  const pointX = (i: number) => pad + (i / (labels.length - 1)) * (width - pad * 2);
  const pointY = (v: number) => height - pad - ((v - min) / range) * (height - pad * 2);

  return (
    <div className="w-full overflow-x-auto">
      <svg viewBox={`0 0 ${width} ${height}`} preserveAspectRatio="none" className="w-full" style={{ height }}>
        {/* Grid lines */}
        {[0, 0.25, 0.5, 0.75, 1].map((r, i) => (
          <line
            key={i}
            x1={pad}
            y1={pad + r * (height - pad * 2)}
            x2={width - pad}
            y2={pad + r * (height - pad * 2)}
            stroke="#E5E7EB"
            strokeWidth="0.3"
          />
        ))}

        {datasets.map((ds, di) => {
          const points = ds.values.map((v, i) => `${pointX(i)},${pointY(v)}`).join(' ');
          return (
            <g key={di}>
              <polyline
                fill="none"
                stroke={ds.color}
                strokeWidth="1.2"
                points={points}
              />
              {ds.values.map((v, i) => (
                <circle key={i} cx={pointX(i)} cy={pointY(v)} r="1.5" fill={ds.color} />
              ))}
            </g>
          );
        })}

        {/* X labels */}
        {labels.map((l, i) => (
          <text key={i} x={pointX(i)} y={height - 1} fontSize="3" textAnchor="middle" fill="#6B7280">
            {l}
          </text>
        ))}
      </svg>

      <div className="flex flex-wrap gap-4 justify-center mt-3 text-xs">
        {datasets.map((ds, i) => (
          <div key={i} className="flex items-center gap-1.5">
            <span className="w-3 h-0.5" style={{ backgroundColor: ds.color }}></span>
            <span className="text-text-secondary">{ds.label}</span>
          </div>
        ))}
      </div>
    </div>
  );
}
