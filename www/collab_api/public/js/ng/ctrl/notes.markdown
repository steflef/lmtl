#Front-end Job Interview Questions (QUESTIONS DE RECRUTEMENT)
Source: [Front-end Developer Interview Questions](https://github.com/darcyclarke/Front-end-Developer-Interview-Questions)


## General Questions:

- Êtes-vous sur Twitter ?
**(R) Oui, prof_lebrun.**
	* Si oui, qui suivez-vous ?
    - **(R) Une quarentaine de personne.
    [Un mix de développeurs Web, géomatique, applications que j'utilise]**

- Êtes-vous sur GitHub ?
**(R)** **Oui, steflef.**
	* Si oui, donnez quelques exemples de dépôt que vous suivez.
    - **(R) Je suis beaucoup de dépôts. J'ai presque 500 dépôts étoilés et je suis 8 groupes.**
    *[Leaflet, Angular, Bootstrap, ext.]*

* A quels blogs êtes-vous abonné ?
    - **(R)** **Je suis abonné à beaucoup de blogs, le tout intégré à un lecteur de fils.**
    *Javascript, Viz, Web Design, Coding, Design, Tech, etc.*

* Quel logiciel de gestion de versions avez-vous déjà utilisé ? (Git, SVN etc.)
    - **(R) Git & SVN**

* Quel est votre environnement de développement préféré ? (OS, Editor, Browsers, Tools etc.)
    - **(R) Mac, PHPStorm & Coda, Chrome & Firefox, Terminal, Nodejs, Yeoman, Transmit, Omnigraffle, Photoshop, etc**

* Pouvez-vous décrire votre workflow lorsque vous créez une page web ?
    - **(R)** Ça dépend vraiment de l'équipe et des specs.
    1. **new HTML template via PHPStorm**
    2. **CSS Bootstrap**
    3. **TODO**

* Pouvez-vous décrire la différence entre amélioration progressive et dégradation progressive ?
	* Un point bonus si vous décrivez des méthodes de détection
	- **(R)** **Une amélioration progressive part d'une base commune alors que la dégradation progressive part de la meilleure des situations.
Possibilité d'utiliser Modernizer pour détecter ce qui est disponible.**

* Expliquez ce que le terme "HTML sémantique" signifie.
    - **(R) Ajouter des balises et attributs qui permettent de mieux comprendre la structure d'un document.
    Par exemple, les balises HTML5 footer, nav ou les attributs microformats disponibles.**

* Quel naviguateur et outils de débogage web utilisez-vous ?
    - **(R) Chrome ou Firefox, IE par la suite. Outils dév. intégrés aux navigateurs + Jasmine BDD, Selenium, PHPUnit, Phantom, log, access file, HTTP Live, REST simulator.**

* Comment optimisez vous les performances de vos page web (assets/resources) ?

	* **La concaténation des fichiers**
	* **La minification des fichiers**
	* **L'utilisation d'un Content Delivery Network (CDN)**
	* **La mise en cache**
	* **etc…**

* Pourquoi est-il préférable de disposer ses assets sur des domaines différents ?
    - **(R) Possibilité de télécharger en paralèle jusqu'à 6 ressources, mais dépend du navigateur.**

* Donnez 3 façons qui permettent de réduire le temps de chargement d'une page. (perçu ou réel)
    1. **Éviter les redirections (3xx)**
    2. **Utiliser des Expiration Cache**
    3. **Gzip**
    4. **Minimiser les # de requêtes HTTP**
    5. **CDN à proximité**
    6. **Maximiser l'utilisation des caches**

* Si vous démarrez sur un projet existant, que votre prédécesseur a utilisé des tabulations pour indenter son code et que vous utilisez des espaces, que faites-vous ?
	* Vous proposez d'utiliser quelque chose comme EditorConfig (http://editorconfig.org)
	* **(R) Rester fidèle aux conventions**

* **Développez un simple slideshow**
	* **(R) Un point bonus si vous le faites sans JS**

* Quel outils utilisez vous pour tester la performance de votre code ?
	* **(R) JSPerf (http://jsperf.com/)**

* Si vous pouviez maitriser parfaitement une technologie cette année, laquelle serait-ce ?
    - **(R) Angularjs**

* Expliquez l'importance des standards et des organisations les édictant.
	- **(R) Standards bodies such as the World Wide Web Consortium (W3C) were created as forums to establish agreement across the industry and among vendors. Other standards groups have formed since, most notably the HTML5-focused Web Hypertext Application Technology Working Group (WHATWG). While there is often political maneuvering involved, the goal of each of these standards bodies is to find agreement and thereby formulate standards, often referred to as specifications, across the industry on the technologies that drive the web, including HTML and CSS.**

* What is FOUC? How do you avoid FOUC?
    - **(R) Flash of unstyle content, lorsqu'il y a des import dans le CSS ou que ce dernier est bas de page**

## Questions spécifiques à HTML :

* Qu'est-ce qu'un `doctype` fait, et combien pouvez-vous en nommer ?
    - **(R) Un doctype est un Document Type Declaration et fait référence à un Document Type Definition (DTD) qui explique au navigateur comment interpréter les éléments de la page.**
        1. **html**
        2. **html 4.01 strict,transitional (support for deprecated tags & document access),frameset**
        3. **xhtml 1.0 strict,transitional,frameset**
        4. **xhtml 1.1**
        - **strict: elements:center,font,iframe,strike,u Attributes:align,height, etc.**

* Quelle est la différence entre les modes `standard` et `quirks`
    - **[Box model] Std = W3C, width exclude padding & borders**
    - **[White-space: pre] IE Win -> strict mode only**
    - **[margin: auto] IE Win -> strict mode only**
    - **unitless values ignored in strict mode**
    - **[img] Std => display: inline; Quirks => display: block;**
    - **[overflow: visible]  Std => flow out if too long; IE Strech container; Opera Same as IE in Quirks mode**
    - **font size of table cells  Std => font-size:80% mean 80% of body text. Quirks = 80% of browser default size (16px);**

* Quelles sont les limitations des pages XHTML ?
    - **(R) IE ne supporte pas le XHTML. Lorsque envoyé comme text/html, interprété mais autrement non compris.**
    - ** Doivent êtres envoyées avec le content-type `application/xhtml+xml`.**

 	* Y a t'il des problèmes à envoyer des pages avec le content-type `application/xhtml+xml` ?
	    - **(R) Voir IE 7**

* Comment servez vous une page avec du contenu multilingue ?
    - **[html 4.01]**

            <html lang="en-US">
    - **[xhtml text/html]**

            <html lang="en-US" xml:lang="en-US">
    - **[xhtml]**

            <html xml:lang="en-US">

	* À quoi devez-vous faire attention quand vous designez ou développez des pages pour des sites multilingues ?
    - **Longueur des phrases**
    - **Texte dans les images**
    - **Date, argent**
    - **Font Face (accents)**

* Pouvez vous utliser une syntaxe XHTML en HTML5 ? **(R) Oui**

* Comment utilisez vous du XML en HTML5 ? **(R) Comme en XHTL**

* À quoi servent les `data-` attributes ?
    - **(R) Entreposer des valeurs arbitraires.**

* Que sont les modèles de contenus en HTML4, et diffèrent-ils de HTML5 ?
    - **(R) [HTML 4.01]**

            <!ENTITY % block "P | %heading; | %list; | %preformatted; | DL | DIV | NOSCRIPT | BLOCKQUOTE | FORM | HR | TABLE | FIELDSET | ADDRESS">and<!ENTITY % inline "#PCDATA | %fontstyle; | %phrase; | %special; | %formctrl;">
- **(R) [HTML 5]**
    - Metadata content
    - Flow content
    - Sectioning content
    - Heading content
    - Phrasing content
    - Embedded content
    - Interactive content

* Décrivez la différence entre cookies, sessionStorage, et localStorage.
    - **Cookies: transmis entre le client et le serveur, max 4k.**
    - **sessionStorage: cache sur le client, temporaire pour la session.**
    - **localStorage: cache sur le client, persistance.**

## Questions spécifiques à JavaScript :
* Quelles sont les librairies JavaScript que vous avez utilisé ?
    - **Angularjs, Backbone, jQuery, Leaflet, et plein d'autres pour des tâches particulières**

* En quoi JavaScript est-il différent de Java ?
    - **Javascript est un langage orienté objet à prototype faiblement typé. Ecmascript.**
    - **(Prototype) les principales interfaces sont fournies par des objets qui ne sont pas des instances de classes, mais qui sont chacun équipés de constructeurs permettant de créer leurs propriétés, et notamment une propriété de prototypage qui permet d'en créer des objets héritiers personnalisés.**

* Qu'est-ce qu'une hashTable ?
    - **Associative array (PHP).**

* Que sont les variables `undefined` et `undeclared` ?
    - **The undefined property indicates that a variable has not been assigned a value.**
    - **undeclared lorsque la variable n'existe pas encore lors de son utilisation (seulement en mode strict)**

* Qu'est-ce qu'une closure, et pourquoi / comment en utiliseriez vous une ?
    - **A "closure" is an expression (typically a function) that can have free variables together with an environment that binds those variables (that "closes" the expression).**
    - **a closure is a function to which the variables of the surrounding context are bound by reference**

	* Votre patten favori pour les créer ? (seulement pour les IIFEs, fonctions-expressions évoquées immédiatement)
    - **MODULE EXPORT**

        var MODULE = (function () {
        	var my = {},
        		privateVariable = 1;

        	function privateMethod() {
        		// ...
        	}

        	my.moduleProperty = 1;
        	my.moduleMethod = function () {
        		// ...
        	};

        	return my;
        }());


* Quel est un cas d'utilisation typique pour une fonction anonyme ?
    - Dans une boucle for

        for (var i = 0; i < 10; i++) {
          document.getElementById('box' + i).onclick = (function(index){
            return function() {
              alert('You clicked on box #' + index);
            };
          })(i);
        }

* Expliquer les pattern JavaScript de "Module", et quand vous l'utiliseriez.
    - **Isolation, scope et namespacing**
	* Un point bonus si vous parlez de namespacing
	* Que se passe t'il si vos modules ne sont pas namespacés ?
	- **Possibilité de collision**

	    var ns = ns || {};

* Comment organisez vous votre code (pattern modulaire, héritage classique ?)

* Quelle est la différence entre objets hôtes et objets natifs ?

* Différence entre :
```javascript
function Person(){}
var person = Person() // et
var person = new Person()
```
* Quelle est la différence entre `.call` et `.apply` ?
* Expliquez `Function.prototype.bind` ?
* Quand optimisez-vous votre code ?
* Pouvez vous expliquer comment fonctionne l'héritage en JavaScript ?
* Quand utiliseriez-vous `document.write()` ?
  * La plupart des pubs utilisent encore `document.write()` même si son utilisation est découragée.
* Quelle est la différence entre détection de feature, inférence de feature, et l'utilisation de l'User Agent ?
* Expliquez un call AJAX dans le plus grand détail possible
* Expliquez comment JSONP fonctionne (et en quoi ca n'est pas réellement de l'AJAX)
* Avez-vous déjà utilisé du templating en JavaScript ?
	* Si oui, quelles librairies avez-vous utilisé (Mustache.js, Handlebars etc…)
* Expliquez "hoisiting".
* Quest-ce que le FOUC, comment l'évitez vous ?
* Décrivez le bubbling d'événement.
* Quelle est la différence entre un "attribut" et une "propriété"
* Pourquoi étendre les objets natifs JavaScript n'est pas une bonne idée ?
* Pourquoi étendre les objets natifs est une bonne idée ?
* Quelle est la différence entre les événements `load` et `ready` du document?
* Quelle est la différence entre `==` et `===` ?
* Expliquez comment vous récupéreriez un paramètre de querystring de l'URL du browser.
* Expliquez la politique d'origine commune et ses implication en JavaScript.
* Expliquez la délégation d'événement.
* Expliquez les patterns d'héritage en JavaScript.
* Faites fonctionner ceci :
```javascript
[1,2,3,4,5].duplicator(); // [1,2,3,4,5,1,2,3,4,5]
```
* Décrivez une stratégie de mémoization (pour éviter la répétition des calculs) en JavaScript.
* Qu'est-que qu'une instruction 'Ternaire', et qu'indique le mot 'Ternaire'
* Qu'est-ce que l'arité d'une fonction ?

## Exemples de code JS

```javascript
~~3.14
```
Question: Que retourne ce code ?
**Réponse: 3**

```javascript
"je suis un bouffeur de lasagne".split("").reverse().join("");
```
Question: Que retourne ce code ?
**Réponse: "engasal ed rueffuob nu sius ej"**

```javascript
( window.foo || ( window.foo = "bar" ) );
```
Question: Que retourne window.foo?
**Réponse: "bar"**
seulement si window.foo n'était pas défini ou faux, autrement il garde sa valeur

```javascript
var foo = "Hello"; (function() { var bar = " World"; alert(foo + bar); })(); alert(foo + bar);
```
Question: Que se passe-t-il à l'éxécution de ce code ?
**Réponse: "Hello World" & ReferenceError: bar is not defined**

```javascript
var foo = [];
foo.push(1);
foo.push(2);
```
Question: Quelle est la valeur de foo.length ?
**Réponse: `2`

```javascript
var foo = {};
foo.bar = 'hello';
```
Question: Quelle est la valeur de foo.length ?
**Réponse: `undefined`


## Questions spécifiques à jQuery :

* Expliquez le chainage
* Expliquez 'deferred'
* Décrivez des optimisations spécifiques à jQuery que vous pouvez implémenter.
* Que fait `.end()` ?
* Pourquoi et comment restreindriez-vous un événement bindé à un un namespace ?
* Nommez 4 valeurs différentes que vous pouvez passer à la méthode jQuery.
	* Sélecteur (string), HTML (string), Callback (function), HTMLElement, objet, tableau, tableau d'éléments, objet jQuery, etc…
* Qu'est-ce que la queue d'effets (fx queue) ?
* Quelle est la différence entre `.get()`, `[]`, et `.eq()` ?
* Quelle est la différence entre `.bind()`, `.live()`, et `.delegate()`?
* Quelle est la différence entre `$` et `$.fn`? Ou bien seulement : qu'est-ce que `$.fn`.
* Optimisez ce sélecteur :
```javascript
$(".foo div#bar:eq(0)")
```

## Questions spécifiques CSS :

* Décrivez ce que fait un fichier CSS Reset, et pourquoi il est utile.
* Décrivez les Floats, et comment ils fonctionnent.
* Quelles sont les différentes méthodes de clearing des floats, et laquelle est appropriée pour chaque contexte ?
* Expliquez les sprites CSS, et comment vous les implémenteriez sur une page ou un site.
* Quelles sont vos techniques favorites de remplacement d'images, et comment les utilisez vous ?
* hacks de propriétés CSS, fichiers .css inclus avec des commentaires conditionnels, ou autre ?
* Comment servez vous vos pages pour les browsers aux fonctionnalités réduites ?
	* Quelles techniques / process utilisez vous ?
* Quelles sont les différentes manières de masquer du contenu (en le laissant disponible pour les lecteurs d'écran) ?
* Avez-vous déjà utilisé un grid system, et si oui, lequel préférez-vous ?
* Avez-vous déjà implémenté des media queries, ou des layouts/CSS spécifiques aux mobiles ?
* déjà touché au styling de SVG ?
* Comment optimisez vous vos pages pour le print ?
* Quelques trucs pour écrire du CSS efficace ?
* Utilisez vous des préprocesseurs CSS ? (SASS, Compass, Stylus, LESS)
	* Si oui, décrivez ce que vous aimez et n'aimez pas des préprocesseurs que vous avez utilisé.
* Comment implémenteriez-vous une créa web qui utilise des typos non standard ?
	* Webfonts (services comme: Google Webfonts, Typekit etc.)
* Expliquez comment un browser détermine ce qui matche un sélecteur CSS.



## Questions optionnelles, pour le geste.

* Quel est le tuc le plus cool que vous ayez jamais codé, de quoi êtes-vous le plus fier ?
* Connaissez vous le signe de gang du HTML5 ?
* Êtes-vous ou avez-vous déjà été sur un bateau ?
* Quelles sont vos parties favorites des outils de développement que vous utilisez.
* Avez-vous des projets chouchous ? Quel genre ?
* Expliquez 'cornify'.
* Sur un bout de papier, écrivez les lettres A B C D E verticalement.
  Maintenant mettez les en ordre descendant sans écrire une ligne de code.
	* Regardez si ils retournent le bout de papier.
* Pirate ou Ninja.
	* Bonus si c'est une doublette, et si il y a une bonne raison. (+2 pour des Zombies Singes Ninja Pirates)
* Si c'était pas du Dev, vous feriez quoi ?
* Ou est Carmen San Diego ?
	* Indice : La réponse est toujours fausse.
* Quelle est votre feature favorite de IE ?
* Complétez cette phrase Brendan Eich et Doug Crockford sont les __________ de JavaScript.
* jQuery : superbe librairie, ou la meilleure ? Discutez.