# Notes & observations

### Questions fondamentales

* Devons-nous supporter les navigateurs mobiles (cellulaires, tablettes, etc.)?
* Est-ce que l'application doit fonctionner minimalement sans connexion Internet?
* Quelles sont les licenses à adopter pour les applications et les données? Elles peuvent également êtres variables en fonction des sources.
* Liste des catégories de lieux à supporter? Et la hiérarchie dans les classes de lieux? Voir plus bas un exemple de *Google Places*.
* Quels sont les métadonnées minimales pour alimenter un type de lieu?
* Quels sont les découpages géographiques pouvant être utilisés pour questionner les lieux?
* En fait, toutes les questions souhaitables. Bref, établir les requêtes possibles sur l'API.
* Est-ce que l'API est bilingue?
* Les limites en nombre de requêtes (par jour/par minute/par membre/par IP, etc.)?


### Licenses

* Open UNPD -> Creative Commons Attribution License (CC-BY)
* [**Licenses GeoCommons**](http://geocommons.com/help/Open_Source) -> liste des standards, données et applications Open Source.
* [Open Source software license](http://en.wikipedia.org/wiki/Open_source_software_license)
* [Open Data Commons](http://opendatacommons.org/faq/licenses/), licenses associées.


### Liens

* [**Open.undp.org**](http://open.undp.org/), API JSON de la United Nations Development Programme.

* [**API Cicero**](http://cicero.azavea.com/docs), API Cicero. Exemple de bonne doumentation d'un API.
* [**GeoCommons**](http://geocommons.com), Répertoire de données géolocalisées avec outils d'analyses.
* [**Metro Extracts**](http://metro.teczno.com/#montreal), Extraits de la BD OpenStreetMap pour Montréal.
* [**Code for America**](http://codeforamerica.org/), *Code for America helps governments work better for everyone with the people and the power of the web*. Projets [Local Data](http://codeforamerica.org/?cfa_project=localdata) ( [portail du projet en développement](http://golocaldata.org) ) et [311 labs](http://codeforamerica.org/?cfa_project=311-labs) en particulier.
* [**MapBox, serving faster maps**](http://mapbox.com/blog/building-mapbox-fast-map-hosting-stack/), article sur les technologies utilisées.
* [**BeyondFog, Websockets and More: 20 awesome node.js npm modules we use every day**](http://blog.beyondfog.com/websockets-and-more-20-awesome-node-js-npm-modules-we-use-every-day/#.ULzXuOOe_Xc), article sur les technologies utilisées.

### Exemples de cartes

* [**How fast is LAFD where you live?**](http://graphics.latimes.com/how-fast-is-lafd/#10/34.0504/-118.6002), hex map.


### Références

* [**GeoReport v2**](http://wiki.open311.org/GeoReport_v2), spec de l'API GeoReport v2, utilisé pour l'Open 311.
* [**Inquiry API v1**](http://wiki.open311.org/Inquiry_v1), spec de l'API Inquiry v1 pour la ville de New-York, utilisé pour l'Open 311.

* [**Cicero Query by location API**](http://cicero.azavea.com/docs/query_by_location.html), exemple de requête par localisation de l'API Cicero.
* [**USA Today Article API**](http://developer.usatoday.com/docs/read/articles), API des articles du quortidien USA Today.
* [**Klout API**](http://developer.klout.com/iodocs), documentation de l'API Klout générée par [ioDocs](http://www.mashery.com/product/io-docs).
* [**Shareabouts**](http://shareabouts.org), *is a mapping tool to gather crowd sourced public input. Use it to collect suggested locations and comments, in a social, engaging process*. Exemple: [Avoid ATM surcharges](http://nosur.shareabouts.org).
[Shareabouts Roadmap](https://github.com/openplans/shareabouts/wiki/roadmap), état des lieux du projet Shareabouts (données locales via questionnaires).
* [**City of Vancouver Open Data Catalogue**](http://vancouver.ca/your-government/open-data-catalogue.aspx), et son [dataset](http://data.vancouver.ca/datacatalogue/index.htm).
* [**Open data**](http://www.odata.org/), *a Web protocol for querying and updating data that provides a way to unlock your data and free it from silos that exist in applications today.*
* [**OSM Points of interest**](http://wiki.openstreetmap.org/wiki/Points_of_interest): *a POI is a feature on a map (or in a geodataset) that occupies a particular point, as opposed to linear features like roads or areas of landuse*.
* [**OSM Overpass API**](http://wiki.openstreetmap.org/wiki/Overpass_API), *a read-only API that serves up custom selected parts of the OSM map data.*
* [**Fulcrum API**](http://developer.fulcrumapp.com/api/fulcrum-api.html)*, exposes access to your data & pictures, form elements, account setup info and more through our RESTful API*. Voir aussi les applications [Fulcrum](http://fulcrumapp.com/), *cloud-based data collection*.
* [**Google Places**](http://www.google.com/places/) et l'[**API Places**](https://developers.google.com/places/documentation/), application Google utilisée pour alimenter Google Maps. Peut être questionnée directement via API. Liste des lieux supportés par l'API -> [lieux](https://developers.google.com/places/documentation/supported_types).
* [**OpenStreetMap's free tagging system**](http://wiki.openstreetmap.org/wiki/Map_Features) allows the map to contain unlimited data about its elements, like roads and buildings. The community agrees on certain key and value combinations for tags that are informal standards, allowing us to improve the style of the map and do analysis that relies on the attributes of features. [Tags](http://wiki.openstreetmap.org/wiki/Tags). [Category:Features](http://wiki.openstreetmap.org/wiki/Category:Features). [Amenity](http://wiki.openstreetmap.org/wiki/Map_Features#Amenity).
* [**GitHub API**](http://developer.github.com/v3/).
* [Designing Great API](http://blog.parse.com/2012/01/11/designing-great-api-docs/).

* [PHP The Right Way](http://www.phptherightway.com/). Conventions pour le code PHP.
* [PHPDocumentor](http://phpdoc.org/). Documentation automatique du code source PHP.

### Concepts

##### API

An application programming interface (API) is a source code based specification intended to be used as an interface by software components to communicate with each other. An API may include specifications for routines, data structures, object classes, and variables.

Référence complète -> [API](http://en.wikipedia.org/wiki/Application_programming_interface)

##### GeoHash
Geohash is a latitude/longitude geocode system invented by Gustavo Niemeyer when writing the web service at geohash.org, and put into the public domain. It is a hierarchical spatial data structure which subdivides space into buckets of grid shape.

Geohashes offer properties like arbitrary precision and the possibility of gradually removing characters from the end of the code to reduce its size (and gradually lose precision).

Référence complète -> [GeoHash](http://en.wikipedia.org/wiki/Geohash)

##### REST
Representational State Transfer (REST) is a style of software architecture for distributed systems such as the World Wide Web.

Référence complète -> [REST](http://en.wikipedia.org/wiki/Representational_state_transfer)

##### JSON
JSON, or JavaScript Object Notation, is a text-based open standard designed for human-readable data interchange. It is derived from the JavaScript scripting language for representing simple data structures and associative arrays, called objects. Despite its relationship to JavaScript, it is language-independent, with parsers available for many languages.

Référence complète -> [JSON](http://en.wikipedia.org/wiki/Json)

##### GeoJSON
GeoJSON is an open format for encoding a variety of geographic data structures. It is so named because it is based on JSON (JavaScript Object Notation). In fact, every GeoJSON data structure is also a JSON object, and thus JSON tools can also be used for processing GeoJSON data.

Référence complète -> [GeoJSON](http://en.wikipedia.org/wiki/GeoJSON)

### Outils

* [**Mou**](http://mouapp.com), éditeur Markdown pour Mac.
* [**GitHub**](http://github.com), web-based hosting service for software development projects that use the Git revision control system.
* [**AngularJS**](http://angularjs.org), open-source JavaScript framework.
* [**ioDocs**](http://www.mashery.com/product/io-docs), application pour générer la documentation d'un API. Voici un [tutoriel](http://spier.hu/2011/10/api-console-with-iodocs/).
* [**CouchDB**](https://blogs.apache.org/couchdb/), base de données NoSQL.
* [**Cradle**](https://github.com/cloudhead/cradle), client CouchDB pour Node.js.
* [**Leaflet**](http://leafletjs.com/), *a modern open-source JavaScript library for mobile-friendly interactive maps*.
* [**Leaflet Collect**](https://projects.bryanmcbride.com/LeafletCollect/), app expérimentale qui reproduit la collecte d'info comme Shareabouts , mais à l'aide de Leaflet.
* [**TileMill**](http://mapbox.com/tilemill/),* design studio you need to create stunning interactive maps.*

* [**Vagrant**](http://vagrantup.com/), *virtualized development made easy*. Tutoriels: [Vagrant: What, Why, and How](http://net.tutsplus.com/tutorials/php/vagrant-what-why-and-how/), [Building Vagrant Boxes With Veewee](http://www.ducea.com/2011/08/15/building-vagrant-boxes-with-veewee/).
* **REST Console**, plugin pour le navigateur Chrome.
* [**Modernizr**](http://modernizr.com/) – open-source JavaScript library that helps you build the next generation of HTML5 and CSS3-powered websites.
* [**Kartograph**](http://kartograph.org/), outils cartographique intéressant, SVG - VML.

### Ressources

* [**Bootstrap**](http://twitter.github.com/bootstrap/). *Sleek, intuitive, and powerful front-end framework for faster and easier web development.*
* [**GeoNames**](http://www.geonames.org/). The GeoNames geographical database covers all countries and contains over eight million placenames that are available for download free of charge. [GeoNames WebServices](http://www.geonames.org/export/web-services.html)

### Projets GitHub

* [Places](http://github.com/jibaku/places) is a django app (using geodjango) that allow you to manage geo-localized places. [Python]
* [geonames_php](https://github.com/emonidi/geonames_php) is a simple php class that uses cURL to connect with api.geonames.org and retrieve information.[PHP]
* [Snappy](https://github.com/KnpLabs/snappy) is a PHP5 library allowing thumbnail, snapshot or PDF generation from a url or a html page. It uses the excellent webkit-based wkhtmltopdf and wkhtmltoimage available on OSX, linux, windows.[PHP]
* [Ushahidi](https://github.com/ushahidi/Ushahidi_Web) is a platform that allows information collection, visualization and interactive mapping, allowing anyone to submit information through text messaging using a mobile phone, email or web form.[PHP]
* [privatesquare](http://straup.github.com/privatesquare/) is a simple web application to record and manage a database of foursquare check-ins.[PHP]
* [Flamework](https://github.com/exflickr/flamework) is a PHP web-application framework, born out of the processes and house-style developed at Flickr.com.[PHP]

* [**dotspotting**](https://github.com/Citytracking/dotspotting) is a tool to help people gather data about cities and make that data more legible. Site Web du projet -> [dotspotting](http://dotspotting.org/)[voir le blog](http://content.stamen.com/working_on_the_knight_moves)[PHP] 

* [showme-the-geojson](https://github.com/straup/showme-the-geojson/tree/gh-pages) is a web application for loading and displaying GeoJSON files on a map. [Site associé](http://straup.github.com/showme-the-geojson/).[javascript]
* [secondcrack](https://github.com/marcoarment/secondcrack) is a static-file Markdown blogging engine. http://www.marco.org/secondcrack [PHP]

* [Carbon](https://github.com/briannesbitt/Carbon) is simple API extension for DateTime with PHP 5.3+.
* [REST](https://github.com/Respect/Rest) is a thin controller for RESTful applications. [PHP]
* [Shield](https://github.com/enygma/shieldframework) A Security-Minded Microframework (based on Slim) [PHP]
* [Leaflet-Maps-Marker](https://github.com/robertharm/Leaflet-Maps-Marker). Pin, organize & show your favorite places through OpenStreetMap, Google Maps, Google Earth (KML), GeoJSON, GeoRSS or Augmented-Reality browsers. [www.mapsmarker.com](http://www.mapsmarker.com/) [PHP, javascript]
* [PHPMAiler](https://github.com/Synchro/PHPMailer). The classic email sending library for PHP.
* [Enplacify](https://github.com/straup/php-lib-enplacify) is designed to be used in conjuction with flamework (Flickr). Geolocation of machine tags. [PHP]
* [Composer](https://github.com/composer/composer) is a dependency manager tracking local dependencies of your projects and libraries. [PHP]

### Blogs & Articles

* [Conversion ShapeFile - GeoJson - SVG](http://techslides.com/online-shapefile-to-geojson-converter/)
* [API design for humans](http://37signals.com/svn/posts/3018-api-design-for-humans)

### INSPIRATION

* [Twitter API]()
* [Cicero API]()
* [FourSquare API](https://developer.foursquare.com/docs/) & [FourSquare Policies](https://developer.foursquare.com/overview/community)
* [Google Fusion](https://developers.google.com/fusiontables/docs/v1/using)
* [CartoDB Spacial SQL API](http://developers.cartodb.com/documentation/cartodb-apis.html)
* [37 Signals API Doc (GitHub)](https://github.com/37signals/api)
* [Campaign Monitor](http://www.campaignmonitor.com/api/account/#getting_countries)
* [GitHub API -> Changes Warning](http://developer.github.com/)
* [Integrate Desk](http://dev.desk.com/)
* [Knight Fondation](http://newschallenge.tumblr.com/)
* [BlightStatu](http://www.blightstatus.com/), le portail et le site de [New Orleans](http://blightstatus.nola.gov/).


### Frameworks RESTful PHP considérés
* [**FRAPI**](http://getfrapi.com/) is a  high-level API framework that powers web apps, mobiles services and legacy systems, enabling a focus on business logic and not the presentation layer.  FRAPI handles multiple media types, response codes and generating API documentation. FRAPI was originally built by echolibre to support the needs of their client’s web apps, and now it’s been open-sourced. 
générateur API pour PHP. [Source](https://github.com/frapi/frapi).

* [**RestLer**](http://luracast.com/products/restler/) is a multi-protocol and open source framework for exposing PHP classes and methods as a REST API.[Source](https://github.com/Luracast/Restler)

* [**Slim**](http://www.slimframework.com/) What began as a weekend project became a simple yet powerful PHP 5 framework to create RESTful web applications. The Slim micro framework is everything you need and nothing you don’t. Slim lets you build a complete PHP web service with only a single PHP file. Features include: RESTful routing, Named routes, Route passing, Route redirects, Route halting, Custom views, HTTP caching, Signed cookies, Custom 404 page, Custom 500 page, Error handling and Logging. [Source](https://github.com/codeguy/Slim). [Tutoriel](http://coenraets.org/blog/2011/12/restful-services-with-jquery-php-and-the-slim-framework/).

### Frameworks RESTful Node.js considérés

* [**Restify**](http://mcavage.github.com/node-restify/) is a node.js module built specifically to enable you to build correct REST web services. It borrows heavily from express (intentionally) as that is more or less the de facto API for writing web applications on top of node.js. [Source](https://github.com/mcavage/node-restify)
* [**Express**](expressjs) is a minimal and flexible node.js web application framework, providing a robust set of features for building single and multi-page, and hybrid web applications. [Tutoriel 1](http://coenraets.org/blog/2012/10/creating-a-rest-api-using-node-js-express-and-mongodb/). [Tutorial 2](http://fabianosoriani.wordpress.com/2011/08/15/express-api-on-node-js-with-mysql-auth/). [Source](https://github.com/visionmedia/express). À utiliser avec [Express-resource](https://github.com/visionmedia/express-resource) -> provides resourceful routing to express.
* [**Perfect API**](http://perfectapi.github.com/node-perfectapi/) Easier service APIs using Node.js. [Source](https://github.com/perfectapi/node-perfectapi).
* [**Api Server**](https://github.com/kilianc/node-apiserver) is a slim, fast, minimal API framework, built to provide you a flexible API consumable both in the browser and from other apps. It ships with JSON, JSONP (GET/POST) transports and a powerful fast routing engine OOTB. The source code is small, heavily tested and decoupled. Your API source will be well organized in context objects, allowing you to keep it in a meaningful maintainable way. [Source](https://github.com/kilianc/node-apiserver)
* [**Journey**](https://github.com/cloudhead/journey). Journey's goal is to provide a fast and flexible RFC 2616 compliant request router for JSON consuming clients. [Source](https://github.com/cloudhead/journey)
* [**ActionHero**](https://github.com/evantahler/actionHero) is a node.js API framework for both tcp sockets, web sockets, and http clients. The goal of actionHero are to create an easy-to-use toolkit for making reusable, scalable APIs. [Source](https://github.com/evantahler/actionHero)
* [**Ogre**](https://github.com/wavded/ogre) is a ogr2ogr web client written for nodejs. [Web Site](http://ogre.adc4gis.com/)

### Serveurs/Services dans les nuages

* [**Rackspace**](http://www.rackspace.com/)
* [**AppFog**](http://www.rackspace.com/)
* [**Amazon Elastic Compute Cloud (Amazon EC2)**](http://aws.amazon.com/ec2/)
* [**MapBox**](http://mapbox.com/),* online storage for TileMill MBTiles*.
* [**Google App Engine**](), Applications in the cloud, Python.
* [**Cloudant**](https://cloudant.com/), Serveur CouchDB dans les nuages.
* [**CartoDB**](http://cartodb.com/), to map and build location-aware apps.
* [**OpenShift**](https://openshift.redhat.com), is Red Hat's free, auto-scaling Platform as a Service (PaaS) for applications.

### Tests unitaires & test des APIs

* [api-easy](https://github.com/flatiron/api-easy), librairie node pour tester les APIs à l'aide de 'vow'. [Docs](http://flatiron.github.com/api-easy/). 
* [JSONLint](http://jsonlint.com/), pour tester la structure des documents JSON.
* [CSS Lint](http://csslint.net/) – an open source CSS code quality tool.


### Documentation

* [Docco](http://jashkenas.github.com/docco/) is a quick-and-dirty, hundred-line-long, literate-programming-style documentation generator. It produces HTML that displays your comments alongside your code. Comments are passed through Markdown, and code is passed through Pygments syntax highlighting. [Source](https://github.com/jashkenas/docco).

### ELASTICSEARCH
* [Loading Shapefiles into ElasticSearch](http://sproke.blogspot.ca/2011/07/loading-shapefiles-into-elasticsearch.html) via a Ruby script that convert ShapeFile to GeoJson. [Gist](https://gist.github.com/1117027)

# API
### Adresse URL de l'API
Sélection d'une adresse Web:

* http://lieuxmontrealais/api
* http://atlas.collectifquartier.org/lieuxmontrealais/api

ou avec un nom de projet différent …

* http://**local**.collectifquartier.org/api
* http://**mtlocal**.ca/api
* etc.

### Format
{ **Adresse de l'api** }/{ **version** }/{ **datasets** }/{ **dataset** }/{ **id** }?{ **options** }

### Hiérarchie des données


* **Niveau 1** - Jeux de données sous forme de liste de jeux de données(datasets)  
*ex.:{[id:1, nom:'csdm', desc:'description'],[..], [..]}*
	* **Niveau 2** - Jeu de données sous forme de liste de lieux  
*ex.:{id:1, nom:'csdm', desc:'desc', count:322, data:[{{[id:1, nom:'csdm', desc:'description'], etc.},{..},{..}…]}*
		* **Niveau 3** - Lieu, descriptif  
*ex.:{id:1, nom:'csdm', desc:'desc', count:322, data:[{},{},{}…]}*


### Requêtes en lecture (GET)


Liste des jeux de données (liste): **api/v1/datasets**

Accès à un jeu de données [numérique] (détails+liste): **api/v1/datasets/1**

Accès à un jeu de données [alpha-numérique] (détails+liste): **api/v1/datasets/csdm**

Options de sélection:  **api/v1/datasets/csdm?opt=ecole_primaire**

Requêtes préconçues (liste): **api/v1/datasets/reqs**

Requête préconçue: **api/v1/datasets/reqs/1**

Recherche (vs option SQL): **api/v1/search?q=**  

Recherche dans une ressource: **api/v1/datasets/csdm?q=ecole_primaire+ecole_secondairemet**

### Options

##### Sélection des variables pour un champ  
**?couleur**=rouge&**type**=primaire

##### Sélection des champs pour des réponses partielles  
**?fields**=title,media

#### Pagination

**?limit**=25&**offset**=50  
Par défaut -> **?limit**=25&**offset**=0

#### SQL

SQL avancé (en fonction de la technologie de la BD ou du moteur de recherche):  
**?sql**=select ecole_primaire from csdm, geo_arrondissement where ST_Within(ecole_primaire.geom, geo_arrondissement) AND geo_arrondissement IN(1,3,12);

### Métadonnées

Liste:
"_metadata":[{"totalCount":327,"limit":25,"offset":100}] }

## Données

### Structure

* **Niveau 1** - Jeux de données sous forme de liste de jeux de données(datasets)  
*ex.:{[id:1, nom:'csdm', desc:'description'],[..], [..]}*
	* **Niveau 2** - Jeu de données sous forme de liste de lieux  
*ex.:{id:1, nom:'csdm', desc:'desc', count:322, data:[{{[id:1, nom:'csdm', desc:'description'], etc.},{..},{..}…]}*
		* **Niveau 3** - Lieu, descriptif  
*ex.:{id:1, nom:'csdm', desc:'desc', count:322, data:[{},{},{}…]}*
	


