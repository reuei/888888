import { Shield, Zap, Globe, Server, Lock, Cloud } from 'lucide-react';

export default function Hero3D() {
  return (
    <div className="relative w-full max-w-lg mx-auto aspect-square">
      {/* Background glow */}
      <div className="absolute inset-0 bg-gradient-to-br from-[var(--sales-primary)]/20 via-[var(--sales-accent)]/10 to-transparent rounded-full blur-3xl" />

      {/* Central shield */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 md:w-52 md:h-52">
        <div className="absolute inset-0 bg-gradient-to-br from-[var(--sales-primary)] to-[var(--sales-accent)] rounded-[2rem] rotate-45 shadow-2xl shadow-[var(--sales-primary)]/40" />
        <div className="absolute inset-2 bg-gradient-to-br from-white/20 to-transparent rounded-[1.8rem] rotate-45" />
        <div className="absolute inset-0 flex items-center justify-center text-white drop-shadow-lg">
          <Shield size={64} strokeWidth={1.5} />
        </div>
      </div>

      {/* Orbiting nodes */}
      <OrbitingNode icon={Globe} orbitClass="orbit-1" delay="0s" />
      <OrbitingNode icon={Server} orbitClass="orbit-2" delay="2s" />
      <OrbitingNode icon={Zap} orbitClass="orbit-3" delay="4s" />
      <OrbitingNode icon={Lock} orbitClass="orbit-4" delay="1s" />
      <OrbitingNode icon={Cloud} orbitClass="orbit-5" delay="3s" />

      {/* Decorative rings */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[70%] h-[70%] border border-[var(--sales-primary)]/20 rounded-full" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] h-[90%] border border-dashed border-[var(--sales-accent)]/20 rounded-full" />
    </div>
  );
}

function OrbitingNode({
  icon: Icon,
  orbitClass,
  delay,
}: {
  icon: typeof Globe;
  orbitClass: string;
  delay: string;
}) {
  return (
    <div
      className={`absolute top-1/2 left-1/2 ${orbitClass}`}
      style={{ animationDelay: delay }}
    >
      <div className="w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)] shadow-xl shadow-[var(--sales-primary)]/10 flex items-center justify-center text-[var(--sales-primary)]">
        <Icon size={22} />
      </div>
    </div>
  );
}
