[![CC BY-NC-SA 4.0][license-shield]][license-url]

## World map vector

This world map is a combination of classic tiles with vector overlays (SVG) based on Leaflet.
The objective is to display an interactive map, in color, as simply as possible, as aesthetically as possible, with open source content.

## Goal

The objective is to try to reproduce a map that I like very much consultable on ArcGIS with the geopolitical background map.

[![ArcGIS map][arcgis-screenshot]](https://www.arcgis.com/apps/mapviewer/)

The final work should be as close as possible to the ArcGIS model and provide all necessary open source tools. A database (json) with the names of the countries and their respective capitals and all the data to display this information on the map will be available.

A fully functional example will also be available.

## Limitations

ArcGIS seems to use "tiled" vectorized files for the limits of countries, their borders, the depths of seas and oceans, which makes the display much less heavy and therefore much more fluid.

The geojson files I have are global and therefore relatively heavy, I do not have the skills to cut these files into tiles.

Also, ArcGIS seems to have a tiled background map (which I don't have) with transparency. This allows for a pretty cool effect, the grids (longitude/latitude) fade under the land masses.

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
