{***************************************************************************
* for license information see LICENSE.md
* Author: hxdimpf
*
* map3.tpl — Leaflet-based live OC map
* Note: Leaflet + MarkerCluster JS loaded via add_header_javascript in map3.php
***************************************************************************}

{* Leaflet CSS *}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" crossorigin=""/>

<style>
{literal}
#mapRoot {
  width: 100%;
  height: calc(100vh - 10px);
  margin: 0;
  padding: 0;
}
.map-button-control  { display: flex; flex-direction: column; gap: 2px; }
.map-button-row      { display: flex; gap: 2px; }
.map-btn {
  width: 30px; height: 30px;
  min-width: 30px; min-height: 30px;
  padding: 0; box-sizing: border-box;
  background: white; border: 2px solid rgba(0,0,0,0.2);
  border-radius: 4px; cursor: pointer; font-size: 14px;
  display: flex; align-items: center; justify-content: center;
  line-height: 1;
}
.map-btn.on       { background: #2f7dc7; color: white; }
.map-btn.off      { background: white; color: black; }
.map-btn.disabled { background: #ccc; color: #888; cursor: not-allowed; }
.zoom-level-display {
  background: white; border: 2px solid rgba(0,0,0,0.2);
  border-radius: 4px; padding: 2px 6px; font-size: 12px;
}
.map-escape-top {
  position: absolute; bottom: 40px; right: 10px; z-index: 1000;
  background: rgba(255,255,255,0.8); border: 1px solid #ccc;
  border-radius: 4px; padding: 4px 8px; cursor: pointer; font-size: 16px;
}
{/literal}
</style>

{* Pass map init coords to JS *}
<script>
  window.initLat    = {$initLat};
  window.initLon    = {$initLon};
  window.initZoom   = {$initZoom};
  window.uniCacheWP = [];
</script>

{* Map container *}
<div id="mapRoot"></div>

{* OCmap entry point (ESM) — Leaflet + MarkerCluster are in <head> via map3.php *}
<script type="module">
{literal}
  import { handleWPs } from '/resource2/misc/map3/map3.js';
  handleWPs();
{/literal}
</script>
