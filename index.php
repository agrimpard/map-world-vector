<?PHP
/*

	# Version : 2022 05 19
	# data : https://www.naturalearthdata.com
	# convertion shape / geojson : https://mapshaper.org
	# labels : https://github.com/mledoze/countries
	# test tiles : https://mc.bbbike.org/mc/?num=2&mt0=mapnik&mt1=hillshading

	# Todo
	- geojson : pays labels / pays capitales / océans / mers
	- Leaflet 1.7.x >>> Leaflet 1.8.x

*/
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>World map vector</title>
		<link href="tpl/leaflet-1.7.1/leaflet.css" rel="stylesheet" />
		<link href="tpl/leaflet-fullscreen/Control.FullScreen.css" rel="stylesheet" />
		<script src="tpl/jquery.js"></script>
		<script src="tpl/leaflet-1.7.1/leaflet.js"></script>
		<script src="tpl/leaflet-fullscreen/Control.FullScreen.js"></script>
		<link href="tpl/app.css?<?PHP echo time(); ?>" rel="stylesheet">
	</head>
	<body>

<div class="geo-debug"></div>
<div id="carte"></div>
<script type="text/javascript">

	/*

		http://localhost/dev/map-vector/arcgis-shade/{z}/{x}-{y}.jpg
		https://cartodb-basemaps-b.global.ssl.fastly.net/light_nolabels/{z}/{x}/{y}@2x.png
		https://worldtiles2.waze.com/tiles/{z}/{x}/{y}.png

		https://server.arcgisonline.com/ArcGIS/rest/services/World_Terrain_Base/MapServer/tile/{z}/{y}/{x}.jpg
		https://server.arcgisonline.com/ArcGIS/rest/services/World_Shaded_Relief/MapServer/tile/{z}/{y}/{x}.jpg
		https://server.arcgisonline.com/ArcGIS/rest/services/World_Physical_Map/MapServer/tile/{z}/{y}/{x}.jpg
		https://services.arcgisonline.com/ArcGIS/rest/services/Ocean/World_Ocean_Base/MapServer/tile/{z}/{y}/{x}.jpg
		https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Reference_Overlay/MapServer/tile/{z}/{y}/{x}.jpg

	*/
	var	map_zoom = 4,
		map_zmin = 4,
		map_zmax = 6,
		tiles_base = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Terrain_Base/MapServer/tile/{z}/{y}/{x}.jpg', {
			minZoom: map_zmin,
			maxZoom: map_zmax,
			tileSize: 256,
			attribution: 'ArcGis Ocean',
			noWrap: true,
			className: 'tiles-base'
		}),
		tiles_overlay = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Reference_Overlay/MapServer/tile/{z}/{y}/{x}.jpg', {
			minZoom: map_zmin,
			maxZoom: map_zmax,
			tileSize: 256,
			attribution: 'ArcGis Overlay',
			noWrap: true,
			className: 'tiles-overlay',
			pane: 'overlayPane'
		}),
		latlng = L.latLng(49, 0),
		map = L.map('carte', { center: latlng, zoom: map_zoom, layers: [tiles_base], fullscreenControl: true, fullscreenControlOptions: { position: 'topleft' } } ),
		zoom_now = map.getZoom(),
		zoom_prev = map.getZoom();

	// style
	function geo_countries() {
		return {
			//fillColor: '#'+(0x1000000+Math.random()*0xffffff).toString(16).substr(1,6),
			fillColor: 'hsla(' + (Math.random() * 360) + ', 100%, 50%, 1)',
			weight: 1,
			opacity: 1,
			color: '#333',
			dashArray: '0',
			fillOpacity: .3
		};
	}

	function geo_frontieres() {
		var w = 8;
		if ( map.getZoom() <= 3 ) {
			w = 3;
		} else {
			w = 8;
		}
		return {
			fillColor: 'black',
			weight: w,
			opacity: 1,
			color: '#CCC',
			dashArray: '0',
			fillOpacity: 0
		};
	}



	function load_geojson() {
		$.getJSON('geojson/ne_10m_admin_0_boundary_lines_land.json',function(data){ geojson_frontiere = L.geoJson(data, {style: geo_frontieres}).addTo(map); });
		$.getJSON('geojson/ne_10m_admin_0_countries.json',function(data){ geojson_pays = L.geoJson(data, {style: geo_countries}).addTo(map); });
	}
	load_geojson();

	function load_debug() {
		$('.geo-debug').html('<b>Zoom</b> : '+map.getZoom()+' | <b>Zoom prev</b> = '+window.zoom_prev+'<br><button class="bt-toggle-overlay">Overlay</button>');
	}
	load_debug();

	map.on('zoomend', function() {
		// zoom in / zoom out
		if ( ( window.zoom_prev == 3 && map.getZoom() == 4 ) || ( window.zoom_prev == 4 && map.getZoom() == 3 ) ) {
			geojson_pays.removeFrom(map);
			geojson_frontiere.removeFrom(map);
			load_geojson();
		}
		load_debug()
		window.zoom_prev = map.getZoom();
	});


	$('body').on('click', '.bt-toggle-overlay', function(){
		if( $('.leaflet-layer.tiles-overlay').hasClass('op-0') ) {
			$('.leaflet-layer.tiles-overlay').removeClass('op-0');
		} else {
			$('.leaflet-layer.tiles-overlay').addClass('op-0');
		}
	});

	tiles_overlay.addTo(map);

</script>

	</body>
</html>