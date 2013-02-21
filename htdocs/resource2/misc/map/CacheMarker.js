function CacheMarker(latlng, wp, type, flags)
{
  this.latlng = latlng;
  this.wp_ = wp;

  if (flags & 4) state = '-inactive';
  else if (flags & 8) state = '-oconly';
  else state = '';

  if (flags & 1)
		this.image_ = 'resource2/ocstyle/images/map/24x24-owned' + state  + '.png';
  else if (flags & 2)
		this.image_ = 'resource2/ocstyle/images/map/24x24-found' + state + '.png';
  else
		this.image_ = 'resource2/ocstyle/images/map/24x24-cachetype-' + type + state + '.png';
  this.height_ = 24;
  this.width_ = 24;
}

CacheMarker.prototype = new GOverlay();

/* Creates the DIV representing this CacheMarker.
 * @param map {GMap2} Map that bar overlay is added to.
 */
CacheMarker.prototype.initialize = function(map) {
  var me = this;

  // Create the DIV representing our CacheMarker
  var div = document.createElement("div");
  div.style.borderWidth = 0;
  div.style.position = "absolute";
  div.style.marginTop = 12;
  div.style.marginLeft = -12;
  div.style.cursor = 'pointer';

  var img = document.createElement("img");
  img.src = me.image_;
  img.style.width = me.width_ + "px";
  img.style.height = me.height_ + "px";
  div.appendChild(img);
  GEvent.addDomListener(div, "click", function()
  {
    GEvent.trigger(me, "click", me.wp_);
  });
  map.getPane(G_MAP_MARKER_PANE).appendChild(div);

  this.map_ = map;
  this.div_ = div;
};

/* Remove the main DIV from the map pane
 */
CacheMarker.prototype.remove = function() {
  this.div_.parentNode.removeChild(this.div_);
};

/* Redraw the CacheMarker based on the current projection and zoom level
 * @param force {boolean} Helps decide whether to redraw overlay
 */
CacheMarker.prototype.redraw = function(force) {

  // We only need to redraw if the coordinate system has changed
  if (!force) return;

  // Calculate the DIV coordinates of two opposite corners 
  // of our bounds to get the size and position of our CacheMarker
  var divPixel = this.map_.fromLatLngToDivPixel(this.latlng);

  // Now position our DIV based on the DIV coordinates of our bounds
  this.div_.style.width = this.width_ + "px";
  this.div_.style.left = (divPixel.x) + "px"
  this.div_.style.height = (this.height_) + "px";
  this.div_.style.top = (divPixel.y) - this.height_ + "px";
};

CacheMarker.prototype.getZIndex = function(m) {
  return GOverlay.getZIndex(marker.getPoint().lat())-m.clicked*10000;
}

CacheMarker.prototype.getPoint = function() {
  return this.latlng;
};

CacheMarker.prototype.setStyle = function(style) {
  for (s in style) {
    this.div_.style[s] = style[s];
  }
};

CacheMarker.prototype.getWaypoint = function() {
  return this.wp_;
};
