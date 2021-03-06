<?PHP
/*

	# Version : 2022 05 19
	# data : https://www.naturalearthdata.com
	# convertion shape / geojson : https://mapshaper.org
	# labels : https://github.com/mledoze/countries
	# test tiles : https://mc.bbbike.org/mc/?num=2&mt0=mapnik&mt1=hillshading
	# country list + translations : https://github.com/stefangabos/world_countries/tree/master/data/countries/_combined

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
		map_center = L.latLng(49, 0),
		map_sw = L.latLng(-89.98155760646617, -180),
		map_ne = L.latLng(89.99346179538875, 180),
		map_bounds = L.latLngBounds(map_sw,map_ne),
		//tiles_base = L.tileLayer('https://services.arcgisonline.com/arcgis/rest/services/Elevation/World_Hillshade/MapServer/tile/{z}/{y}/{x}', {
		tiles_base = L.tileLayer('http://localhost/dev/map-vector/arcgis-shade/{z}/{x}-{y}.jpg', {
		//tiles_base = L.tileLayer('https://worldtiles2.waze.com/tiles/{z}/{x}/{y}.png', {
			minZoom: map_zmin,
			maxZoom: map_zmax,
			tileSize: 256,
			attribution: '&copy <a href="https://www.arcgis.com">ArcGis</a>',
			noWrap: true,
			className: 'tiles-base'
		}),
		map = L.map('carte', {
			center: map_center,
			maxBounds: map_bounds,
			zoom: map_zoom,
			layers: [tiles_base],
			fullscreenControl: true,
			fullscreenControlOptions: { position: 'topleft' }
		}),
		icon_city = L.divIcon({
			html: '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="red" class="bi bi-record-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>',
			className: 'icon-city',
			iconSize: [10, 10],
			iconAnchor:   [3, 8],
			popupAnchor:  [0, 0]
		}),
		zoom_now = map.getZoom(),
		zoom_prev = map.getZoom(),
		icon_country = L.icon({
			iconUrl: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
			iconSize:     [1, 1],
			iconAnchor:   [0, 0],
			popupAnchor:  [0, 0]
		});


	// panes bathymetry
	map.createPane('b_0'); map.getPane('b_0').style.zIndex = 501;
	map.createPane('b_1'); map.getPane('b_1').style.zIndex = 502;
	map.createPane('b_2'); map.getPane('b_2').style.zIndex = 503;
	// pane graticule
	map.createPane('g_0'); map.getPane('g_0').style.zIndex = 510;
	// pane countries
	map.createPane('c_0'); map.getPane('c_0').style.zIndex = 520;
	map.createPane('c_1'); map.getPane('c_1').style.zIndex = 521;
	// pane labels
	map.createPane('l_0'); map.getPane('l_0').style.zIndex = 530;

	// cities
	var cities = [
		{
			coords:[48.8589466,2.2769956],
			country:'France',
			label:'Paris',
		},
		{
			coords:[52.5069704,13.2846502],
			country:'Allemagne',
			label:'Berlin',
		},
		{
			coords:[41.9102415,12.3959152],
			country:'Italie',
			label:'Rome',
		},
		{
			coords:[40.4381311,-3.8196195],
			country:'Espagne',
			label:'Madrid',
		},
		{
			coords:[51.5287718,-0.2416803],
			country:'Royaume-Uni',
			label:'Londres',
		},
	];


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
		$.getJSON('geojson/ne_10m_bathymetry_L_0_sm.json',function(data){ geojson_b_0 = L.geoJson(data, {style: geo_water_0, pane: 'b_0', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_K_200_sm.json',function(data){ geojson_b_200 = L.geoJson(data, {style: geo_water, pane: 'b_1', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_J_1000_sm.json',function(data){ geojson_b_1000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		/**/
		$.getJSON('geojson/ne_10m_bathymetry_I_2000_sm.json',function(data){ geojson_b_2000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_H_3000_sm.json',function(data){ geojson_b_3000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_G_4000_sm.json',function(data){ geojson_b_4000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_F_5000_sm.json',function(data){ geojson_b_5000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_E_6000_sm.json',function(data){ geojson_b_6000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_D_7000_sm.json',function(data){ geojson_b_7000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_C_8000_sm.json',function(data){ geojson_b_8000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_B_9000_sm.json',function(data){ geojson_b_9000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_10m_bathymetry_A_10000_sm.json',function(data){ geojson_b_10000 = L.geoJson(data, {style: geo_water, pane: 'b_2', interactive: false}).addTo(map); });
		/**/
		$.getJSON('geojson/ne_10m_graticules_10.json',function(data){ geojson_frontiere = L.geoJson(data, {style: geo_graticules, pane: 'g_0', interactive: false}).addTo(map); });
		// 10m
		//$.getJSON('geojson/ne_10m_admin_0_boundary_lines_land.json',function(data){ geojson_frontiere = L.geoJson(data, {style: geo_frontieres, pane: 'c_0', interactive: false}).addTo(map); });
		//$.getJSON('geojson/ne_10m_admin_0_countries.json',function(data){ geojson_pays = L.geoJson(data, {style: geo_countries, pane: 'c_1'}).addTo(map); });
		// 50m
		$.getJSON('geojson/ne_50m_admin_0_boundary_lines_land.json',function(data){ geojson_frontiere = L.geoJson(data, {style: geo_frontieres, pane: 'c_0', interactive: false}).addTo(map); });
		$.getJSON('geojson/ne_50m_admin_0_countries.json',function(data){ geojson_pays = L.geoJson(data, {style: geo_countries, pane: 'c_1'}).addTo(map); });
		L.control.scale().addTo(map);

		// ville
		cities.forEach(function(obj) {
			var m_city = new L.Marker(obj.coords, { icon: icon_city, pane: 'l_0' }).bindTooltip(obj.label, { pane: 'l_0', permanent: true, className: 'city-name', direction : 'right' }).addTo(map);
		});

		// pays
		var m_country = new L.marker([46.7511877,2.4738183], { icon: icon_country, opacity: 0 }).bindTooltip('France', { pane: 'l_0', permanent: true, className: 'country-name', direction : 'center' }).addTo(map);

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