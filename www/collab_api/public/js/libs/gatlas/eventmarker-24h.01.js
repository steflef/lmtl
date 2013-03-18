// eventmarkers, map markers with Raphael
// version 1.0.0a
// (c) 2008-2011 StefLef [sl@steflef.com]
// released under the MIT license

var overTip = 0; // Flag
var style= {
	externalGraphic:"",
	pointRadius:"5",
	fillColor:"#cc3344",
	strokeColor:"#cc3344",
	strokeWidth:"3",
	strokeOpacity:"0.2",
	fillOpacity:"0.7"
};
var vClusters=[];
var r_pos = {left:0,top:0};

var markerPath = "M33.3,33.3L33.1,33.3L32.9,33.3L32.7,33.3L32.5,33.3L32.3,33.3L32.1,33.3L31.8,33.3L31.6,33.3L31.4,33.3L31.1,33.3L30.9,33.3L30.6,33.3L30.4,33.3L30.1,33.3L29.8,33.3L29.5,33.3L29.3,33.3L29,33.3L28.7,33.3L28.4,33.3L28.1,33.3L27.8,33.3L27.4,33.3L27.1,33.3L26.8,33.3L26.5,33.3L26.1,33.3L25.8,33.3L25.5,33.3L25.1,33.3L24.8,33.3L24.4,33.3L24.1,33.3L23.7,33.3L23.4,33.3L23,33.3L22.6,33.3L22.3,33.3L21.9,33.3L21.5,33.2L21.1,33.2L20.7,33.2L20.4,33.2L20,33.2L19.6,33.2L19.2,33.2L18.8,33.2L18.4,33.2L18,33.2L17.1,33.2L16.6,33.2L16.1,33.2L15.6,33.1L15.1,33L14.6,33L14.2,32.8L13.7,32.7L13.2,32.6L12.7,32.4L12.3,32.3L11.8,32.1L11.4,31.9L10.9,31.7L10.5,31.5L10.1,31.2L9.7,31L9.3,30.7L8.9,30.5L8.5,30.2L8.1,29.9L7.7,29.6L7.3,29.3L7,28.9L6.6,28.6L6.3,28.3L6,27.9L5.7,27.5L5.4,27.2L5.1,26.8L4.8,26.4L4.5,26L4.3,25.6L4,25.2L3.8,24.7L3.6,24.3L3.3,23.9L3.2,23.4L3,23L2.8,22.5L2.7,22L2.5,21.6L2.4,21.1L2.3,20.6L2.2,20.1L2.1,19.6L2.1,19.1L2,18.6L2,18.1L2,17.1L2,16.6L2.1,16.1L2.1,15.6L2.2,15.1L2.3,14.6L2.4,14.1L2.5,13.6L2.6,13.2L2.8,12.7L3,12.2L3.1,11.8L3.3,11.3L3.5,10.9L3.7,10.5L4,10L4.2,9.6L4.5,9.2L4.7,8.8L5,8.4L5.3,8L5.6,7.6L5.9,7.3L6.2,6.9L6.6,6.6L6.9,6.2L7.3,5.9L7.6,5.6L8,5.3L8.4,5L8.8,4.7L9.2,4.5L9.6,4.2L10,4L10.5,3.7L10.9,3.5L11.3,3.3L11.8,3.1L12.2,3L12.7,2.8L13.2,2.7L13.6,2.5L14.1,2.4L14.6,2.3L15.1,2.2L15.6,2.1L16.1,2.1L16.6,2L17.1,2L18.1,2L18.6,2L19.1,2.1L19.6,2.1L20.1,2.2L20.6,2.3L21.1,2.4L21.6,2.5L22,2.7L22.5,2.8L23,3L23.4,3.2L23.9,3.3L24.3,3.6L24.7,3.8L25.2,4L25.6,4.3L26,4.5L26.4,4.8L26.8,5.1L27.2,5.4L27.5,5.7L27.9,6L28.3,6.3L28.6,6.6L29,7L29.3,7.3L29.6,7.7L29.9,8.1L30.2,8.5L30.5,8.9L30.7,9.3L31,9.7L31.2,10.1L31.5,10.5L31.7,10.9L31.9,11.4L32.1,11.8L32.3,12.3L32.4,12.7L32.6,13.2L32.7,13.7L32.8,14.2L33,14.6L33,15.1L33.1,15.6L33.2,16.1L33.2,16.6L33.2,17.1L33.3,18L33.3,18.4L33.3,18.8L33.3,19.2L33.3,19.6L33.3,20L33.3,20.4L33.3,20.7L33.3,21.1L33.3,21.5L33.3,21.9L33.3,22.3L33.3,22.6L33.3,23L33.3,23.4L33.3,23.7L33.3,24.1L33.3,24.4L33.3,24.8L33.3,25.1L33.3,25.5L33.3,25.8L33.3,26.1L33.3,26.5L33.3,26.8L33.3,27.1L33.3,27.4L33.3,27.8L33.3,28.1L33.3,28.4L33.3,28.7L33.3,29L33.3,29.3L33.3,29.5L33.3,29.8L33.3,30.1L33.3,30.4L33.3,30.6L33.3,30.9L33.3,31.1L33.3,31.4L33.3,31.6L33.3,31.8L33.3,32.1L33.3,32.3L33.3,32.5L33.3,32.7L33.3,32.9L33.3,33.1L33.3,33.3Z";

function eventManager(mapObject,markersLayer,oParams)
{
	this.map = mapObject;
	this.markersLayer = markersLayer;
    this.oParams = oParams;
    this.aClusters = [];
    this.aPointers = [];
    this.aEvents = [];
    this.aLookup = [];
    this.aMarkers = [];
    this.aInClusters = [];
    this.aOutOfBounds = [];
    this.aFilters = [];
    this.zoom;
    
    this.element = 'dl.event';
    this.vT = 50;
    this.hT = 50;
    this.vT = 100;
    this.hT = 100;
    this.initialize();
}
eventManager.prototype = new Object();
eventManager.prototype.initialize = function (){
    
    var _this = this;

	this.map.on('zoomend', function(e) {
		DEBUG("ZOOMEND");
		_this.update();
	});
	
/*
	this.map.on('moveend', function(e) {
		DEBUG("MOVE END");
		_this.update();
	});
*/

    _this.aEvents = oEvs;
   	var e = _this.aEvents;
	var l = _this.aEvents.length;

	////console.time("===== Parser =====");
	$j('#status').text('mise à jour de la carte ...');
	_this.parseEvents(e, l,function()
	{
	    _this.update();
	    $j('#status').text('');
	    ////console.timeEnd("===== Parser =====");
	});
}

eventManager.prototype.parseEvents = function (e, l, onComplete){

    var pos = 0;
	var step = 50;
	var l_up = this.aLookup;
	var map = this.map;

    (function(){
    	DEBUG("// BATCH ("+pos+")//");
		var s = pos+step;
        for(var i= pos;i<s;i++)
		{
			if (i >= l)	break;
	
			var t =  e[i];
			if (t.lon == 0 || t.lat == 0)	continue;
			
			var latlon = new L.LatLng(t.lat,t.lon );
/*
			var circleOptions = {
								color: '#f03', 
								opacity: 0.7,
								radius:8
								}
			var itemCircle = new L.CircleMarker( latlon, circleOptions );
			itemCircle.data = t;
			itemCircle.on('click',markerClick);
			map.addLayer(itemCircle);
*/
			//DEBUG('addLayer');
			t.point = new L.Marker(t.lat , t.lon);
			t.px = map.latLngToLayerPoint(latlon);
			l_up[t.id]= i;
		}

        pos = s;
		//////console.log("pos = "+pos);
        if (pos < l)
        {
        	var p = Math.abs((pos/l)*100);
        	$j('#map_bar').find('div').css({width:p+'px'});
            setTimeout(arguments.callee,10);
        } else {
           ////console.info(" /// PARSER END ///");
           $j('#map_bar').find('div').css({width:'100px'});
           $j('#map_bar').hide();
           onComplete();
        }
    })();
}

// FROM STATS
eventManager.prototype.addEvent = function ( oInfos )
{
	var _this = this;
	oInfos.point = new OpenLayers.LonLat( oInfos.lon, oInfos.lat).transform(serverProj, map.getProjectionObject());
	oInfos.px = map.getLayerPxFromLonLat( oInfos.point );
	_this.aEvents.push( oInfos );
	_this.update();
	map.setLayerIndex( icons, 5);
}

eventManager.prototype.removeEvent = function ( id )
{
	var _this = this;
	for(var i=0; i<_this.aEvents.length;i++)
	{
		if( _this.aEvents[i].id == id )
		{
			_this.aEvents.splice(i,1);
			_this.update();
			break;
		}
	}
}

eventManager.prototype.getCardInfos = function( oCard )
{
	if ($j("dd.geo abbr", oCard ).length == 2)
    {
        var id = $j.trim($j(oCard).attr("id"));
        var title = $j("dt", oCard).text();
        var desc = $j("dd.description", oCard).text();
        var lat = $j("abbr.lat", oCard).text();
        var lon = $j("abbr.lon", oCard).text();

        var err = ( lat==0 || lon==0 )?1:0;
        return { id : id, title : title, desc : desc, lon : lon, lat : lat, err : err }
    }
    return false;
}

eventManager.prototype.reset = function (forceReset, onComplete)
{
	////console.time("===== RESET() =====");
	var z = this.map.getZoom();
	this.markersLayer.clearLayers();
	
	
	if( z != zoom || forceReset === true )
	{
		zoom =z;
		vClusters=[];
		this.aInClusters = [];
		//r_clusters.clear();
	}

	this.aMarkers=[];
	this.aClusters=[];
	this.aPointers=[];
	
	this.runReset(this.aEvents, this.aEvents.length, onComplete);


}

eventManager.prototype.runReset = function (e, l, onComplete){

	var _this = this;
    var pos = 0;
	var step = 50;
	$j('#map_bar').show();
	$j('#map_bar').find('div').css({width:'1px'});
	
    (function(){
		var s = pos+step;
		$j('#status').text("mise à jour de la carte "+pos+"/"+l+" ...");
		////console.log("RESET :: batch => " + pos);
/*
		for(var i= pos;i<s;i++)
		{
			o = e[i] || {};
			if( o.g === 'noGeo' || o.g === undefined ) continue ;
			
			o.vector = null;
			o.visible = (o.err)?0:1;
			o.cluster = null;
			o.px = _this.map.latLngToLayerPoint(o.point);
		}
*/
        pos = s;
        if (pos < l)
        {
        	var p = Math.abs((pos/l)*100);
        	$j('#map_bar').find('div').css({width:p+'px'});
            setTimeout(arguments.callee,20);
        } else {
        	$j('#status').text("");
        	$j('#map_bar').find('div').css({width:'100px'});
            $j('#map_bar').hide();
            
            ////console.timeEnd("===== RESET() =====");	
        	onComplete();
        }
    })();
}

// AUTO-FIRE ON MAP MOVEEND EVENT
eventManager.prototype.update = function (forceReset)
{
	console.log('UPDATE');
	//console.info(map.getZoom());
	//////console.info(map.getExtent());
	
	//this.panVectors();
	//DEBUG(this.markersLayer);
	
	//this.markersLayer._iterateLayers(this.markersLayer.removeLayer, this.markersLayer);
	DEBUG("CLEAR");
	DEBUG(this.markersLayer);
	this.reset(forceReset, function(){});
	this.displayMarkers();
}

eventManager.prototype.filter = function(param,value)
{
	////console.time("===== FILTER =====");
	//console.log(param +", "+value);
	
	var _this = this;
	$j('#status').text('filtre des données ...');
	$j('#list_bar').show();
	$j('#list_bar').css({width:'100%'});
	
    var pos = 0;
	var step = 50;
	var l = $j(_this.aEvents).length;
    
	var bounds = new OpenLayers.Bounds();
	
	(function(){
		var s = pos+step;
		
		for(var i= pos;i<s;i++)
		{
			if (i >= l)	break;
		
			if(_this.aEvents[i][param] == value)
			{
				_this.aEvents[i].f = 1;
				if(_this.aEvents[i].point.lat!=0 && _this.aEvents[i].point.lon!=0 )	bounds.extend( _this.aEvents[i].point );
				$j('#_'+ _this.aEvents[i].id).parent().show();
			}
			else
			{
				_this.aEvents[i].f = 0;
				$j('#_'+ _this.aEvents[i].id).parent().hide();
			}
		}

		pos = s;

        if (pos < l)
        {
        	var p = 100-Math.abs((pos/l)*100);
        	$j('#list_bar').css({width:p+'%'});
            setTimeout(arguments.callee,10);
        }else{
			map.zoomToExtent( bounds );
			$j('#list_bar').css({width:'0'});
			$j('#list_bar').hide();
			$j('#status').text('');
			////console.log( bounds);
			////console.timeEnd("===== FILTER =====");
        }
	})();
}

eventManager.prototype.removeFilter = function()
{
	$j(this.aEvents).each(function(i,o)
	{
		o.f = 1;
		$j('#_'+ o.id).parent().show();
	});
	this.update();
}

/*
eventManager.prototype.panVectors = function()
{		
	var r_map = $j('#r_map');
	var c_div = $j(map.layerContainerDiv);
	var iw = $j("#mapPort").innerWidth();
	var ih = $j("#mapPort").innerHeight();

	r_map.css({left:-(c_div.position().left)+"px",top:-(c_div.position().top)+"px"});
	r_clusters.setSize(iw,ih);
	
	r_pos.x = c_div.position().left - r_pos.left;
	r_pos.y = c_div.position().top -r_pos.top;
	r_pos.left = c_div.position().left;
	r_pos.top = c_div.position().top;
	
	if( st.length > 0 )
	{
		st.translate(r_pos.x,r_pos.y);
	}
}
*/


/// QUICK CLUSTERING (NO K-MEANS)
eventManager.prototype.clusterize = function ( calls )
{
	////console.time("===== clusterize =====");
	var _this = this;
	var ex = _this.map.getBounds();
	var zoom = _this.map.getZoom();
	var aCalls = [];

	aCalls = _this.aEvents;

	if( zLevels['z_' + zoom] != undefined )
	{
		var z = zoom -10;
		 $j(aCalls).each(function(i,o){
		 	if(o.g !== 'noGeo')
		 	{
			  	if(o.point.lat < ex._northEast.lat && o.point.lat > ex._southWest.lat && o.point.lon > ex._southWest.lon && o.point.lon < ex._northEast.lon  && o.f == 1)
	    		{
			 		if(o['z'+z] == 0)
			 		{
			 			o.visible = 1;
			 		}
			 		else
			 		{
			 			o.visible = 0;
			 		}
			 	}
			 	else
			 	{
			 		o.visible = 0;
			 	}
			 	
			 	o.visible = 0;
			 }	
		 });
		 
		////console.timeEnd("===== clusterize ====="); 
		return false;
	}

	// CREATE A ZOOM CLUSTERS
     $j(aCalls).each(function(i,o){
    	if(o.lat == 0) return true;

    	if(o.point.lat < ex.top && o.point.lat > ex.bottom && o.point.lon > ex.left && o.point.lon < ex.right  && o.f == 1)
    	{
	    	if( o.visible != 0 )
	    	{
	    		var point_1 = o.point;
	    		var px_1 = o.px;
	    		o.visible=1;
	    	
		    	for(var j=i+1;j<_this.aEvents.length;j++)
		    	{
			    	var o2 =  _this.aEvents[j];
			    	//var _len = 0;
			    	//////console.log(o);
					if( !o2.cluster && o2.visible==1 && o2.lat!=0 )
					{
			    		var dx= Math.abs(px_1.x-o2.px.x);
			    		var dy= Math.abs(px_1.y-o2.px.y);
			    		
			    		if( (dx < _this.hT) && (dy < _this.vT) )
			    		{
			    			if( !o.cluster )
			    			{
			    				o.cluster = [o.id];
			    				o.clusterId = _this.aClusters.length;
			    				_this.aPointers[o.id] = {id:_this.aClusters.length,pos:0};
			    				_this.aClusters[(_this.aClusters.length)] = [o.id];
			    				o.geom = {lat:o.point.lat,lon:o.point.lon}; // TO CALCULATE CLUSTER CENTROID			    				
			    			}
			    			
			    			o.cluster.push(o2.id);
			    			_this.aPointers[o2.id] = {id:_this.aClusters.length-1,pos:o.cluster.length};
			    			_this.aClusters[(_this.aClusters.length -1 )].push(o2.id);
			    			o.geom.lat += o2.point.lat;
			    			o.geom.lon += o2.point.lon;
			    			o2.visible = 0;
			    			o2.clusterId = _this.aClusters.length-1;
			    		}
		    		}
		    	}
			}	
		}
		else
		{
			o.visible = 0;
		}	
				
    }); 
    ////console.timeEnd("===== clusterize =====");
}

eventManager.prototype.displayMarkers = function ()
{
	////console.time("===== displayMarkers including clusterize =====");
console.log(' >>>> ' +this.getFilter());	
	
	if( this.getFilter().length > 0 )
	{
		this.displayFilterMarkers();
		return true;
	}
	
	var _this = this;
	zoom = _this.map.getZoom();
	
	_this.clusterize();
	_this.markersLayer.clearLayers();
	
	if( zLevels['z_' + zoom] != undefined ) 
	{
		DEBUG( "drawGeneratedClusters" );
		_this.drawGeneratedClusters(zoom);
	}
	else
	{
	    $j(_this.aEvents).each(function(i,o){
	
	    		//if( o.visible == 0 ) return true ;
	    		if( o.g == 'noGeo' ) return true ;
	    		if( o.cluster )
	    		{
	    			var m =  new Cluster();
	    			m.setCount( o.cluster.length );
	    		}
	    		else
	    		{
	    			//DEBUG("o");
	    			//DEBUG(o);
					var latlon = new L.LatLng(o.lat,o.lon );
					var circleOptions = {
										color: '#f03', 
										opacity: 0.7,
										radius:8
										}
					var itemCircle = new L.CircleMarker( latlon, circleOptions );
					itemCircle.data = o;
					itemCircle.on('click',markerClick);
					_this.markersLayer.addLayer(itemCircle);
	    			//var m =  new eventMarker();
	    		}

	    		//m.init(o);
	    		//m.display();
	    });
    }
    ////console.timeEnd("===== displayMarkers including clusterize =====");
}

eventManager.prototype.setFilter = function ( calls )
{
	DEBUG("DISPLAY FILTERS");
	DEBUG(calls);
	this.aFilters = calls;
	this.displayFilterMarkers();
}

eventManager.prototype.getFilter = function ()
{
	return this.aFilters;
}

eventManager.prototype.clearFilter = function ()
{
	////console.log("CLEAR FILTER!");
	this.aFilters = [];
	this.update(true);
}

eventManager.prototype.displayFilterMarkers = function ()
{
	var _this= this, o, calls;
	calls = this.getFilter();
	DEBUG("displayFilterMarkers");
	_this.reset(true, function(){ 
		
		////console.log("displayFilterMarkers RESET COMPLETED");

		(function(){
			//var bounds = new OpenLayers.Bounds();
			DEBUG("calls.length = " + calls.length);
			var lat = [];
			var lon = [];
			 
			for(var i=0;i<calls.length;i++)
			{			
				o =  _this.aEvents[ _this.aLookup[ calls[i] ]] || {};
	
				if( o.g === 'noGeo' || o.g === undefined ) continue ;
				
				o.visible = 1;
		
/*
		    	var m =  new eventMarker();
		    		m.init(o);
		    		m.display();
*/
				
				lat.push(o.lat);
				lon.push(o.lon);
				
				var latlon = new L.LatLng(o.lat,o.lon );
				var circleOptions = {
									color: '#f03', 
									opacity: 0.7,
									radius:8
									}
				var itemCircle = new L.CircleMarker( latlon, circleOptions );
				itemCircle.data = o;
				itemCircle.on('click',markerClick);
				_this.markersLayer.addLayer(itemCircle);
		    	//bounds.extend( o.point );
			}
			
			//return bounds;
			//map.zoomToExtent( bounds );
			
			//DEBUG( "southWest= "+Array.min(lat)+","+ Array.min(lon) ) ;
			var southWest = new L.LatLng( Array.min(lat), Array.min(lon) ),
			    northEast = new L.LatLng( Array.max(lat), Array.max(lon) ),
			    bounds = new L.LatLngBounds(southWest, northEast);
			_this.map.fitBounds(bounds);
	
		})(calls);
	});
}

eventManager.prototype.zoomToBounds = function ()
{
	var bounds = new OpenLayers.Bounds();
	
	$j(this.aEvents).each(function(i,o){
		
		if(o.point.lat!=0 && o.point.lon!=0 )
		{
			bounds.extend( o.point );
		}
		
	});
	map.zoomToExtent( bounds );
}

eventManager.prototype.drawGeneratedClusters =function(new_zoom)
{
	////console.time("+++++ drawGeneratedClusters +++++");
	var z = zLevels['z_' + new_zoom];
	var d = zLevelsR['z_' + new_zoom];
	var _this = this;
	var pos = 0;
	var step = 10;

	(function(){
		var l =  z.length;
		var s = pos+step;
		
		
		//////console.info("DRAW CLUSTER STEP :: "+s);
		var ex = _this.map.getBounds();

		for(var i= pos;i<s;i++){	
			if (i >= l)	break;
			
			var c_lon = z[i].lon;
			var c_lat = z[i].lat;

			// IN BOUNDS ONLY
			//DEBUG(c_lat + ", " + c_lon);
			//DEBUG(ex._northEast.lat + "< "+c_lat+" >" + ex._southWest.lat );
			//DEBUG(ex._northEast.lng + "< "+c_lon+" >" + ex._southWest.lng);
			
			//if(c_lat < ex._northEast.lat && c_lat > ex._southWest.lat && c_lon > ex._southWest.lng && c_lon < ex._northEast.lng && vClusters[i]== undefined)
			//{
				//DEBUG("* INBOUND");

				// CREATE CIRCLE STYLE
				var latlon = new L.LatLng(c_lat, c_lon);
				var rad = Math.floor(z[i].len * 2.5)+8;
				 	//DEBUG('rad:'+ (Math.floor(z[i].len * 2.5)+8));
				 	DEBUG('--------');

				 	DEBUG('len:'+ z[i].len );
				 	DEBUG('rad:'+ d[i] );
				 	//rad = Math.floor( d[i] / z[i].len ) + 20;
				 	if(d[i]== 0)
				 	{
				 		rad = 20
				 	}else
				 	{
				 		rad = (Math.floor(z[i].len /  d[i] )+1) *25;
				 	}
					
					DEBUG('rad:'+ rad );
				var circleOptions = {
						stroke: true,
						color: '#fff',
						weight: 2,
						opacity: 0.8,
						fillColor:'#8bd2ff',
						//fillColor:'#'+i+'bd'+i+'ff',
						fillOpacity: 0.7,
						radius:rad
						};
				
					var z_id = "z"+ (_this.map.getZoom() - 10);
					var cluster_ids = [];
					var c_id = z[i].id;
					
					$j(_this.aEvents).each(function(i,e){
						if( this[z_id] == c_id ){	
							cluster_ids.push(this.id);
						}
					});
				
				(function ()
				{
					var c = cluster_ids;
					var itemCircle = new L.CircleMarker( latlon, circleOptions );
					itemCircle.data = z[i];
					itemCircle.on('click',function(){
						
						DEBUG(c);
						list_cluster_24h(c);
						});
					_this.markersLayer.addLayer(itemCircle);

					var iconOptions = {
							
							icon:icon,
						    iconUrl: BASE_URL+'public/imgs/interface/leaflet/marker.png',
						    shadowUrl:BASE_URL+'public/imgs/interface/leaflet/marker-shadow.png',
						    opacity: 0
						};
					var itemLabel = new L.Marker.Text( latlon, z[i].len , iconOptions );
					_this.markersLayer.addLayer(itemLabel);
					
					vClusters[i]= z[i].id;
				})();
			//}
		}
		
		pos = s;
	    if (pos < l)
        {
            setTimeout(arguments.callee,10);
        }
        else
        {
        	////console.timeEnd("+++++ drawGeneratedClusters +++++");
        } 
	})();	
}

/* !MARKER */
function eventMarker(){}
eventMarker.prototype = new Object();

eventMarker.prototype.init = function ( oParam )
{
	this.oParam = oParam;
    this.visible = oParam.visible;
}

eventMarker.prototype.display = function ()
{
    var _this = this;
    
    /// IF IT EXISTS
/*
    if( _this.visible == 1 )
    {
*/
   		//////console.info(">>> ");
		////console.info(_this.oParam.vector);

        if( _this.oParam.vector != undefined ) return false; /// sinon retrace par dessus à chaque mousemove.
        
    	//console.info(">>> MARKER PROCESSING");
    	//console.log(_this.oParam.vector);
    	
	    var px = map.getLayerPxFromLonLat(_this.oParam.point);
	    var objs = this.getTag( px );
	    
	    _this.tag = objs.marker;
	    _this.lbl = objs.label;
	    
	    //////console.info(_this.oParam);
	    _this.tag.node.setAttribute("id","v_"+_this.oParam.id);
	    _this.tag.node.setAttribute("class","marker");

		_this.injectedTag = $j(_this.tag);
		_this.label = $j(_this.lbl);

		/// EVENTS MANAGEMENT
	    $j(_this.tag.node).bind("mouseenter", function ()
	    {
	    	_this.over()
	    }).bind("mouseleave", function ()
	    {
	    	_this.out()
	    }).bind("mousedown", function (e)
	    {
		    e.stopPropagation();
		    _this.openPop(e);
	    }).bind("overMark", function(e,d){
	    	_this.listenToOverMark(e,d);
	    }).bind("outMark", function(e,d){
	    	_this.listenToOutMark(e,d);
	    });
	    
	   	$j(_this.tag.node).bind("test", function(e,p1,p2,c1,c2)
	    {
	    	////console.info("TRIGER!!!");
	    	e.stopPropagation();
	    	_this.myCall(e,p1,p2,c1,c2);
	    });
	    
	  	$j(_this.lbl.node).bind("mouseenter", function ()
	    {
	    	$j(_this.tag.node).mouseenter();
	    }).bind("mouseleave", function ()
	    {
	    	//$j(_this.tag.node).mouseenter();
	    	$j(_this.tag.node).mouseleave();
	    }).bind("mousedown", function (e)
	    {
		    	e.stopPropagation();	
		    	$j(_this.tag.node).mousedown();
	    });
//	}

    //map.events.register("zoomend", map , function(){ _this.update() });
}

eventMarker.prototype.myCall = function (e,d,f)
{
	this.openPop(e);
}

eventMarker.prototype.getTag = function( px )
{
	var posx = (px.x-38) +"px";
    var posy = (px.y-37) +"px";
    var tag = this.oParam.desc.substring(0,2);
 	
 	var c_div = $j(map.layerContainerDiv);
 	
 	/// RAPHAEL MARKER
 	var p= r_clusters.path(markerPath).attr({fill:"#8bd2ff","fill-opacity":0.7,stroke:"#fff","stroke-width":2,"stroke-opacity":0.8,cursor:"pointer"}).translate( ((c_div.position().left)+px.x-38),((c_div.position().top)+px.y-38));
 	
 	/// RAPHAEL LABEL
	var lbl = r_clusters.text(px.x , px.y, tag).attr({"font": '16px Verdana,Arial,Helvetica,sans-serif', stroke: "none", fill: "#333",cursor:"pointer"}).translate( c_div.position().left - 19 , c_div.position().top -18);
	
	/// ADD TO VECTOR GROUP
	st.push(p,lbl);
	
	this.oParam.vector = {marker:p,label:lbl}; 	
 	return {marker:p,label:lbl};
}

eventMarker.prototype.update = function ()
{
	//alert('UPDATED');
 		var px = map.getLayerPxFromLonLat(this.point);
	    var posx = ( px.x-38) +"px";
    	var posy = ( px.y-37) +"px";
    	this.injectedTag.css('left',posx).css('top',posy);
}

eventMarker.prototype.over = function ()
{
	var t = this;
	t.injectedTag[0].attr({"fill":"#333"});
	t.label[0].attr({"fill":"#fff"});
	t.openTooltip();
}

eventMarker.prototype.out = function ()
{
	// RESUME STYLE	
	this.injectedTag[0].attr({"fill":"#8bd2ff"});
	this.label[0].attr({"fill":"#333"});
	this.closeTooltip();
}

eventMarker.prototype.setTooltip = function ()
{ 	
	return $j('<div class="markerTip"><span>'+this.oParam.desc+'</span><div class="marketTip-arrow"></div></div>');
}

eventMarker.prototype.openTooltip = function ()
{ 	
	/// CLOSE PREVIOUS TOOLTIP
	if ( g_atlas.tooltip ) 
	{
        this.closeTooltip();
    }
    else
    {
    	/// TOOLTIP CONTENTS
    	g_atlas.tooltip = this.setTooltip();
       
		/// TOOLTIP POSITIONNING	       
		var mapOffset = $j("#mapPort").offset();
		var path_x = this.injectedTag[0].attrs.path[0][1];
		var path_y = this.injectedTag[0].attrs.path[0][2];
	 	g_atlas.tooltip.css({position: "absolute",top : (mapOffset.top + path_y + 5)+ "px", left : (mapOffset.left + path_x -20) + "px"}).insertAfter( $j("#bd")).fadeIn("slow");
	}
}

eventMarker.prototype.closeTooltip = function ()
{
    if (g_atlas.tooltip)
    {
        g_atlas.tooltip.remove();
        g_atlas.tooltip = null;
    }
}

/// SINGLE EVENT POPINFO
eventMarker.prototype.openPop = function (e)
{
	getRef(this.oParam['id']);
	return;	
}

eventMarker.prototype.updateMarkers = function ()
{
	/// GESTION DES MARQUEURS
	if(g_atlas.event)
	{
		if(g_atlas.event.oParam)
		{
			if(!(g_atlas.event.oParam.id == this.oParam.id))
			{
				if(!g_atlas.event.oParam.icon.hasClass('.eventSelected'))
				{
					g_atlas.event.oParam.icon.css({ backgroundPosition : "top",zIndex : 1000}).find("p").css({color:"#666"});
				}
				
			    if( $j.support.opacity )
			    {
			        $j(".marked").trigger('outMark',g_atlas.event.oParam.code); 
			        $j(".marker").css({opacity:0.5});
				}
	
				this.injectedTag.css({backgroundPosition : "bottom",zIndex : 999500}).find("p").css({color:"#fff"});
				if( !g_atlas.is_ie )
				{
					//$j(".marker").trigger('overMark',this.oParam.code);
		    		$j(".marked").stop().animate({opacity : 1}, 500, function (){} );
				}
			}
		}	
	}
}

eventMarker.prototype.panTo = function ()
{
	map.panTo(this.oParam.point);
}

/* !CLUSTER */
function Cluster(){}

Cluster.prototype = new eventMarker(); // inherit

Cluster.prototype.setCount = function( c )
{
	this.clusterCount = c;
}
Cluster.prototype.setEvents = function( e )
{
	this.events = e;
}

Cluster.prototype.getTag = function( px )
{
    var tag = this.oParam.cluster.length;
    var geom = this.oParam.geom;
    var lon =  Number(geom.lon/tag);
    var lat = Number(geom.lat/tag);
    
    var point = new OpenLayers.LonLat( lon, lat );
	var test = map.getLayerPxFromLonLat( point );

	var px = test;
 	var c_div = $j(map.layerContainerDiv);
 	
 	/// RAPHAEL CIRCLE
 	var c= r_clusters.circle(px.x, px.y, 20).attr({fill:"#8bd2ff","fill-opacity":0.7,stroke:"#fff","stroke-width":2,"stroke-opacity":0.8,cursor:"pointer"}).translate( c_div.position().left -10 , c_div.position().top -10 );

 	/// RAPHAEL LABEL
	var lbl = r_clusters.text(px.x , px.y, tag).attr({"font": '16px Verdana,Arial,Helvetica,sans-serif', stroke: "none", fill: "#333"}).translate( c_div.position().left - 10 , c_div.position().top -10);
	
	/// ADD TO VECTOR GROUP
	st.push(c,lbl);
	
	this.oParam.vector = {cluster:c,label:lbl};
 	return {cluster:c,label:lbl};
}

Cluster.prototype.display = function ()
{	
 	var _this = this;
    /// IF IT EXISTS
    if( _this.visible == 1)
    {
        if( _this.oParam.vector != undefined ) return false;
    	////console.info(">>> MARKER PROCESSING");
    	
	    var px = map.getLayerPxFromLonLat(_this.oParam.point);
	    var objs = _this.getTag( px );
	    
	    _this.tag = objs.cluster;
	    _this.lbl = objs.label;
	    
	    //////console.info(_this.oParam);
	    _this.tag.node.setAttribute("id","v_"+_this.oParam.id);
	    _this.tag.node.setAttribute("class","marker");

		_this.injectedTag = $j(_this.tag);
		_this.label = $j(_this.lbl);

		/// EVENTS MANAGEMENT
	    $j(_this.tag.node).bind("mouseenter", function ()
	    {
	    	_this.over()
	    }).bind("mouseleave", function ()
	    {
	    	_this.out()
	    }).bind("mousedown", function (e)
	    {
		    	e.stopPropagation();
		    	_this.openPop(e);
	    	
	    }).bind("overMark", function(e,d){_this.listenToOverMark(e,d)}).bind("outMark", function(e,d){_this.listenToOutMark(e,d)});
	    
	    
	  	$j(_this.lbl.node).bind("mouseenter", function ()
	    {
	    	$j(_this.tag.node).mouseenter();
	    }).bind("mouseleave", function ()
	    {
	    	//$j(_this.tag.node).mouseenter();
	    	$j(_this.tag.node).mouseleave();
	    }).bind("mousedown", function (e)
	    {
		    	e.stopPropagation();	
		    	$j(_this.tag.node).mousedown();
	    });
	}
}

Cluster.prototype.myCall = function (e,d,f,c_id,pos)
{
/*
	////console.log("-- Cluster --");
	////console.log("----------------");
	////console.log(e);
	////console.log(e.target);
	////console.log(d);
	////console.log(f);
	////console.log('Cluster id:' + c_id);
	////console.log('Position:' + pos);
	////console.log("----------------");
*/

	this.openPop(e);
}

/// CLUSTERED EVENTs POPINFO
Cluster.prototype.openPop = function (e)
{
	//console.log("openPop");
	g_atlas.event=this;
	g_atlas.event.oParam.icon = $j(this.injectedTag);

	/// CLOSE PREVIOUS POP && TOOLTIP
	if ( g_atlas.pop ) 
	{
	    this.closePop();
	}
	this.closeTooltip();
	this.setPop();
}

Cluster.prototype.setTooltip = function ()
{ 	
	return $j('<div class="markerTip"><span>'+this.oParam.cluster.length+' événements</span><div class="marketTip-arrow"></div></div>');
}

Cluster.prototype.openTooltip = function ()
{ 	
	/// CLOSE PREVIOUS TOOLTIP
	if ( g_atlas.tooltip ) {
        this.closeTooltip();
    }
    else{
    	/// TOOLTIP CONTENTS
    	g_atlas.tooltip = this.setTooltip();

        var bbox = this.injectedTag[0].getBBox();
        var mapOffset = $j("#mapPort").offset();
		var cx = this.injectedTag[0].attrs.cx;
		var cy = this.injectedTag[0].attrs.cy;
       
		/// TOOLTIP POSITIONNING	       
       g_atlas.tooltip.css({position: "absolute",top : Math.abs(mapOffset.top + cy + (bbox.height/2)) + "px", left : mapOffset.left + cx -10+ "px"}).insertAfter( $j("#bd")).fadeIn("slow");
	}
}

Cluster.prototype.setPop = function ()
{
	/// Return list
	var l = g_atlas.event.clusterCount;
	var ids = [];
	 
	for(var i=0;i<l;i++){
		var e_id = g_atlas.event.oParam.cluster[i];
		ids.push(e_id);
	}

	list_cluster_24h(ids);
}

/* !SERVER_CLUSTER */
function serverCluster(){}

serverCluster.prototype = new Cluster(); // inherit

serverCluster.prototype.init = function ( oZ )
{
	this.oZ = oZ;
	
	//this.oParam = oParam;
    //this.visible = oParam.visible;
}

serverCluster.prototype.getTag = function( px, len )
{
    //var tag = this.oParam.cluster.length;
 	var c_div = $j(map.layerContainerDiv);

 	var rad = Math.abs(len * 0.4) + 25;
 
	/// RAPHAEL CIRCLE
	var c= r_clusters.circle(px.x, px.y, rad).attr({fill:"#8bd2ff","fill-opacity":0.7,stroke:"#fff","stroke-width":2,"stroke-opacity":0.8,cursor:"pointer"}).translate(c_div.position().left,c_div.position().top);

	/// RAPHAEL LABEL
	var lbl = r_clusters.text(px.x , px.y , len).attr({"font": '18px Verdana,Arial,Helvetica,sans-serif', stroke: "none", fill: "#333",cursor:"pointer"}).translate(c_div.position().left,c_div.position().top);	

	/// ADD TO VECTOR GROUP
	st.push(c,lbl);
	
	//this.oParam.vector = {cluster:c,label:lbl};
 	return {cluster:c,label:lbl};
}

serverCluster.prototype.display = function ()
{
 	var _this = this;
    
    /// IF IT EXISTS
   // if( _this.visible == 1)
   // {
        //if( _this.oParam.vector != undefined ) return false;
    	//////console.info(">>> MARKER PROCESSING");
    	
    	var lonlat = new OpenLayers.LonLat( _this.oZ.lon, _this.oZ.lat);
		//var px = map.getLayerPxFromLonLat( lonlat.transform(serverProj_2, map.getProjectionObject()) );
		var px = map.getLayerPxFromLonLat( lonlat.transform(serverProj, map.getProjectionObject()) );	
	    //var px = map.getLayerPxFromLonLat(_this.oParam.point);
	    var objs = _this.getTag( px,_this.oZ.len  );
	    
	    _this.tag = objs.cluster;
	    _this.lbl = objs.label;
	    
	  //  ////console.info(_this.oZ);
	    
	    _this.tag.node.setAttribute("id","c_"+_this.oZ.id);
	    _this.tag.node.setAttribute("class","marker");

		_this.injectedTag = $j(_this.tag);
		_this.label = $j(_this.lbl);

		/// EVENTS MANAGEMENT
	    $j(_this.tag.node).bind("mouseenter", function ()
	    {
	    	_this.over()
	    }).bind("mouseleave", function ()
	    {
	    	_this.out()
	    }).bind("mousedown", function (e)
	    {
		    	e.stopPropagation();
		    	_this.openPop(e);
	    	
	    }).bind("overMark", function(e,d){_this.listenToOverMark(e,d)}).bind("outMark", function(e,d){_this.listenToOutMark(e,d)});
	    
	    
	  	$j(_this.lbl.node).bind("mouseenter", function ()
	    {
	    	$j(_this.tag.node).mouseenter();
	    }).bind("mouseleave", function ()
	    {
	    	//$j(_this.tag.node).mouseenter();
	    	$j(_this.tag.node).mouseleave();
	    }).bind("mousedown", function (e)
	    {
		    	e.stopPropagation();	
		    	$j(_this.tag.node).mousedown();
	    });
	//}
}

serverCluster.prototype.setTooltip = function ()
{ 	
	return $j('<div class="markerTip"><span>'+this.oZ.len+' événements</span><div class="marketTip-arrow"></div></div>');
}

serverCluster.prototype.openTooltip = function ()
{ 	
	/// CLOSE PREVIOUS TOOLTIP
	if ( g_atlas.tooltip ) 
	{
        this.closeTooltip();
    }
    else
    {
    	//////console.info("TOOLTIP _________");
    	/// TOOLTIP CONTENTS
    	g_atlas.tooltip = this.setTooltip();
       
        var bbox = this.injectedTag[0].getBBox();
        var mapOffset = $j("#mapPort").offset();
		var cx = this.injectedTag[0].attrs.cx;
		var cy = this.injectedTag[0].attrs.cy;
       
		/// TOOLTIP POSITIONNING	       
         g_atlas.tooltip.css({position: "absolute",top : Math.abs(mapOffset.top + cy + (bbox.height/2)) + "px", left : mapOffset.left + cx -10+ "px"}).insertAfter( $j("#bd")).fadeIn("slow");
	}
}

serverCluster.prototype.over = function ()
{
	if(!g_atlas.pop)
	{	
		var t = this;
		
		// STYLE CHANGE
		t.injectedTag[0].attr({"fill":"#333"});
		t.label[0].attr({"fill":"#fff"});
		
		$j(".marker").trigger('overMark',t.oZ.id);
		t.openTooltip();
	}
}

serverCluster.prototype.out = function ()
{
	//////console.log("OUT!");
	if(! g_atlas.pop)
	{
/*
		if(!this.injectedTag.hasClass('.eventSelected'))
		{
			this.injectedTag.css({ backgroundPosition : "top",zIndex : 1000}).find("p").css({color:"#666"});
		}
*/
	    $j(".marked").trigger('outMark',this.oZ.id);
	}
}
serverCluster.prototype.listenToOverMark = function (e,d)
{
	//////console.log("OVERMARKED");
    if( this.oZ.id == d)
    {
		// CLASS CHANGE (ONLY USED AS A TARGET)
		var n = this.injectedTag[0].node;
		n.setAttribute("class","marked");
		
		// STYLE CHANGE
		this.injectedTag[0].attr({"fill":"#333"});
		this.label[0].attr({"fill":"#fff"});
	}
}

serverCluster.prototype.listenToOutMark = function (e,d)
{
	//////console.log("== OUT_MARK ==");
	
	// CLASS CHANGE (ONLY USED AS A TARGET)
	var n = this.injectedTag[0].node;
	n.setAttribute("class","marker");
	//////console.log( this.injectedTag[0].node );
	
	// RESUME STYLE	
	this.injectedTag[0].attr({"fill":"#8bd2ff"});
	this.label[0].attr({"fill":"#333"});
	
	// CLOSE TOOLTIP
	this.closeTooltip();
}

/* !POP */
serverCluster.prototype.openPop = function (e)
{
	var z_id = "z"+ (zoom - 10);
	var cluster_ids = [];
	var c_id = this.oZ.id;
	$j(oEv.aEvents).each(function(i,e){
	
		if( this[z_id] == c_id )
		{	
			cluster_ids.push(this.id);
		}
	});

	list_cluster_24h(cluster_ids);
	return;
};