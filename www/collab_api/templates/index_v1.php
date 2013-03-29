<? require 'header_v1.php' ?>

<div class="content w-scrollbar">
    <section ng-controller="CollectCtrl"  ng-init="init()">

        <div class="datasets w-scrollbar" >
            <div ng-repeat="dataset in datasets"
                 class="box"
                 ng-click="getPlaces(dataset.id)">
                {{dataset.name}} .{{dataset.id}}<span class="label-datacount pull-right">{{dataset.count}}</span></div>
            <button class="btn btn-mini" onclick="$('#panel').toggleClass('flipped');">FLIP</button>
        </div>

        <section class="flip-container">
            <div id="panel">
                <div class="places face">
                    <div class="box" ng-repeat="place in places">
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
                    <div class="hero-unit" style="padding:10px 20px;margin:10px;">
                        <h4>Complétez l'activation du jeu de données</h4>
                        <p>Utilisez les onglets pour naviguer et compléter les métadonnées pour ensuite procéder à la publication.</p>
                        <small>Consultez l'onglet publication pour les droits et licences attribuées aux documents.</small>
                        <br><small ng-model="place">({{place.id}} {{place.label}})</small>
                        <div>
                            <p>Tags</p>
                            <ul style="list-style: none;margin:0;">
                                <li style="border-bottom:1px solid #ccc;" ng-show="(val.length > 1)" ng-repeat="(tag, val) in place.tags"><small>{{tag}}<br>{{val}}</small></li>
                            </ul>
                        </div>


                        <br><button class="btn btn-mini" onclick="$('#panel').toggleClass('flipped');">C'EST FLIPPANT</button>
                        <br><button class="btn btn-mini" ng-click="getScope()">SCOPE</button>
                    </div>


                </div>
            </div>
        </section>

    </section>
    <div class="map-wrapper" ng-controller="MapCtrl">
        <div map id="mapPort"
             style="position:fixed;
             top:40px;
             bottom:0;
             left:540px;
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
</div>
<? require 'footer_v1.php' ?>