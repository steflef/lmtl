var ga = {};
var map, layer, parser;
var zoom = 12;
var eventinfos = [];
var templates = [];


/*
OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
    defaultHandlerOptions: {
        'single': true,
        'double': false,
        'pixelTolerance': 0,
        'stopSingle': false,
        'stopDouble': false
    },

    initialize: function(options) {
        this.handlerOptions = OpenLayers.Util.extend(
            {}, this.defaultHandlerOptions
        );
        OpenLayers.Control.prototype.initialize.apply(
            this, arguments
        ); 
        this.handler = new OpenLayers.Handler.Click(
            this, {
                'click': this.trigger
            }, this.handlerOptions
        );
    }, 

});
*/

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
			//console.log("CLICK");
			$j('#status').text("suppression du filtre ...");
			$j('#w_panel').show();
			runFilter(eventinfos, eventinfos.length, 'show_all', function(){
				//evntsList.find("div.evnts-header").show();
				evntsList.find("div.evnts-cluster").remove();
				//console.info('SHOW ALL FILTER'); 
				oEv.clearFilter();
				$j('#w_panel').hide();
			});

			$j(this).unbind("click");
		});
	
	runFilter(items2hide, items2hide.length, 'hide', function(){ 
		//console.info('HIDE FILTER'); 
		$j('#w_panel').hide();
		
		runFilter(items2show, len, 'show', function( ids ){ 
			//console.info('SHOW FILTER');  
			oEv.setFilter(ids);
			});
		});
}

runFilter = function (e, l, sh, onComplete){

    var pos = 0;
	var step = 100;
	//var l_up = this.aLookup;
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
			//DEBUG("CLICK BINDING");
			$j('#w_panel').show();
			evntsList.find("div.eventInfos").show();
			evntsList.find("div.evnts-header").show();
			evntsList.find("div.evnts-cluster").remove();
			$j('#w_panel').hide();
			$j(this).unbind("click");
		});
	
	runFilter(items2hide, items2hide.length, 'hide', 
		function(){ 
			//console.info('HIDE CLUSTER FILTER'); 
			runCluster(items2show, len, function(){ 
				//console.info('SHOW FILTER'); 
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
	//console.log( $j('.tmpl').length );
	$j('.tmpl').each(function(){
		//console.log($j(this).get(0).id);
		templates[ $j(this).get(0).id ] = $j(this).html();
		$j(this).remove();
	})

	/// MAP EVENT LISTENER
	function listnerMoveEnd(event){}

	/// CLEAR THE POPUP-INFO IF VISIBLE
	function listnerMoveStart(event){}

	// STATISTIQUE 24H SELECT BOX
	$j('#par_code').bind('change', function(e){
		t = Number($j(this).val());
		$j(this).parent().parent().find('ul').addClass('noDisplay');
		$j('#par_code_'+t).removeClass('noDisplay');
	}).change();
		
   	/// OL OPTIONS
   	OpenLayers.Util.onImageLoadError = function() {
        this.src = BASE_URL + "public/js/ol/img/blank.gif";
    };
	OpenLayers.IMAGE_RELOAD_ATTEMPTS = 1;

	serverProj = new OpenLayers.Projection("EPSG:4326"); //Google & server Mtl rues
	serverProj_2 = new OpenLayers.Projection("EPSG:4326"); //Google & server Mtl rues
	proj = new OpenLayers.Projection("EPSG:900913"); // OpenLayer Mercator

	/// MAP
    var options = {
            projection: proj,
            units: "m",
            maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
          	resolutions: [156543.0339,78271.51695,39135.758475,19567.8792375,9783.93961875,4891.96980938,2445.98490469,1222.99245234,611.496226172,305.748113086,152.874056543,76.4370282715,38.2185141357,19.1092570679,9.55462853394,4.77731426697,2.38865713348,1.19432856674,0.597164283371],
            controls: [],
            useOverlayPopups: false
        };
        	
	map = new OpenLayers.Map( 'mapPort', options );
		
	/// CONTROLS
	nav = new OpenLayers.Control.Navigation({'zoomWheelEnabled': false});
	map.addControl( nav );

	var panel = new OpenLayers.Control.Panel();
        panel.addControls([
			new OpenLayers.Control.ZoomIn({	displayClass: "zoomIn", title:'Zoom IN'}),
			new OpenLayers.Control.ZoomOut({ displayClass: "zoomOut", title:'Zoom OUT'})
        ]);
    
    map.addControl(panel);
    //map.addControl(new OpenLayers.Control.LayerSwitcher());

	/// LAYERS ======================
	
	/// OPEN STREET MAP
	//!!!  TODO DYN
	var osm = new OpenLayers.Layer.OSM("OMS",null,{buffer:1});

	//!!!  TODO DYN
	var lim_admin = new OpenLayers.Layer.TileCache("Limites administratives",
       "http://localhost/sjsr/public/maps/",
        "sjsr_limites_admin_tms",
        {
			isBaseLayer: false,
			buffer:1,
			format: "image/png",
			serverResolutions: [156543.0339,78271.51695,39135.758475,19567.8792375,9783.93961875,4891.96980938,2445.98490469,1222.99245234,611.496226172,305.748113086,152.874056543,76.4370282715,38.2185141357,19.1092570679,9.55462853394,4.77731426697,2.38865713348,1.19432856674,0.597164283371],
			//resolutions: [76.4370282715,38.2185141357]      
			resolutions: [156543.0339,78271.51695,39135.758475,19567.8792375,9783.93961875,4891.96980938,2445.98490469,1222.99245234,611.496226172,305.748113086,152.874056543,76.4370282715,38.2185141357,19.1092570679,9.55462853394,4.77731426697,2.38865713348,1.19432856674,0.597164283371]
        }
        
    );
        
    /// RAO 911
	rao_911 = new OpenLayers.Layer('RAO 911'); 

	/// ====================== LAYERS 
	map.addLayers([osm,lim_admin, rao_911]);
			
	/// CENTRER LA CARTE SUR SJSR
	//!!!  TODO DYN
	//var point = new OpenLayers.LonLat(-71.3, 46.8); //QC
	var point = new OpenLayers.LonLat(-73.26, 45.3); //SJSR
	map.setCenter( point.transform(serverProj, map.getProjectionObject()),12);
		
    /// RAPHAEL VECTORS
	$j(rao_911.div).prepend('<div id="r_map" style="border:0;height:100%;position:absolute;left:0;top:0;">test</div>');
	r_clusters = Raphael($j("#r_map")[0], 564, 600);

	st = r_clusters.set();
	ev = r_clusters.set();
	
	/// RAO 911
    oEv = new eventManager({});
	
	/// MAP EVENTS
	map.events.register('moveend', null, listnerMoveEnd);
	map.events.register('movestart', null, listnerMoveStart);
	
	$j("div#last24hour").delegate('dl','click', function(e){
		var id =  e.currentTarget.id.substring(1);
		if( id !== '') getRef( id );
		$j("div#last24hour").find(".evt-selected").removeClass("evt-selected");
		$j(this).parent().addClass('evt-selected');
	});
	
	
	if( $j(".diff-48h >h1").text().substring(0,1) == '+'){
		$j(".diff-48h >h1").addClass("plus");
	}else
	{
		$j(".diff-48h >h1").addClass("minus");
	}
	
	//$j(".alert-message").alert()
	/* !END onReady */ 
});

/// AFFICHE UNE RÉFÉRENCE GÉORÉFÉRENCÉE
/*
function getRef(e){
	getRef_sansGeo(e);
}
*/

/// AFFICHE UNE RÉFÉRENCE NON-GÉORÉFÉRENCÉE
//function getRef_sansGeo(e)
function getRef(e)
{	
	// BUILD TEMPORARY BOX
	var infos = $j("#_"+e);
	//console.log(infos);
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
	//console.log(">>>" + sidebarWidth);
	//$j(".c-details").css('left', (sidebarWidth +20)+"px");
	$j(".c-details")
		.css('left', (sidebarWidth -200)+"px")
		.stop()
		.animate({
		    left: '+=220'
		  }, "fast", function() {
		    // Animation complete.
		});
	
	
	//console.log( "tabs = > "+ $j('.c-details').find('#ui-tabs').length);
	//$j('.c-details').find('#ui-tabs').tabs();
	//return true;
/*
	$j.colorbox({width:'600px', height:'662px', html:html_content, opacity:0.6, scrolling:false, onComplete:function(){
		$j("#cboxContent").find('#ui-tabs').tabs();
	}});
*/

	// LOADING DATA	
	  	$j.post(BASE_URL + "raoevnts/ref/"+ e, null, function(data)
		{
			$j("#loading").hide();

			if( data.status !== 1 ){
				//console.log("Erreur JSON getRef !!!");
			}

			parseRefData(data);		
		},
		"json").error(
		function(request, error)
		{ 
			//console.info("POST ERROR => " + error); 
			//console.log( request );
		});
}

/// PARSE SERVER JSON DATA
function parseRefData(data)
{
	/// RESUME
	//$j("#cboxContent").find("#tabs-1").find(".i-content").append(data.results.resumeHtml);
	$j('.c-details').find("#tabs-1").find(".i-content").append(data.results.resumeHtml);
	
	lonlat = { lon:data.results.overview_data.lon, lat:data.results.overview_data.lat };
	show_map(lonlat);
	//$j("#cboxContent").find("#tabs-1").find(".lieu-infos").html("<dl>"+data.results.localisationHtml+"</dl>");
	$j('.c-details').find("#tabs-1").find(".lieu-infos").html("<dl>"+data.results.localisationHtml+"</dl>");
	
	/// SEQUENCES
	if( data.results.sequenceHtml.length < 10 )
	{
		data.results.sequenceHtml = '<span class="round">Aucune séquence pour cet appel.</span>';
	}
	
	//console.log( 'SEQUENCES -> ' +data.results.sequenceHtml);
	
	//$j("#cboxContent").find("#tabs-2").find(".i-content").html(data.results.sequenceHtml);
	$j('.c-details').find("#tabs-2").find(".i-content").html(data.results.sequenceHtml);
	
	
	// HISTORY dynamic templating with Mustache
	if (data.results.history.results_count > 1 )
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
		//$j("#cboxContent").find("#tabs-3").find(".i-content").html(html_content);
		$j('.c-details').find("#tabs-3").find(".i-content").html(html_content);
	}
	else
	{
		//$j("#cboxContent").find("#tabs-3").find(".i-content").html("Aucun historique pour cet appel.");
		$j('.c-details').find("#tabs-3").find(".i-content").html("Aucun historique pour cet appel.");
	}

	// RAW DATA TERMINAL STYLE 
	var html = '';
	$j.each(data.results.raw_data, function(i, val)
	{
		html += '<li><abbr title="' + val.desc + '">'+i+'</abbr> => '+ val.value + '</li>';
	});
	//$j("#cboxContent").find("#tabs-4").find(".i-content").html("<ul>"+html+"</ul>");
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
    
   var options = {
        projection: proj,
        units: "m",
        maxResolution: 156543.0339,
        maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
        controls: [],
        useOverlayPopups: false
    };
        	
	e.map = new OpenLayers.Map( 'g-map', options );
	var m = e.map;

	var g_osm = new OpenLayers.Layer.OSM("OMS",null,{buffer:0});	

	e.icons = new OpenLayers.Layer('Icons',{});

	m.addLayers([ g_osm, e.icons]);
	//m.addLayers([ g_osm]);
	
	//console.log('lon => '+e.lon + '  lat=>'+e.lat);
	var point = new OpenLayers.LonLat(Number(e.lon), Number(e.lat));
	m.setCenter( point.transform(serverProj, m.getProjectionObject()), 15 );
	
	//console.log('point => '+point);
	var px = m.getLayerPxFromLonLat(point);
    
    var posx = (px.x-38) +"px";
	var posy = (px.y-37) +"px";
	//var tag = e.desc.substring(0,2);
	var tag = "";
		var i_tag =$j("<div style='top:"+posy+";left:"+posx+";'><p class='eventCode'>"+tag+"</p></div>").addClass('marker');
    
    i_tag.appendTo($j(e.icons.div));
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