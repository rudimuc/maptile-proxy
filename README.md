# maptile-proxy
Proxy Server to cache Tile Servers locally

### Motivation

I have different websites which include [OpenLayers](https://github.com/openlayers/openlayers) or [Leaflet](https://github.com/Leaflet/Leaflet) to display maps.
These libraries need a Tile Server who provides small images to show the region you are looking at.

Unfortunately there are some disadvantages using this method:
- Privacy: the tile server can detect when you are looking at some places
- Bandwidth: Images need to be loaded from the tile servers
- Speed: depending on your internet connection loading the tiles can be slowly. A local cache could speed it up.

For this reason I've built a small lightweight proxy to eliminate the disadvantages.

### Setup

- You need to make the PHP script accessible within your network.
- The PHP script needs write permission to a folder named "tiles" in it's own directory
- A .htaccess file will tell the Apache Webserver how to map a call to the index.php. This file is mandatory.

### Calling the script from your services

The map library of your choice needs to address the map tile proxy. 
This can be achieved with a URL like https://myservice.anydomain.com/maps/{provider}/{z}/{x}/{y}.png

**Parameters**
- **provider**: lets you choose which provider you want to display (e.g. Google Maps, Here, OpenStreetMaps). I've already included some of my favorites in the script.
- **z**: the zoom factor
- **x**: the x coordinate
- **y**: the y coordinate
