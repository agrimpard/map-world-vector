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
		https://maps-for-free.com/layer/relief/z{z}/row{y}/{z}_{x}-{y}.jpg

		https://server.arcgisonline.com/ArcGIS/rest/services/World_Terrain_Base/MapServer/tile/{z}/{y}/{x}.jpg // problème mer Caspienne
		https://server.arcgisonline.com/ArcGIS/rest/services/World_Shaded_Relief/MapServer/tile/{z}/{y}/{x}.jpg
		https://server.arcgisonline.com/ArcGIS/rest/services/World_Physical_Map/MapServer/tile/{z}/{y}/{x}.jpg
		https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Reference_Overlay/MapServer/tile/{z}/{y}/{x}.jpg
		https://services.arcgisonline.com/ArcGIS/rest/services/Ocean/World_Ocean_Base/MapServer/tile/{z}/{y}/{x}.jpg
		https://services.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}.jpg
		https://services.arcgisonline.com/arcgis/rest/services/Elevation/World_Hillshade/MapServer/tile/{z}/{y}/{x}
		
	*/
	var	map_zoom = 4,
		map_zmin = 4,
		map_zmax = 6,
		tiles_base = L.tileLayer('https://services.arcgisonline.com/arcgis/rest/services/Elevation/World_Hillshade/MapServer/tile/{z}/{y}/{x}', {
			minZoom: map_zmin,
			maxZoom: map_zmax,
			tileSize: 256,
			attribution: '&copy <a href="https://www.arcgis.com">ArcGis</a>',
			noWrap: true,
			className: 'tiles-base'
		})
		latlng = L.latLng(49, 0),
		map = L.map('carte', { center: latlng, zoom: map_zoom, layers: [tiles_base], fullscreenControl: true, fullscreenControlOptions: { position: 'topleft' } } ),
		zoom_now = map.getZoom(),
		zoom_prev = map.getZoom();

	// panes bathymetry
	map.createPane('b_0'); map.getPane('b_0').style.zIndex = 501;
	map.createPane('b_1'); map.getPane('b_1').style.zIndex = 502;
	map.createPane('b_2'); map.getPane('b_2').style.zIndex = 503;
	// pane graticule
	map.createPane('g_0'); map.getPane('g_0').style.zIndex = 510;
	// pane countries
	map.createPane('c_0'); map.getPane('c_0').style.zIndex = 520;
	map.createPane('c_1'); map.getPane('c_1').style.zIndex = 521;

	// style
	function geo_countries() {
		return {
			//fillColor: '#'+(0x1000000+Math.random()*0xffffff).toString(16).substr(1,6),
			fillColor: 'hsla(' + (Math.random() * 360) + ', 100%, 50%, 1)',
			weight: 1,
			opacity: 1,
			color: '#333',
			dashArray: '0',
			fillOpacity: .2
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
	function geo_water() {
		return {
			fillColor: '#FFF',
			fillOpacity: .3,
			opacity: 0
		};
	}
	function geo_water_0() {
		return {
			fillColor: '#DEEFF7',
			fillOpacity: 1,
			opacity: 0
		};
	}
	function geo_graticules() {
		return {
			weight: 2,
			opacity: .1,
			color: '#A3BCC7',
			dashArray: '0',
			fillOpacity: 0
		};
	}


	function map_load() {
		$.getJSON('geojson/ne_10m_bathymetry_L_0.json',function(data){ geojson_b_0 = L.geoJson(data, {style: geo_water_0, pane: 'b_0'}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_K_200.json',function(data){ geojson_b_200 = L.geoJson(data, {style: geo_water, pane: 'b_1'}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_J_1000.json',function(data){ geojson_b_1000 = L.geoJson(data, {style: geo_water, pane: 'b_2'}).addTo(map); });
		/*
		$.getJSON('geojson/ne_10m_bathymetry_I_2000.json',function(data){ geojson_b_2000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_H_3000.json',function(data){ geojson_b_3000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_G_4000.json',function(data){ geojson_b_4000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_F_5000.json',function(data){ geojson_b_5000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_E_6000.json',function(data){ geojson_b_6000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_D_7000.json',function(data){ geojson_b_7000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_C_8000.json',function(data){ geojson_b_8000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_B_9000.json',function(data){ geojson_b_9000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_A_10000.json',function(data){ geojson_b_10000 = L.geoJson(data, {style: geo_water}).addTo(map); });
		*/
		$.getJSON('geojson/ne_10m_graticules_10.json',function(data){ geojson_frontiere = L.geoJson(data, {style: geo_graticules, pane: 'g_0'}).addTo(map); });
		//$.getJSON('geojson/ne_10m_time_zones.json',function(data){ geojson_frontiere = L.geoJson(data, {style: geo_graticules, pane: 'g_0'}).addTo(map); });
		$.getJSON('geojson/ne_10m_admin_0_boundary_lines_land.json',function(data){ geojson_frontiere = L.geoJson(data, {style: geo_frontieres, pane: 'c_0'}).addTo(map); });
		$.getJSON('geojson/ne_10m_admin_0_countries.json',function(data){ geojson_pays = L.geoJson(data, {style: geo_countries, pane: 'c_1'}).addTo(map); });
		L.control.scale().addTo(map);
	}
	map_load();

	function debug_load() {
		$('.geo-debug').html('<b>Zoom</b> : '+map.getZoom()+' | <b>Zoom prev</b> = '+window.zoom_prev);
	}
	debug_load();

	map.on('zoomend', function() {
		// zoom in / zoom out
		if ( ( window.zoom_prev == 3 && map.getZoom() == 4 ) || ( window.zoom_prev == 4 && map.getZoom() == 3 ) ) {
			geojson_pays.removeFrom(map);
			geojson_frontiere.removeFrom(map);
			map_load();
		}
		debug_load()
		window.zoom_prev = map.getZoom();
	});


</script>

	</body>
</html>