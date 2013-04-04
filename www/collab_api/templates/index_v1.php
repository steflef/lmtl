<? require 'header_v1.php' ?>

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
                                    <button class="btn btn-mini pull-right" ng-click="getPlace(place.id)" onclick="$('#panel').toggleClass('flipped');"><i class="icon-pencil" style="margin-top:1px;"></i></button>
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
                            <div  ng-show="(place.id)" class="place-details-content">
                                <button class="close" onclick="$('#panel').toggleClass('flipped');">&times;</button>
                                <br>
                                <h4>{{place.name_fr}}</h4>
                                <p>{{place.description}}</p>
                                <ul style="list-style: none;margin:0;">
                                    <li style="border-bottom:1px solid rgb(150, 150, 150);border-left:4px solid rgb(150, 150, 150);padding-left:8px;margin-bottom: 4px;">
                                        <small>iD<br>{{place.id}}</small>
                                    </li>
                                    <li style="border-bottom:1px solid rgb(150, 150, 150);border-left:4px solid rgb(150, 150, 150);padding-left:8px;margin-bottom: 4px;">
                                        <small>Étiquette<br>{{place.label}}</small>
                                    </li>
                                </ul>
                                <hr>
                                <div>
                                    <p>Attributs</p>
                                    <ul style="list-style: none;margin:0;">
                                        <li style="border-bottom:1px solid #ccc;border-left:4px solid #ccc;padding-left:8px;margin-bottom: 4px;" ng-show="(val.length > 1)" ng-repeat="(tag, val) in place.tags"><small>{{tag}}<br>{{val}}</small></li>
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
                <div map id="mapPort"
                     style="position:fixed;
             top:40px;
             top:70px;
             bottom:0;
             left:340px;
             width:100%;
             z-index: 1;">
                    <div id="map_bar" class="progressbar">
                        <div></div>
                    </div>
                </div>
                <div class="map-pull">
                    <i class="icon-chevron-down icon-white"></i>
                </div>
            </div>
        </section>

    </section>



</div>
<? require 'footer_v1.php' ?>