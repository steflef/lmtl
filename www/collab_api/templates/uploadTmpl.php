<? require 'uploadHeader.php' ?>
<div class="wrapper">

<div ng-controller="StepperCtrl" class="container" style="padding-top: 50px;">
    <h3>Ajoutez des données!</h3>


    <?if (!empty($infos)): ?>
<p>[$_FILES] <?= $infos ?>
    <? endif;?>

    <?if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>Attention!</strong>
        <ul>
            <?php
            foreach ($errors as $msg) {
                echo " <li>$msg</li>";
            }
            ?>
        </ul>
    </div>
    <? endif;?>

    <div class="upload_section" ng-cloak>
        <div ng-controller="TabPaneCtrl">
            <ul class="nav nav-tabs">
                <li  ng-repeat="tab in tabs"
                     class="{{tab.classActivated}} {{tab.classDisabled}}">
                    <a href="#"
                       onclick="return false;"
                       style="font-weight: bold;"
                       ng-show="tab.show"
                       ng-click="toStep(tab.step)">{{tab.text}}</a>
                </li>
            </ul>
        </div>

        <div ng-show="(step == 1)">
            <div class="well"
                 ng-controller="UploadCtrl"
                 ng-show="($parent.$$childTail.uData.length==0)"
                 style="border-left: 8px solid #ccc;padding-left: 16px">

                <div class="alert alert-error" ng-show="(status == 403)">
                    <strong>Votre session est expirée!</strong>
                    <a href="./upload" style="padding-left: 20px;">
                        <button class="btn">
                            <i class="icon icon-refresh"></i> Recharger la page
                        </button>
                    </a>
                </div>
                <div class="alert alert-error" ng-show="(status == 400)">
                    <strong>Erreur!</strong> {{msg}}.
                </div>
                <form id="ng_upload" action="upload?" onsubmit="return false;">
                    <input type="file"
                           size="40"
                           name="file_upload"
                           onchange="angular.element(this).scope().changeFormat(this.value.substring(this.value.length-3));"
                           style="font-size: 16px;line-height: 18px;">
                    <br>
                    <input id="fileFormat"
                           type="hidden"
                           ng-model="fileFormat"
                           ng-change="fileChange()"
                           >
                    <div style=" background-color: #eee;border-radius: 10px;margin-bottom: 10px;padding: 10px 16px 16px;"
                         ng-show="(fileFormat == 'csv' || fileFormat == 'txt')">
                        <div class="form-inline">
                            <label class="radio inline">
                                <input type="radio"
                                       ng-model="delimiter"
                                       name="optionsDelimiter" id="optionsDelimiter1" value="," checked="checked">
                                [&nbsp;<strong>,</strong>&nbsp;]
                            </label>
                            <label class="radio inline">
                                <input type="radio"
                                       ng-model="delimiter"
                                       name="optionsDelimiter" id="optionsDelimiter2" value=";">
                                [&nbsp;<strong>;</strong>&nbsp;]
                            </label>
                            <label class="radio inline">
                                <input type="radio"
                                       ng-model="delimiter"
                                       name="optionsDelimiter" id="optionsDelimiter3" value="|">
                                [&nbsp;<strong>|</strong>&nbsp;] <i class="icon-chevron-left"></i> <strong>Sélection du délimiteur</strong>
                            </label>
                        </div>
                    </div>
                    <input type="hidden" name="seal" value="">
                    <button ng-click="upload()" class="btn">Télécharger</button>
                    <button ng-click="showScope()" class="btn hide">Show Scope</button>
                </form>
                <div style="margin-top: 50px;">
                    LMTL importe actuellement les types de fichiers suivants:
                    <ul>
                        <li><b>CSV</b> - CSV (comma-separated values)</li>
                        <li><b>TXT</b> - Texte (CSV)</li>
                        <li><b>XLS</b> - Microsoft Excel</li>
                    </ul>

                    <p>Pour la phase expérimentale, seulement les 1000 premiers items d'un fichier seront importés</p>
                </div>
            </div>
        </div>

        <div ng-controller="GridCtrl">

            <div ng-show="(step == 2)">
                <div  id="grid" name="grid">
                    <div ng-show="(uData.features.length>0)">
                        <div class="hero-unit hero-unit-small-margin">
                            <h4>Complétez l'activation du jeu de données</h4>
                            <p>Utilisez les onglets pour naviguer et compléter les métadonnées pour ensuite procéder à la publication.</p>
                            <small>Consultez l'onglet publication pour les droits et licences attribuées aux documents.</small>
                        </div>
                        <div class="row">
                            <div class="span12">
                                <h4>Aperçu des données (3 premières lignes)</h4>
                            </div>
                            <div class="span12" style="overflow-x: auto;">
                                <table class="table table-striped">
                                    <caption class="hide">Aperçu des données (3 premières lignes)</caption>
                                    <thead>
                                    <tr>
                                        <!--<th ng-repeat="item in uHeaders">{{item.title}}</th>-->
                                        <th ng-repeat="item in uMetadata.properties">{{item.title}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="item in dataExtract">
                                        <td ng-repeat="p in uMetadata.properties">{{item.properties[p.title]}}</td>
                                    </tr>
                                    </tbody>
                                </table>


                            </div>
                            <div class="span12" style="margin-top: 20px;">
                                <button class="btn btn-danger  pull-left" ng-click="removeDataset()">Supprimer le document téléchargé / Charger un autre document</button>
                                <button class="hide btn btn-success  pull-right" ng-click="next()">Étape suivante</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div ng-show="(step == 3)">
                <div ng-show="(uData.features.length>0)">
                    <div class="hero-unit hero-unit-small-margin">
                        <h4>Géocodage</h4>
                        <p  ng-show="(uMetadata.geocoded==1)">Vos données sont géolocalisées.</p>
                        <p  ng-show="(uMetadata.geocoded==0)">Besoin d'aide pour géolocaliser vos données?</p>
                    </div>

                    <div ng-show="(uMetadata.geocoded==0)">
                        <p>Vous avez <strong>{{uData.features.length}} lieux </strong>à géocoder. Nous estimons le temps nécessaire au traitement de vos données à <strong>{{uData.length*0.65}} secondes</strong>.</p>
                        <p>Le champs "<strong>{{uMetadata.locField}}</strong>" à été identifié pour les données de localisation.</p>
                        <p>L'opération de géocodage ajoutera les champs longitude et latitude à votre jeu de données.</p>
                        <hr>
                        <div>
                            <h5>{{geoConsole}}</h5>
                            <div class="progress">
                                <div class="bar" ng-style="geocodingProgress" style="width: 0%;"></div>
                            </div>
                            <textarea class="span12"
                                      name="geocodeConsole"
                                      id="geocodeConsole"
                                      cols="40"
                                      rows="10">{{geoLog}}</textarea>
                        </div>
                        <button class="btn btn-info"  ng-disabled="geocodingBtn.isDisabled" ng-click="geocode()">Géocodage</button>
                        <button class="btn btn-error"  ng-show="geocodingBtn.isDisabled" ng-click="clearGeo()">Stop</button>
                        <button class="btn btn-info  hide"  ng-click="disable()">test</button>
                    </div>

                    <div ng-show="(uMetadata.geocoded==1)" class="row">
                        <div class="span12 map-wrapper">
                            <div map id="mapPort" style="height: 300px;"></div>
                        </div>

                    </div>
                </div>
            </div>

            <div ng-show="(step == 4)">
                <div class="hero-unit hero-unit-small-margin">
                    <h4>Métadonnées</h4>
                    <p>Données sur votre jeu de données. Le nom et la description seront utilisés pour la recherche des données.</p>
                    <!--<div class="control-group"><button class="btn" ng-click="viewScope()">View Scope</button></div>-->
                </div>
                <h4 class="hide">Métadonnées du jeu de données</h4>
                <form name="metaForm" class="form-horizontal">
                    <div class="control-group"><h4>Jeu de données</h4></div>
                    <div id="md" class="control-group info">

                        <label for="" class="control-label">{{uMetadata.form.name.label}}</label>
                        <div class="controls">
                            <input class="input-xxlarge" name="r_name" type="text" ng-model="uMetadata.form.name.value" required ng-minlength="5" placeholder="Nom du jeu de données ...">
                            <span class="help-inline" ng-show="metaForm.r_name.$error.required">Requis</span>
                            <span class="help-inline" ng-show="metaForm.r_name.$error.minlength">Minimum de 5 caractères</span>
                        </div>
                    </div>
                    <div class="control-group info">
                        <label for="" class="control-label">{{uMetadata.form.desc.label}}</label>
                        <div class="controls">
                            <textarea rows="4" class="input-xxlarge" name="r_description" ng-model="uMetadata.form.desc.value" required ng-minlength="10" placeholder="Description du jeu de données ..."></textarea>
                            <span class="help-inline" ng-show="metaForm.r_description.$error.required">Requis</span>
                            <span class="help-inline" ng-show="metaForm.r_description.$error.minlength">Minimum de 10 caractères</span>
                        </div>
                    </div>

                    <div class="control-group info">
                        <label for="" class="control-label">{{uMetadata.form.attributions.label}}</label>
                        <div class="controls">
                            <textarea rows="4" class="input-xxlarge" name="r_attributions" ng-model="uMetadata.form.attributions.value" required ng-minlength="10" placeholder="Provenance des données ..."></textarea>
                            <span class="help-inline" ng-show="metaForm.r_attributions.$error.required">Requis</span>
                            <span class="help-inline" ng-show="metaForm.r_attributions.$error.minlength">Minimum de 10 caractères</span>
                        </div>
                    </div>

                    <div class="control-group info">
                        <label for="" class="control-label">Catégories</label>
                        <div class="controls">
                            <div class="chosen-holder">
                                <select id="rao"
                                        multiple="multiple"
                                        tabindex="4"
                                        data-placeholder="Vos catégories associées au jeu de données"
                                        class="chzn-select"
                                        style="width:350px;"
                                        ng-model="cat.hash"
                                        ng-options="d.fr group by d.group for d in cat.options"
                                        chosen-rao>
                                </select>
                            </div>
                            <span class="help-inline">Max. de 3 catégories</span>
                        </div>
                    </div>

                    <div class="control-group"><h4>Catégorisation et affichage des lieux</h4></div>

                    <div class="control-group info">
                        <label for="" class="control-label">Nom du lieu</label>
                        <div class="controls">
                            <select ng-model="uMetadata.fieldName" ng-options='item.title as item.title for item in uMetadata.properties'>
                            </select>
                            <span class="help-inline">Attribut utilisé pour le nom des lieux</span>
                        </div>
                    </div>

                    <div class="control-group info">
                        <label for="" class="control-label">Description du lieu</label>
                        <div class="controls">
                            <select ng-model="uMetadata.fieldDescription" ng-options='item.title as item.title for item in uMetadata.properties'>
                            </select>
                            <span class="help-inline">Attribut utilisé pour la description des lieux</span>
                        </div>
                    </div>

                    <div class="control-group info">
                        <label for="" class="control-label">Étiquette</label>
                        <div class="controls">
                            <select ng-model="uMetadata.form.label.value" ng-options='item.title as item.title for item in uMetadata.properties'>
                            </select>
                            <span class="help-inline">Attribut utilisé pour les étiquettes cartographiques</span>
                            <input type="hidden" id="r_label_value" name="r_label_value" value="">
                        </div>
                    </div>
                    <div class="control-group info">
                        <label for="" class="control-label">Catégories</label>
                        <div class="controls">
                            <select ng-model="uMetadata.form.field_category.value" ng-options='item.title as item.title for item in uMetadata.properties'>
                                <option value="">Hérite du jeu de données</option>
                            </select>
                            <span class="help-inline">Utilisation d'un attribut pour la catégorisation?</span>
                            <input type="hidden" id="r_field_cat" name="r_field_cat" value="">
                        </div>
                    </div>

                    <div class="control-group"><h4>Description des Champs <small>(facultatif)</small></h4></div>
                    <div class="control-group" ng-repeat="item in uMetadata.properties">
                        <label class="control-label" for="input{{item.title}}">{{item.title}}</label>
                        <div class="controls">
                            <input class="input-mini" disabled="disabled" type="text" id="input{{item.title}}" placeholder="{{item.type}}" value="{{item.type}}">
                            <input ng-model="item.desc" class="input-xlarge" type="text" placeholder="Description ..." value="{{item.desc}}">
                        </div>
                    </div>

                    <div class="control-group"><h4>Document physique</h4></div>
                    <div class="control-group">
                        <label for="" class="control-label">URI</label>
                        <div class="controls">
                            <input name="r_url" type="text" class="input-xxlarge" disabled="disabled" value="{{uMetadata.fileName}}.{{uMetadata.fileExtension}}">
                            <input name="r_url" type="hidden" value="">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="" class="control-label">Format <i class="icon-file"></i></label>
                        <div class="controls">
                            <input name="r_format" type="text" value="{{uMetadata.fileExtension}}" class="input-large" placeholder="e.g. csv, html, xls, rdf, ..." disabled="disabled">
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="" class="control-label">Taille (Bytes)</label>
                        <div class="controls">
                            <input name="r_size" type="text" value="{{uMetadata.fileSize}}" class="long" disabled="disabled">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="" class="control-label">Mimetype</label>
                        <div class="controls">
                            <input name="r_mimetype" type="text" value="{{uMetadata.fileMime}}" disabled="disabled">
                        </div>
                    </div>

                </form>
            </div>


        </div> <!--GridCtrl-End-->

        <div ng-show="(step == 5)">

            <div class="hero-unit hero-unit-small-margin">
                <h4>Droits & licence</h4>
                <p>Informations nécessaires sur les licences, l'entreposage de l'information ...</p>
            </div>

            <h4>Licence</h4>
            <h5>ODbl</h5>
            <textarea style="width: 60%;height: 240px" cols="30" rows="10"><? require 'licence_odbl.php' ?></textarea>
            <form>
                <fieldset>
                    <legend>Consentement</legend>
                    <label class="checkbox">
                        <input type="checkbox" ng-model="certification.licence">
                        J'accèpte la publication publique sous la licence OBdl
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" ng-model="certification.right">
                        Je certifie avoir les droits de rendre public ces informations
                    </label>
                </fieldset>
            </form>
        </div>
        <div ng-show="(step == 6)">
            <div class="hero-unit hero-unit-small-margin">
                <h4>Publication</h4>
                <p>Les données publiées seront validées par Collectif Quartier qui se réserve le droit de non-publication en cas de doute sur l'exactitude des données, sur les droits associées ou sur la nature des données.</p>
            </div>
            <div class="row">
                <div id="validation" class="span12">
                    <h4>Vérication des informations</h4>
                </div>

                <div class="span4 validation-list">
                    <section>
                        <h5>Localisation des données</h5>
                        <ul>
                            <li><span ng-class="{'label-success': check.geo==true, 'label-important': check.geo==false}" class="label">
                            <i ng-class="{'icon-ok': check.geo==true, ' icon-exclamation-sign': check.geo==false}" class="icon-white"></i></span> Géolocalisation
                            </li>
                        </ul>
                    </section>

                </div>
                <div class="span4 validation-list">
                    <section>
                        <h5>Métadonnées</h5>
                        <ul>
                            <li><span ng-class="{'label-success': check.name==true, 'label-important': check.name==false}" class="label">
                            <i ng-class="{'icon-ok': check.name==true, ' icon-exclamation-sign': check.name==false}" class="icon-white"></i></span> Nom / Name
                            </li>
                            <li><span ng-class="{'label-success': check.description==true, 'label-important': check.description==false}" class="label">
                            <i ng-class="{'icon-ok': check.description==true, ' icon-exclamation-sign': check.description==false}" class="icon-white"></i></span> Description
                            </li>
                            <li><span ng-class="{'label-success': check.source==true, 'label-important': check.source==false}" class="label">
                            <i ng-class="{'icon-ok': check.source==true, ' icon-exclamation-sign': check.source==false}" class="icon-white"></i></span> Source
                            </li>
                            <li><span ng-class="{'label-success': check.categories==true, 'label-warning': check.categories==false}" class="label">
                            <i ng-class="{'icon-ok': check.categories==true, ' icon-exclamation-sign': check.categories==false}" class="icon-white"></i></span> Catégories
                            </li>
                        </ul>
                    </section>

                </div>
                <div class="span4 validation-list">
                    <section>
                        <h5>Consentement</h5>
                        <ul>
                            <li><span ng-class="{'label-success': certification.licence==true, 'label-important': certification.licence==false}" class="label">
                            <i ng-class="{'icon-ok': certification.licence==true, ' icon-exclamation-sign': certification.licence==false}" class="icon-white"></i></span> Licence
                            </li>
                            <li><span ng-class="{'label-success': certification.right==true, 'label-important': certification.right==false}" class="label">
                            <i ng-class="{'icon-ok': certification.right==true, ' icon-exclamation-sign': certification.right==false}" class="icon-white"></i></span> Droits
                            </li>
                        </ul>
                    </section>

                </div>
            </div>
            <hr>
            <div class="alert alert-error" ng-show="(validationAlert.visibility == 1)">
                <!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
                <strong>Attention!</strong> {{validationAlert.msg}}
            </div>

            <button ng-click="publish()" class="btn btn-success btn-large pull-left" >Publier</button>
        </div>
    </div>

</div>
<div class="push"><!--//--></div>
</div>

<? require 'footer_v1.php' ?>