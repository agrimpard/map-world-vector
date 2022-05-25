[![CC BY-NC-SA 4.0][license-shield]][license-url]

**This is a work in progress !**

<details>
  <summary>Table of Contents</summary>
  <ol>
    <li><a href="#the-project">The project</a></li>
    <li><a href="#goal">Goal</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#limitations">Limitations</a></li>
    <li><a href="#result">Result</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#sources">Sources</a></li>
  </ol>
</details>

## The project

This world map is a combination of classic tiles with vector overlays (SVG) based on Leaflet.
The objective is to display an interactive map, in color, as simply as possible, as aesthetically as possible, with open source content.

## Goal

The objective is to try to reproduce a map that I like very much consultable on ArcGIS with the geopolitical background map.

[![ArcGIS map][arcgis-screenshot]](https://www.arcgis.com/apps/mapviewer/)

The final work should be as close as possible to the ArcGIS model and provide all necessary open source tools. A database (json) with the names of the countries and their respective capitals and all the data to display this information on the map will be available.

A fully functional example will also be available.

## Roadmap

- [x] Base map with leaflet 1.7.x and ArcGis tiles
- [x] Add geojson : countries limits, countries boundaries, bathymetry, latlon grid
- [x] CSS : countries limits, countries boundaries, bathymetry, latlon grid
- [ ] Database countries and capitals cities
- [ ] Add labels for : countries and capitals cities
- [ ] CSS : countries and capitals cities
- [ ] Database oceans/seas
- [ ] Add labels for : oceans and seas
- [ ] CSS : oceans names
- [ ] Zoom based labels display
- [ ] Add custom font for : countries names (Montserrat), cities names (Roboto), oceans names (Arial)
- [ ] One color per country and random color
- [ ] Multi-language Support
    - [ ] English
    - [ ] French
- [ ] Update leaflet 1.7.x to 1.8.x
- [ ] 3 levels precision : light, normal, full
- [ ] Provide all sources

## Limitations

ArcGIS seems to use "tiled" vectorized files for the limits of countries, their borders, the depths of seas and oceans, which makes the display much less heavy and therefore much more fluid.

The geojson files I have are global and therefore relatively heavy, I do not have the skills to cut these files into tiles.

Also, ArcGIS seems to have a tiled background map with transparency (which I don't have). This allows for a pretty cool effect, the grids (longitude/latitude) fade under the land masses.

## Result

Here is the rendering I get for the moment !

![Result map][map-screenshot]

## License

[Creative Commons - Attribution - NonCommercial - ShareAlike 4.0 International][license-url].

[![CC BY-NC-SA 4.0][license-image]][license-url]

[license-url]: https://creativecommons.org/licenses/by-nc-sa/4.0/
[license-image]: https://licensebuttons.net/l/by-nc-sa/4.0/88x31.png
[license-shield]: https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-lightgrey.svg?style=for-the-badge
[arcgis-screenshot]: map-arcgis.png
[map-screenshot]: map.png

## Sources

ASAP !