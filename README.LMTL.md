# API

### Licenses associées aux jeux de données

 La libéralisation des données se fait par le biais de licences qui fixent les conditions dans lesquelles ces données pourront être copiées, diffusées, réutilisées. Voir l'[article wikipedia](http://fr.wikipedia.org/wiki/Open_data) sur le sujet.

Les données disponibles devraient, au sens du droit de la propriété intellectuelle, êtres du domaine public associées à une licence libre. Ainsi chaque jeu de données (datasets) doit être distribué avec sa source et sa license. Un lien vers la license est essentiel. 

Voici quelques licenses typiques ([source](http://fr.wikipedia.org/wiki/Open_data)):

[Open Data Commons](http://opendatacommons.org/faq/licenses/), regroupe PDDL, ODC-by et ODC-bl.

**PDDL**  
La licence Public Domain and License (PDDL) donne la possibilité d’utiliser, de copier, de modifier, de redistribuer une base de données sans aucune restriction. C’est une licence libre de tout droit, de type domaine public. Les données peuvent donc être exploitées de façon totalement libre et l’auteur abandonne son droit moral.

**ODC-by**  
Cette licence que l'on retrouve sous le sigle ODC-by autorise l’utilisation, la copie, la redistribution, la modification, la réalisation de travaux dérivés de la base de données, sous réserve d’indiquer le nom de l’auteur de la base de données originale. On retrouve ces principes dans la licence Creative Commons By.

**Open Database License (ODbL)**  
La licence ODC-ODbl38 est fondée sur le droit d'auteur et le droit sui generis des bases de données. Elle donne la possibilité aux utilisateurs de copier, distribuer, utiliser, modifier et produire une œuvre dérivée à partir d’une base de données sous réserve de la redistribuer sous les mêmes conditions imposées par la licence originale. Elle implique aussi d’indiquer le nom de l’auteur de la base de données d’origine. On peut citer à titre d’exemple le projet Open Street Map qui place ses bases de données sous licence ODC-ODbl comme plusieurs collectivités locales en France (Paris, Nantes, Toulouse, ...).

**Creative Commons **  
À l'exception de la licence CC-0 spécialement conçue à cette fin, les licences de la famille Creative Commons ne sont pas adaptées à un usage sur une base de données. Voir cet [article](http://fr.wikipedia.org/wiki/Open_data) pour plus de détails.

Quelque exemples:

* United Nations Development Programme ([Open UNPD](https://data.undp.org/)) -> Creative Commons Attribution License (CC-BY)

##### Liens intéressants 
* [Summary of OpenStreetMap Contributor Terms](http://www.osmfoundation.org/wiki/License/Contributor_Terms_Summary)  
* [OpenStreetMap Contributor Terms ](http://www.osmfoundation.org/wiki/License/Contributor_Terms/FR) 
* [Liste des contributeurs de OSM](http://wiki.openstreetmap.org/wiki/Contributors)  




### Interface de programmation (API) inspirantes

* [**Open.undp.org**](http://open.undp.org/), API JSON de la United Nations Development Programme.

* [**API Cicero**](http://cicero.azavea.com/docs), API Cicero. Exemple de bonne doumentation d'un API.


* [Cicero API]()
* [FourSquare API](https://developer.foursquare.com/docs/) & [FourSquare Policies](https://developer.foursquare.com/overview/community)
* [Google Fusion](https://developers.google.com/fusiontables/docs/v1/using)
* [CartoDB Spacial SQL API](http://developers.cartodb.com/documentation/cartodb-apis.html)
* [37 Signals API Doc (GitHub)](https://github.com/37signals/api)
* [Campaign Monitor](http://www.campaignmonitor.com/api/account/#getting_countries)
* [GitHub API -> Changes Warning](http://developer.github.com/)
* [Integrate Desk](http://dev.desk.com/)

* [BlightStatu](http://www.blightstatus.com/), le portail et le site de [New Orleans](http://blightstatus.nola.gov/).


### Exemple de documentation d'API

* [**GeoReport v2**](http://wiki.open311.org/GeoReport_v2), spec de l'API GeoReport v2, utilisé pour l'Open 311.
* [**Inquiry API v1**](http://wiki.open311.org/Inquiry_v1), spec de l'API Inquiry v1 pour la ville de New-York, utilisé pour l'Open 311.
* [**Cicero Query by location API**](http://cicero.azavea.com/docs/query_by_location.html), exemple de requête par localisation de l'API Cicero.
* [**USA Today Article API**](http://developer.usatoday.com/docs/read/articles), API des articles du quortidien USA Today.
* [**Klout API**](http://developer.klout.com/iodocs), documentation de l'API Klout générée par [ioDocs](http://www.mashery.com/product/io-docs).
* [**GitHub API**](http://developer.github.com/v3/).
* [Twitter API]()

### Projets similaires OpenSources

* [**Dotspotting**](https://github.com/Citytracking/dotspotting) is a tool to help people gather data about cities and make that data more legible. Site Web du projet -> [dotspotting](http://dotspotting.org/)[voir le blog](http://content.stamen.com/working_on_the_knight_moves)[PHP] 
* [**Shareabouts**](http://shareabouts.org), *is a mapping tool to gather crowd sourced public input. Use it to collect suggested locations and comments, in a social, engaging process*. Exemple: [Avoid ATM surcharges](http://nosur.shareabouts.org).
[Shareabouts Roadmap](https://github.com/openplans/shareabouts/wiki/roadmap), état des lieux du projet Shareabouts (données locales via questionnaires).
* [**Code for America**](http://codeforamerica.org/), *Code for America helps governments work better for everyone with the people and the power of the web*. Projets [Local Data](http://codeforamerica.org/?cfa_project=localdata) ( [portail du projet en développement](http://golocaldata.org) ) et [311 labs](http://codeforamerica.org/?cfa_project=311-labs) en particulier.
* [**OSM Overpass API**](http://wiki.openstreetmap.org/wiki/Overpass_API), *a read-only API that serves up custom selected parts of the OSM map data.*
* [**OpenStreetMap's free tagging system**](http://wiki.openstreetmap.org/wiki/Map_Features) allows the map to contain unlimited data about its elements, like roads and buildings. The community agrees on certain key and value combinations for tags that are informal standards, allowing us to improve the style of the map and do analysis that relies on the attributes of features. [Tags](http://wiki.openstreetmap.org/wiki/Tags). [Category:Features](http://wiki.openstreetmap.org/wiki/Category:Features). [Amenity](http://wiki.openstreetmap.org/wiki/Map_Features#Amenity).
* [**Leaflet Collect**](https://projects.bryanmcbride.com/LeafletCollect/), app expérimentale qui reproduit la collecte d'info comme Shareabouts , mais à l'aide de Leaflet.
* [**OSM Points of interest**](http://wiki.openstreetmap.org/wiki/Points_of_interest): *a POI is a feature on a map (or in a geodataset) that occupies a particular point, as opposed to linear features like roads or areas of landuse*.

### Projets similaires 

* [**Fulcrum API**](http://developer.fulcrumapp.com/api/fulcrum-api.html)*, exposes access to your data & pictures, form elements, account setup info and more through our RESTful API*. Voir aussi les applications [Fulcrum](http://fulcrumapp.com/), *cloud-based data collection*.

* [**Google Places**](http://www.google.com/places/) et l'[**API Places**](https://developers.google.com/places/documentation/), application Google utilisée pour alimenter Google Maps. Peut être questionnée directement via API. Liste des lieux supportés par l'API -> [lieux](https://developers.google.com/places/documentation/supported_types).
* **FourSquares**

### Outils et services divers

* [**Mou**](http://mouapp.com), éditeur Markdown pour Mac.
* [**TileMill**](http://mapbox.com/tilemill/),* design studio you need to create stunning interactive maps.*


### Projets GitHub pertinants

* [Places](http://github.com/jibaku/places) is a django app (using geodjango) that allow you to manage geo-localized places. [Python]
* [geonames_php](https://github.com/emonidi/geonames_php) is a simple php class that uses cURL to connect with api.geonames.org and retrieve information.[PHP]
* [Snappy](https://github.com/KnpLabs/snappy) is a PHP5 library allowing thumbnail, snapshot or PDF generation from a url or a html page. It uses the excellent webkit-based wkhtmltopdf and wkhtmltoimage available on OSX, linux, windows.[PHP]
* [Ushahidi](https://github.com/ushahidi/Ushahidi_Web) is a platform that allows information collection, visualization and interactive mapping, allowing anyone to submit information through text messaging using a mobile phone, email or web form.[PHP]
* [privatesquare](http://straup.github.com/privatesquare/) is a simple web application to record and manage a database of foursquare check-ins.[PHP]
* [Flamework](https://github.com/exflickr/flamework) is a PHP web-application framework, born out of the processes and house-style developed at Flickr.com.[PHP]

* [**Leaflet Collect**](https://projects.bryanmcbride.com/LeafletCollect/), app expérimentale qui reproduit la collecte d'info comme Shareabouts , mais à l'aide de Leaflet.

* [showme-the-geojson](https://github.com/straup/showme-the-geojson/tree/gh-pages) is a web application for loading and displaying GeoJSON files on a map. [Site associé](http://straup.github.com/showme-the-geojson/).[javascript]
* [secondcrack](https://github.com/marcoarment/secondcrack) is a static-file Markdown blogging engine. http://www.marco.org/secondcrack [PHP]

* [Carbon](https://github.com/briannesbitt/Carbon) is simple API extension for DateTime with PHP 5.3+.
* [REST](https://github.com/Respect/Rest) is a thin controller for RESTful applications. [PHP]
* [Shield](https://github.com/enygma/shieldframework) A Security-Minded Microframework (based on Slim) [PHP]
* [Leaflet-Maps-Marker](https://github.com/robertharm/Leaflet-Maps-Marker). Pin, organize & show your favorite places through OpenStreetMap, Google Maps, Google Earth (KML), GeoJSON, GeoRSS or Augmented-Reality browsers. [www.mapsmarker.com](http://www.mapsmarker.com/) [PHP, javascript]
* [PHPMAiler](https://github.com/Synchro/PHPMailer). The classic email sending library for PHP.
* [Enplacify](https://github.com/straup/php-lib-enplacify) is designed to be used in conjuction with flamework (Flickr). Geolocation of machine tags. [PHP]
* [Composer](https://github.com/composer/composer) is a dependency manager tracking local dependencies of your projects and libraries. [PHP]

### Blogs & articles & autres ressources

* [Conversion ShapeFile - GeoJson - SVG](http://techslides.com/online-shapefile-to-geojson-converter/)
* [API design for humans](http://37signals.com/svn/posts/3018-api-design-for-humans)
* [Knight Fondation](http://newschallenge.tumblr.com/)

* [Designing Great API](http://blog.parse.com/2012/01/11/designing-great-api-docs/).

* [PHP The Right Way](http://www.phptherightway.com/). Conventions pour le code PHP.
* [**City of Vancouver Open Data Catalogue**](http://vancouver.ca/your-government/open-data-catalogue.aspx), et son [dataset](http://data.vancouver.ca/datacatalogue/index.htm).
* [**Open data**](http://www.odata.org/), *a Web protocol for querying and updating data that provides a way to unlock your data and free it from silos that exist in applications today.*  
* [**BeyondFog, Websockets and More: 20 awesome node.js npm modules we use every day**](http://blog.beyondfog.com/websockets-and-more-20-awesome-node-js-npm-modules-we-use-every-day/#.ULzXuOOe_Xc), article sur les technologies utilisées.

## Quincaillerie logiciel retenue pour le serveur

Les composantes principales:  

* OS Linux Ubuntu
* Serveur Apache 2
* PHP 5.3 ou +
* PostgreSQL avec modules PostGIS et HStore


### Tests

* [api-easy](https://github.com/flatiron/api-easy), librairie node pour tester les APIs à l'aide de 'vow'. [Docs](http://flatiron.github.com/api-easy/). 
* [JSONLint](http://jsonlint.com/), pour tester la structure des documents JSON.
* **REST Console**, plugin pour le navigateur Chrome.


### Documentation du code source

* **Docco** is a quick-and-dirty, hundred-line-long, literate-programming-style documentation generator. It produces HTML that displays your comments alongside your code. Comments are passed through Markdown, and code is passed through Pygments syntax highlighting.  
-> Excellent pour documenter du code sans objet.  
[Source](https://github.com/jashkenas/docco).

* [**ioDocs**](http://www.mashery.com/product/io-docs), application pour générer la documentation d'un API. Voici un [tutoriel](http://spier.hu/2011/10/api-console-with-iodocs/).  
-> Super pour générer une interface de tests pour les développeurs.

### Moteur de recherche
* ElasticSearch, moteur de recherche.

### Services

* [**GeoNames**](http://www.geonames.org/). The GeoNames geographical database covers all countries and contains over eight million placenames that are available for download free of charge. [GeoNames WebServices](http://www.geonames.org/export/web-services.html)
* **Google Map API**
* **Yahoo Map API**
* **MapQuest API**
* [**Metro Extracts**](http://metro.teczno.com/#montreal), Extraits de la BD OpenStreetMap pour Montréal.
* [**GeoCommons**](http://geocommons.com), Répertoire de données géolocalisées avec outils d'analyses.
* [**MapBox, serving faster maps**](http://mapbox.com/blog/building-mapbox-fast-map-hosting-stack/), article sur les technologies utilisées.

## Architecture préliminaire
### Adresse URL de l'API
Sélection d'une adresse Web:

* http://**lieuxmontrealais**/api
* http://atlas.collectifquartier.org/lieuxmontrealais/api  
ou si finalement intégré dans le site principal 
* http://collectifquartier.org/api/**lmtl** (retenu pour les exemples)


ou avec un nom de projet différent …

* http://**local**.collectifquartier.org/api
* http://**mtlocal**.ca/api  
* http://**lmtl**.ca/api
* etc.

### Format
**Adresse de l'api**/**:version**/**endpoint**/**:id**?**:options**

### EndPoints

**Jeux de données**  
http://collectifquartier.org/api/lmtl/v1/**datasets** */:id?options*

**Collections**  
(regroupements de jeux de données pour un téléchargement unique)  
http://collectifquartier.org/api/lmtl/v1/**collections** */:id*

**Requêtes préconçues**  
http://collectifquartier.org/api/lmtl/v1/**reqs** */:id*

**Recherche**  
http://collectifquartier.org/api/lmtl/v1/**search** *?options*

#### Détail ->  Requêtes en lecture (GET)

Liste des jeux de données (liste): **api/v1/datasets**

Accès à un jeu de données [numérique] (détails+liste): **api/v1/datasets/1**

Accès à un jeu de données [alpha-numérique] (détails+liste): **api/v1/datasets/csdm**

Options de sélection:  **api/v1/datasets/csdm?opt=ecole_primaire**

Requêtes préconçues (liste): **api/v1/datasets/reqs**

Requête préconçue: **api/v1/datasets/reqs/1**

Recherche (vs option SQL): **api/v1/search?q=**  

Recherche dans une ressource: **api/v1/datasets/csdm?q=ecole_primaire+ecole_secondairemet**

##### Options

###### Sélection des variables pour un champ  
**?couleur**=rouge&**type**=primaire

###### Sélection des champs pour des réponses partielles  
**?fields**=title,media

###### Pagination

**?limit**=25&**offset**=50  
Par défaut -> **?limit**=25&**offset**=0

##### SQL

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
	


