var map_image_cache;

//detect browser:
if ((navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 3) || parseInt(navigator.appVersion) >= 4) {
        rollOvers = 1;
}       else {
        if (navigator.appName == "Microsoft Internet Explorer" && parseInt(navigator.appVersion) >= 4) {
                rollOvers = 1;
        } else {
                rollOvers = 0;
        }
}

window.onload = function() {
	//preload images
	if (rollOvers) {
		map_image_cache = [];
		map_image_cache[-1] = new Image();
		map_image_cache[-1].src = document.getElementById('main-cachemap').getAttribute('basesrc');
		for (i = 0; i < 20; i++)
		{
			var nc_elem = document.getElementById('newcache' + i);
			if (nc_elem != null)
			{
				map_image_cache[i] = new Image();
				map_image_cache[i].src = nc_elem.getAttribute('maphref');
			}
		}
	}
}

//image swapping function:
function Lite(nn) {
	if (rollOvers) {
		document.getElementById('main-cachemap').src = map_image_cache[nn].src;
	}
}

function Unlite() {
	if (rollOvers) {
		document.getElementById('main-cachemap').src = map_image_cache[-1].src;
	}
}