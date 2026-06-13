/**
 * 语云科技官网 - 全球分布地图
 * Map Component with Tencent/Placeholder Map Support
 */

(function() {
  'use strict';

  class GlobalMap {
    constructor(containerId = 'globalMap') {
      this.container = document.getElementById(containerId);
      if (!this.container) return;

      this.markers = [];
      this.infoWindow = null;
      this.mapInstance = null;

      this.init();
    }

    async init() {
      const data = await window.YuyunAPI?.apiFetch('/home');
      const nodes = data?.globalNodes || [];

      if (nodes.length === 0) {
        this.renderPlaceholder();
        return;
      }

      // 尝试加载腾讯地图API
      this.tryLoadTencentMap(nodes);
    }

    tryLoadTencentMap(nodes) {
      // 检查是否已加载腾讯地图SDK
      if (window.TMap) {
        this.initTencentMap(nodes);
        return;
      }

      // 创建占位符界面（当腾讯地图API Key未配置时）
      this.renderInteractiveMap(nodes);
    }

    /**
     * 腾讯地图初始化（需要配置API Key）
     */
    initTencentMap(nodes) {
      const center = nodes.find(n => n.city === '北京') || nodes[0];

      this.mapInstance = new TMap.Map(this.container, {
        center: new TMap.LatLng(center.lat, center.lng),
        zoom: 2,
        mapStyleId: 'style1', // 样式ID
        baseMap: {
          type: 'vector'
        }
      });

      // 创建标记点
      nodes.forEach(node => {
        const marker = new TMap.MultiMarker({
          map: this.mapInstance,
          styles: {
            marker: new TMap.MarkerStyle({
              width: 24,
              height: 32,
              anchor: { x: 12, y: 32 },
              color: '#FF6B00'
            })
          },
          geometries: [{
            id: node.id,
            position: new TMap.LatLng(node.lat, node.lng),
            properties: { ...node }
          }]
        });

        // 点击事件
        marker.on('click', (evt) => {
          const nodeData = evt.geometry.properties;
          this.showInfoWindow(evt.geometry.position, nodeData);
        });

        this.markers.push(marker);
      });
    }

    /**
     * 无腾讯地图API时的交互式替代方案
     * 使用SVG世界地图+可点击节点
     */
    renderInteractiveMap(nodes) {
      this.container.innerHTML = '';
      this.container.className = 'map-container';

      // 构建交互式地图界面
      const wrapper = document.createElement('div');
      wrapper.style.cssText = `
        width: 100%; height: 100%;
        background: linear-gradient(135deg, #e8f0fe 0%, #f0f4ff 50%, #e0ecff 100%);
        position: relative; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
      `;

      // 世界地图背景（简化版SVG）
      wrapper.innerHTML = `
        <svg viewBox="0 0 1000 500" style="width:90%;max-width:900px;height:auto;opacity:0.15;" preserveAspectRatio="xMidYMid meet">
          <!-- 简化的世界大陆轮廓 -->
          <path d="M150,120 Q200,100 280,110 L320,140 Q350,130 380,150 L400,180 Q420,200 410,240 L380,260 Q340,270 300,250 L260,230 Q220,210 190,180 Z" fill="#0052D9"/>
          <path d="M420,100 Q480,80 550,95 L600,120 Q650,115 700,130 L750,160 Q780,190 760,230 L720,260 Q680,275 630,260 L580,240 Q530,225 490,200 L450,170 Q430,140 420,100 Z" fill="#0052D9"/>
          <path d="M140,260 Q180,250 220,265 L250,290 Q240,330 210,360 L170,370 Q130,365 110,330 L120,290 Z" fill="#0052D9"/>
          <path d="M780,280 Q830,270 870,290 L900,330 Q890,370 850,390 L800,385 Q760,370 750,330 L765,295 Z" fill="#0052D9"/>
        </svg>

        <div id="mapNodesContainer" style="position:absolute;inset:0;"></div>

        <!-- 信息提示框 -->
        <div id="mapInfoBox" style="
          display:none; position:absolute; z-index:100;
          background:white; border-radius:12px; padding:20px 24px;
          box-shadow:0 8px 32px rgba(0,0,0,0.15); min-width:240px;
          transform:translate(-50%,-100%); margin-top:-16px;
          border:1px solid rgba(0,82,217,0.1);
        ">
          <div id="mapInfoBoxTitle" style="font-size:1rem;font-weight:700;color:#1A1A1A;margin-bottom:6px;"></div>
          <div id="mapInfoBoxCity" style="font-size:0.8125rem;color:#FF6B00;font-weight:600;margin-bottom:8px;"></div>
          <div id="mapInfoBoxDesc" style="font-size:0.8125rem;color:#666;line-height:1.6;"></div>
          <div style="width:36px;height:3px;background:linear-gradient(90deg,#0052D9,#FF6B00);border-radius:2px;margin-top:12px;"></div>
        </div>
      `;

      this.container.appendChild(wrapper);

      const nodesContainer = wrapper.querySelector('#mapNodesContainer');
      const infoBox = wrapper.querySelector('#mapInfoBox');

      // 节点坐标映射（将经纬度映射到SVG视口坐标）
      const mapNodePositions = this.calculateNodePositions(nodes);

      // 渲染节点
      mapNodePositions.forEach((pos, index) => {
        const node = nodes[index];

        const nodeEl = document.createElement('div');
        nodeEl.style.cssText = `
          position:absolute; left:${pos.x}%; top:${pos.y}%;
          transform:translate(-50%,-50%);
          cursor:pointer; z-index:10;
        `;

        nodeEl.innerHTML = `
          <div style="
            width:16px; height:16px; border-radius:50%;
            background:linear-gradient(135deg,#0052D9,#3385FF);
            border:3px solid white;
            box-shadow:0 2px 10px rgba(0,82,217,0.4);
            animation:pulse 2s ease-in-out infinite;
            transition:all 0.25s ease;
          "></div>
          <div style="
            position:absolute; top:22px; left:50%; transform:translateX(-50%);
            white-space:nowrap; font-size:0.75rem; font-weight:600;
            color:#0052D9; opacity:0; transition:opacity 0.2s;
            text-shadow:0 1px 2px rgba(255,255,255,0.8);
            pointer-events:none;
          ">${node.city}</div>
        `;

        // 悬停效果
        const dot = nodeEl.querySelector('div:first-child');
        const label = nodeEl.querySelector('div:last-child');

        nodeEl.addEventListener('mouseenter', () => {
          dot.style.transform = 'scale(1.4)';
          dot.style.boxShadow = '0 4px 20px rgba(0,82,217,0.6)';
          label.style.opacity = '1';
          this.showNodeInfo(infoBox, node, pos);
        });

        nodeEl.addEventListener('mouseleave', () => {
          dot.style.transform = 'scale(1)';
          dot.style.boxShadow = '0 2px 10px rgba(0,82,217,0.4)';
          label.style.opacity = '0';
          infoBox.style.display = 'none';
        });

        nodeEl.addEventListener('click', () => {
          this.showNodeInfo(infoBox, node, pos);
          infoBox.style.display = 'block';
        });

        nodesContainer.appendChild(nodeEl);
      });

      // 添加脉冲动画样式
      const style = document.createElement('style');
      style.textContent = `
        @keyframes pulse {
          0%, 100% { box-shadow: 0 0 0 0 rgba(0,82,217,0.4); }
          50% { box-shadow: 0 0 0 8px rgba(0,82,217,0); }
        }
      `;
      this.container.appendChild(style);
    }

    /**
     * 计算节点在地图上的位置百分比
     */
    calculateNodePositions(nodes) {
      // 经纬度范围
      const minLng = -130, maxLng = 150;
      const minLat = -35, maxLat = 60;

      return nodes.map(node => ({
        x: ((node.lng - minLng) / (maxLng - minLng)) * 85 + 7.5,
        y: ((maxLat - node.lat) / (maxLat - minLat)) * 80 + 10
      }));
    }

    /**
     * 显示节点信息提示框
     */
    showNodeInfo(infoBox, node, pos) {
      if (!infoBox) return;

      const titleEl = infoBox.querySelector('#mapInfoBoxTitle');
      const cityEl = infoBox.querySelector('#mapInfoBoxCity');
      const descEl = infoBox.querySelector('#mapInfoBoxDesc');

      if (titleEl) titleEl.textContent = node.name;
      if (cityEl) cityEl.textContent = `${node.country} · ${node.city}`;
      if (descEl) descEl.textContent = node.description || '';

      infoBox.style.left = `${pos.x}%`;
      infoBox.style.top = `${pos.y}%`;
      infoBox.style.display = 'block';
    }

    /**
     * 纯占位符模式
     */
    renderPlaceholder() {
      this.container.innerHTML = `
        <div class="map-placeholder">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="10" r="3"/>
            <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 7 8 11.7z"/>
          </svg>
          <p>全球分布地图正在加载中...</p>
          <p style="font-size:0.8125rem;margin-top:8px;">覆盖亚洲、欧洲、北美等12个全球节点</p>
        </div>
      `;
    }

    showInfoWindow(position, nodeData) {
      if (this.infoWindow) {
        this.infoWindow.close();
      }

      this.infoWindow = new TMap.InfoWindow({
        map: this.mapInstance,
        position: position,
        content: `
          <div style="padding:12px 16px;min-width:200px;">
            <strong style="font-size:14px;">${nodeData.name}</strong>
            <div style="color:#FF6B00;font-size:12px;font-weight:600;margin:4px 0;">${nodeData.country} · ${nodeData.city}</div>
            <div style="color:#666;font-size:12px;line-height:1.6;margin-top:6px;">${nodeData.description || ''}</div>
          </div>
        `
      });

      this.infoWindow.open();
    }
  }

  // DOM Ready初始化
  document.addEventListener('DOMContentLoaded', () => {
    // 首页地图
    const homeMap = document.getElementById('globalMap');
    if (homeMap) {
      new GlobalMap('globalMap');
    }

    // 关于我们页面地图
    const aboutMap = document.getElementById('aboutMap');
    if (aboutMap) {
      // 关于页面使用简化的单点地图
      new GlobalMap('aboutMap');
    }

    // 联系我们页面小地图
    const contactMap = document.getElementById('contactMap');
    if (contactMap) {
      new GlobalMap('contactMap');
    }
  });

  window.GlobalMap = GlobalMap;

})();
