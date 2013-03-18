var ga = {};
var map, layer, parser;
var zoom = 12;
var eventinfos = [];
var templates = [];

// Leaflet Default Icon
var MyIcon = L.Icon.extend({
			    iconUrl: BASE_URL+'public/imgs/interface/leaflet/marker.png',
			    shadowUrl:BASE_URL+'public/imgs/interface/leaflet/marker-shadow.png'/*
,
			    iconSize: new L.Point(25, 41),
			    shadowSize: new L.Point(41, 41),
			    iconAnchor:new L.Point(13,41),
			    popupAnchor:new L.Point(0,-33)
*/
			});

var icon = new MyIcon();

function showEvents(e)
{
	$j("#last24hour div.filter-box-mod").addClass("noDisplay");
	$j(e).each(function(){
		$j('.'+this).removeClass("noDisplay");
	});
	
	// Header
	$j("#showAllEvents").removeClass("noDisplay");
}

function showAllEvents()
{
	$j("#last24hour div").removeClass("noDisplay");
	$j("#showAllEvents").addClass("noDisplay");
}

function zoomTo(lon,lat)
{
	if( lon==0 || lat==0)
	{
		alert("La géoréférence de cet événement est partielle ou inconnue.");
	}
	else
	{
		var point = new OpenLayers.LonLat(lon, lat);
		map.setCenter( point.transform(serverProj, map.getProjectionObject()),5,false,true);
	}
}

function filter_24h( filterName, param ){
	var evntsList, len, header=[], items2hide, items2show;
	
	$j('#w_panel').show();
	
	items2hide =	$j("div.eventInfos")
						.find("dt."+filterName+":not(:contains('"+param+"'))")
						.parents(".eventInfos");
						
	items2show =	$j("div.eventInfos")
						.find("dt."+filterName+":contains('"+param+"')")
						.parents(".eventInfos");						
	
	if( eventinfos.length === 0 ) eventinfos = $j.merge(items2hide,items2show);

	len = items2show.length;
	evntsList = $j('#last24hour');
	
	evntsList.find("div.evnts-cluster").remove();
								
	header.push( '<div class="evnts-header evnts-cluster">' );
		header.push( '<div class="filter-box-mod left_area">' );
			header.push( '<dl class="event font_style" >' );
				header.push( '<dt style="right:10px;top:2px;"><a href="#" class="close" ></a></dt>' );
				header.push( '<dd>Filtre ('+param+') '+len+' appel(s)</dd>' );			
			header.push( '</dl>' );
		header.push( '</div>' );
	header.push( '</div>' );
	
	evntsList.find("div.evnts-header").hide();
	
	evntsList.prepend( header.join('') );
	$j("div.evnts-cluster")
		.unbind("click")
		.bind("click", function()
		{
			$j('#status').text("suppression du filtre ...");
			$j('#w_panel').show();
			runFilter(eventinfos, eventinfos.length, 'show_all', function(){
				evntsList.find("div.evnts-cluster").remove();
				oEv.clearFilter();
				$j('#w_panel').hide();
			});

			$j(this).unbind("click");
		});
	
	runFilter(items2hide, items2hide.length, 'hide', function(){
		$j('#w_panel').hide();
		
		runFilter(items2show, len, 'show', function( ids ){ 
			oEv.setFilter(ids);
			});
		});
}

runFilter = function (e, l, sh, onComplete){

    var pos = 0;
	var step = 100;
	var call_ids = [];

    (function(){
    	//console.log("RUN FILTER ("+sh+"):: batch => " + pos);
    	$j('#status').text("traitement du filtre "+pos+"/"+l+" ...");
		var s = pos+step;
		if( sh === 'hide')
		{
	        for(var i= pos;i<s;i++)
			{
				if (i >= l)	break;
		
				$j(e[i]).hide();
			}		
		}
		
		if( sh === 'show')
		{
	        for(var i= pos;i<s;i++)
			{
				if (i >= l)	break;
		
				call_ids.push( $j(e[i])
								.show()
								.attr('id')
								.substring(5) );
			}		
		}

		if( sh === 'show_all')
		{
	        for(var i= pos;i<s;i++)
			{
				if ( i >= l )	break;
				$j(e[i]).show();
			}		
		}

        pos = s;
        if (pos < l)
        {
            setTimeout(arguments.callee,10);
        } else {
        	$j('#status').text("");
        	onComplete(call_ids);
        }
    })();
}

function list_cluster_24h( call_ids ){
	var evntsList, len, header=[], items2hide, items2show;
	
	$j('#w_panel').show();
	
	items2hide = $j("div.eventInfos");		
	items2show = call_ids;
	len = items2show.length;
	evntsList = $j('#last24hour');
	
	evntsList.find("div.evnts-cluster").remove();
								
	header.push( '<div class="evnts-header evnts-cluster">' );
		header.push( '<div class="filter-box-mod left_area">' );
			header.push( '<dl class="event font_style" >' );
				header.push( '<dt style="right:10px;top:2px;"><a href="#" class="close" ></a></dt>' );
				header.push( '<dd>Regroupement de '+len+' appel(s)</dd>' );			
			header.push( '</dl>' );
		header.push( '</div>' );
	header.push( '</div>' );
	
	evntsList.find("div.evnts-header").hide();

	evntsList.prepend( header.join('') );
	$j("div.evnts-cluster")
		.unbind("click")
		.bind("click", function()
		{	
			DEBUG("CLICK BINDING");
			$j('#w_panel').show();
			evntsList.find("div.eventInfos").show();
			evntsList.find("div.evnts-header").show();
			evntsList.find("div.evnts-cluster").remove();
			$j('#w_panel').hide();
			$j(this).unbind("click");
		});
	
	runFilter(items2hide, items2hide.length, 'hide', 
		function(){ 
			runCluster(items2show, len, function(){
				$j('#w_panel').hide();
				});
			});
}

runCluster = function (e, l, onComplete){

    var pos = 0;
	var step = 100;

	//console.log(arguments);
    (function(){
    	//console.log("RUN CLUSTER :: batch => " + pos);
    	$j('#status').text("traitement du regroupement ("+pos+") ...");
		var s = pos+step;
		

        for(var i= pos;i<s;i++)
		{
			if (i >= l)	break;
			
			$j("div.eventInfos#call_"+e[i]).show();
		}		
		
        pos = s;
        if (pos < l)
        {
            setTimeout(arguments.callee,10);
        } else {
        	$j('#status').text("");
           	onComplete();
        }
    })();
}

/* !onReady */
$j(document).ready(function(){

	/// CLEANING UP THE TEMPLATE FROM THE CODE
	// INJECT TMPL in DOM + insert in array + remove from DOM
	var tmpTmpl = $j('footer').append( $j('#tmpl').html() ).addClass('noDisplay');
	DEBUG( '** Template(s) trouvée(s)-> '+ $j(tmpTmpl).find('.tmpl').length );
	$j(tmpTmpl).find('.tmpl').each(function(){
		templates[ $j(this).get(0).id ] = $j(this).html();
		$j(this).remove();
	})
	$j(tmpTmpl).remove();

	/// MAP EVENT LISTENER
	//function listnerMoveEnd(event){}

	/// CLEAR THE POPUP-INFO IF VISIBLE
	//function listnerMoveStart(event){}

	/// STATISTIQUE 24H SELECT BOX
/*
	$j('#par_code').bind('change', function(e){
		t = Number($j(this).val());
		$j(this).parent().parent().find('ul').addClass('noDisplay');
		$j('#par_code_'+t).removeClass('noDisplay');
	}).change();
*/
	
	/// CREATE LEAFLET MAP
	 map = new L.Map('mapPort', {'scrollWheelZoom':false});
	var markersLayer = new L.LayerGroup();
    	
    var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		osmAttrib='Map data © openstreetmap contributors',
		osm = new L.TileLayer(osmUrl,{minZoom: 8, maxZoom:18, attribution: osmAttrib});

    var osmLocalUrl= BASE_URL+'public/maps/sjsr_roads/{z}/{x}/{y}.png',
		osmLocalAttrib='© openstreetmap',
		osmLocal = new L.TileLayer(osmLocalUrl,{minZoom: 9, maxZoom:17, attribution: osmLocalAttrib, scheme:'tms', format: 'image/png',transparent: true});
	
	var lim_admin_url = BASE_URL+'public/maps/sjsr_secteurs/{z}/{x}/{y}.png',
		lim_admin_attrib='GAtlas © 2011';
	var lim_admin = new L.TileLayer(lim_admin_url, {minZoom: 9,maxZoom: 17, attribution: lim_admin_attrib, format: 'image/png',transparent: true});
	
	var geojsonLayer = new L.GeoJSON(null);
	
	
	var canvasTiles = new L.TileLayer.Canvas();

/*
	geojsonLayer.setStyle(
			 {
	            "color": "#ff0000",
	            "weight": 4,
	            "opacity": 0.9
	        }
	);
*/
	
/*
	geojsonLayer.on("featureparse", function (e){
	    if (e.properties && e.properlties.style && e.layer.setStyle){
	        // The setStyle method isn't available for Points. More on that below ...
	        e.layer.setStyle(e.properties.style);
	    }
	});
	
*/

var defaultStyle = {
    color: '#666', 
    weight: 0.2,
    opacity: 0.9,
    fillOpacity: 0.4,
    fillColor: '#fff'
};
var highlightStyle = {
    color: '#333', 
    weight: 1,
    opacity: 1,
    fillOpacity: 0,
    fillColor: '#fff'
};
	geojsonLayer.on("featureparse", function (e){
  		e.layer.setStyle(defaultStyle); 
  		
  		// Create a self-invoking function that passes in the layer
	    // and the properties associated with this particular record.
	    (function(layer, properties) {
	      // Create a mouseover event
	      layer.on("mouseover", function (e) {
	        // Change the style to the highlighted version
	        layer.setStyle(highlightStyle);
	        // Create a popup with a unique ID linked to this record
	        var popup = $j("<div></div>", {
	            id: "popup-" + properties.hex_id,
	            css: {
	                position: "absolute",
	                bottom: "85px",
	                left: "50px",
	                zIndex: 100002,
	                backgroundColor: "white",
	                padding: "8px",
	                border: "1px solid #ccc"
	            }
	        });
	        // Insert a headline into that popup
	        var hed = $j("<div></div>", {
	            text: "Hex ID : " + properties.hex_id,
	            css: {fontSize: "16px", marginBottom: "3px"}
	        }).appendTo(popup);
	        // Add the popup to the map
	        popup.appendTo("#mapPort");
	      });
	      // Create a mouseout event that undoes the mouseover changes
	      layer.on("mouseout", function (e) {
	        // Start by reverting the style back
	        layer.setStyle(defaultStyle); 
	        // And then destroying the popup
	        $j("#popup-" + properties.hex_id).remove();
	      });
	      // Close the "anonymous" wrapper function, and call it while passing
	      // in the variables necessary to make the events work the way we want.
	    })(e.layer, e.properties);
	});

	geojsonLayer.addGeoJSON(geojsonFeature);
	
	
	var sjsr = new L.LatLng( 46.723301297642678,-71.507612794586862);
	//var sjsr = new L.LatLng(45.3,-73.26); 
	map.setView(sjsr, 13).addLayer(osm).addLayer(geojsonLayer).addLayer(canvasTiles);
	//map.setView(sjsr, 13).addLayer(osm).addLayer(lim_admin).addLayer(geojsonLayer).addLayer(markersLayer);
	DEBUG( map.getBounds() );
	
	
		//return false;
	
	
	map.on('click', function(e) {
		DEBUG('MAP CLICK');
		//DEBUG(e);
	});

	
	markerClick = function(e) {
		DEBUG('markerClip CLICK');
		DEBUG(e);
		DEBUG(e.target.data.desc);
	}

		
    /// RAPHAEL VECTORS
    $j("#mapPort").find("div").eq(1).prepend('<div id="r_map" style="border:0;height:100%;position:absolute;left:0;top:0;">test</div>');
	r_clusters = Raphael($j("#r_map")[0], 564, 600);
	st = r_clusters.set();
	ev = r_clusters.set();
	
	/// RAO 911
    oEv = new eventManager(map,markersLayer,{});
	
	/// MAP EVENTS
	//map.events.register('moveend', null, listnerMoveEnd);
	//map.events.register('movestart', null, listnerMoveStart);
	
	// CLICK HANDLER FOR LIST OF CALLS
	$j("div#last24hour").delegate('dl','click', function(e){
		var id =  e.currentTarget.id.substring(1);
		if( id !== '') getRef( id );
		$j("div#last24hour").find(".evt-selected").removeClass("evt-selected");
		$j(this).parent().addClass('evt-selected');
	});
	
	// TEXT COLORATION FOR THE 48Hours tendency 
	if( $j(".diff-48h >h1").text().substring(0,1) == '+'){
		$j(".diff-48h >h1").addClass("plus");
	}else{
		$j(".diff-48h >h1").addClass("minus");
	}
	
	// ASSIGNATION POUR LE MESSAGE USAGER
	$j(".alert-message").alert()
	/* !END onReady */ 
});


/// AFFICHE UNE RÉFÉRENCE
function getRef(e)
{	
	// BUILD TEMPORARY BOX
	var infos = $j("#_"+e);
	DEBUG(infos);
	var date_time = infos.find("dd:first").text();
	var desc = infos.find("dd:eq(1)").text();

	var data = {e:e,
				infos:infos,
				evnt_date: date_time.substring(0,11),
				evnt_time: date_time.substring(11),
				desc:desc
				};

	var tmpl = templates['tmpl-details-noGeo'];
	var html_content = Mustache.to_html(tmpl, data);
	
	$j('.c-details').html( html_content ).find('#ui-tabs').tabs();
	$j('.c-details').append('<div class="c-close"><a href="#" title="Fermer"></a></div>');
	//$j('.c-close').before( html_content ).parent().find('#ui-tabs').tabs();
	$j('.c-close').bind("click", function(){
		$j('.content-details').hide();
		$j("div#last24hour").find(".evt-selected").removeClass("evt-selected");
	});
	
	$j('.content-details').show();
	var sidebarWidth = $j(".sidebar").outerWidth();
	DEBUG(">>>" + sidebarWidth);
	//$j(".c-details").css('left', (sidebarWidth +20)+"px");
	$j(".c-details")
		.css('left', (sidebarWidth -200)+"px")
		.stop()
		.animate({
		    left: '+=220'
		  }, "fast", function() {
		    // Animation complete.
		});

	// LOADING DATA	
	  	$j.post(BASE_URL + "raoevnts/ref/"+ e, null, function(data)
		{
			$j("#loading").hide();

			if( data.status !== 1 ){
				DEBUG("Erreur JSON getRef !!!");
			}

			parseRefData(data);		
		},
		"json").error(
		function(request, error)
		{ 
			DEBUG("POST ERROR => " + error); 
			DEBUG( request );
		});
}

/// PARSE SERVER JSON DATA
function parseRefData(data)
{
	/// RESUME
	$j('.c-details').find("#tabs-1").find(".i-content").append(data.results.resumeHtml);
	
	lonlat = { lon:data.results.overview_data.lon, lat:data.results.overview_data.lat };
	show_map(lonlat);
	$j('.c-details').find("#tabs-1").find(".lieu-infos").html("<dl>"+data.results.localisationHtml+"</dl>");
	
	/// SEQUENCES
	if( data.results.sequenceHtml.length < 10 )
	{
		data.results.sequenceHtml = '<span class="round">Aucune séquence pour cet appel.</span>';
	}
	
	$j('.c-details').find("#tabs-2").find(".i-content").html(data.results.sequenceHtml);
	
	
	// HISTORY dynamic templating with Mustache
	if (data.results.history.results_count > 1 )
	{
		var actualCode = data.results.overview_data.call_id;
		var aHisto = [];
		$j.each(data.results.history.results, function(i, val)
		{
			if (val.call_id == actualCode) return true;
			var hTime = val.call_dt.substring(11);
			var aTime = hTime.split('.');
			var oHdata = {
							card_id:val.call_id,
							card_date: val.call_dt.substring(0,11),
							card_time: aTime[0],
							card_desc:val.call_desc,
							distance: (Math.round( val.distance *100, 2 ))/100
							};
			aHisto.push(oHdata);				
		});
		
		var oD = {cards:aHisto};
		var tmpl = templates['tmpl-card'];
		var html_content = Mustache.to_html(tmpl, oD);
		$j('.c-details').find("#tabs-3").find(".i-content").html(html_content);
	}
	else
	{
		$j('.c-details').find("#tabs-3").find(".i-content").html("Aucun historique pour cet appel.");
	}

	// RAW DATA TERMINAL STYLE 
	var html = '';
	$j.each(data.results.raw_data, function(i, val)
	{
		html += '<li><abbr title="' + val.desc + '">'+i+'</abbr> => '+ val.value + '</li>';
	});
	$j('.c-details').find("#tabs-4").find(".i-content").html("<ul>"+html+"</ul>");
}

function show_map(lonlat)
{
	var html ='<div style="margin-top:10px;border:0;padding:8px;-moz-border-radius:5px;background-color:#e6e6e6;float:left;width:96%;">';	
		html +='	<div id="g-map" style="border:2px solid #666;height:200px;width:220px;float:left;"></div>';	
		html +='	<div class="lieu-infos" style="float:right;width:350px;font-size:14px;"></div>';
		html +='</div>';							
	//$j("#cboxContent").find("#tabs-1").find(".i-content").append(html);
	$j(".c-details").find("#tabs-1").find(".i-content").append(html);
	
	g_atlas.event = {lat:lonlat.lat, lon:lonlat.lon};
	var e = g_atlas.event;

    e.map = new L.Map('g-map', {'scrollWheelZoom':false, 'dragging':false, 'touchZoom':false, 'zoomControl':false});
    	
    var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		osmAttrib='Map data © openstreetmap contributors',
		osm = new L.TileLayer(osmUrl,{minZoom: 10, maxZoom:18, attribution: osmAttrib});	
    
    var callPos = new L.LatLng(g_atlas.event.lat,g_atlas.event.lon); 
   	e.map.setView(callPos, 17).addLayer(osm);
    
    // MARKER
    var marker = new L.Marker(callPos, {'icon':icon});
    e.map.addLayer(marker);
}

/// TOGGLE MAP FULLVIEW // CENTERVIEW
function toggleView()
{
	if( $j("#map-viewer").css('width') == '100%' )
	{
		var p = map.getCenter();
		$j("#map-viewer").css({width:'57%'});
		$j("#stats").show();
		map.setCenter( p);
		$j("#toggle-view").css({backgroundPosition:'top left'});
	}
	else
	{
		var p = map.getCenter();
		$j("#stats").hide();
		$j("#map-viewer").css({width:'100%'});
		$j("#toggle-view").css({backgroundPosition:'bottom left'});
		map.setCenter( p);
	}
	
	oEv.update(true);
}

/// CENTRE LA CARTE SUR UN POINT, ZOOM FIXE(17)
function toCenter(e)
{
		var o = oEv.aEvents[oEv.aLookup[e]];
		//console.log("TO CENTER  >>>");
		//console.log(o);
		map.setCenter( o.point,17);

		// OUVERTURE AUTOMATIQUE DU POPUP
		if( o.vector.marker != undefined)
		{
			$j(o.vector.marker.node).trigger('test', [t,e]);
		}
		/// TODO make it works with clusters usin aEvent.aPointers
}

/// ERROR HANDLER IN CASE NO FLASH IN BROWSER
function handleNoFlash(errorCode) {
  if (errorCode == 603)
  {
    alert("Error: Flash doesn't appear to be supported by your browser");
    return;
  }
}

/// XYZ FONCTION FOR HEATMAP
function get_my_url (bounds) {
    var res = this.map.getResolution();
    //console.info(this.map.getResolution());
    // console.info("bounds");
    // console.info(bounds);
    var x = Math.round ((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
    var y = Math.round ((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
    var z = this.map.getZoom();

    var path = z + "/" + x + "/" + y + "." + this.type;
    var url = this.url;
    if (url instanceof Array) {
        url = this.selectUrl(path, url);
    }
    return url + path;
}

function scrollContent(n,pos)
{
	if(!g_atlas.event.scroll)
	{
		g_atlas.event.scroll = 0;
		$j('.infoPop-scroller').css({left:'0'});
		g_atlas.event.flag = 1;
	}
	
	if(  g_atlas.event.flag == 0){
		return;
	}
	
	if( n == null && pos > 0 )
	{
		g_atlas.event.scroll = 0;
		//console.log('Scroll Content pos=> '+pos);

		if(g_atlas.event.scroll < (g_atlas.event.maxscroll -1 ) )
		{
			$j('a.back').css({backgroundColor:'#ccc'});
			var newPos = (pos -1);
			g_atlas.event.scroll = newPos;
			g_atlas.event.flag = 0;
			
			//console.log('Positionning');
			var l = newPos * 333;
			$j('.infoPop-scroller').animate({left:'-='+l}, 'fast', function()
			{
				if(g_atlas.event.scroll == (g_atlas.event.maxscroll -1 ) ) 
				{
					$j('a.next').css({backgroundColor:'#666'});
				}
				
				g_atlas.event.flag = 1;
				//console.log('END Positionning');
			});
		}
	}
	else
	{
		if(n == 'next')
		{
			if(g_atlas.event.scroll < (g_atlas.event.maxscroll -1 ) )
			{
				$j('a.back').css({backgroundColor:'#ccc'});
				g_atlas.event.scroll ++;
				g_atlas.event.flag = 0;
				
				$j('.infoPop-scroller').animate({left:'-=333'}, 'fast', function()
				{
					if(g_atlas.event.scroll == (g_atlas.event.maxscroll -1 ) )
					{
						$j('a.next').css({backgroundColor:'#666'});
					}
					
					g_atlas.event.flag = 1;
				});
			}				
		}
		else
		{
			if(g_atlas.event.scroll > 0 )
			{
				$j('a.next').css({backgroundColor:'#ccc'});
				g_atlas.event.scroll --;
				g_atlas.event.flag = 0;
				
				$j('.infoPop-scroller').animate({left:'+=333'}, 'fast', function()
				{
					
					if(g_atlas.event.scroll == 0 )
					{
						$j('a.back').css({backgroundColor:'#666'});
					}
					
					g_atlas.event.flag = 1;
				});
			}				
		}
	}
}