<? require 'header_v1.php' ?>
    <!-- Modal -->
    <div id="myModal" class="modal hide fade" style="z-index: 3000" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4>Ajouter/Editer un lieu</h4>
        </div>
        <div class="modal-body">
            <p>One fine body…</p>
            <p ng-repeat="p in places">One fine body…{{val}}</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-small">Sauvegarder</button>
        </div>
    </div>
<div class="content w-scrollbar perspective">
    <section ng-controller="CollectCtrl"  ng-init="init()">

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
                             ng-repeat="dataset in datasets | filter:query | orderBy:updated_at:reverseCheck">

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

        <section>
            <div class="location-toolbar">
                <ul>
                    <li style="float: right;">{{datasets[0].name}}</li>
                    <li><a href="#" onclick="return false;" ng-click="showDatasets()"><i class="icon-th-large icon-white"></i> Menu</a></li>
                    <li><a href=""><i class="icon-plus icon-white"></i> Ajouter un lieu</a></li>
                    <li ng-show="isMobile.any()"><a href="#" onclick="return false;" ng-click="geoLocation()"><i class="icon-map-marker icon-white"></i> Ma localisation</a></li>
                    <li><a href="#" onclick="$('#myModal').modal({show:true,backdrop:'static'});return false;">Launch demo modal</a></li>
                </ul>
            </div>

            <section class="flip-container">
                <div id="panel">
                    <div class="places face">
                        <div  ng-show="(places.length == 0)" class="no-places">
                            <p>Aucun lieu dans ce jeu de données. <br>À vous de jouer!</p>
                            <p>
                                <a href="#" onclick="return false;" style="padding-top: 20px;">Ajouter un lieu</a>
                            </p>
                        </div>
                        <div ng-show="(places.length > 0)" class="box" ng-repeat="place in places">
                            <div class="left-space">
                                <div>
                                    <img src="./public/img/icons/mapiconscollection-health-education-cccccc-default/hospital-building.png" alt="icon">
                                </div>
                            </div>
                            <div class="right-space">
                                <section>
                                    <!--<button class="btn btn-link pull-right" ng-click="getPlace(place.id)" onclick="$('#panel').toggleClass('flipped');"><span class="badge" style="padding-left: 4px;"><i class="icon-info-sign icon-white" style="margin-top:1px;"></i></span></button>-->
                                    <a href="#" class="pseudo-btn pull-right" ng-click="getPlace(place.id)" onclick="$('#panel').toggleClass('flipped');"><span class="badge" style="padding-left: 4px;"><i class="icon-info-sign icon-white" style="margin-top:1px;"></i></span></a>
                                    <!--<button class="btn btn-mini pull-right" ng-click="getPlace(place.id)" onclick="$('#panel').toggleClass('flipped');"><i class="icon-info-sign icon-white" style="margin-top:1px;"></i></button>-->
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

                    <div class="place-details face">
                        <div class="hero-unit" style="padding:10px 20px;margin:8px;">
                            <div  ng-hide="(place.id)" class="loading">Chargement en cours</div>
                            <div style="display:none;"  class="place-details-content">
                                <button class="close" onclick="$('#panel').toggleClass('flipped');">&times;</button>
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
                                    <a href="#" class="pseudo-btn pull-right" ng-click="getPlace(place.id)" onclick="$('#panel').toggleClass('flipped');"><span class="label" ><i class="icon-map-marker icon-white" style="margin-top:1px;"></i></span></a>
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
                                <button class="btn btn-mini" ng-click="showDatasets()">DATASETS</button>
                            </div>
                            <div  ng-show="((place.id))" class="place-details-content ">
                                <button class="close" onclick="$('#panel').toggleClass('flipped');">&times;</button>
                                <br>
                                <h4>Édition</h4>


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
                                <hr>
<!--                                <div>
                                    <p>Localisation
                                        <a href="#" class="pseudo-btn pull-right" ng-click="getPlace(place.id)" onclick="$('#panel').toggleClass('flipped');"><span class="label" ><i class="icon-map-marker icon-white" style="margin-top:1px;"></i></span></a>
                                        <br>
                                        <a href="#" class="pseudo-btn" ng-click="getPlace(place.id)" onclick="$('#panel').toggleClass('flipped');"><span class="label" >Actualiser en fonction de la mire</span></a>
                                    </p>

                                    <ul>
                                        <li class="level2-list" ng-repeat="(tag, val) in place.location">
                                            <small>{{tag}}<br>
                                                <input type="text" ng-model="place.location[tag]" updateModelOnBlur/>
                                            </small>
                                        </li>
                                    </ul>
                                </div>
                                <hr>-->
                                <div>
                                    <p>Localisation (!)
                                        <a href="#" class="pseudo-btn pull-right" ng-click="setMapCenter()" onclick="return false;"><span class="label" ><i class="icon-map-marker icon-white" style="margin-top:1px;"></i></span></a>
                                        <br>
                                        <a href="#" class="pseudo-btn" ng-click="getMapCenter()" onclick="return false;"><span class="label" >Actualiser en fonction de la mire</span></a>
                                        <br>
                                        <a href="#" class="pseudo-btn" ng-click="reverseGeocoding()" onclick="return false;"><span class="label" >Géocodage Inverse</span></a>
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
<!--                                <div>
                                    <p>Contacts</p>
                                    <ul>
                                        <li class="level2-list" ng-repeat="(tag, val) in place.contacts">
                                            <small>{{tag}}<br>
                                                <input type="text" ng-model="place.contacts[tag]" updateModelOnBlur/>
                                            </small>
                                        </li>
                                    </ul>
                                </div>
                                <hr>-->
                                <div>
                                    <p>Contacts (!)</p>
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
                                        <li class="level2-list" ng-show="(val.length > 1)" ng-repeat="(tag, val) in place.tags">
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
                                </div>

                                <br><button class="btn btn-mini" ng-click="getScope()">SCOPE</button>
                                <button class="btn btn-mini" ng-click="showDatasets()">DATASETS</button>
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

                <div class="crosshair"></div>

            </div>
        </section>

    </section>



</div>
<? require 'footer_v1.php' ?>