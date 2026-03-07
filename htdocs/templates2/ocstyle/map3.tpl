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
.map-btn.wide     { width: auto; min-width: 44px; padding: 0 6px; }
.map-btn.off      { background: white; color: black; }
.map-btn.disabled { background: #ccc; color: #888; cursor: not-allowed; }
.zoom-level-display {
  background: white; border: 2px solid rgba(0,0,0,0.2);
  border-radius: 4px; padding: 2px 6px; font-size: 12px;
}
.map-info-display,
.length-display {
  font-size: 0.7rem; padding: 4px 8px;
  background: rgba(240,248,255,0.95);
  color: #333; border-radius: 4px;
  border: 1px solid rgba(0,0,0,0.1); white-space: nowrap;
}
.map-dropdown { position: relative; display: inline-block; }
.map-dropdown-menu {
  display: none; position: absolute; left: 0; top: 100%;
  background: white; border: 1px solid rgba(0,0,0,0.2);
  border-radius: 4px; z-index: 2000; min-width: 80px;
  flex-direction: column; box-shadow: 2px 2px 6px rgba(0,0,0,0.2);
}
.map-dropdown-menu.show { display: flex; }
.map-dropdown-item {
  background: white; border: none; text-align: left;
  padding: 5px 10px; cursor: pointer; font-size: 13px;
  white-space: nowrap;
}
.map-dropdown-item:hover { background: #f0f0f0; }
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
