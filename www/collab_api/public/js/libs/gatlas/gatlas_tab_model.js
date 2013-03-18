/*global console, $j, OpenLayers, BASE_URL, Mustache, Modernizr, localStorage, QUOTA_EXCEEDED_ERR, escape, document, Raphael, EventManager, GStreetviewPanorama, GLatLng, GEvent, g_atlas, window */


/* !OL OPTIONS ============== */ 
OpenLayers.Util.onImageLoadError = function () {
	this.src = BASE_URL + "public/js/ol/img/blank.gif";
	DEBUG("onImageError = > " + this.src);
};
OpenLayers.IMAGE_RELOAD_ATTEMPTS = 1;
OpenLayers.ImgPath = "http://localhost/spvq_masala/public/js/ol/theme/default/img/";
DEBUG("OpenLayers.Util.getImagesLocation() => " + OpenLayers.Util.getImagesLocation());

//TODO METTRE DANS UN OBJET
var serverProj = new OpenLayers.Projection("EPSG:4326"); //Google & server Mtl rues
//var serverProj = new OpenLayers.Projection("EPSG:32768"); //Google & server Mtl rues
var proj = new OpenLayers.Projection("EPSG:900913"); // OpenLayer Mercator
var map_options = {
	    projection: proj,
	    units: "m",
	    maxResolution: 156543.0339,
	    maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
	    controls: [],
	    useOverlayPopups: false,
	    theme: BASE_URL + "public/js/ol/theme/default/style.css"
	};

var Nav = function () {
	return new OpenLayers.Control.Navigation({'zoomWheelEnabled' : false});
};

var Panel = function () {
	
	var p = new OpenLayers.Control.Panel();
	p.addControls([
		new OpenLayers.Control.ZoomIn({	displayClass: "zoomIn", title: 'Zoom IN'}),
		new OpenLayers.Control.ZoomOut({ displayClass: "zoomOut", title: 'Zoom OUT'})
	]);
	return p;
};

var Target = new OpenLayers.LonLat(zoomIni.lon, zoomIni.lat);  

//UI WIDGETS ==================
/* ! LIST WIDGET FACTORY */
var ListWidget =  (function () {
	var	myList = {
	
		version: 1,
		description: 'ListWidget',
		// TODO USE TO POPULATE BOITE À FILTRES
		filters:{
					//'par-code' : {'title':"Par Code(s)"}
				},
		params:{},
		init:function(){
		
			DEBUG("ListWidget INIT !!! "+ $j("#filter-selection").length );
			var _this = this;
			$j("#filter-selection").find("option").each(function(){
				DEBUG( $j(this).val() );
				_this.filters[$j(this).val()] = {'title': $j(this).text()};
				//this.find("option[value='"+filterId+"']").text()
			})
			
			DEBUG(_this.filters);

		},
		generate:function(filterId, handler, target, items){
			var _this = this;
			//url = BASE_URL + "raoevnts/get_list/"+ filterId;
			
			_this.newList(filterId, handler, target, true);
			//_this.loadList(url);
			_this.updateBox( filterId,target, items);
		},
		
		loadList:function(url){
			DEBUG("LOADLIST=>"+url);
			var _this = this;
		
			$j.getJSON(
				url,
				null,
				(function(_this){
					return function(data,status,xhr){
						_this.parseList(data,status,xhr);
					};
				}(_this))
			);
		},
		parseList:function(data,status,xhr){
			DEBUG(xhr);
			var tmpl = $j('#tmpl-code').html(),
				view = { 
					titre: this.filters[data.filter].title,
					attr:data.list
					},
				html =  Mustache.to_html(tmpl, view),
				cbox = $j('#cboxLoadedContent');
			
			DEBUG(data.list);
			
			
			//CACHE
			//this.cacheList(data.filter,data.list,html);
			
			//UPDATE VIEW
			
			cbox.html(html);
			
			
			this.updateList( this.params.filterId, this.params.target, cbox);
			
			cbox.find(".f-status").hide();
		},
		
		newList:function(filterId, handler, target, generated){
		
			
			if(typeof( this.filters[filterId] ) === 'undefined')
			{
				DEBUG('Filter Id "'+filterId+'" not in CodeWidget!');
				return false;
			}
			//var filterId = 	filterObj.val();
			//var filterTitle = 	filterObj.find("option[value='"+filterId+"']").text();
			var filterTitle = 	this.filters[filterId].title;
						
			var _this = this,
				tmpl = $j('#tmpl-box').html(),
				html = "",
				view = {},
				url = BASE_URL + "raoevnts/get_list/"+ filterId,
				fromCache = false,
				cached = false;
			
			if(typeof( this.filters[filterId] ) == 'undefined')
			{
				this.filters[filterId] = {'title': filterTitle};
			}
			
			generated = generated || false;
			
			_this.params = {
							filterId:filterId,
							handler:handler,
							target: target
							};
			
			//TODO CHECK IF BOX ALREADY EXISTS 
			
			//NEW BOX
			if( target.find("div."+filterId).length === 0){
			
/*
				view = { box_title: this.filters[filterId].title,
						 filterId: filterId
						};
*/
				view = { box_title: filterTitle,
						 filterId: filterId
						};
				target.find('.code0').before( Mustache.to_html(tmpl, view) );
				target.find('div.'+filterId).slideToggle('normal','easeOutBack',function(){});
			}
			
			if(generated === false)
			{
				//CheckCache
				cached = this.getCachedList(filterId);
				
				if( cached )
				{
					DEBUG("FROM CACHE RESULTS");
					html = cached;
					fromCache = true;
				}
				else
				{
					//var tmpl = $j('#tmpl-code').html();
					view = { titre: this.filters[filterId].title };
					html =  Mustache.to_html(tmpl, view) ;
				}
				
				// POPUP
				$j.colorbox({
							width:'432px', 
							height:'640px',
							inline:true, 
							href:html,
							scrolling:false,
							onComplete:function(){ 
								_this.updateList( filterId,target, $j('#cboxLoadedContent'));
								},
							onCleanup:function(){ 
								//handler.debug('onCleanup: colorbox has begun the close process');
								_this.updateBox( filterId,target, $j('#cboxLoadedContent').find("input:checked") );
								}
				});
				
				
				// IF CheckCache /// STOP LOADING
				if( fromCache ){ return; }
					
				// LOAD LIST
				$j('#cboxLoadedContent').find(".f-status").show();
				
				this.loadList(url);
			
			}
		},
		updateList:function(filterId,board,container){
			DEBUG("updateList");
			
			var ids = board.find("dl."+filterId+">dt:eq(0)").html(),
			aValues = ids.split(",");
			DEBUG(ids);
			
			$j(aValues).each(function(){
				var filterId = this;
				
				container.find("input").each(function(){
					if( this.value === filterId )
					{
						this.checked = "checked";
					}
				});
			});
		},
		updateBox:function(targetBox, target,items){
			DEBUG("updateBox");
			DEBUG(items);
			
			//TODO IF NOTHING IS SELECTED CLEAR THE FILTER
			DEBUG("items length => " + items.length );
			if(items.length === 0)
			{
				this.deleteBox(targetBox,target);
				return false;
			}
			
			var ul = "",
				ids = [],
				_this = this;
				
			items.each( function(){
				ul += "<li>"+this.title+"</li>";
				ids.push(this.value);
			});
			
			target.find("div."+targetBox).find(".spawn-ulist").html(ul);
			target.find("dl."+targetBox).find("dt").html( ids.join(",")).change();
			
			//CHANGE STATUS TO DESABLED IN THE SELECT BOX (PREVENT ANOTHER SPAWNING)
			target.find(".filter-selection").find("select>option").each(function(){
				var item = $j(this);
				if(item.val() === targetBox )
				{
					item.attr("selected","");
					item.attr("disabled","disabled");
					
					this.selected = "";
					this.disabled = "disabled";
				}
			});
			
			//BUTTONS BINDING
			
			target.find("div."+targetBox).find("a.edit-box").bind('click',function(){
				_this.editList(targetBox,target);
			});
			
			target.find("div."+targetBox).find("a.delete-box").bind('click',function(){
				_this.deleteBox(targetBox,target);
			});
			
			target.find("div."+targetBox).change();
			
		},
		deleteBox:function(boxClass, board){
			DEBUG("deleteBox");
			
			// REMOVE BOX
			board.find("div."+boxClass).change().remove();
			
			//CHANGE SELECT STATUS
			board.find(".filter-selection").find("select>option").each(function(){
				var item = $j(this);
				if(item.val() === boxClass )
				{
					item.attr("disabled","");
					this.disabled = "";
				}
			});
		},
		editList:function(filterId,board){
			DEBUG("editList TRIGERED");
			this.newList(filterId,null,board);
		
		},
		cacheList:function(filterId,content,html){
			this.filters[filterId].content = html;
			
			// LOCAL STORAGE
			if(Modernizr.localstorage)
			{
				try {
					localStorage.setItem(filterId, html); //saves to the database, "key", "value"
				} catch (e) {
					DEBUG('localStorage error');
					if(e === QUOTA_EXCEEDED_ERR){
						DEBUG('Quota exceeded!'); //data wasn't successfully saved due to quota exceed so throw an error
					}
				}
				//DEBUG("localStorage => "+localStorage.getItem(filterId));
			}
		},
		getCachedList:function(filterId){
					
			// LOCAL STORAGE
			if(Modernizr.localstorage)
			{
				if( localStorage.getItem(filterId) )
				{
					DEBUG("CACHED FROM localStorage");
					return localStorage.getItem(filterId);
				}
			}
			
			
			if(typeof( this.filters[filterId].content ) !== 'undefined')
			{
				DEBUG("CACHED FROM ARRAY");
				return this.filters[filterId].content;
			}
			
			return false;
		}
	};
	return myList;		
}());



/* ! DATE WIDGET */
var DateWidget =  (function()
{
	var	myWidget = {
	
		version: 1,
		description: 'DateWidget',
		target: '',
		options:{
					dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
					dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'], 
					monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
					numberOfMonths: 1,
					showButtonPanel: false,
					changeYear: true,
					dateFormat: 'yy-mm-dd',
					onSelect: function(selectedDate){ DateWidget.setConstraint(); }
					//onClose: function(selectedDate){ DateWidget.setConstraint(); }
		},
		today:null,
		yesterday:null,
		setConstraint:function(){
		
			var maxDate = this.target.find('.rangeB').val(),
			    minDate = this.target.find('.rangeA').val(),
			    dt = this.target.find('.datepicker').find("dt");

			this.target.find('.rangeA').datepicker( "option", "maxDate", maxDate ).change();
			this.target.find('.rangeB').datepicker( "option", "minDate", minDate ).change();

			$j(dt[0]).html(this.target.find('.rangeA').val()+","+this.target.find('.rangeB').val());
		},
		init:function( target, params ){
			this.target = target;
			
			var dynDates, fromDate, toDate, d, curr_date, curr_month, curr_year, today, y, y_date, y_month, y_year, yesterday;
			
			if( params.parDate )
			{
				///var dynDates = params.parDate.data.split(",");
				dynDates = params.parDate.data;
				fromDate = dynDates[0];
				toDate = (dynDates.length === 2)?dynDates[1]:fromDate;
				
				DEBUG("dynDates.length >> " + dynDates.length);
				DEBUG(dynDates);
				
				target.find('.rangeA').val(fromDate).datepicker(this.options);
				target.find('.rangeB').val(toDate).datepicker(this.options);
				DateWidget.setConstraint();
				return;
			}
			
			d = new Date();
			curr_date = d.getDate();
			curr_month = d.getMonth() + 1;
			curr_year = d.getFullYear();
			today = curr_year + "-" + curr_month + "-" + curr_date;
			
			this.today =  today;
				
			y = d.setDate(d.getDate()-1);
			y_date = d.getDate();
			y_month = d.getMonth() + 1;
			y_year = d.getFullYear();
			y_year = "1999";
			yesterday = y_year + "-" + y_month + "-" + y_date;
			
			this.yesterday =  yesterday;
			
			//yesterday = "2011-01-01";			
			
			target.find('.rangeA').val(yesterday).datepicker(this.options);
			target.find('.rangeB').val(today).datepicker(this.options);

			DateWidget.setConstraint();
		}
	};
	return myWidget;		
}());

/* ! HOUR WIDGET */
var HourWidget =  (function()
{
	var	myWidget = {
	
		version: 1,
		description: 'HourWidget',
		target: null,
		options:{},
		initSlider:function(target, id, fromTo){
			var hoursLimits = fromTo;
			target.find(".slider-range").slider({
				range: true,
				min: 0,
				max: 24,
				values: hoursLimits,
				//values: [6,17],
				slide: function( event, ui ) {
					target.find( ".slider-label").val( "De " +ui.values[0] + " à "  +ui.values[1] + " heure").change();
					//$j('#heure_url').attr({'href': BASE_URL +'rapports/filtres'+ FILTER_URI +'par-heure/'+ui.values[0]+':00,'+ui.values[1]+':00/'});
					
					var dt = target.find('.hourpicker').find("dt");
					$j(dt[0]).html(ui.values[0]+':00,'+ui.values[1]+':00');
					//$j(dt[id]).html(ui.values[0]+':00,'+ui.values[1]+':00');
				}
			});
			//target.find(".slider-label").val( "De "+ target.find( ".slider-range").slider( "value", 6 ) +" à "+ target.find( ".slider-range").slider( "value", 17 ) + " heure");
			target.find(".slider-label").val( "De "+ target.find( ".slider-range").slider( "value", hoursLimits[0] ) +" à "+ target.find( ".slider-range").slider( "value", hoursLimits[1] ) + " heure");
		},
		init:function( target, params ){
			this.target = target;
			//HourWidget.initSlider(target, 0);
			HourWidget.initSlider(target, 0, [8,17]);
			//DEBUG(this);
			
			if( params.parHeure ){
				HourWidget.initSlider(target, 0, params.parHeure.data);
				return;
			}
			
			target.find("div.hourpicker").removeClass("fb-active").addClass("fb-inactive");
		}
	};
	return myWidget;		
}());

/* ! DAY WIDGET */
var DayWidget =  (function()
{
	var	myWidget = {
	
		version: 1,
		description: 'DayWidget',
		target: '',
		options:{},
		init:function( target, params ){
			var i, widget ;
			target.find("div.days").find("input").change( function(event){
				
				var output = [];
				target.find("div.days").find("input:checked").each(function(index,value){
					$j(this).val();
					output.push( $j(this).val());
				});

				target.find("dl.par-jour>dt:first").html(output.join(','));
				DEBUG(output.join(',') );
			});
			
			if( params.parJour )
			{
				//params.parJour = ["lun","ven"];
				
				widget = target.find("div.days");
				
				widget.find("input:checked").each(function(index,value){
					$j(this).attr("checked", "");
				});

				for(i=0;i<params.parJour.data.length;i++)
				{
					widget.find("input[value|='"+params.parJour.data[i]+"']").attr("checked","checked");
				}
				widget.change();
				return;
			}
			
			target.find("div.daypicker").removeClass("fb-active").addClass("fb-inactive");
		}
	};
	return myWidget;		
}());

/* ! MAP WIDGET */
var MapWidget =  (function( target, lonlat )
{
	var	myWidget = {
	
		version: 1,
		description: 'MapWidget',
		target: target || '',
		lonlat: lonlat || {},
		options:{},
		map:{},
		// TODO USE TO POPULATE BOITE À FILTRES
		init:function(){
			var  g_osm, e_icons, point;
			
			this.map = new OpenLayers.Map( this.targetId, map_options );
			g_osm = new OpenLayers.Layer.OSM("OMS",null,{buffer:0});		
			e_icons = new OpenLayers.Layer('Icons',{});
		
			this.map.addLayers([ g_osm, e_icons]);
		    this.map.addControls([nav,panel]);
		    
		    // PinPoint
		    point = new OpenLayers.LonLat(this.lonlat.lon,this.lonlat.lat);
			this.map.setCenter( point.transform(serverProj, this.map.getProjectionObject()),12);
		}
	};
	return myWidget;		
}());

/* ! DRAW MAP WIDGET */
var MapDrawWidget =  (function(){

	var DrawWidget = function( params ){

		this.version = 1;
		this.description = 'MapDrawWidget';
		this.options = params.options || {};
		this.map = params.map || {};
		this.r = params.r || {};
		this.board = params.board || $j('body');
		this.styleMap = {};
		this.parser = {};
		this.vectors = {};
		this.toolListeners = {};
		this.drawControls = {};

		// TODO USE TO POPULATE BOITE À FILTRES
		this.init = function(){
			DEBUG("DRAW INIT");
			var _this = this,k;
			this.styleMap = new OpenLayers.StyleMap({
													pointRadius: 5,
													strokeColor: "#333",
													strokeWidth: 2,
													strokeDashstyle: "solid",
													fillColor:"#ee9900",
													fillOpacity:0.4
													});

			this.parser = new OpenLayers.Format.WKT();
			this.vectors = new OpenLayers.Layer.Vector('Filtre géographique',{ displayInLayerSwitcher:false, styleMap:this.styleMap	});
			
			
			this.toolListeners ={
									"activate": function(){
										DEBUG("IN FUNCTION toolActivated :: clearing Vectors");
										_this.vectors.destroyFeatures();
										_this.removeGeoFilter();
									},
										"deactivate": function(event) {
										// alert("Deactivate " + event.object.displayClass);
									}
								};
			
			this.drawControls = {
					point: new OpenLayers.Control.DrawFeature(
						this.vectors, OpenLayers.Handler.Point,
						{
							"featureAdded": function(e){
								_this._getGeometry('point');
							},
							eventListeners: _this.toolListeners,
							displayClass: "Point"
						}
			
					),
					line: new OpenLayers.Control.DrawFeature(
						this.vectors, OpenLayers.Handler.Path,
						{
							"featureAdded": function(e){
								//this.drawControls.line.deactivate();
								_this._getGeometry('line');
							},
							eventListeners: _this.toolListeners,
							displayClass: "Line"
						}
					),
					polygon: new OpenLayers.Control.DrawFeature(
						this.vectors, OpenLayers.Handler.Polygon,
						{
							"featureAdded": function(e){
								//this.drawControls.polygon.deactivate();
								//_this.addGeometry(_this.vectors.features[0].geometry);
								_this._getGeometry('poly');
							},
							eventListeners: _this.toolListeners,
							displayClass: "Poly"
						}
					)
				};

			for( k in _this.drawControls){
				if(_this.drawControls.hasOwnProperty(k)) {
					this.map.addControl(this.drawControls[k]);
				}
			}
			
			this.map.addLayers([ this.vectors ]);
		};

		this.toggleControl = function(element){
			var _this = this, k, control;
			for(k in _this.drawControls){
				if(_this.drawControls.hasOwnProperty(k))
				{
					control = this.drawControls[k];
					if(element.name === k){
						control.activate();
						this.toggleActiveState(element.name);
					} else {
						control.deactivate();
					}
				}	
			}
			if(element.name === 'pointer'){ this.toggleActiveState(element.name);}
			if(element.name === 'clear'){ this.clearGeoFilter();}
			if(element.name === 'panel'){ this.toggleFullMap();}
		};
		
		this.toggleActiveState = function( activeFeature )
		{
			this.board.find("div.geo-item a").each(function(){
				var item = $j(this);
				//item.addClass('standby');
				item.parent().removeClass('activated').addClass('standby');
				//DEBUG(item[0] );
				//DEBUG(activeFeature + " :: "+ item[0].name );
				if(activeFeature === item[0].name){ item.parent().removeClass('standby').addClass('activated');}
			});
			
			//var _this = this;
			//if(activeFeature == "panel") this.toggleFullMap();
		};
		
		this._getGeometry = function(aTool, param)
		{
			var l, g, d, _this = this;
			this.board.find("div.mini-loader").show();
		
			if ( this.vectors.features.length > 0 )
			{
				l = this.vectors.features.length -1;
				//var g = this.vectors.features[0].geometry;
				g = this.vectors.features[l].geometry;
				d = param || 500;
				
				if(aTool === "poly"){
					this.addGeometry(g);
					return false;
				} 
								
				switch( aTool )
				{
					case "point":
						d = this.board.find("input.point_meters").val() || d;
					break;
					
					case "line":
						d = this.board.find("input.line_meters").val() || d;
					break;
					
					default:
						d = 0;
				}

				DEBUG("Send via POST geometry:"+ escape(g)+", distance:"+ d);
				$j.post( 
					BASE_URL+"raoevnts/get_geometry/", 
					{ geometry: escape(g), distance: d }, 
					function(data,status){ _this.addGeometry(data,status); } ,
					 "json"
					 );
			}
		};
		
		this.addGeometry = function( data , status )
		{
			//alert(data);
			//DEBUG(data);
			var i, features, aFeatures;
			
			features = this.parser.read( data);
				
			if ( this.vectors.features.length > 0 )
			{
				aFeatures =[];
				for(i=0;i< this.vectors.features.length;i++)
				{
					aFeatures.push( this.vectors.features[i] );
					DEBUG("REMOVING Features");
					DEBUG(this.vectors.features[i]);
				}
				this.vectors.removeFeatures( aFeatures );
			}
			//var features = this.parser.read( data);
			DEBUG(features);
			this.vectors.addFeatures(features);
			
			this.board.find("div.mini-loader").hide();
			this.addGeoFilter();
			
			// RETURN TO POINTER
			//this.toggleActiveState('pointer');
			this.toggleControl({name:'pointer'});
		};
		
		this.addGeoFilter = function( )
		{
			var tmpl, filterId, g, features, g_32187, view;
			//this.board.find("div.mini-loader").hide();
			this.removeGeoFilter();

			tmpl = $j('#tmpl-box-geo').html();
			filterId = 'par-geo';

			DEBUG("VECTORS LENGTH => "+this.vectors.features.length );
			if( this.vectors.features.length === 0 ){
				return false;
			}
			
			g = this.vectors.features[0].geometry;
			features = this.parser.read(g);
			//g_32187 = features.geometry.transform( new OpenLayers.Projection('EPSG:900913'),new OpenLayers.Projection('EPSG:32187'));
			
			
			//NEW BOX
			if( this.board.find("div."+filterId).length === 0)
			{
				view = { box_title: 'Par Géographie',
						filterId: filterId,
						geom: features.geometry
						//geom: g_32187
						};
				this.board.find('.code0').before( Mustache.to_html(tmpl, view) );
				this.board.find("div."+filterId).show();
			}
			this.board.find("div.geo-item a.clear").parent().show();	
		};
		
		this.removeGeoFilter = function()
		{
			this.board.find("div.par-geo").remove();
			this.board.find("div.geo-item a.clear").parent().hide();	
		};
		
		this.clearGeoFilter = function()
		{
			this.vectors.destroyFeatures();
			this.removeGeoFilter();
			this.toggleActiveState('pointer');
		};
		
		this.toggleFullMap = function()
		{
			DEBUG("toggleFullMap "+ $j('div.calls_list').is(':hidden'));
			DEBUG("toggleFullMap "+ $j('div.calls_list').is(':visible'));

			var _this = this;	
			if($j('div.calls_list').is(':visible') === false)
			{
				$j('div.filter-map').animate({width:'50%'},500, function(){
					$j('div.calls_list').show();
					_this.toggleActiveState('pointer');
					_this.r.setSize( 300, 600);
				});
			}
			else
			{
				$j('div.calls_list').hide();
				$j('div.filter-map').animate({width:'100%'},500, function(){
					_this.map.updateSize();
					_this.toggleActiveState('pointer');
					_this.r.setSize( 700, 600);
				});
			}
		};
	};

	return function (params) {
		return new DrawWidget(params);
	};

}());

/* ! EvntList FACTORY */
var EvntList = (function(){

	var Elist = function(params){
		var tab_index, serverData, referenceMap;
		this.version = '1.0';
		this.description = 'instanceOf Elist';
		this.tmpl = $j('#tmpl-list').html();
		this.params = params;
		this.first = params.first || false;
		this.map = {};
		this.evntManager = {};
		this.id = params.pos;
				
		this.filters = params.filters || {
											
/*
											parDate:{	
														filterId: "par-date",
														data:["2010-03-16","2010-03-16"]
														},
											parHeure:{	
														filterId: "par-heure",
														data:[3,18]
														},	
											parJour:{	
														filterId: "par-jour",
														data:["lun","ven"]
														},						
											parGeom:{ 
														lonlat:{ lon:-71.3, lat:46.8},
														distance: 3000
														},

											parListe:[{filterId:"par-code",
														items:[{title:"Alarme (commerce, residence)",value:"ALAHUP"},{title:"STEF TEST (test)",value:"STEFLEF"}]
														}
													] 
*/
											};
		this.evnts = params.evnts || [];
		this.limit = 50;
		this.offset = 0;
		this.queryString = '';
		this.qID='';
		this.appendEvnts = false;

		this.geom = '';
		//this.serverData;
		//this.referenceMap;
		this.activeRequest = null;
		//this.zoom;
		
		this.createView = function(){

			this.debug('EvntList.createView');
			var _this = this,
				view = {
					board_id: 'l-'+ this.id,
					evnt_title: "F",
					evnt_id: this.id,
					histo: [],
					evnts:[]
				},
				board_index = EvntBoards.boards.length,
				boardName = "",
				point, g, d;
			
			//Append to DOM
			tab_index = $j("#boards").tabs("length");
			
			//var board_index = EvntBoards.boards.length;
			this.debug(this.tab_index);
			
			//var boardName = ( board_index == 0) ? "RAO 911" : "boards-" + (board_index);
			if(board_index === 0)
			{
				boardName = "RAO 911";
				ListWidget.init();
			}
			else
			{
				boardName = ( this.params.proxiId ) ? "PROXY "+this.params.proxiId : "boards-" + (board_index);
			}
			
			
			$j("#boards").append('<div id="boards-'+board_index+'" class="noDisplay">'+Mustache.to_html(this.tmpl, view)+'</div>');
			$j("#boards").tabs("add","#boards-"+board_index,boardName);
			
			$j("#boards").tabs('select', tab_index);
			
			if( board_index === 0 )
			{
				$j("#boards>ul>li").eq(0).find('span').hide();
			}
			
			//// TODO
			//// Check for url paramaters to set widgets before the delegation and the forceRefresh
			
			/// INI WIDGETS WITH PARAM FROM THE URI
			_this.initWidgets(this.filters);
			
			/// IF PROXY, CENTER MAP ON IT!
			_this.initMap(this.filters);
			
			_this.delegateCustomFilters();
			
			/// IF GEO FILTER (DISTANCE)
			/// TEST /// TEST /// TEST /// TEST ///
			
			if( this.filters.parGeom )
			{
				//var g = "POINT("+QC.lon+" "+QC.lat+")";
				//var d = 2000;
				
				point = new OpenLayers.LonLat(this.filters.parGeom.lonlat.lon,this.filters.parGeom.lonlat.lat).transform(serverProj, proj);

				g = "POINT("+point.lon+" "+point.lat+")";
				d = this.filters.parGeom.distance || 1000;
				$j.post( 
					BASE_URL+"raoevnts/get_geometry/", 
					{ geometry: escape(g), distance: d }, 
					function(data,status){ 
						_this.DrawWidget.addGeometry(data,status);
						_this.forceRefresh();
						},
					 "json"
				);
				return;
			}

			_this.forceRefresh();
		};
		
		this.delegateCustomFilters = function(){

			var board = $j('#l-'+this.id),
				_this = this;

			// DELEGATE
			board.find('div.filter-selection').delegate('a', 'click', function(event) {
				_this.debug("CreateFilter DELEGATION");
				_this.createFilter(event);
			});
			
			board.find('div.f-toolbar').delegate('a.f-refresh', 'click', function(event) {
				_this.forceRefresh();
			});
			
			board.find('div.calls_list').delegate('dl.call', 'click', function(event) {
				EvntBoards.createEvntDetails(event);

			});

			board.delegate('#filters', 'change', function(event) {
				//DEBUG(event);
				//DEBUG(event.target);
				
				$j(this).find("a.f-refresh").parent().addClass('changed');
			});
		};

		this.buildQuery = function(){
			var board = $j('#l-'+this.id), rs;
			rs = board.find(".fb-active").find("dl");
			
			this.debug(rs);
			
			return rs;
		};

		this.initWidgets = function(params){
			this.debug("params");
			this.debug(params);
			
			var board = $j('#l-'+this.id),
				_this = this,
				dynFilters = params.parListe || [],
				filterId ="",
				items = [],
				i,l;
			
			DateWidget.init(board, params);
			HourWidget.init(board, params);
			DayWidget.init(board, params);
			
			board.find("div.filter-box").click( function(event){
				if( ($j(event.target).hasClass("fb-title")||  $j(event.target).hasClass("fb-content")) &&  $j(event.currentTarget).hasClass('datepicker') === false )
				{
					$j(this).toggleClass('fb-active');
					$j(this).toggleClass('fb-inactive');
				}
			});
			
			/// TEST /// TEST /// TEST ///
			
			/// DYNAMIC FILTERS FROM THE MAGIC BOX
			//var dynFilters = params.parListe || [];
			
			l=dynFilters.length;
			for(i=0;i<l;i++)
			{
				filterId = dynFilters[i].filterId;
				items = dynFilters[i].items;
				
				ListWidget.generate(filterId,_this,board,$j(items));
			}
		};
		
		this.initMap = function(params){
			var _this = this,
				board = $j('#l-'+this.id),
				coord, LL, e_map, g_osm, e_icons, Draw_widget, st ;
			
			// CENTER ON TARGET IF THERE IS ONE
			coord = params.lonlat || Target;
			if( typeof(coord) === "string" )
			{
				LL = coord.split(" ");
				coord = new OpenLayers.LonLat(LL[0], LL[1]);
			}

			e_map = new OpenLayers.Map( "map-"+ this.id, map_options );
			g_osm = new OpenLayers.Layer.OSM("OMS",null,{buffer:0});
			e_icons = new OpenLayers.Layer('Icons',{});
			
			e_map.addLayers([ g_osm, e_icons]);
			//e_map.addControls([nav,panel]);
			e_map.addControls([Nav(),Panel()]);
			e_map.setCenter( coord.transform(serverProj, e_map.getProjectionObject()),12);

			/// RAPHAEL VECTORS
			$j(e_map.div).find('.olMapViewport').find('div:eq(0)').prepend('<div id="r_map" style="border:0;height:100%;position:absolute;left:58%;top:0;z-index:2000;">SVG</div>');

			this.r_clusters = new Raphael(board.find("#r_map")[0], 330, 600);
			st = this.r_clusters.set();

/*
			e_map.events.register('moveend', null, function(event){
				var iZoom = +e_map.getZoom();
				DEBUG("Resolution:"+e_map.getResolution());
				DEBUG("Scale:"+e_map.getScale());
				DEBUG("Zoom:"+iZoom);
			});
*/

			this.referenceMap = e_map;
			this.evntManager = new EventManager({map:e_map, clusters_set:st, r:this.r_clusters, board:board});
			
			Draw_widget = new MapDrawWidget( {map:e_map,board:board,r:this.r_clusters} );
			Draw_widget.init();

			this.DrawWidget = Draw_widget;

			// DELEGATE
			board.find('div.geo-item').delegate('a', 'click', function(event) {
				_this.debug(event);
				_this.debug(event.target.name);
				Draw_widget.toggleControl(event.target);
			});
		};

		this.show = function(){
			var pos = (this.id * 1000) + 20;
			this.debug("Pos => "+ pos);
			$j('#content_horiz').animate({'left':'-'+pos+'px'},'normal','easeOutBack', function(){});
		};

		this.forceRefresh = function(event)
		{
			this.debug("Force Refresh");
			this.debug(tab_index);
			
			var board, oQuery, input, temp, queryString, i;
			
			$j("#boards>ul>li").eq(tab_index).find('a').addClass("loading");
			
			board = $j('#l-'+this.id);
			board.find("a.f-refresh").parent().removeClass('changed');
			
			oQuery = this.buildQuery();
			input = [];
			oQuery.each(function(index){
	
				temp = {};
				temp.filterId = this.className;
				temp.data = [];
				$j(this).find("dt").each(function(index){
					temp.data.push($j(this).html());
				});
				
				input.push(temp);
			});
			
			this.debug(input);
			
			queryString ='';
			for(i=0;i<input.length;i++)
			{
				if(input[i].filterId === "par-geo")
				{
					queryString += input[i].filterId +"/_geom/";
					this.geom = input[i].data[0];
				}
				else
				{
					queryString += input[i].filterId +"/"+input[i].data[0] +"/";
				}
			}

			this.debug('QueryString: '+queryString );
			this.offset = 0;
			this.loadEvnts(queryString, 0);
		};

		this.loadEvnts = function(qString)
		{
			var _this = this, url, wkt;
			// APPEND VS NEW QUERY TESTS
			_this.appendEvnts = (_this.queryString === qString)? true : false ;
			if(_this.queryString !== qString){
				_this.offset = 0;
			}
			//TODO CHECK geom EQUALITY
			
			_this.queryString = qString;
			
			url = (this.offset === 0)?BASE_URL + "raoevnts/filtres/"+qString : BASE_URL + "raoevnts/filtres/"+qString + "offset/"+this.offset;
			this.debug("Offset : "+ this.offset);
			
			// GeoFilter
			wkt = _this.geom || null;
			
			if( _this.activeRequest !== null ){
				_this.activeRequest.abort();
			}
			
			_this.activeRequest = $j.post(
				url,
				{geo:wkt},
				(function(_this){
					return function(data,status,xhr){
						_this.parseEvnts(data,status,xhr);
					};
				}(_this)),
				'json'
			);
		};
				
		this.parseEvnts = function(data,status,xhr)
		{
			var _this = this, board, old_offset, tmpl, view;
			//_this.debug(this);
			_this.debug(status);
			//_this.debug(data);
			//_this.debug(xhr);
			if( data.status === 2 ){
				board.find('.loading').remove();
				alert("Votre session est terminée. Vous devez recharger la page et vous identifier de nouveau pour accéder aux données.");
				return;
			}
			
			board = $j('#l-'+_this.id);
			tmpl = $j('#evnts_list').html();
			
			_this.qID = data.qid || '';
			old_offset = _this.offset;
			_this.offset = data.offset || 0;
			
			/// TODO GET INFOS FROM event.target
			if(  data.data.length > 0 )
			{
				this.serverData = data;
				view = {
					//evnt: [{evnt_id: '1',evnt_desc:'desc 1'}, {evnt_id: '1',evnt_desc:'desc 1'}]
					e_tot: data.total +" appel(s)",
					e_batch: old_offset +"-" + _this.offset,
					evnt: data.data
				};
			}
			else
			{
				view = {
					e_tot: "",
					e_batch: "Aucun appel pour cette sélection"
				};
			}
			
			if( _this.appendEvnts )
			{
				board.find('.calls_list').append( Mustache.to_html(tmpl, view) );
			}
			else{
				board.find('.calls_list')[0].scrollTop = 0;
				board.find('.calls_list').html( Mustache.to_html(tmpl, view) );
			}
			
			board.find('.loading').remove();
			
			if( data.status === "ok" || data.status === "end")
			{
				DEBUG("data.status: "+data.status);
				
				_this.evntManager.setEvents( data.data,  data.geom );
				
				// CLUSTER CLICK EVENT HANDLING
				board.find('#r_map').bind('mousedown', function(event) {
					var full_id, id, zoom, items;
					//DEBUG(event);
					//DEBUG("EVENT BUBBLING FROM R MAP");
					
					// EXIT IF NOT ON A SPOT (NAVIGATING FOR EX.)
					if(event.target.id === ''){
						return;
					}
					
					full_id = event.target.id;
					id = full_id.substring(2);
					zoom = _this.referenceMap.getZoom();
					DEBUG(id);
					items =   _this.serverData.geom['z_'+zoom][id].items;
					DEBUG( items );
					_this.listCluster(items);
				});

			}
			
			$j("#boards>ul>li").eq(tab_index).find('a').removeClass("loading");
		};

		this.loadClusters = function(sqlId){
			$j.post(
				BASE_URL + "get_clusters",
				{ "sql": sqlId },
				function(data){
					EvntList.parseEvnts(data);
				}
				,
				"json"
				);
		};
		
		// FILTER LIST BY CLUSTER 
		this.listCluster = function( ids )
		{
			DEBUG("CLUSTER LIST");
			var _this = this, board, evntsList, i,len, id, header=[];
				
			board = $j('#l-'+_this.id);
			evntsList = board.find('.calls_list');
			evntsList.find("div.eventInfos").hide();
			evntsList.find("div.evnts-cluster").remove();
			
			len = ids.length;
			for( i=0;i<len;i++)
			{
				id = "evnt_" + ids[i];
				evntsList.find("div.#"+id).show();
			}
						
			header.push( '<div class="evnts-header evnts-cluster">' );
				header.push( '<div class="filter-box-mod left_area">' );
					header.push( '<dl class="event font_style" >' );
						header.push( '<dt style="right:10px;top:2px;"><a href="#" class="close" ></a></dt>' );
						header.push( '<dd>Groupe: '+len+' appel(s)</dd>' );			
					header.push( '</dl>' );
				header.push( '</div>' );
			header.push( '</div>' );

			evntsList.prepend( header.join('') ).bind("click", function()
			{	
				//DEBUG("CLICK BINDING");
				evntsList.find("div.eventInfos").show();
				evntsList.find("div.evnts-cluster").remove();
			});
		};

		this.createFilter = function(event)
		{		
			this.debug('createFilter');
			this.debug(event);
			
			var _this = this,
				board = $j('#l-'+this.id),
				filterId = board.find(".filter-selection select").val();		
				filterTitle = board.find(".filter-selection select").text();
				filterObj = board.find(".filter-selection select");
				
			// 1)  CREATE NEW EMPTY FILTER BOX


			this.debug( "Selected :"+ filterId );
			
			/// TODO add parameter for the loadView
			ListWidget.newList(filterId,_this,board);
			//ListWidget.newList(filterObj,_this,board);
		};

/*
		this.editFilter = function(filterId)
		{

		};
		
		this.deleteFilter = function(filterId)
		{

		};
*/
			
		this.debug = function(log){
			if (DEBUG_MODE && typeof(console) !== 'undefined') { 
				console.log(log);
				//debugger; 
			}
		};
	};
		
/*
		parseClusters:function(){},
		reload: function(){},
		buildQuerystring:function(){},
		parseQuerystring:function(){},
		initFilters:function(){},
		loadFilters:function(){}
*/
	return function (params) {
		return new Elist(params);
	};

}());


/* ! EvntDetails FACTORY */
var EvntDetails = (function(){

	var Details = function(params){
		
		console.log(params);
		var pano, map, eventID, board;
		this.version = '1.0';
		this.description = 'instanceOf Details';
		this.event = params.event || {};
		this.tmpl = $j('#tmpl-details').html();
		this.params = params;
		//this.pano;
		//this.map;
		//this.eventID;
		this.id = params.pos;
		//this.board;
		this.view = {};
		this.tab = {};
		
		this.createView = function(){
			this.debug('EvntDetails.createView');
			var _this = this, dl, view, tab_l, boardName, lonlat;			
			
			/// CARD INFOS
			ref = params.ref || {};
			dl =  $j(this.event.currentTarget) || {};
			eventID = ref.call_id || dl.find("dt:first").html();
			
			view = {
				board_id: 'e-'+ this.id,
				evnt_title: ref.call_id || dl.find("dt:first").html() || '',
				evnt_id: this.id,
				evnt_time: ref.call_dt || dl.find(".evnt-time").html() || ' ',
				evnt_date: dl.find("dd:first").html() || ' ',
				evnt_desc: ref.call_desc || dl.find("dd:last").html() || ' ',
				evnt_lonlat: ref.lonlat || dl.find("dt.lonlat").html() || ' ',
				evnt_geom: ref.valid || parseInt( dl.find("dt.geom").html() ,10) || 0
			};
			this.view = view;
			
			//Append to DOM
			tab_l = $j("#boards").tabs("length");
			boardName =	'#'+view.evnt_title;
			
			$j("#boards").append('<div id="boards-'+this.id+'" class="noDisplay">'+Mustache.to_html(this.tmpl, view)+'</div>')
				//.find("#boards-"+board_index)
				.find("#boards-"+this.id)
				.find('#ui-tabs')
				.tabs()
				.css("left","-9999px");	
			
			$j("#boards").tabs("add","#boards-"+this.id,boardName);
			$j("#boards").tabs('select', tab_l);
			
			this.tab = $j("#boards").find('ul:first').find('li:last');
			this.debug("TAB => "+ this.tab.html());
			
			
			board = $j('#e-'+this.id);
			
			//this.debug(view);
			if( view.evnt_geom > 0 )
			{
				/// GEO ITEM
				lonlat = view.evnt_lonlat.split(" ");
				_this.initMap( 'g-map-'+ this.id,{lon:lonlat[0], lat:lonlat[1]});			
				
				board.find("div.proxi")
					.show()
					.delegate('a', 'click', function(event) {
						_this.createProxi(event);
					})
					.find("dl.par-proxi dt:eq(0)")
					.html(view.evnt_lonlat)
					.next()
					.html(view.evnt_date.split(" ")[0]);
			}

			_this.loadData();
		};
		
		this.show = function(){
			var pos = (this.id * 1000) + 20;
			this.debug("Pos => "+ pos);
			$j('#content_horiz').animate({'left':'-'+pos+'px'},'normal','easeOutBack', function(){});
		};
		
		this.initMap = function(targetId,lonlat){
			var options, g_osm, e_icons, point, px, posx, posy, tag, i_tag;
			board.find("#s-maps").show();
			
			options = map_options;
			options.controls = [];
			
			map = new OpenLayers.Map( targetId, options );
			g_osm = new OpenLayers.Layer.OSM("OMS",null,{buffer:0});		
			e_icons = new OpenLayers.Layer('Icons',{});
		
			map.addLayers([ g_osm, e_icons]);
			// this.map.addControls([nav,panel]);
			
			DEBUG(this.map);
			
			// PinPoint
			point = new OpenLayers.LonLat(lonlat.lon,lonlat.lat);
			map.setCenter( point.transform(serverProj, map.getProjectionObject()),16);

			//Marker
			px = map.getLayerPxFromLonLat(point);
			posx = (px.x-38) +"px";
			posy = (px.y-37) +"px";

			tag = "";
			i_tag =$j("<div style='top:"+posy+";left:"+posx+";'><p class='eventCode'>"+tag+"</p></div>").addClass('marker');

			i_tag.appendTo($j(e_icons.div));
		};
		
		this.initStreetView = function(targetId,lonlat){
		
		console.log( "initSV " + targetId + " " +lonlat.lat + " " + lonlat.lon );
			//GOOGLE MUST BE LOADED
			var myPos, myPOV;
		
			if(typeof(google.maps.StreetViewPanorama) === 'undefined'){
				this.debug('noGoogle');
				$j('#'+ targetId).hide();
				return false;
			}

			//pano = new GStreetviewPanorama(document.getElementById(targetId));
			//myPos = new GLatLng(lonlat.lat,lonlat.lon);
			//var myPOV = {yaw:370.64659986187695,pitch:-20};
			//myPOV = {yaw:370.64659986187695,pitch:-2};
			//pano.setLocationAndPOV(myPos, myPOV);

			// ON ERROR EX.: FLASH PLUGGIN

/*
			GEvent.addListener( pano, "error", function(errorCode){
				// 600 = no view available (not near of a street)
				// 603 = no flash availabled
				DEBUG("StreetView error code =>"+errorCode);
				$j('#'+ targetId).hide();
			});
*/
			
			
			var target = new google.maps.LatLng(lonlat.lat,lonlat.lon);
			
		    var panoramaOptions = {
		      position: target,
		        addressControl: false,
		        linksControl: false,
		        panControl: false,
		        zoomControlOptions: {
		        style: google.maps.ZoomControlStyle.SMALL
		        },
		      pov: {
		        heading: 1,
		        pitch: 10,
		        zoom: 1
		      }
		    };
		    var panorama = new  google.maps.StreetViewPanorama(document.getElementById(targetId),panoramaOptions);
		};

		this.loadData = function(){
			this.debug('loadData');
			var _this = this;
			$j.post(
				BASE_URL + "raoevnts/ref/"+ eventID, 
				null,
				function(data){
					_this.parseData(data);
				},
				"json"
				);
		};

		this.createProxi = function(event){
			this.debug("createProxy");
			this.debug(event);
			
			var base, proxiUseDate, proxiDistance, proxiLonlat, proxiDate, params, aLL;

			base = $j(event.target).parent();
			proxiUseDate = ( base.find("input#uDate").val() === "on" )? 1 : 0;
			proxiDistance = parseInt( base.find("input#mDistance").val() ,10);
			proxiLonlat = base.find("dl dt:eq(0)").html();
			proxiDate = base.find("dl dt:eq(1)").html();

			//this.debug(proxiUseDate + " :: " + distance + " :: " + lonlat);

			params = {};
			params.proxiId = this.view.evnt_title;
			params.filters = {};

			params.parProxi = 1;
			params.filters.lonlat = proxiLonlat;

			aLL = proxiLonlat.split(" ");
			params.filters.parGeom = {
										lonlat : {lon: parseFloat(aLL[0]), lat:parseFloat(aLL[1])},
										distance : proxiDistance
										};

			if( proxiUseDate )
			{
				params.filters.parDate = {
											filterId: "par-date",
											data: [proxiDate]
											};
			}
			
			EvntBoards.createEvntList(null,params);
		};

		this.parseData = function(data){

			this.debug(data);
			var _this = this,
				board = $j('#e-'+ _this.id),
				tmpl, resume_content, aLieu, aLieuRight, sv_holder, aDetails, Op, Unit, sTime, oData, oD, html_content,
				actualCode, aHisto, hTime, aTime, oHdata, html;

			/// 24H INTEGRATION *****
			if( data.status === 2 ){
				board.find('.loading').remove();
				alert("Votre session est terminée. Vous devez recharger la page et vous identifier de nouveau pour accéder aux données.");
				return;
			}
			/// RESUME
/*
			tmpl = $j('#tmpl-resume').html();
			resume_content = Mustache.to_html(tmpl, data.results.resume);
			board.find("div.details").find("#tabs-1").find(".i-content").append(resume_content);
*/

			board.find("div.details").find("#tabs-1").find(".i-content").append(data.results.resumeHtml+'<div class="lieu-infos"></div>');
			
			aLieu = [];
			aLieuRight = [];
			if(data.results.overview_data.valid !== 'null' && data.results.overview_data.valid > 0)
			{

/*
				aLieu.push( "<dt>Adresse</dt>");
				aLieu.push( "<dd>"+data.results.raw_data.CHLOCN.value +"</dd>" );

				if(!g_atlas.isUndefined(data.results.raw_data.CHXCOORD) ){
					aLieu.push( "<dd>"+ data.results.raw_data.CHXCOORD.value +"</dd>" );	
				}

				if(!g_atlas.isUndefined(data.results.raw_data.arr_desc) ){
					aLieuRight.push( "<dt>Arrondissement</dt>"  );
					aLieuRight.push( "<dd>"+ data.results.raw_data.arr_desc.value +"</dd>" );
				}

				if(!g_atlas.isUndefined(data.results.raw_data.quartier_desc) ){
					aLieuRight.push( "<dt>Quartier</dt>"  );
					aLieuRight.push( "<dd>"+ data.results.raw_data.quartier_desc.value +"</dd>" );
				}

				if(!g_atlas.isUndefined(data.results.raw_data.atome_no) ){
					
					aLieuRight.push( "<dt>Atome</dt>"  );
					aLieuRight.push( "<dd>"+ data.results.raw_data.atome_no.value +"</dd>" );
				}

				if( !g_atlas.isUndefined(data.results.overview_data.lon) &&  !g_atlas.isUndefined(data.results.overview_data.lat)){
					aLieuRight.push( "<dt>Coordonnées. Type (" +data.results.overview_data.valid +")</dt>"  );
					aLieuRight.push( "<dd>"+data.results.overview_data.lon + ' ' + data.results.overview_data.lat +"</dd>" );

				}
				sv_holder = '<div id="g-sview-wide-'+_this.id+'" style="background-color:#ddd;width:100%;height:230px;float:left;clear:both;margin-top:10px;"></div>';
				board.find("div.details").find("#tabs-1").find(".lieu-infos").html("<div style='float:left;width:50%;'><dl>"
					//+aLieu.join('')
					+data.results.localisationHtml
					+"</dl></div><div style='border-left:2px solid #cf0;float:left;padding-left:20px;width:40%;'><dl>"
					//+aLieuRight.join('')
					+data.results.localisationHtml
					+"</dl></div>").append(sv_holder);
				_this.initStreetView( 'g-sview-wide-'+_this.id,{lon:data.results.overview_data.lon, lat:data.results.overview_data.lat});
*/

				sv_holder = '<div id="g-sview-wide-'+_this.id+'" style="background-color:#ddd;width:100%;height:230px;float:left;clear:both;margin-top:10px;"></div>';
				board.find("div.details").find("#tabs-1").find(".lieu-infos").html("<div style='float:left;width:50%;'><dl>"
					+data.results.localisationLeftHtml
					+"</dl></div><div style='border-left:2px solid #cf0;float:left;padding-left:20px;width:40%;'><dl>"
					+data.results.localisationRightHtml
					+"</dl></div>").append(sv_holder);
				_this.initStreetView( 'g-sview-wide-'+_this.id,{lon:data.results.overview_data.lon, lat:data.results.overview_data.lat});
			}
			else
			{
				aLieu.push( "<dt>Adresse Non géocodée</dt>");
				aLieu.push( "<dd>"+data.results.raw_data.CHLOCN.value +"</dd>" );
				board.find("div.details")
					.find("#tabs-1")
					.find(".quick-infos")
					.append("<dl>"+aLieu.join('')+"</dl>");
			}

			// DETAILS 
			aDetails = [];
			if( data.results.details.length === 0 )
			{
				board.find("div.details").find("#tabs-2").find(".i-content").html("<p>AUCUNE SÉQUENCE POUR CET APPEL.</p>");
			}
			else
			{
/*
				$j.each(data.results.details, function(index, val)
				{
				
					Op = (val.Operateur === null )?'Opérateur non identifié.':'Opérateur: '+ val.Operateur + '.';
					Unit = (val.Unit === null)?'Unité non identifiée.':'Unité: '+ val.Unit + '.';
					sTime = (val.Heure.length < 4)? val.Heure.substring(0,1)+":"+val.Heure.substring(1,3):val.Heure.substring(0,2)+":"+val.Heure.substring(2,4);
					
					oData = {
								e_sequence:val.Sequence,
								e_date: val.DateActive.substring(0,11),
								e_time: sTime,
								e_segment_desc: val.Segment,
								e_operateur:Op,
								e_unit:Unit,
								e_details:val.Details
								};
					aDetails.push(oData);
				});

				oD = {sequences:aDetails};

				tmpl = $j('#tmpl-sequences').html();
				html_content = Mustache.to_html(tmpl, oD);
				board.find("div.details").find("#tabs-2").find(".i-content").html(html_content);
*/
				board.find("div.details").find("#tabs-2").find(".i-content").html(data.results.sequenceHtml);
			}

			// HISTORY 
			if (data.results.history.results_count > 1 )
			{
			
				actualCode = data.results.overview_data.call_id;
				aHisto = [];
				
				$j.each(data.results.history.results, function(index, val)
				{
					if (val.call_id === actualCode){ 
						return;
						}
					
					hTime = val.call_dt.substring(11);
					aTime = hTime.split('.');
					oHdata = {
								card_id:val.call_id,
								card_date: val.call_dt.substring(0,11),
								card_time: aTime[0],
								card_desc:val.call_desc,
								card_lon:val.lon,
								card_lat:val.lat,
								card_geom:val.geom_valid,
								distance:val.distance
								};
					aHisto.push(oHdata);
				});

				oD = {cards:aHisto};
				tmpl = $j('#tmpl-card').html();
				html_content = Mustache.to_html(tmpl, oD);
				board.find("div.details").find("#tabs-3").find(".i-content").html(html_content);
				
				// DELEGATE
				board.find("div.details")
					.find("#tabs-3")
					.delegate('dl', 'click', function(event) {
						DEBUG('DELEGATING HISTORY EVNTS');
						EvntBoards.createEvntDetails(event);
					});
			}
			else
			{
				board.find("div.details").find("#tabs-3").find(".i-content").html("<p>AUCUN HISTORIQUE POUR CET APPEL.</p>");
			}

			// RAW DATA TERMINAL STYLE 
			html = '';
			$j.each(data.results.raw_data, function(i, val)
			{
				html += '<li><abbr title="' + val.desc + '">'+i+'</abbr> => '+ val.value + '</li>';
			});

			board.find("div.details")
				.find("#tabs-4")
				.find(".i-content")
				.html("<ul>"+html+"</ul>");

			/// 24H INTEGRATION END *****

			// REMOVE THE TAB AJAX SPINNER
			//$j("#boards>ul>li").eq(this.tab_index).find('a').removeClass("loading");
			//$j("#boards>ul>li").eq(this.id).find('a').removeClass("loading");
			this.tab.find("a").removeClass('loading');

			// REMOVE DETAILS SPINNER
			board.find("div.details").find("#loading").hide();
			

			board.find('div.server-loader').hide();
			board.find('#ui-tabs').tabs().css("left","0px");
		};

		this.debug = function(log){
			if (DEBUG_MODE && typeof(console) !== 'undefined'){
				console.log(log);
				//debugger; 
			}
		};
	};

	return function (params) {
		return new Details(params);
	};

}());

/* ! EvntBoards MAIN OBJECT  */
var EvntBoards = {

	version: 1,
	description: 'EvntBoard',
	name: 'board',
	clock: '#clock',
	options:{ajax_controller:'raoevnts',tab_default_name:'RAO 911',data_type:'appel'},
	boards: [],
	filters:[],
	init:function(){

		DEBUG('EvntBoards INIT');

/*
		$j('#main-board').find('div.calls_list').delegate('dl', 'click', function(event) {
			DEBUG('DELEGATING');
			EvntBoards.createEvntDetails(event);
		});
*/
		
		//// TODO Check if we have an id? /id/#923427/
		//// If so, create a detail instead + button to open an EvntList


		if( sf.ref ){
			EvntBoards.createEvntDetails( null, { first:true, 'ref': sf.ref });
		}else{
			EvntBoards.createEvntList(null, {first:true, filters:sf});
		}
	},
	getOptions:function(){
		return this.options;
	},
	setOptions:function(options){
		this.options = options;
		return this.options;
	},
	createEvntList:function(event, params){
		var L;
		params = params || {};
		params.event = event;
		params.pos = this.boards.length;
		
		L = new EvntList(params);
		L.createView();
		EvntBoards.boards.push( L );


	},
	createEvntDetails:function(event, params){
		var D;
		params = params || {};
		params.event = event;
		params.pos = this.boards.length;
	
		D = new EvntDetails(params);
		EvntBoards.boards.push( D );
		D.createView();

	},
	addBoard:function(){
		EvntBoards.ids.push(EvntBoards.name +'_'+(EvntBoards.ids.length+1));
	},
	removeBoard:function(ui){
		DEBUG("EvntBoards.removeBoard()");
		DEBUG(ui);
		var hash = ui.tab.hash,
			index = hash.substring(8,hash.length);
		DEBUG("index:"+ index);
		EvntBoards.boards[index] = 'deleted';
		DEBUG(EvntBoards.boards);
	}
};