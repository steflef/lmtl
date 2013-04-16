<? require 'header_v1.php' ?>

<div class="content w-scrollbar perspective">
    <section ng-controller="CollectCtrl"  ng-init="init()">
        <div class="ui-overlay" style="display: none;" ng-show="modal.display" ng-animate="{show: 'panelIn', hide: 'fadeOut'}"></div>
        <!-- Modal -->
        <div id="myModal" ng-show="modal.display" ng-animate="{show: 'fadeIn', hide: 'fadeOut'}" class="modal" style="z-index: 3000;display: none;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" ng-click="modal.hide()">×</button>
                <h4>{{modal.header.title}}</h4>
            </div>
            <div class="modal-body">
                <p><strong>{{modal.body.head}}</strong></p>
                <p ng-repeat="item in modal.body.content">{{item}}</p>
            </div>
            <div class="modal-footer">
                <button ng-click="modal.btn.target()" ng-show="modal.btn.display" class="btn btn-small">{{modal.btn.text}}</button>
            </div>
        </div>

        <!-- Datasets Panel -->
        <div ng-class="datasets_panel" class="datasets_ui">
            <div  class="datasets_panel">
                <div class="datasets_list">
                    <div class="row" >
                        <div class="span9">
                            <p class="lead" style="margin-left: 4px;">Jeux de données</p>
                        </div>
                    </div>
                    <div class="row" >
                        <div class="span5"
                             style="border: none;"
                             ng-repeat="dataset in datasets | filter:query | orderBy:updated_at:reverseCheck"
                             ng-animate="{enter: 'fadeIn', leave: 'fadeOut'}">

                            <div class="{{dataset.id}} fbm watcher-well test-{{dataset.id|number}}">
                                <dl id="_{{dataset.id}}" class="event fs call" style="border: none;" >

                                    <dd>
                                        <a href="#"
                                           onclick="return false;"
                                           ng-click="getPlaces(dataset.id)"
                                           class="bold"
                                           style="font-size: 15px;">{{dataset.name}} ({{dataset.id}})</a>
                                    </dd>
                                    <dd><small>Dernière mise à jour {{dataset.updated_at | date:'MM/dd/yyyy @ h:mma'}}</small></dd>
                                    <dd ng-show="(dataset.count > 0)"><span class="bold">{{dataset.count | number}} lieux</span></dd>
                                    <dd ng-show="(dataset.count < 1)"><span class="bold">Aucun lieu pour l'instant</span></dd>
                                    <dd class="c-desc"><hr>{{dataset.desc}}<hr></dd>
                                    <dd>Catégories<br><span style="margin-right: 8px;" class="label" ng-repeat="category in dataset.categories">{{category.fr}}</span></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- List/Details Panel -->
        <section>
            <div class="location-toolbar">
                <ul>
                    <li style="float: right;">{{datasets[0].name}}</li>
                    <li><a href="#" onclick="return false;" ng-click="showDatasets()"><i class="icon-th-large icon-white"></i> Menu</a></li>
                    <li><a href="#" onclick="return false;" ng-click="addLocation()"><i class="icon-plus icon-white"></i> Ajouter un lieu</a></li>
                    <li ng-show="isMobile.any()"><a href="#" onclick="return false;" ng-click="geoLocation()"><i class="icon-map-marker icon-white"></i> Ma localisation</a></li>
                    <!--<li><a href="#" onclick="$('#myModal').modal({show:true,backdrop:'static'});return false;">Launch demo modal</a></li>-->
                    <li><a href="#" onclick="return false;" ng-show="(mode=='read')" ng-click="toggleMode()"><span class="badge badge-inverse">Mode Lecture</span></a></li>
                    <li><a href="#" onclick="return false;" ng-show="(mode=='edit')" ng-click="toggleMode()"><span class="badge badge-info">Mode Edition</span></a></li>

                </ul>
            </div>

            <section class="flip-container">
                <div id="panel" ng-class="place_panel">
                    <div class="places face">
                        <div  ng-show="(places.length == 0)" class="no-places">
                            <p>Aucun lieu dans ce jeu de données. <br>À vous de jouer!</p>
                            <p>
                                <a href="#" onclick="return false;" style="padding-top: 20px;">Ajouter un lieu</a>
                            </p>
                        </div>

                        <!-- List -->
                        <div ng-show="(places.length > 0)" class="box" ng-repeat="place in places">
                            <div class="left-space">
                                <div>
                                    <img src="./public/img/icons/mapiconscollection-health-education-cccccc-default/hospital-building.png" alt="icon">
                                </div>
                            </div>
                            <div class="right-space">
                                <section>
                                    <a href="#" class="pseudo-btn pull-right" ng-click="getPlace(place.id)" onclick="return false;">
                                        <span class="badge" style="padding-left: 4px;">
                                            <i class="icon-info-sign icon-white" style="margin-top:1px;"></i>
                                        </span>
                                    </a>
                                    <div class="title">{{place.label}}</div>
                                    <div class="title">Nom:{{place.name_fr}}</div>
                                    <div class="desc">Desc.: {{place.description}}</div>
                                    <div class="bar">
                                        <span class="label-soft" ng-repeat="category in place.categories">{{category.fr}}</span>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="place-details face">
                        <div class="hero-unit" style="padding:10px 20px;margin:8px;">
                            <div  ng-hide="(place.id)" class="loading">Chargement en cours</div>
                            <div ng-show="((mode=='read'))" ng-animate="{show: 'fadeIn', hide: 'panelOut'}" class="read-panel">
                                <div ng-show="(place.id)" ng-animate="{show: 'fadeIn', hide: 'fadeOut'}" class="place-details-content">
                                    <button class="close" onclick="return false;" ng-click="showList()">&times;</button>
                                    <ul>
                                        <li><a href="#" onclick="return false;" ng-show="(mode=='read')" ng-click="toggleMode()"><span class="badge badge-inverse">Mode Lecture</span></a></li>
                                        <li><a href="#" onclick="return false;" ng-show="(mode=='edit')" ng-click="toggleMode()"><span class="badge badge-info">Mode Edition</span></a></li>
                                    </ul>
                                    <!--<a class="pseudo-btn" style="cursor:pointer;"  onclick="return false;" ng-click="toggleMode()"><span class="label"><i class="icon-pencil icon-white"></i></span></a>-->
                                    <br>
                                    <h4>{{place.name_fr}}</h4>
                                    <p>{{place.description}}</p>
                                    <ul>
                                        <li class="level1-list">
                                            <small>iD<br>{{place.id}}</small>
                                        </li>
                                        <li class="level1-list">
                                            <small>Étiquette<br>{{place.label}}</small>
                                        </li>
                                    </ul>
                                    <hr>
                                    <div>
                                        <p>Localisation
                                            <button class="btn btn-mini btn-info pull-right" ng-click="setMapCenter()"><i class="icon-map-marker icon-white" style="margin-top:1px;"></i></button>
                                            <!--<a href="#" class="pseudo-btn pull-right" ng-click="getPlace(place.id)" onclick="$('#panel').toggleClass('flipped');"><span class="label" ><i class="icon-map-marker icon-white" style="margin-top:1px;"></i></span></a>-->
                                        </p>
                                        <ul>
                                            <li class="level2-list" ng-repeat="(tag, val) in place.location"><small>{{tag}}<br>{{val}}</small></li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div>
                                        <p>Contacts</p>
                                        <ul>
                                            <li class="level2-list"  ng-repeat="(tag, val) in place.contacts"><small>{{tag}}<br>{{val}}</small></li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div>
                                        <p>Attributs</p>
                                        <ul>
                                            <li class="level2-list" ng-show="(val.length > 1)" ng-repeat="(tag, val) in place.tags"><small>{{tag}}<br>{{val}}</small></li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="bar">
                                        <p>Catégories</p>
                                        <span class="label" ng-repeat="category in place.categories">{{category.fr}}</span>
                                    </div>
                                    <br><button class="btn btn-mini" ng-click="getScope()">SCOPE</button>
                                </div>
                            </div>

                            <div ng-show="((mode=='edit'))" ng-animate="{show: 'fadeIn', hide: 'panelOut'}" class="edit-panel">
                                <div  ng-show="((place.id))" class="place-details-content ">
                                    <!--<button class="close" onclick="$('#panel').toggleClass('flipped');">&times;</button>-->
                                    <button class="close" onclick="return false;" ng-click="showList()">&times;</button>
                                    <ul>
                                        <li><a href="#" onclick="return false;" ng-show="(mode=='read')" ng-click="toggleMode()"><span class="badge badge-inverse">Mode Lecture</span></a></li>
                                        <li><a href="#" onclick="return false;" ng-show="(mode=='edit')" ng-click="toggleMode()"><span class="badge badge-info">Mode Edition</span></a></li>
                                    </ul>
                                    <br>
                                    <!--<a class="pseudo-btn" onclick="return false;"><span class="label">sauvegarder</span></a>-->
                                    <!--<a class="pseudo-btn" onclick="return false;" ng-click="toggleMode()"><span class="label"><i class="icon-pencil icon-white"></i></span></a>-->
                        <!--            <br>
                                    <h4>Mode Édition</h4>-->
                                    <ul>
                                        <li class="level1-list">
                                            <small>Nom
                                                <br>
                                                <input type="text" ng-model="place.name_fr"/>
                                            </small>
                                        </li>
                                        <li class="level1-list">
                                            <small>Description<br>
                                                <textarea ng-model="place.description" name="" id="" cols="30" rows="10"></textarea>
                                            </small>
                                        </li>
                                        <li class="level1-list">
                                            <small>Étiquette
                                                <br>
                                                <input type="text" ng-model="place.label"/>
                                            </small>
                                        </li>
                                    </ul>
                                    <div>
                                        <p>Etiquette</p>
                                        <label for="selLabel">Selection <br>
                                            <small>
                                                <i class="icon-chevron-right"></i> <strong>{{selectedLabel[0]}}</strong>
                                            </small>
                                        </label>

                                        <select id="selLabel"
                                                size="4"
                                                style="width:280px;"
                                                ng-model="testSelected"
                                            >
                                            <option ng-repeat="(tag, val) in place.tags" value="{{$index}}">{{tag}}</option>

                                        </select>
                                        <div class="hide" ng-repeat="(tag, val) in place.tags">
                                            <label for="selectedLabel">
                                            <input name="selectedLabel" type="radio" value="{{$index}}"/>&nbsp;<span>{{tag}}</span></label>
                                        </div>

                                    </div>
                                    <hr>
                                    <div>
                                        <p>Localisation
                                            <button class="btn btn-mini btn-info pull-right" ng-click="setMapCenter()"><i class="icon-map-marker icon-white" style="margin-top:1px;"></i></button>
                                            <br>
                                            <button class="btn btn-mini btn-block btn-info" ng-click="getMapCenter()">Actualiser en fonction de la mire</button>
                                        </p>
                                        <ul>
                                            <li class="level2-list">
                                                <small>Adresse<br>
                                                    <input type="text" ng-model="place.location.address" updateModelOnBlur/>
                                                </small>
                                            </li>
                                            <li class="level2-list">
                                                <small>Ville<br>
                                                    <input type="text" ng-model="place.location.city" updateModelOnBlur/>
                                                </small>
                                            </li>
                                            <li class="level2-list">
                                                <small>Latitude<br>
                                                    <input type="text" disabled="disabled" ng-model="place.location.latitude" updateModelOnBlur/>
                                                </small>
                                            </li>
                                            <li class="level2-list">
                                                <small>Longitude<br>
                                                    <input type="text"  disabled="disabled" ng-model="place.location.longitude" updateModelOnBlur/>
                                                </small>
                                            </li>
                                            <li class="level2-list">
                                                <small>Code postal<br>
                                                    <input type="text" ng-model="place.location.postal_code" updateModelOnBlur/>
                                                </small>
                                            </li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div>
                                        <p>Contacts</p>
                                        <ul>
                                            <li class="level2-list">
                                                <small>Téléphone<br>
                                                    <input type="text" ng-model="place.contacts.phone" updateModelOnBlur/>
                                                </small>
                                            </li>
                                            <li class="level2-list">
                                                <small>Site Web<br>
                                                    <input type="text" ng-model="place.contacts.website" updateModelOnBlur/>
                                                </small>
                                            </li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div>
                                        <p>Attributs</p>
                                        <ul>
                                            <li class="level2-list" ng-repeat="(tag, val) in place.tags">
                                                <small>{{tag}}
                                                    <br>
                                                    <input type="text" ng-model="place.tags[tag]" updateModelOnBlur/>
                                                </small>
                                            </li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="bar">
                                        <p>Catégories</p>
                                        <label for="categoriePri">Categorie Primaire <br>
                                            <small>
                                                <i class="icon-chevron-right"></i> <strong>{{selectedCategorie[0].fr}}</strong>
                                            </small>
                                        </label>

                                        <select id="categoriePri"
                                                size="4"
                                                style="width:280px;"
                                                ng-model="selectedCategorie[0]"
                                                ng-options="d.fr group by d.group for d in cat.options"
                                                ng-selected="selectedCategorie[0]"
                                                >
                                        </select>
                                        <br>
                                        <label for="categorieSec">Categorie Secondaire<br>
                                            <small>
                                                <i class="icon-chevron-right"></i> <strong>{{selectedCategorie[1].fr}}</strong>
                                            </small>
                                        </label>

                                        <select id="categorieSec"
                                                size="4"
                                                style="width:280px;"
                                                ng-model="selectedCategorie[1]"
                                                ng-options="d.fr group by d.group for d in cat.options"
                                                ng-selected="selectedCategorie[1]"
                                            >
                                        </select>
                                        <button class="btn btn-mini" ng-click="getScope()">$SCOPE</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <div class="details_ui hide">
                <div class="details_panel">
                    <div class="details_content">

                    </div>
                </div>
            </div>


            <div class="map-wrapper" ng-controller="MapCtrl">
                <div>
                    <div map id="mapPort"></div>
                 </div>

                <div ng-show="((mode=='edit'))" class="crosshair"></div>

            </div>
        </section>

    </section>



</div>
<? require 'footer_v1.php' ?>