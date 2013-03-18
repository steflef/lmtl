var ga = {};
var map, layer, startpoint, finalpoint, parser;
var action='start';
var clone ='';
var pod_cache = new Array();
var pod_level = new Array('Recensement 2006');
var level = 0;
var cardinals = ['north','east','south','west'];
var cardinal_c = 0;
var switchInterval;
var SHADOW_Z_INDEX = 10;
var MARKER_Z_INDEX = 11;
var zoom = 12;
var vClusters=[];
var r_pos = {left:0,top:0};

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

    trigger: function(e) {

    }
});

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

function setConstraintDate()
{
	var maxDate = $j('#rangeB').val();
	var minDate = $j('#rangeA').val();
	
	$j('#rangeA').datepicker( "option", "maxDate", maxDate );
	$j('#rangeB').datepicker( "option", "minDate", minDate );
	
	var dt = $j('#datepicker').find("dt");
	$j(dt[0]).html(minDate+","+maxDate);
}

	function hour_slider( id )
	{
		$j("#slider-range-"+id).slider({
				range: true,
				min: 0,
				max: 24,
				values: [6,17],
				slide: function( event, ui ) {
					$j( "#label-" + id ).val( "De " +ui.values[0] + " à "  +ui.values[1] + " heure");
					//$j('#heure_url').attr({'href': BASE_URL +'rapports/filtres'+ FILTER_URI +'par-heure/'+ui.values[0]+':00,'+ui.values[1]+':00/'});
					
					var dt = $j('#hourpicker').find("dt");
					$j(dt[id]).html(ui.values[0]+':00,'+ui.values[1]+':00');
				}
			});
		$j( "#label-"+id ).val( "De "+ $j( "#slider-range-" +id).slider( "value", 6 ) +" à "+ $j( "#slider-range-" + id).slider( "value", 17 ) + " heure");
	}
	
	
	function clone_slider()
	{
		var dt = $j('#hourpicker').find("dt");
		var c = dt.length;
		////console.log(c);
		//var clone = $j(".obj-slider").clone();
		var clone = $j(".slider-range").clone();
		clone.removeClass("noDisplay").removeClass("slider-range").addClass("clone");
		clone.find("input").attr("id", "label-"+c);
		clone.find("div").attr("id", "slider-range-"+c);
		//$j(".obj-slider").after(clone);
		$j('#hourpicker').find("dl").before(clone);
		$j('#hourpicker').find("dl").append("<dt>"+c+" -cloned</dt>");
		hour_slider(c);
	}
	
	function remove_slider(obj)
	{
		////console.log( $j(obj).parent().find("div").attr("id") );
		var id = $j(obj).parent().find("div").attr("id");
		var list_id = id.substring(id.length -1);
		var dt = $j('#hourpicker').find("dt");
		//$j(dt[list_id]).remove();
		$j(dt[list_id]).html("-removed");
		$j(obj).parent().remove();
	}
	
	
	function test_map(o)
	{
		$j("#s-maps").remove();
		//$j("#g-map").remove();
		//$j("#g-sview").remove();
	//	//console.log("ScrollTop => "+o.parent().scrollTop());
	//	//console.log(o.parent().position());
	//	//console.log($j('#cnt').scrollTop());

		//o.parent().after('<div id="g-map" style="width:100%;height:246px;float:left;clear:both;margin:0 0 4px 0"></div><div id="g-sview" style="background-color:#ccc;width:100%;height:250px;float:left;clear:both;"></div>');
		
		var p = o.parent().position();
		var st = $j('#cnt>div.call_list').scrollTop();
		
		//$j('#cnt').scrollTop( st + p.top);
		
		$j('#cnt>div.call_list').animate({'scrollTop': st + p.top},'normal','easeOutBack', function(){
			
			var maps = '<div id="s-maps" style="display:none;"><div id="g-map" style="width:100%;height:246px;float:left;clear:both;margin:0 0 4px 0"></div><div id="g-sview" class="noDisplay" style="background-color:#ccc;width:100%;height:250px;float:left;clear:both;"></div></div>';
			o.parent().after( maps );
			$j('#s-maps').slideDown('normal','easeOutBack', function(){
			
			
				var options = {
		            projection: proj,
		            units: "m",
		            maxResolution: 156543.0339,
		            maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
		            controls: [],
		            useOverlayPopups: false
		        };
		        
				e_map = new OpenLayers.Map( 'g-map', options );
				var g_osm = new OpenLayers.Layer.OSM("OMS",null,{buffer:0});		
		    	e_icons = new OpenLayers.Layer('Icons',{});
		
				e_map.addLayers([ g_osm, e_icons]);
				/// CENTRER LA CARTE SUR QC
				var point = new OpenLayers.LonLat(-71.3, 46.8);
				e_map.setCenter( point.transform(serverProj, e_map.getProjectionObject()),15);
				
/*
				var myPano = new GStreetviewPanorama(document.getElementById("g-sview"));
				//var myPano = new GStreetviewPanorama(document.getElementById("e-sv-test"));
				
				myPos = new GLatLng(46.82533,-71.289784);
				myPOV = {yaw:370.64659986187695,pitch:-20};
				myPano.setLocationAndPOV(myPos, myPOV);
*/
		
			
			});
		
		});
		
	
	}
	
function showDetails(o)
{
	$j('#cnt>div.call_list').css('overflow','hidden').css('width','300px');
	$j('#filter-map-1').hide();

	//$j('div.left_area').css('background','#666');
	//o.parent().css('background','#fff');
	$j('#cnt>div.call_list>div>div.left_area>dl').css('color','#ddd');
	o.css('color','#000');
	
	$j('#content_horiz').animate({'left':'-=340'},'normal','easeOutBack',function(){
	
		test_map(o.parent());
	
	});
	$j('#bt_filtre').removeClass('noDisplay');
	
	return false;
}
	

function quickMap()
{
	serverProj = new OpenLayers.Projection("EPSG:4326"); //Google & server Mtl rues
	proj = new OpenLayers.Projection("EPSG:900913"); // OpenLayer Mercator
		
	var options = {
        projection: proj,
        units: "m",
        maxResolution: 156543.0339,
        maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
        controls: [],
        useOverlayPopups: false
    };
    
    
	var e_map = new OpenLayers.Map( 'ini-map', options );
	var g_osm = new OpenLayers.Layer.OSM("OMS",null,{buffer:0});		
	var e_icons = new OpenLayers.Layer('Icons',{});

	e_map.addLayers([ g_osm, e_icons]);
	
	// CONTROLS
	var nav = new OpenLayers.Control.Navigation({'zoomWheelEnabled': false});
	
	var panel = new OpenLayers.Control.Panel();
    panel.addControls([
		new OpenLayers.Control.ZoomIn({	displayClass: "zoomIn", title:'Zoom IN'}),
		new OpenLayers.Control.ZoomOut({ displayClass: "zoomOut", title:'Zoom OUT'})
    ]);
    
    e_map.addControls([nav,panel]);
	
	/// CENTRER LA CARTE SUR QC
	var point = new OpenLayers.LonLat(-71.3, 46.8);
	e_map.setCenter( point.transform(serverProj, e_map.getProjectionObject()),12);

}
	
/* !START onReady */ 	
$j(document).ready(function(){

	//// TABS
	var $tabs = $j("#boards").tabs({
									tabTemplate: "<li><a  class='loading' href='#{href}'>#{label}</a><span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
									remove: function( event, ui ) {
										//var tab_content = $tab_content_input.val() || "Tab " + tab_counter + " content.";
										//$j( ui.panel ).append( "Mon test" );
										EvntBoards.removeBoard(ui);

									}
								});

	$j( "#boards span.ui-icon-close" ).live( "click", function() {
		var index = $j( "li", $tabs ).index( $j( this ).parent() );
		$tabs.tabs( "remove", index );
	});

	//// EventBoard
	EvntBoards.init();

	/// FILTRES
	
	$j("#geo-iframe").colorbox({width:'740px', height:'540px',iframe:true, scrolling:false});
	/// STOPPPPPPPPPPPP
		return false;
		
		
	//  DATE WIDGET
	var options = {
		dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
		dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'], 
		monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
		numberOfMonths: 1,
		showButtonPanel: false,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		onSelect: function(selectedDate){ setConstraintDate() }
	};
	
	$j('#rangeA').datepicker(options);
	$j('#rangeB').datepicker(options);
	setConstraintDate();
	
	//  HEURE WIDGET
	hour_slider(0);
	
	$j("div.filter-box").click( function(event){
		////console.info(event);
		////console.info(event.target);
		if( $j(event.target).hasClass("fb-title")||  $j(event.target).hasClass("fb-content"))
		{
			$j(this).toggleClass('fb-active');
			$j(this).toggleClass('fb-inactive');
		}
	}); 

	//  JOUR WIDGET
	$j("div.days").find("input").change( function(event){
		
		var output = [];
		$j("div.days").find("input:checked").each(function(index,value){
			$j(this).val();
			output.push( $j(this).val());
		});
		$j("#par-jour>dt:first").html(output.join(','));
	}); 
});

function scrollContent(n,pos)
{
	////console.info("n:"+ n + " pos:" +pos)
	if(!g_atlas.event.scroll)
	{
		g_atlas.event.scroll = 0;
		$j('.infoPop-scroller').css({left:'0'});
		g_atlas.event.flag = 1;
	}
	
	if(  g_atlas.event.flag == 0)
	{
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

function getRef(e)
{
	if(! g_atlas.event)
	{
		 g_atlas.event = {};
	}
	
	var target = oEv.aLookup[e];
	var ev = oEv.aEvents[target];
	////console.info(ev);
	
	
	$j.post(BASE_URL +'rapport_spvq/ref/'+e+'/simple', function(data)
	{
		$j("#loading").hide();
		//$j("#e-infos").html(data);
		$j(".i-content").html(data);
	},"html");
	
		var infos = $j('#_'+e);
		var date_time = ev.d + '  ' + ev.t;
		var desc = ev.desc;
		var code = '';
		var sia = ev.s;
		var lon = ev.lon;
		var lat = ev.lat;
		////console.log('LON => '+lon + '  LAT=>'+lat);
		g_atlas.event.desc = desc;
		g_atlas.event.lon = lon;
		g_atlas.event.lat = lat;


	var d = [];
		d.push( '<div id="e-details" style="padding:0;background-color:#fff;height:100%;position:relative;">');
		
		d.push('	<div id="e-menu" style="padding:0;background-color:#000;height:675px;width:88px;position:relative;float:left;">');
		d.push('		<ul style="margin:20px 0 0 0;padding:0;">');
		d.push('			<li><a class="v-selected" style="width:88px;display:block;height:100px;border:0;background: #000 url(\''+BASE_URL+'public/images/interface/v-details.png\') top left no-repeat;" href="#" onclick="return false;"></a></li>');
		d.push('			<li><a class="" style="width:88px;display:block;height:69px;border:0;background: #000 url(\''+BASE_URL+'public/images/interface/v-histo.2.png\') top left no-repeat;" href="#" onclick="return false;"></a></li>');
		d.push('			<li><a class="" style="width:88px;display:block;height:69px;border:0;background: #000 url(\''+BASE_URL+'public/images/interface/v-distance.png\') top left no-repeat;" href="#" onclick="return false;"></a></li>');
		d.push('			<li><a class="" style="width:88px;display:block;height:69px;border:0;background: #000 url(\''+BASE_URL+'public/images/interface/v-brut.png\') top left no-repeat;" href="#" onclick="return false;"></a></li>');

		d.push('		</ul>');
		d.push('	</div>');

		d.push('	<div id="e-event" style="display:block">');		
		d.push('		<div class="filter-box-mod" style="text-align:left;height:675px;float:left;padding:0px 15px;width:400px;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;font-size:16px;">');
		d.push('			<dl id="" class="event" style="background-color:#f2f2f2;border-radius: 5px; -moz-border-radius:5px; -webkit-border-radius:5px;padding:8px;">');
		d.push('				<dd><strong>Événement # '+e+'</strong></dd>');
		d.push('				<dd><strong>'+date_time.substring(0,11)+'</strong>'+date_time.substring(11)+'</dd>');
		d.push('				<dd><strong><a href="{base_url}rapports/filtres/par-code/'+code+'/">'+desc+'</a></strong></dd>');
		d.push('				<dd class="sia">SIA '+sia+'</dd>');
		d.push('				<dd id="loading"><strong>Chargement en cours ...</strong></dd>');
		d.push('				<dd class="geo">Geo (');
		d.push('					<abbr class="lon" title="longitude">'+lon+'</abbr>,');
		d.push('					<abbr class="lat" title="latitude">'+lat+'</abbr>)');
		d.push('				</dd>');
		d.push('			</dl>');
		
		d.push('			<div id="e-infos" style="text-align:left;height:340px;background-color:#f2f2f2;border-radius: 5px; -moz-border-radius:5px; -webkit-border-radius:5px;padding:8px;margin-top:4px;"><div style="overflow-y:auto;height:330px;margin:5px 0" class="i-content"></div></div>');	
		
		d.push('			<dl id="" class="event" style="color:#fff;background: #999 url(\''+BASE_URL+'public/images/interface/v-right-pointer.png\') top right no-repeat;border-radius: 5px; -moz-border-radius:5px; -webkit-border-radius:5px;padding:8px;margin:4px 0 0 120px;">');
		d.push('				<dd><strong>// Localisation</strong></dd>');
		d.push('			</dl>');
		
		d.push('			<dl id="" class="event" style="background: #ccc url(\''+BASE_URL+'public/images/interface/v-interface-mini.png\') center right no-repeat;border-radius: 5px; -moz-border-radius:5px; -webkit-border-radius:5px;padding:8px;margin:4px 0 0 120px;">');
		d.push('				<dd><strong>// 2 Individus fichés</strong></dd>');
		d.push('			</dl>');
		
		d.push('			<dl id="" class="event" style="background: #ccc url(\''+BASE_URL+'public/images/interface/v-interface-mini.png\') center right no-repeat;border-radius: 5px; -moz-border-radius:5px; -webkit-border-radius:5px;padding:8px;margin:4px 0 0 120px;">');
		d.push('				<dd><strong>// Détails de l\'appel</strong></dd>');
		d.push('			</dl>');
		d.push('			<div style="position:absolute;bottom:88px;left:10px;width:120px;height:120px;border:0;background: #fff url(\''+BASE_URL+'public/images/interface/v-interface.png\') top left no-repeat;"></div>');
		
		d.push('		</div>');
		
		
		d.push('		<div style="color:#fff;border:0;background-color:#fff;padding:0;float:right;width:330px;">');
		d.push('			<div id="g-sv" style="color:#fff;border:0;height:280px;background-color:#ccc; padding:0;float:right;width:330px;margin-bottom:10px;"></div>');
		d.push('			<div id="g-map" style="color:#fff;border:2px solid #666;height:290px;background-color:#ccc; float:right;width:326px;"></div>');		
		d.push('		</div>');
		d.push('	</div>');
		d.push('</div>');

	$j.colorbox({width:'900px', height:'662px', html:d.join(''), opacity:0.6, scrolling:false, onComplete:function(){
	
		
		////console.log("COMPLETED lat:"+ lat + ' lon:'+ lon);
		
		/// GOOGLE STREET VIEW

		var e = g_atlas.event;
		
		var eView = new GLatLng(e.lat,e.lon);
		e.yaw = 0;
        var newPov = {
            yaw : e.yaw, pitch :- 2
        };
		panoramaOptions = { latlng:eView, pov : newPov };
		e.streetview = new GStreetviewPanorama(document.getElementById("g-sv"), panoramaOptions);
		GEvent.addListener(e.streetview, "error", handleNoFlash);
    	if (e.yaw  == 0)
        {
            e.timer = window.setInterval("g_atlas.spiralStairs()", 2500);
            $j("#g-sv").mouseover(function ()
            {
                if (e.timer) {
                    clearInterval(e.timer)
                }
            })
        }
        
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
		m.setCenter( point.transform(serverProj, m.getProjectionObject()),15);
		
		////console.log('point => '+point);
		var px = m.getLayerPxFromLonLat(point);
	    
	    var posx = (px.x-38) +"px";
    	var posy = (px.y-37) +"px";
    	var tag = e.desc.substring(0,2);
 		var i_tag =$j("<div style='top:"+posy+";left:"+posx+";'><p class='eventCode'>"+tag+"</p></div>").addClass('marker');
	    
	    i_tag.appendTo($j(e.icons.div));   
	    
		$j(document).bind('cbox_closed',function(){
			 if (g_atlas.event.timer) 
			 {
                 clearInterval(g_atlas.event.timer);
                 g_atlas.event.timer = null;
             }
		});
	}});
}

/*
function updateTime()
{
	var d = new Date();
	var h = d.getHours();
	var m = d.getMinutes();
	
	if( m < 9 ) m = '0'+m;
	if( h < 9 ) h = '0'+m;
	
    $j('#clock').text( h + ':'+ m);
}
*/

/// TOGGLE MAP FULL VIEW
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

function handleNoFlash(errorCode) {
  if (errorCode == 603)
  {
    alert("Error: Flash doesn't appear to be supported by your browser");
    return;
  }
}

function get_my_url (bounds) {
    var res = this.map.getResolution();
    ////console.info(this.map.getResolution());
    // //console.info("bounds");
    // //console.info(bounds);
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

