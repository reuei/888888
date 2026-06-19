interface Props {
  data: { date: string; minutes: number }[];
}

export default function HeatmapCalendar({ data }: Props) {
  // Generate last 84 days (12 weeks)
  const days: { date: string; minutes: number }[] = [];
  const today = new Date();
  for (let i = 83; i >= 0; i--) {
    const d = new Date(today);
    d.setDate(d.getDate() - i);
    const dateStr = d.toISOString().split('T')[0];
    const entry = data.find((item) => item.date === dateStr);
    days.push({ date: dateStr, minutes: entry?.minutes || 0 });
  }

  const getColor = (minutes: number) => {
    if (minutes === 0) return 'bg-warm-100';
    if (minutes < 30) return 'bg-accent-200';
    if (minutes < 60) return 'bg-accent-300';
    if (minutes < 90) return 'bg-accent-400';
    return 'bg-accent-500';
  };

  const weeks: typeof days[] = [];
  for (let i = 0; i < days.length; i += 7) {
    weeks.push(days.slice(i, i + 7));
  }

  const weekDays = ['日', '一', '二', '三', '四', '五', '六'];

  return (
    <div className="overflow-x-auto">
      <div className="inline-flex gap-1">
        {/* Week day labels */}
        <div className="flex flex-col gap-1 mr-2">
          {weekDays.map((d) => (
            <div key={d} className="w-6 h-6 flex items-center justify-center text-xs text-warm-400">
              {d}
            </div>
          ))}
        </div>

        {/* Heatmap grid - transpose so weeks are columns */}
        <div className="flex gap-1">
          {weeks.map((week, weekIdx) => (
            <div key={weekIdx} className="flex flex-col gap-1">
              {week.map((day, dayIdx) => (
                <div
                  key={dayIdx}
                  className={`w-6 h-6 rounded-sm ${getColor(day.minutes)} transition-colors hover:ring-2 hover:ring-primary-300`}
                  title={`${day.date}: ${day.minutes} 分钟`}
                />
              ))}
            </div>
          ))}
        </div>
      </div>

      {/* Legend */}
      <div className="flex items-center gap-2 mt-4 text-xs text-warm-500">
        <span>少</span>
        <div className="flex gap-1">
          <div className="w-4 h-4 rounded-sm bg-warm-100" />
          <div className="w-4 h-4 rounded-sm bg-accent-200" />
          <div className="w-4 h-4 rounded-sm bg-accent-300" />
          <div className="w-4 h-4 rounded-sm bg-accent-400" />
          <div className="w-4 h-4 rounded-sm bg-accent-500" />
        </div>
        <span>多</span>
      </div>
    </div>
  );
}
