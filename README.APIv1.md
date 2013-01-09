# API v1

## Présentation de l'API  

L'API du projet Lieux montréalais permet d'accéder directement aux données collectées et partagées par les collaborateurs de [Collectif Quartiers](http://collectifquartier.org/). 

Il s'agit pour l'instant d'un API public en lecture seule.  
*À faire …*

### Architecture technologique

L'API est composé de documents à la fois statiques et dynamiques. La majorité des documents sont auto-générés sous forme de fichiers statiques json et stockés sur le serveur de Collectif Quartier. Cette façon de procéder permet des temps de réponse élévé du serveur. Les documents issus de requêtes dynamiques qui ne sont pas disponibles dans les fichiers tampons sont générés via des scripts PHP.

Les données sont stockées sur des serveurs distants à l'aide de services Web, notamment Google Drive et CartoDB.

### Authentification

Dans un premier temps, pour la phase de développement, l'API est public et ne requiers pas de clef d'autorisation. Il s'agit d'un API en lecture seule et limité à des requêtes simples. Les contribueurs doivent générés une clef d'autorisation via leurs comptes.

##### Clef d'API
Une clef d'autorisation sera nécessaire pour les contribueurs. Cette clef permet d'accéder aux lieux qui sont en attente de modération et d'utiliser les requêtes dynamiques.

## Accès à l'API

L'adresse URL de l'API sera déterminé par Collectif Quartier en fonction de la restructuration de son site Web. Voici quelques exemples possibles:  

* http://**lieuxmontrealais**/api
* http://atlas.collectifquartier.org/lieuxmontrealais/api  
ou si finalement intégré dans le site principal 
* http://collectifquartier.org/api/**lmtl** (retenu pour les exemples)


ou avec un nom de projet différent …

* http://**local**.collectifquartier.org/api
* http://**mtlocal**.ca/api  
* http://**lmtl**.ca/api
* etc.

Pour le reste du document, nous allons considéré le point d'entrée principal de l'API comme étant **http://collectifquartier.org/api/lmtl**

#### Version

La version de l'API doit être indiqué dans l'URL et suivre immédiatement l'URL d'accès.  
Nous sommes à la version 1 de l'API.  
Exemple: http://collectifquartier.org/api/lmtl/**v1**/   

#### Format

Toute les réponses sont en format **JSON**.  
Spécification du format disponible [ici](http://www.json.org/).

#### Résultat du service 

Toutes les requêtes produiront une réponse avec les composantes meta et reponses:
 
	{
	    meta: {
	    		code:200
	          },
	    response: {}
	}

## Points d'entrées de l'API

### Collections  
Regroupements de jeux de données en fonction d'une thématique ou d'un contributeur. 

##### Forme 
http://collectifquartier.org/api/lmtl/v1/**collections** */:id*

**-> Collections**    
--> Datasets  
---> Places

##### Paramètres

**?dump=true -> voir si utile ou si nous limitons le tout  **

##### Résultat du service  
$> curl http://collectifquartier.org/api/lmtl/v1/**collections**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     collections: [
	         {
	         	id: 1,
	         	slug: "csdm",
	         	name: "Données de la CSDM",
	         	description: "",
	         	datasetsCount: 2
	         },
	         {
	         	id: 2,
	         	slug: "",
	         	name: "",
	         	description: "",
	         	datasetsCount: 1
	         }
	     ]           
	    }
	}
$> curl http://collectifquartier.org/api/lmtl/v1/**collections/1**  
$> curl http://collectifquartier.org/api/lmtl/v1/**collections/csdm**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     collection: {
	         	  id: 1,
	         	  slug: "csdm",
	         	  name: "Données de la CSDM",
	         	  description: "",
	         	  datasetsCount: 2,
	         	  created_at: iso vs timestamp,
	         	  updated_at: ,
	         	  url: "http://www.csdm.qc.ca/",
	         	  notes: ""
	             }          
	    }
	}

----------------------------------------------

### Jeux de données
Jeux de données disponibles.

##### Forme  
http://collectifquartier.org/api/lmtl/v1/**collections** */:id*/**datasets** */:id?options*

-> Collections   
**--> Datasets**  
---> Places

##### Paramètres  
**?q=**  
Recherche dans un dataset les lieux en fonction de paramètres.
*À définir …*

##### Résultat du service
$> curl http://collectifquartier.org/api/lmtl/v1/collections/csdm/**datasets**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     collection: {
	     	      id: 1,
	     	      slug: "csdm",
	         	  name: "Données de la CSDM",
	         	  description: ""
	     },
	     datasets: [
	         {
	         	id: 1,
	         	slug: "ecoles_primaires",
	         	name: "École primaires de la CSDM",
	         	description: "Écoles publiques primaires et préscolaires de la CSDM",
	         	placesCount: 123
	         },
	         {
	         	id: 2,
	         	slug: "ecoles_secondaires",
	         	name: "École secondaires de la CSDM",
	         	description: "Écoles publiques secondaires de la CSDM",
	         	placesCount: 26
	         }
	     ]           
	    }
	}
$> curl http://collectifquartier.org/api/lmtl/v1/**collections/1**  
$> curl http://collectifquartier.org/api/lmtl/v1/**collections/csdm**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     collection: {
	     	      id: 1,
	     	      slug: "csdm",
	         	  name: "Données de la CSDM",
	         	  description: ""
	     },
	     dataset: {
	           id: 1,
	           slug: "ecoles_primaires",
	           name: "École primaires de la CSDM",
	           description: "Écoles publiques primaires et préscolaires de la CSDM",
	           placesCount: 123,
	           created_at: iso vs timestamp,
	           updated_at: ,
	           url: "http://www.csdm.qc.ca/",
	           notes: "",
	           license: "",
	           source: "",
	           sourceUrl: "",
	           verified: false,
	           
	           }      
	    }
	}


**Version #2 à évaluer, CKAN type.     <--**

$> curl http://collectifquartier.org/api/lmtl/v1/collections/csdm/**datasets**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     	     *language: "fr",    <--
	             *fields: [    <--
	     	     {
	     	       type: "int4",
	     	       id: "id"
	         	  },	     	     
	         	  {
	     	       type: "text",
	     	       id: "slug"
	         	  },
	         	  ...
	         	  
	     		  ],
	     datasets: [
	         {
	         	id: 1,
	         	slug: "ecoles_primaires",
	         	name: "École primaires de la CSDM",
	         	description: "Écoles publiques primaires et préscolaires de la CSDM",
	         	placesCount: 123
	         },
	         {
	         	id: 2,
	         	slug: "ecoles_secondaires",
	         	name: "École secondaires de la CSDM",
	         	description: "Écoles publiques secondaires de la CSDM",
	         	placesCount: 26
	         }
	     ],
	     *limit: 2,    <--
	     *offset: 0,    <--
	     *links: {    <--
	         start: "/api/lmtl/v1/collections/csdm/**datasets**?limit=2",
	         next: "/api/lmtl/v1/collections/csdm/**datasets**?limit=2&offset=2"
	     },
	     *total: 10    <--           
	    }
	}

----------------------------------------------

### Lieux
Lieux répertoriés.

##### Forme  
http://collectifquartier.org/api/lmtl/v1/collections/:id/datasets/:id/**places/:id**?options

-> Collections   
--> Datasets  
**---> Places**

##### Résultat du service
$> curl http://collectifquartier.org/api/lmtl/v1/collections/csdm/datasets/ecoles_primaires/**places**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	      dataset: {
	          id: 1,
	          slug: "ecoles_primaires",
	          name: "École primaires de la CSDM",
	          description: "Écoles publiques primaires et préscolaires de la CSDM",
	          placesCount: 123
	      },
	      places: [
	         {
                 id: 1,
                 slug: "annexe_charlevoix",
                 name: "Annexe Charlevoix",
                 description: "Écoles publiques primaires et préscolaires de la CSDM",
                 location: {
	                 address: "633, rue De Courcelle",
	                 lat: 40.721394,
	                 lng: -73.983994,
	                 postalCode: "H4C 3C7",
	                 city: "Montréal",
	                 province: "Québec",
	                 country: "Canada"
	             },
	             verified: false,
	             updated_at: 1357586281118,
	             uri: "http://collectifquartier.org/api/lmtl/v1/collections/csdm/datasets/ecoles_primaires/places/1",
	             icon: "http:// … /v1/.../ecole.png"
	         },
	         ...
	      ]           
	    }
	}
$> curl http://collectifquartier.org/api/lmtl/v1/collections/csdm/datasets/ecoles_primaires/**places/1**  
$> curl http://collectifquartier.org/api/lmtl/v1/collections/csdm/datasets/ecoles_primaires/**places/annexe_charlevoix**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     place: {
	         id: 1,
	         slug: "annexe_charlevoix",
	         name: "Annexe Charlevoix",
	         description: "Annexe Charlevoix",
	         contact: {
	             phone: "(514) 596-5670"
	             },
	         location: {
	             address: "633, rue De Courcelle",
	             crossStreet: "",
	             lat: 40.721394,
	             lng: -73.983994,
	             postalCode: "H4C 3C7",
	             city: "Montréal",
	             province: "Québec",
	             country: "Canada",
	             cc: "CDN"
	             },
	         cqUrl: "",
	         categories: [
	             {
	                 id: 328,
	                 name: "école",
	                 pluralName: "écoles",
	                 icon: "http:// … /v1/.../ecole.png",
	                 primary: true
	             },
	             {
	                 id: 412,
	                 name: "lieu d'enseignement",
	                 pluralName: "lieux d'enseignement",
	                 icon: "http:// … /v1/.../lieu_d_enseignement.png"
	             }
	             ],
	         verified: false,
	         created_at: 1357586281118,
	         updated_at: 1357586281118,
	         url: "http://www.csdm.qc.ca/RechercheEtablissement/810141.aspx",
             uri: "http://collectifquartier.org/api/lmtl/v1/collections/csdm/datasets/ecoles_primaires/places/1",
	         tags: ["école","csdm", ...],
	         license: "",
	         source: "",
	         sourceUrl: "http://www.csdm.qc.ca/RechercheEtablissement/810141.aspx",
	         verified: false,
	         extras: [
	             {
	                 name: "Clientèle étudiante",
	                 value: "97"
	             },
	             {
	                 name: "Équipements",
	                 value: "Bibliothèque, gymnase, local polyvalent pour des ateliers: cuisine, bricolage, arts, etc. "
	             },
	             {
	                 name: "Horaire",
	                 value: "de 8 h 5 à 11 h 25 et de 12 h 45 à 15 h 35"
	             }
	             ]  
	         }      
	    }
	}

----------------------------------------------	

### Requêtes préconçues  

Requêtes spécifiques mises en cache pour alléger le serveur et répondre à des besoins particuliers. Par exemple, le croisement entre plusieurs jeu de données ou une problématique ponctuelle. 

##### Forme

http://collectifquartier.org/api/lmtl/v1/**reqs** */:id*

##### Résultat du service
$> curl http://collectifquartier.org/api/lmtl/v1/**reqs**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     requests: [
	         {
	         	id: 1,
	         	slug: "csdm",
	         	name: "Données de la CSDM",
	         	description: "Description de la requête",
	         	_code: "SELECT * FROM x,y WHERE x.join = y.join AND …",
	         	placesCount: 25,
	         	created_at: 1357586281118,
	         	updated_at: 1357586281118
	         },
	         ...
	     ]           
	    }
	}
$> curl http://collectifquartier.org/api/lmtl/v1/**reqs/1**  
$> curl http://collectifquartier.org/api/lmtl/v1/**reqs/csdm**  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	      places: [
	         {
                 id: 1,
                 slug: "annexe_charlevoix",
                 name: "Annexe Charlevoix",
                 description: "Écoles publiques primaires et préscolaires de la CSDM",
                 location: {
	                 address: "633, rue De Courcelle",
	                 lat: 40.721394,
	                 lng: -73.983994,
	                 postalCode: "H4C 3C7",
	                 city: "Montréal",
	                 province: "Québec",
	                 country: "Canada"
	             },
	             verified: false,
	             updated_at: 1357586281118,
	             uri: "http://collectifquartier.org/api/lmtl/v1/collections/csdm/datasets/ecoles_primaires/places/1",
	             icon: "http:// … /v1/.../ecole.png"
	         },
	         ...
	      ]           
	    }
	}


----------------------------------------------
### Usage

Liste des points d'entrées et des paramètres disponibles.  

##### Forme

http://collectifquartier.org/api/lmtl/v1/**usage**

##### Résultat du service  

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     usage: {
	         text: "Texte explicatif",
	         url: "Lien vers cette page sur GitHub"
	     }    
	    }
	}

----------------------------------------------

### Categories

Liste des catégories de lieux.  
**Hiérarchique comme Foursquare ou à un seul niveau comme Google.**

##### Forme  
http://collectifquartier.org/api/lmtl/v1/**categories**

##### Résultat du service

	{
	    meta: {
	    		code:200
	    },
	    response: {
	     categories: [
	         {
	             id: 328,
	             name: "Arts & loisirs",
	             pluralName: "Arts et loisirs",
	             icon: "http:// … /v1/.../arts_loisirs.png",
	             categories: [
	                 {
	                     id: 328,
	                     name: "Arts & loisirs",
	                     pluralName: "Arts et loisirs",
	                     icon: "http:// … /v1/.../arts_loisirs.png",
	                     categories: []
	          		  },
	          		  ...
	          	  ]	  
	          },
	          {
	             id: 412,
	             name: "CEGEP et universités",
	             pluralName: "CEGEP et universités",
	             icon: "http:// … /v1/.../lieu_d_enseignement.png",
	             categories: [...]
	          },
	          ...
	      ]     
	    }
	}

----------------------------------------------

### Paramètres généraux

##### Sélection des variables pour un champ  
**?couleur**=rouge&**type**=primaire

##### Sélection des champs pour des réponses partielles  
**?fields**=title,media

##### Pagination  
**?limit**=25&**offset**=50  
Par défaut -> **?limit**=25&**offset**=0

----------------------------------------------

### **Erreurs de services** 

Il y aura toujours une réponse, même s'il y a une erreur, sauf pour le type 500. Le code d'erreur est obtenu via la variable code de l'objet meta de la réponse. 


#### Résultat du service
 
	{
	    meta: {
	    		code: 401,
	    		errorDetail: "not_authorized"
	          },
	    response: {}
	}

#### Liste des codes d'erreurs

*À compléter … directement de Foursquare*

<table>
          <tbody><tr>
            <td>400 (Bad Request)</td>
            <td>Any case where a parameter is invalid, or a required parameter is missing. This includes the case where no OAuth token is provided and the case where a resource ID is specified incorrectly in a path.</td>
          </tr>
          <tr>
            <td>401 (Unauthorized)</td>
            <td>The OAuth token was provided but was invalid.</td>
          </tr>
          <tr>
            <td>403 (Forbidden)</td>
            <td>The requested information cannot be viewed by the acting user, for example, because they are not friends with the user whose data they are trying to read.</td>
          </tr>
          <tr>
            <td>404 (Not Found)</td>
            <td>Endpoint does not exist.</td>
          </tr>
          <tr>
            <td>405 (Method Not Allowed)</td>
            <td>Attempting to use POST with a GET-only endpoint, or vice-versa.</td>
          </tr>
          <tr>
            <td>409 (Conflict)</td>
            <td>The request could not be completed as it is. Use the information included in the response to modify the request and retry.</td>
          </tr>
          <tr>
            <td>500 (Internal Server Error)</td>
            <td>Foursquare’s servers are unhappy. The request is probably valid but needs to be retried later.</td>
          </tr>
        </tbody>
   </table>


<table>
          <tbody><tr>
            <td>invalid_auth</td>
            <td>OAuth token was not provided or was invalid.</td>
          </tr>
          <tr>
            <td>param_error</td>
            <td>A required parameter was missing or a parameter was malformed. This is also used if the resource ID in the path is incorrect.</td>
          </tr>
          <tr>
            <td>endpoint_error</td>
            <td>The requested path does not exist.</td>
          </tr>
          <tr>
            <td>not_authorized</td>
            <td>Although authentication succeeded, the acting user is not allowed to see this information due to privacy restrictions.</td>
          </tr>
          <tr>
            <td>rate_limit_exceeded</td>
            <td>Rate limit for this hour exceeded.</td>
          </tr>
          <tr>
            <td>deprecated</td>
            <td>Something about this request is using deprecated functionality, or the response format may be about to change.</td>
          </tr>
          <tr>
            <td>server_error</td>
            <td>Server is currently experiencing issues. Check <a href="http://status.foursquare.com">status.foursquare.com</a> for updates.</td>
          </tr>
          <tr>
            <td>other</td>
            <td>Some other type of error occurred.</td>
          </tr>
        </tbody>
        </table>
        
## Licenses associées aux jeux de données

*À faire: sélectionner une license ou permettre de multiples licenses.*

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

##### Présentation des termes aux utilisateurs, exemples:
* [Summary of OpenStreetMap Contributor Terms](http://www.osmfoundation.org/wiki/License/Contributor_Terms_Summary)  
* [OpenStreetMap Contributor Terms ](http://www.osmfoundation.org/wiki/License/Contributor_Terms/FR) 
* [Liste des contributeurs de OSM](http://wiki.openstreetmap.org/wiki/Contributors)  


## Tests 

L'API du projet **Lieux montréalais** est testé par les applications suivantes:

* [api-easy](https://github.com/flatiron/api-easy), librairie node pour tester les APIs à l'aide de 'vow'. [Docs](http://flatiron.github.com/api-easy/). 
* [JSONLint](http://jsonlint.com/), pour tester la structure des documents JSON.
* **REST Console**, plugin pour le navigateur Chrome.


## Documentation pour les développeurs

La documentation est générée à l'aide des outils suivants:

* **Docco** is a quick-and-dirty, hundred-line-long, literate-programming-style documentation generator. It produces HTML that displays your comments alongside your code. Comments are passed through Markdown, and code is passed through Pygments syntax highlighting.  
-> Excellent pour documenter du code sans objet.  
[Source](https://github.com/jashkenas/docco).

* [**ioDocs**](http://www.mashery.com/product/io-docs), application pour générer la documentation d'un API. Voici un [tutoriel](http://spier.hu/2011/10/api-console-with-iodocs/).  
-> Super pour générer une interface de tests pour les développeurs.

* Documents statiques en format Markdown.
	


