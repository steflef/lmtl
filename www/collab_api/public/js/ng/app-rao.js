angular.module('appRao', ['ngSanitize'])

    .config(function ($routeProvider, $locationProvider) {
//        $routeProvider.
//            when('/details/:idCall', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/details.html", controller:DetailsCtrl}).
//            //when('/home', {controller:HomeCtrl}).
//            otherwise({redirectTo:'/home'});
    })
    .directive('toolbar', function () {
        var linker = function(scope, element, attrs) {
            scope.$broadcast('newMenu', {id:'rao'});
        };

        return{
            restrict:'E',
            replace: true,
            templateUrl:'../public/js/ng/tmpl/toolbar_partial.html',
            link:linker
        }
    })
    .directive('map', function () {

        var linker = function(scope, element, attrs) {
            //console.log(scope);
            scope.map = new L.Map(attrs.id, {'scrollWheelZoom':false});
            scope.markersLayer = new L.LayerGroup();
            scope.markers = new L.MarkerClusterGroup({showCoverageOnHover:false});
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                osmAttrib = 'Map data © openstreetmap contributors',
                osm = new L.TileLayer(osmUrl, {minZoom:10, maxZoom:16, attribution:osmAttrib}),
                osmBrightUrl = 'http://amqc.ca:8888/v2/OSMBrightSJSR/{z}/{x}/{y}.png',
                osmBright = new L.TileLayer(osmBrightUrl, {minZoom:10, maxZoom:16, attribution:osmAttrib}),
                heatmap = new L.TileLayer.HeatCanvas({},{'step':0.5, 'degree':HeatCanvas.LINEAR, 'opacity':0.6}),
                mapCenter = new L.LatLng(origin.lat, origin.lon),
                drawControl = new L.Control.Draw({
                    position:'topright',
                    polygon:{
                        title:'Filtre polygonal',
                        allowIntersection:false,
                        drawError:{
                            color:'#b00b00',
                            timeout:1000
                        },
                        shapeOptions:{
                            //color:'#bada55'
                            color:'#000000'
                        }
                    },
                    circle:{
                        title:'Filtre circulaire',
                        shapeOptions:{
                            color:'#000000'
                        }
                    },
                    rectangle:{
                        title:'Filtre rectangulaire',
                        shapeOptions:{
                            color:'#000000'
                        }
                    },

                    polyline:false,
                    marker:false
                });
            scope.map
                .addControl(drawControl)
                .attributionControl.setPrefix('');

            var drawnItems = new L.LayerGroup();
            scope.drawnItems = drawnItems;
            scope.map.on('draw:poly-created', function (e) {
                drawnItems.clearLayers();
                drawnItems.addLayer(e.poly);

                var feature = {
                    "type":"Feature",
                    "geometry":{
                        "type":"Polygon",
                        "coordinates":e.poly.getLatLngs()
                    },
                    "properties":{
                        bounds:e.poly.getBounds()
                    }
                }
                scope.setGeoFilter(feature);
            });
            scope.map.on('draw:rectangle-created', function (e) {
                drawnItems.clearLayers();
                drawnItems.addLayer(e.rect);

                var feature = {
                    "type":"Feature",
                    "geometry":{
                        "type":"Polygon",
                        "coordinates":e.rect.getLatLngs()
                    },
                    "properties":{
                        bounds:e.rect.getBounds()
                    }
                }
                scope.setGeoFilter(feature);
            });
            scope.map.on('draw:circle-created', function (e) {
                drawnItems.clearLayers();
                drawnItems.addLayer(e.circ);
                var feature = {
                    "type":"Feature",
                    "geometry":{
                        "type":"Point",
                        "coordinates":e.circ.getLatLng()
                    },
                    "properties":{
                        radius:e.circ.getRadius()
                    }
                }

                scope.setGeoFilter(feature);
            });

            scope.map.on('draw:reset', function (e) {
                //console.log("RESET");
                drawnItems.clearLayers();
                scope.setGeoFilter({});
            });

            scope.$on('heat', function (e,data) {
//                console.log("HEAT EVENT");
//                console.log(e);
//                console.log(data);
//                console.log(scope.map);
//                var colorscheme = function(value){
//                    var h = 0.66;
//                    //var h = (value * 0.6);
//                    var s = 0.8;
//                    var l = (1- value);
//                    var a = (0+value);
//                    return [h, s, l, a];
//                   // return [(1-value), value *0.8, 0.8,1];
//                }
//                //scope.heatMap.clear();
//                scope.map.removeLayer(scope.heatMap);
//                scope.heatMap = new L.TileLayer.HeatCanvas({},{'step':0.6, 'degree':HeatCanvas.LINEAR, 'opacity':0.8, colorscheme:colorscheme});
//                _.each(data, function(element){
//                    scope.heatMap.pushData(element.lt, element.lg, 20);
//                });
//                scope.map.addLayer(scope.heatMap);
            });

            //map.addLayer(drawnItems);


            scope.heatMap = heatmap;

            scope.map.setView(mapCenter, 13)
                .addLayer(scope.heatMap)
                .addLayer(drawnItems)
                .addLayer(osmBright);

            //jQuery stuff
            // Todo Convert to Angular Model, DOM oriented right now :(
            var mapStretch = {
                min:function () {
                    $(".mapColumn").removeClass("maximazer");
                    $(".contentColumns").removeClass("minimazer");
                    $(".map-pull").find("i").removeClass("icon-chevron-up").addClass("icon-chevron-down");

                    self.map.panBy(new L.Point(0, 100));
                    var innerself = this;
                    $(".map-pull").unbind("click").click(function () {
                        innerself.max();
                    });
                },
                max:function () {
                    $(".mapColumn").addClass("maximazer");
                    $(".contentColumns").addClass("minimazer");
                    $(".map-pull").find("i").removeClass("icon-chevron-down").addClass("icon-chevron-up");

                    self.map.panBy(new L.Point(0, -100));
                    //self.map.heatmap.redraw();
                    var innerself = this;
                    $(".map-pull").unbind("click").click(function () {
                        innerself.min();
                    });
                }
            };

            $(".map-pull").click(function () {
                mapStretch.max();
            });
        };

        return{
            restrict:'A',
            //template:'<div id="mapPort"></div>',
            link:linker
        }
    })
    .directive('chosenRao', function () {
        var linker = function(scope, element, attrs) {
            scope.$watch('forage.rao.data', function(item){

                if(scope.forage.rao.hash.lenght !== 0 ){
                    var codesList = _.pluck(scope.forage.rao.data, 'id');
                     var indexes = [];
                     _.each(scope.forage.rao.hash, function (code) {
                        indexes.push(_.indexOf(codesList, code));
                     });
                    $(element).val(indexes).trigger("liszt:updated");
                }else{
                    $(element).trigger('liszt:updated');
                }
            },true);

            _scope = scope;
            $(element).chosen().change( function(event, item){
                //console.log("CHOSEN CHANGE !");
                //console.log(item);
                //console.log(_.keys(item));

                scope.$apply(function(){
                    var hash = _scope.forage.rao.hash;

                    if( item.selected !== undefined ){
                        hash.push(_scope.forage.rao.data[item.selected].id);
                    }
                    if( item.deselected !== undefined ){
                        var id= _scope.forage.rao.data[item.deselected].id;
                        hash.splice(_.indexOf(hash,id),1);
                    }
                    console.log(_scope.forage.rao);
                })

            });
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .directive('gaDatepicker', function(){

        return{
            restrict: 'A',
            link:function(scope, element, attrs) {

               var to = scope.forage.date.to;
               var from = scope.forage.date.from;

                $(element).DatePicker({
                    inline: true,
                    date: [from, to],
                    calendars: 3,
                    mode: 'range',
                    current: new Date(to.getFullYear(), to.getMonth() - 1, 1),
                    _scope:scope,
                    onChange: function(dates,el) {
                        // update the range display
                        //console.log("CHANGE");
                        $('#date-range-field span').text(dates[0].getDate()+' '+dates[0].getMonthName(true)+', '+dates[0].getFullYear()+' - '+ dates[1].getDate()+' '+dates[1].getMonthName(true)+', '+dates[1].getFullYear());
                        var from =  dates[0].getFullYear()+'-'+("0" + (dates[0].getMonth() + 1)).slice(-2)+'-'+("0" + dates[0].getDate()).slice(-2);
                        var to =  dates[1].getFullYear()+'-'+("0" + (dates[1].getMonth() + 1)).slice(-2)+'-'+("0" + dates[1].getDate()).slice(-2);
                        //$('#date-range-field span').text( dates[0].getFullYear()+'-'+("0" + (dates[0].getMonth() + 1)).slice(-2)+'-'+("0" + dates[0].getDate()).slice(-2) +','+dates[1].getFullYear()+'-'+("0" + (dates[1].getMonth() + 1)).slice(-2)+'-'+("0" + dates[1].getDate()).slice(-2));
                        scope.$apply( function(){
                            scope.forage.date.rangeText = $('#date-range-field span').text();
                            scope.forage.date.from = from;
                            scope.forage.date.to =to;
                        });
                    }
                })
                //console.log("to.===============");
                //console.log(to);
                //console.log(to.getFullYear()+'-'+("0" + (to.getMonth() + 1)).slice(-2)+'-'+("0" + to.getDate()).slice(-2));
                scope.forage.date.to = to.getFullYear()+'-'+("0" + (to.getMonth() + 1)).slice(-2)+'-'+("0" + to.getDate()).slice(-2);
                scope.forage.date.from = from.getFullYear()+'-'+("0" + (from.getMonth() + 1)).slice(-2)+'-'+("0" + from.getDate()).slice(-2);


                $(element).parent().find('#date-range-field span').text(from.getDate()+' '+from.getMonthName(true)+', '+from.getFullYear()+' - '+to.getDate()+' '+to.getMonthName(true)+', '+to.getFullYear());

                $(element).parent().find('#date-range-field').bind('click', function(){
                    $('#datepicker-calendar').toggle();
                    if($('#date-range-field a').text().charCodeAt(0) == 9660) {
                        // switch to up-arrow
                        $('#date-range-field a').html('&#9650;');
                        $('#date-range-field').css({borderBottomLeftRadius:0, borderBottomRightRadius:0});
                        $('#date-range-field a').css({borderBottomRightRadius:0});
                        $('#filtersColumn').css('width','540px');
                        $('#listColumn').css({'left':'560px','width':'390px'});
                    } else {
                        // switch to down-arrow
                        $('#date-range-field a').html('&#9660;');
                        $('#date-range-field').css({borderBottomLeftRadius:5, borderBottomRightRadius:5});
                        $('#date-range-field a').css({borderBottomRightRadius:5});

                        $('#filtersColumn').css('width','470px');
                        $('#listColumn').css({'left':'490px','width':'450px'});
                        //$('input#date-input').change();
                    }
                    return false;
                });

                $('html').click(function() {
                    if($('#datepicker-calendar').is(":visible")) {
                        $('#datepicker-calendar').hide();
                        $('#date-range-field a').html('&#9660;');
                        $('#date-range-field').css({borderBottomLeftRadius:5, borderBottomRightRadius:5});
                        $('#date-range-field a').css({borderBottomRightRadius:5});

                        $('#filtersColumn').css('width','470px');
                        $('#listColumn').css({'left':'490px','width':'450px'});

                        $('input#date-input').change();
                    }
                });
                $(element).parent().find('#datepicker-calendar').click(function(event){
                    event.stopPropagation();
                });
            }
        }
    })
    .directive('typeahead', function(){

        return{
            restrict: 'A',
            link:function(scope, element, attrs) {

                $(element)
                    .typeahead({
                        source:function (query, process) {
                            return $.get(BASE_URL+'api/typeahead/query/format/json/q/' + query, null, function (data) {
                                return process(data.options);
                            });
                        }
                    })
                    .change(function () {
                        $(this).parent().submit();
                    });
            }
        }
    })
    .directive('displayModal', function () {
        var linker = function(scope, element, attrs) {
            var openDialog = function(){
                $("#myModal").modal('show');
            }
            element.bind('click',openDialog);
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .directive('hideModal', function () {
        var linker = function(scope, element, attrs) {
            var openDialog = function(){
                $("#myModal").modal('hide');
                element.controller().saveForage();
            }
            element.bind('click',openDialog);
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .filter('sub', function () {
        return function (text, from, to) {
            if (isNaN(from))
                from = 0;

            if (isNaN(to))
                to = 1;

            if (text.length < to) {
                return text;
            }
            else {
                return String(text).substring(from, to);
            }
        }
    })
;