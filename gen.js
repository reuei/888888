const d3 = require('d3-geo');
const topojson = require('topojson-client');
const fs = require('fs');

const world = JSON.parse(fs.readFileSync('/tmp/mapgen/node_modules/world-atlas/land-110m.json'));
const land = topojson.feature(world, world.objects.land);

const width = 1000;
const height = 500;
const projection = d3.geoEquirectangular()
  .scale(width / (2 * Math.PI))
  .translate([width / 2, height / 2]);
const path = d3.geoPath(projection);
const d = path(land);

const compact = d.replace(/([+-]?\d*\.?\d+)/g, (m) => {
  const v = parseFloat(m);
  if (isNaN(v)) return m;
  return (Math.round(v * 10) / 10).toString();
});

console.log('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 500">');
console.log('<path d="' + compact + '"/>');
console.log('</svg>');
console.log('\nLength: ' + compact.length);
