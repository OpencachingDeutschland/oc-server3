<script>
<!-- Basiskarten: https://leaflet-extras.github.io/leaflet-providers/preview/ -->
<!-- https://leafletjs.com/reference.html -->

const CyclOSMEUrl = 'https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png';
const CyclOSMAttribution = '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

const mapboxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const mapboxAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

const openTopoMapUrl = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
const openTopoMapAttribution = 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)';

const OSMDEUrl = 'https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png';
const OSMDEAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';


const map_icon_size = 20;
const map_icon_anchor = map_icon_size / 2;
const popupAnchorOffsetX = 0;
const popupAnchorOffsetY = - map_icon_anchor;


const CacheIcon_drivein = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/drivein-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size], // size of the icon
    iconAnchor:   [map_icon_anchor, map_icon_anchor], // point of the icon which will correspond to marker's location
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY] // point from which the popup should open relative to the iconAnchor
});

const CacheIcon_event = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/event-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY]
});

const CacheIcon_mathe = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/mathe-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY]
});

const CacheIcon_moving = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/moving-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY]
});

const CacheIcon_multi = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/multi-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY]
});

const CacheIcon_mystery = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/mystery-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY]
});

const CacheIcon_tradi = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/traditional-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY]
});

const CacheIcon_unknown = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/unknown-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY]
});

const CacheIcon_virtual = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/virtual-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [popupAnchorOffsetX, popupAnchorOffsetY]
});

const CacheIcon_webcam = L.icon({
    iconUrl: '{{ asset('images/cacheTypes/webcam-active-untried.svg') }}',

    iconSize:     [map_icon_size, map_icon_size],
    iconAnchor:   [map_icon_anchor, map_icon_anchor],
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});


</script>
