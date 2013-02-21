function CacheMarker(latlng, wp, type, flags, map)
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
  this.map_ = map;
  this.setMap( map );
}

CacheMarker.prototype = new google.maps.OverlayView;

/* Creates the DIV representing this CacheMarker.
 * @param map {GMap2} Map that bar overlay is added to.
 */
CacheMarker.prototype.onAdd = function() {
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
  google.maps.event.addDomListener(div, "click", function()
  {
    google.maps.event.trigger(me, "click", me.wp_);
  });
  //map.getPane(G_MAP_MARKER_PANE).appendChild(div);
  var pane = this.getPanes().overlayMouseTarget;
  pane.appendChild(div);
  
  this.div_ = div;
};

/* Remove the main DIV from the map pane
 */
CacheMarker.prototype.onRemove = function() {
  this.div_.parentNode.removeChild(this.div_);
  this.div_ = null;
};

/* Redraw the CacheMarker based on the current projection and zoom level
 * @param force {boolean} Helps decide whether to redraw overlay
 */
CacheMarker.prototype.draw = function() {
  // Calculate the DIV coordinates of two opposite corners 
  // of our bounds to get the size and position of our CacheMarker
  var divPixel = this.getProjection().fromLatLngToDivPixel(this.latlng);

  // Now position our DIV based on the DIV coordinates of our bounds
  this.div_.style.width = this.width_ + "px";
  this.div_.style.left = (divPixel.x-this.width_/2) + "px"
  this.div_.style.height = (this.height_) + "px";
  this.div_.style.top = (divPixel.y+this.height_/2) - this.height_ + "px";
};

CacheMarker.prototype.getZIndex = function(m) {
  return google.maps.Overlay.getZIndex(marker.getPoint().lat())-m.clicked*10000;
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
