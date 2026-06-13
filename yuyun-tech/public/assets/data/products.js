window.PRODUCT_DATA = {
  categories: [
    {
      id: "cloud-server",
      name: "云服务器 CVM",
      icon: "💻",
      items: [
        {
          title: "通用型云服务器",
          price: "¥68",
          unit: "/月起",
          features: ["2核4G 基础配置", "NVMe SSD 80GB", "独立公网IP", "免费基础防护"],
          desc: "适合中小型网站、Web应用、开发测试等场景",
          tag: "热销"
        },
        {
          title: "计算型云服务器",
          price: "¥128",
          unit: "/月起",
          features: ["4核8G 高性能", "Intel Xeon 处理器", "独享计算资源", "支持弹性扩容"],
          desc: "适合高计算量业务、大数据分析、视频编码等",
          tag: "推荐"
        },
        {
          title: "GPU 云服务器",
          price: "¥688",
          unit: "/月起",
          features: ["NVIDIA T4 GPU", "16GB 显存", "支持深度学习", "弹性计费"],
          desc: "AI训练、科学计算、图形渲染等GPU加速场景",
          tag: "AI"
        }
      ]
    },
    {
      id: "cloud-db",
      name: "云数据库",
      icon: "🗄️",
      items: [
        {
          title: "云数据库 MySQL",
          price: "¥98",
          unit: "/月起",
          features: ["双机热备", "自动备份", "只读实例", "性能监控"],
          desc: "高可用MySQL数据库服务，支持一主一从架构",
          tag: "热门"
        },
        {
          title: "云数据库 PostgreSQL",
          price: "¥128",
          unit: "/月起",
          features: ["PostgreSQL 14/15", "GIS 支持", "JSON 原生支持", "高可用架构"],
          desc: "企业级开源关系型数据库，适合复杂业务场景",
          tag: ""
        },
        {
          title: "Redis 缓存",
          price: "¥58",
          unit: "/月起",
          features: ["主从架构", "集群版", "数据持久化", "毫秒级响应"],
          desc: "高性能内存数据库，适合缓存、会话、实时排行榜",
          tag: ""
        }
      ]
    },
    {
      id: "cdn",
      name: "CDN 加速",
      icon: "⚡",
      items: [
        {
          title: "全站加速 CDN",
          price: "¥0.15",
          unit: "/GB",
          features: ["2800+ 边缘节点", "动态内容加速", "HTTPS 免费", "智能调度"],
          desc: "动静分离智能加速，提升全球用户访问体验",
          tag: "推荐"
        },
        {
          title: "视频直播 CDN",
          price: "¥0.8",
          unit: "/GB",
          features: ["RTMP/HLS 协议", "超低延迟", "实时转码", "防盗链"],
          desc: "稳定高效的视频直播分发，支持万人并发",
          tag: ""
        },
        {
          title: "下载加速",
          price: "¥0.12",
          unit: "/GB",
          features: ["大文件分片", "断点续传", "多线程下载", "全球节点"],
          desc: "软件包、游戏客户端、补丁文件高速分发",
          tag: ""
        }
      ]
    },
    {
      id: "security",
      name: "安全防护",
      icon: "🛡️",
      items: [
        {
          title: "Web应用防火墙 WAF",
          price: "¥198",
          unit: "/月起",
          features: ["SQL 注入防护", "XSS 防御", "CC 攻击防护", "规则自定义"],
          desc: "智能识别Web攻击，保护您的网站与API安全",
          tag: "安全"
        },
        {
          title: "DDoS 高防",
          price: "¥2888",
          unit: "/月起",
          features: ["T 级防护带宽", "智能清洗", "秒级响应", "7×24 监控"],
          desc: "抗DDoS攻击解决方案，保障业务连续性",
          tag: ""
        },
        {
          title: "SSL 证书",
          price: "¥0",
          unit: "/年起",
          features: ["DV/OV/EV 证书", "浏览器信任", "HTTPS 加密", "自动续期"],
          desc: "支持免费DV证书及企业级OV/EV证书",
          tag: "免费"
        }
      ]
    }
  ]
};
