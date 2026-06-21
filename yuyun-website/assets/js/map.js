/**
 * 语云科技企业官网 - 全球分布地图组件
 * SVG世界地图 + 标记点 + Tooltip
 */

(function() {
    'use strict';

    // 全球节点数据
    var mapLocations = [
        { id: 1, name: '北京', region: '中国', type: 'hq', x: 75, y: 35, desc: '中国总部 | 数据中心' },
        { id: 2, name: '青岛', region: '中国', type: 'dc', x: 78, y: 38, desc: '数据中心 | 海底光缆接入' },
        { id: 3, name: '新加坡', region: '东南亚', type: 'dc', x: 72, y: 55, desc: '亚太节点 | CDN加速' },
        { id: 4, name: '首尔', region: '韩国', type: 'dc', x: 79, y: 33, desc: '韩国节点 | 游戏加速' },
        { id: 5, name: '迪拜', region: '中东', type: 'dc', x: 55, y: 43, desc: '中东节点 | 覆盖西亚北非' },
        { id: 6, name: '法兰克福', region: '欧洲', type: 'dc', x: 48, y: 30, desc: '欧洲主节点 | 德国法兰克福' },
        { id: 7, name: '莫斯科', region: '俄罗斯', type: 'dc', x: 58, y: 25, desc: '俄罗斯 | 东欧覆盖' },
        { id: 8, name: '圣彼得堡', region: '俄罗斯', type: 'dc', x: 55, y: 23, desc: '俄罗斯 | 波罗的海节点' },
        { id: 9, name: '悉尼', region: '澳洲', type: 'dc', x: 88, y: 72, desc: '澳洲节点 | 大洋洲覆盖' },
        { id: 10, name: '纽约', region: '北美', type: 'dc', x: 22, y: 34, desc: '美国东海岸 | 金融数据中心' },
        { id: 11, name: '华盛顿', region: '北美', type: 'dc', x: 20, y: 36, desc: '美国东海岸 | 政企服务' },
        { id: 12, name: '旧金山', region: '北美', type: 'dc', x: 10, y: 36, desc: '美国西海岸 | 科技创新中心' },
        { id: 13, name: '东京', region: '日本', type: 'dc', x: 84, y: 35, desc: '日本节点 | 亚太互联' },
        { id: 14, name: '香港', region: '中国', type: 'dc', x: 74, y: 45, desc: '中国香港 | 国际出口' },
        { id: 15, name: '伦敦', region: '欧洲', type: 'dc', x: 45, y: 28, desc: '英国 | 欧洲金融中心' },
        { id: 16, name: '孟买', region: '南亚', type: 'dc', x: 62, y: 48, desc: '印度 | 南亚枢纽' }
    ];

    // 类型图标映射
    var typeIcons = {
        hq: '<i class="fa-solid fa-building" style="color:#FF6B00;"></i>',
        dc: '<i class="fa-solid fa-server" style="color:#00A8E8;"></i>'
    };

    // 类型颜色映射
    var typeColors = {
        hq: '#FF6B00',
        dc: '#00A8E8'
    };

    function WorldMap(containerSelector) {
        this.container = document.querySelector(containerSelector);
        if (!this.container) return;

        this.locations = mapLocations;
        this.activePoint = null;

        this.render();
        this.bindEvents();
    }

    WorldMap.prototype.render = function() {
        var self = this;

        // 创建地图容器
        var mapHtml = '<div class="map-container">';

        // SVG世界地图(简化版)
        mapHtml += this.getSVGMap();

        // 地图标记点
        mapHtml += '<div class="map-points">';

        this.locations.forEach(function(loc) {
            var color = typeColors[loc.type] || '#00A8E8';

            mapHtml += '' +
                '<div class="map-point" data-id="' + loc.id + '" style="left:' + loc.x + '%;top:' + loc.y + '%;">' +
                '  <div class="map-dot" style="background:' + color + ';box-shadow:0 0 0 4px rgba(' + self.hexToRgb(color) + ',0.3),0 0 20px rgba(' + self.hexToRgb(color) + ',0.4);"></div>' +
                '  <div class="map-tooltip-card">' +
                '    <div class="map-tooltip-title">' + (typeIcons[loc.type] || '') + ' ' + loc.name + '</div>' +
                '    <div class="map-tooltip-sub">' + loc.region + ' · ' + loc.desc + '</div>' +
                '  </div>' +
                '</div>';
        });

        mapHtml += '</div>'; // .map-points
        mapHtml += '</div>'; // .map-container

        this.container.innerHTML = mapHtml;
    };

    WorldMap.prototype.getSVGMap = function() {
        return '' +
            '<svg class="map-svg" viewBox="0 0 100 55" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg">' +
            '  <!-- 简化世界地图轮廓 -->' +
            '  <defs>' +
            '    <linearGradient id="mapGradient" x1="0%" y1="0%" x2="100%" y2="100%">' +
            '      <stop offset="0%" style="stop-color:rgba(0,102,204,0.15);stop-opacity:1" />' +
            '      <stop offset="100%" style="stop-color:rgba(0,168,232,0.08);stop-opacity:1" />' +
            '    </linearGradient>' +
            '  </defs>' +
            '  <!-- 北美洲 -->' +
            '  <path d="M5,12 L25,10 L28,18 L26,28 L20,32 L12,30 L5,24 L3,18 Z" fill="url(#mapGradient)" stroke="rgba(0,168,232,0.2)" stroke-width="0.3"/>' +
            '  <!-- 南美洲 -->' +
            '  <path d="M18,35 L26,33 L28,42 L24,52 L18,50 L16,42 Z" fill="url(#mapGradient)" stroke="rgba(0,168,232,0.2)" stroke-width="0.3"/>' +
            '  <!-- 欧洲 -->' +
            '  <path d="M42,14 L54,12 L56,18 L54,26 L46,28 L40,24 L41,18 Z" fill="url(#mapGradient)" stroke="rgba(0,168,232,0.2)" stroke-width="0.3"/>' +
            '  <!-- 非洲 -->' +
            '  <path d="M44,29 L54,27 L58,36 L54,48 L46,49 L42,40 Z" fill="url(#mapGradient)" stroke="rgba(0,168,232,0.2)" stroke-width="0.3"/>' +
            '  <!-- 亚洲 -->' +
            '  <path d="M56,10 L88,8 L94,18 L90,32 L82,38 L70,36 L58,30 L54,20 Z" fill="url(#mapGradient)" stroke="rgba(0,168,232,0.2)" stroke-width="0.3"/>' +
            '  <!-- 东南亚/印尼 -->' +
            '  <path d="M76,40 L86,38 L90,44 L84,48 L76,46 Z" fill="url(#mapGradient)" stroke="rgba(0,168,232,0.2)" stroke-width="0.3"/>' +
            '  <!-- 澳大利亚 -->' +
            '  <path d="M82,50 L94,48 L98,54 L92,58 L82,56 Z" fill="url(#mapGradient)" stroke="rgba(0,168,232,0.2)" stroke-width="0.3"/>' +
            '  <!-- 连接线(示意全球网络) -->' +
            '  <g stroke="rgba(0,168,232,0.12)" stroke-width="0.2" fill="none" stroke-dasharray="2,2">' +
            '    <line x1="75" y1="35" x2="79" y2="33"/>' +
            '    <line x1="75" y1="35" x2="72" y2="55"/>' +
            '    <line x1="75" y1="35" x2="84" y2="35"/>' +
            '    <line x1="75" y1="35" x2="74" y2="45"/>' +
            '    <line x1="48" y1="30" x2="58" y2="25"/>' +
            '    <line x1="48" y1="30" x2="45" y2="28"/>' +
            '    <line x1="22" y1="34" x2="20" y2="36"/>' +
            '    <line x1="22" y1="34" x2="10" y2="36"/>' +
            '    <line x1="88" y1="72" x2="84" y2="35"/>' +
            '  </g>' +
            '</svg>';
    };

    WorldMap.prototype.bindEvents = function() {
        var points = this.container.querySelectorAll('.map-point');
        var self = this;

        points.forEach(function(point) {
            point.addEventListener('mouseenter', function() {
                self.activePoint = this;
            });

            point.addEventListener('mouseleave', function() {
                self.activePoint = null;
            });

            point.addEventListener('click', function() {
                // 点击时保持tooltip显示
                points.forEach(function(p) {
                    p.querySelector('.map-tooltip-card').style.opacity = '0';
                    p.querySelector('.map-tooltip-card').style.visibility = 'hidden';
                });
                this.querySelector('.map-tooltip-card').style.opacity = '1';
                this.querySelector('.map-tooltip-card').style.visibility = 'visible';
            });
        });

        // 点击其他区域关闭所有tooltip
        this.container.addEventListener('click', function(e) {
            if (!e.target.closest('.map-point')) {
                points.forEach(function(p) {
                    p.querySelector('.map-tooltip-card').style.opacity = '';
                    p.querySelector('.map-tooltip-card').style.visibility = '';
                });
            }
        });
    };

    WorldMap.prototype.hexToRgb = function(hex) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        if (result) {
            return parseInt(result[1], 16) + ',' + parseInt(result[2], 16) + ',' + parseInt(result[3], 16);
        }
        return '0,168,232';
    };

    // 自动初始化
    document.addEventListener('DOMContentLoaded', function() {
        var mapContainer = document.getElementById('world-map');
        if (mapContainer) {
            mapContainer._worldMap = new WorldMap('#world-map');
        }
    });

    // 暴露全局
    window.WorldMap = WorldMap;

})();
