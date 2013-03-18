// eventmarkers, map markers with Raphael
// version 1.0.0a
// (c) 2008-2011 StefLef [sl@steflef.com]
// released under the MIT license

/* !ARRAY ADD-ON */
Array.prototype.max = function(){
    return Math.max.apply( Math, this );
};

Array.prototype.min = function(){
    return Math.min.apply( Math, this );
};

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

var markerPath = "M33.3,33.3L33.1,33.3L32.9,33.3L32.7,33.3L32.5,33.3L32.3,33.3L32.1,33.3L31.8,33.3L31.6,33.3L31.4,33.3L31.1,33.3L30.9,33.3L30.6,33.3L30.4,33.3L30.1,33.3L29.8,33.3L29.5,33.3L29.3,33.3L29,33.3L28.7,33.3L28.4,33.3L28.1,33.3L27.8,33.3L27.4,33.3L27.1,33.3L26.8,33.3L26.5,33.3L26.1,33.3L25.8,33.3L25.5,33.3L25.1,33.3L24.8,33.3L24.4,33.3L24.1,33.3L23.7,33.3L23.4,33.3L23,33.3L22.6,33.3L22.3,33.3L21.9,33.3L21.5,33.2L21.1,33.2L20.7,33.2L20.4,33.2L20,33.2L19.6,33.2L19.2,33.2L18.8,33.2L18.4,33.2L18,33.2L17.1,33.2L16.6,33.2L16.1,33.2L15.6,33.1L15.1,33L14.6,33L14.2,32.8L13.7,32.7L13.2,32.6L12.7,32.4L12.3,32.3L11.8,32.1L11.4,31.9L10.9,31.7L10.5,31.5L10.1,31.2L9.7,31L9.3,30.7L8.9,30.5L8.5,30.2L8.1,29.9L7.7,29.6L7.3,29.3L7,28.9L6.6,28.6L6.3,28.3L6,27.9L5.7,27.5L5.4,27.2L5.1,26.8L4.8,26.4L4.5,26L4.3,25.6L4,25.2L3.8,24.7L3.6,24.3L3.3,23.9L3.2,23.4L3,23L2.8,22.5L2.7,22L2.5,21.6L2.4,21.1L2.3,20.6L2.2,20.1L2.1,19.6L2.1,19.1L2,18.6L2,18.1L2,17.1L2,16.6L2.1,16.1L2.1,15.6L2.2,15.1L2.3,14.6L2.4,14.1L2.5,13.6L2.6,13.2L2.8,12.7L3,12.2L3.1,11.8L3.3,11.3L3.5,10.9L3.7,10.5L4,10L4.2,9.6L4.5,9.2L4.7,8.8L5,8.4L5.3,8L5.6,7.6L5.9,7.3L6.2,6.9L6.6,6.6L6.9,6.2L7.3,5.9L7.6,5.6L8,5.3L8.4,5L8.8,4.7L9.2,4.5L9.6,4.2L10,4L10.5,3.7L10.9,3.5L11.3,3.3L11.8,3.1L12.2,3L12.7,2.8L13.2,2.7L13.6,2.5L14.1,2.4L14.6,2.3L15.1,2.2L15.6,2.1L16.1,2.1L16.6,2L17.1,2L18.1,2L18.6,2L19.1,2.1L19.6,2.1L20.1,2.2L20.6,2.3L21.1,2.4L21.6,2.5L22,2.7L22.5,2.8L23,3L23.4,3.2L23.9,3.3L24.3,3.6L24.7,3.8L25.2,4L25.6,4.3L26,4.5L26.4,4.8L26.8,5.1L27.2,5.4L27.5,5.7L27.9,6L28.3,6.3L28.6,6.6L29,7L29.3,7.3L29.6,7.7L29.9,8.1L30.2,8.5L30.5,8.9L30.7,9.3L31,9.7L31.2,10.1L31.5,10.5L31.7,10.9L31.9,11.4L32.1,11.8L32.3,12.3L32.4,12.7L32.6,13.2L32.7,13.7L32.8,14.2L33,14.6L33,15.1L33.1,15.6L33.2,16.1L33.2,16.6L33.2,17.1L33.3,18L33.3,18.4L33.3,18.8L33.3,19.2L33.3,19.6L33.3,20L33.3,20.4L33.3,20.7L33.3,21.1L33.3,21.5L33.3,21.9L33.3,22.3L33.3,22.6L33.3,23L33.3,23.4L33.3,23.7L33.3,24.1L33.3,24.4L33.3,24.8L33.3,25.1L33.3,25.5L33.3,25.8L33.3,26.1L33.3,26.5L33.3,26.8L33.3,27.1L33.3,27.4L33.3,27.8L33.3,28.1L33.3,28.4L33.3,28.7L33.3,29L33.3,29.3L33.3,29.5L33.3,29.8L33.3,30.1L33.3,30.4L33.3,30.6L33.3,30.9L33.3,31.1L33.3,31.4L33.3,31.6L33.3,31.8L33.3,32.1L33.3,32.3L33.3,32.5L33.3,32.7L33.3,32.9L33.3,33.1L33.3,33.3Z";

/* !EVENT MANAGER */
function EventManager(oParams)
{
    this.oParams = oParams;
    this.map = oParams.map;
    this.clusters_set = oParams.clusters_set;
    this.r = oParams.r;
    this.board = oParams.board;
    
    this.aClusters = [];
    this.aEvents = [];
    this.aGeoEvents = [];
    this.aLookup = [];
    this.aMarkers = [];
   
    this.zoom;

    this.element = 'dl.event';
    this.vT = 30;
    this.hT = 30;
    this.initialize();
}
EventManager.prototype = new Object();
EventManager.prototype.initialize = function ()
{    
    var _this = this;
    _this.map.events.register("moveend", _this.map , function(){
    	_this.update()
    });
}

// NEW EVENTS
// FOR CLUSTERS GENERATION 
EventManager.prototype.setEvents = function (e,c)
{
	this.aEvents = e || {};
	
	var oC = c || {};
	this.setClusters(oC);
	
	var l = e.length;
	var _this = this;
	
	this.aGeoEvents = []; //Reset calculated clusters

	//console.time("===== Parser =====");
	//$j('#status').text('mise à jour de la carte ...');
	_this.parseEvents(e, l,function()
	{
	    _this.update();
	    //console.timeEnd("===== Parser =====");
	});
	
	return e;	 
}

// NEW SERVER GENERATED CLUSTERS
EventManager.prototype.setClusters = function (c)
{
	return this.aClusters = c;	
}

// DRAW ZOOM CLUSTER TO SCREEN
// ONLY FOR THE VISUAL EXTENT
// NON-BLOCKING
EventManager.prototype.drawZoomCluster = function (iZoom)
{
	var z = this.aClusters['z_' + iZoom];
	var pos = 0;
	var step = 10;
	var _this = this;

	(function(){
		var l =  z.length;
		var s = pos+step;
		
		////console.info("DRAW CLUSTER STEP :: "+s);
		var c_div = $j(_this.map.layerContainerDiv);
		var ex = _this.map.getExtent().transform( _this.map.getProjectionObject(),serverProj);
		var pix_resolution = _this.map.getResolution();
		
		for(var i= pos;i<s;i++)
		{	
			if (i >= l)	break;
			
			var c_lon = z[i].lon;
			var c_lat = z[i].lat;
			
			//DEBUG(z[i]);
			// IN BOUNDS ONLY
			if( c_lat < ex.top && c_lat > ex.bottom && c_lon > ex.left && c_lon < ex.right )
			{
				(function ()
				{
					/// DISPLAY MARKER
					var my_len = z[i].len;
					var c =  new serverCluster();
					c.init(z[i], _this.map, _this.clusters_set, _this.r);
					c.display();
					
					//vClusters[i]= z[i].id;
				})(_this);
			}
		}
		pos = s;
	    if (pos < l)
        {
        	// PROGRESS BAR
        	//var p = 100-Math.abs((pos/l)*100);
        	//$j('#list_bar').css({width:p+'%'});
        	
        	// NON-BLOCKING
            setTimeout(arguments.callee,10);
        }
        else
        {
        	//console.timeEnd("+++++ drawGeneratedClusters +++++");
        } 
	})(_this);
}

EventManager.prototype.clusterizeData = function ()
{
	//console.time("===== clusterizeData =====");
	var _this = this;
	var ex = _this.map.getExtent();
	var zoom = _this.map.getZoom();	

	// CREATE A ZOOM CLUSTERS
	var aInExtent = [];

    $j(this.aGeoEvents).each(function(i,o)
    {
    	//if( o.lat < ex.top && o.lat > ex.bottom && o.lon > ex.left && o.lon < ex.right )
    	//{
    	if( o.lon != null && o.lat != null )
    	{
    		var point = new OpenLayers.LonLat( o.lon, o.lat ).transform(serverProj, _this.map.getProjectionObject());
			o.px = _this.map.getLayerPxFromLonLat( o.point );
    		o.vis = 1;
    		aInExtent.push(o);
    	}
    	//}
     });
     
     var clusters = [];
     var l = aInExtent.length;
     
     for(var i=0;i< aInExtent.length;i++)
     {
     	o = aInExtent[i];
     	
    	if( o.vis != 0 )
    	{
    		var point_1 = o.point;
    		var px_1 = o.px;
    		var cluster = {id:clusters.length,items:[o.e_id],lat:o.lat,lon:o.lon,len:1,px:0,itemsY:[px_1.y],itemsX:[px_1.x]};
    		
    		//aInExtent.splice(i,1);
    		aInExtent[i].vis = 0;
    		//DEBUG(clusters.length +" : "+o.e_id + " ====> " +i + "  " + px_1.x+","+px_1.y);
	    	
	    	//for(var j=i+1;j< l;j++)
	    	for(var j=0;j<aInExtent.length;j++)
	    	{
	    	//DEBUG("i="+i + "  j="+j);
		    	var o2 =  aInExtent[j];
				    		//DEBUG(o2);
				if( o.e_id!=o2.e_id && o2.vis==1)
				{
		    		var dx= Math.abs(px_1.x-o2.px.x);
		    		var dy= Math.abs(px_1.y-o2.px.y);
		    		
		    		if( (dx < _this.hT) && (dy < _this.vT) )
		    		{
		    			cluster.items.push(o2.e_id);
		    			cluster.itemsX.push(o2.px.x);
		    			cluster.itemsY.push(o2.px.y);
		    			cluster.len ++;
		    			
		    			aInExtent.splice(j,1);
		    		}
	    		}
	    	}
	    	
			var difX = cluster.itemsX.max()-cluster.itemsX.min();
			var difY = cluster.itemsY.max()-cluster.itemsY.min();;
			var r = (difX > difY)? difX : difY;
			cluster.px = r;
			
	   		clusters.push(cluster);
		}	
	}

    this.aClusters['z_' + zoom] = clusters;
    //DEBUG(this.aClusters);
    //console.timeEnd("===== clusterizeData =====");
}

EventManager.prototype.parseEvents = function (e, l, onComplete)
{
    var pos = 0;
	var step = 50;
	this.aGeoEvents = [];
	var ge = this.aGeoEvents;
	var _this = this;

    (function(){
		var s = pos+step;
        for(var i= pos;i<s;i++)
		{
			if (i >= l)	break;
	
			var t =  e[i];
			if ( t.geom_valid == 0 )	continue;

			t.point = new OpenLayers.LonLat( t.lon, t.lat ).transform(serverProj, _this.map.getProjectionObject());
			//t.px = _this.map.getLayerPxFromLonLat( t.point );
			t.px = 0;
			ge.push(t);
		}

        pos = s;
		////console.log("pos = "+pos);
        if (pos < l)
        {
        	var p = Math.abs((pos/l)*100);
        	$j('#map_bar').find('div').css({width:p+'px'});
            setTimeout(arguments.callee,15);
        } else {
           ////console.info(" /// PARSER END ///");
           $j('#map_bar').find('div').css({width:'100px'});
           $j('#map_bar').hide();
           onComplete();
        }
    })(_this);
}

// AUTO-FIRE ON MAP MOVEEND EVENT
EventManager.prototype.update = function (forceReset)
{
	this.panVectors();	
	this.r.clear();
	this.displayMarkers(this.map.getZoom()); 
}

EventManager.prototype.panVectors = function()
{
	////console.log(this.parent.board);
/*
	var r_map = $j('#r_map');
	var c_div = $j(this.map.layerContainerDiv);
	var iw = $j("#mapPort").innerWidth();
	var ih = $j("#mapPort").innerHeight();
*/

	var r_map =	this.board.find('#r_map');
	var c_div = $j(this.map.layerContainerDiv);
	var iw = this.board.find("#mapPort").innerWidth();
	var ih = this.board.find("#mapPort").innerHeight();
	r_map.css({left:-(c_div.position().left)+"px",top:-(c_div.position().top)+"px"});
	//r_clusters.setSize(iw,ih);
	this.r.setSize(iw,ih);

	var r_pos={};
	r_pos.x = c_div.position().left - r_pos.left;
	r_pos.y = c_div.position().top -r_pos.top;
	////console.info("déplacement x >"+r_pos.x);
	////console.info("déplacement y >"+r_pos.y);
	r_pos.left = c_div.position().left;
	r_pos.top = c_div.position().top;
	
	if( this.clusters_set.length > 0 )
	{
		this.clusters_set.translate(r_pos.x,r_pos.y);
	}
/*
	if( st.length > 0 )
	{
		st.translate(r_pos.x,r_pos.y);
	}
*/
}

EventManager.prototype.displayMarkers = function (zoom)
{
	//console.time("===== displayMarkers including clusterize =====");
	
	if( this.aClusters['z_' + zoom] )
	{
		this.drawZoomCluster(zoom);
		//console.timeEnd("===== displayMarkers including clusterizeData =====");
		return;
	} 
	
	/// Build Clusters for Zoom Level
	this.clusterizeData(zoom);
	this.drawZoomCluster(zoom);
	
    //console.timeEnd("===== displayMarkers including clusterizeData =====");
}

EventManager.prototype.zoomToBounds = function ()
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

/* !SERVER_CLUSTER */
function serverCluster(){}

serverCluster.prototype = new Object(); // inherit

serverCluster.prototype.init = function ( oZ, mapID,c_set, r )
{
	this.oZ = oZ;
	this.map = mapID;
	this.c_set = c_set;
	this.r = r;
}

serverCluster.prototype.getTag = function( px, len )
{
 	var c_div = $j( this.map.layerContainerDiv);
 	var rad = (this.oZ.px < 5)?7:this.oZ.px ;
 	//var rad = Math.abs(len * 0.05) + 10;
 	//var rad =50;
 	
	/// RAPHAEL CIRCLE
	//var c= r_clusters.circle(px.x, px.y, rad).attr({fill:"#8bd2ff","fill-opacity":0.7,stroke:"#fff","stroke-width":2,"stroke-opacity":0.8,cursor:"pointer"}).translate(c_div.position().left,c_div.position().top);
	var c= this.r.circle(px.x, px.y, rad).attr({fill:"#8bd200","fill-opacity":0.7,stroke:"#fff","stroke-width":2,"stroke-opacity":0.8,cursor:"pointer"}).translate(c_div.position().left,c_div.position().top);
	
	/// RAPHAEL LABEL
/*
	if( rad > 10 )
	{
		var lbl = this.r.text(px.x , px.y , len).attr({"font": '18px Verdana,Arial,Helvetica,sans-serif', stroke: "none", fill: "#333",cursor:"pointer"}).translate(c_div.position().left,c_div.position().top);	
		/// ADD TO VECTOR GROUP
		this.c_set.push(c,lbl);
	}
	else
	{
		var lbl = this.r.text(px.x , px.y , len).attr({"font": '12px Verdana,Arial,Helvetica,sans-serif', stroke: "none", fill: "#333",cursor:"pointer"}).translate(c_div.position().left,c_div.position().top);	
		/// ADD TO VECTOR GROUP
		this.c_set.push(c,lbl);	
	}


 	return {cluster:c,label:lbl};
*/

	this.c_set.push(c);
 	return {cluster:c,label:''};
}

serverCluster.prototype.display = function ()
{
 	var _this = this;

    	var lonlat = new OpenLayers.LonLat( _this.oZ.lon, _this.oZ.lat);
	    var px = this.map.getLayerPxFromLonLat( lonlat.transform(serverProj, this.map.getProjectionObject()) );
	    var objs = _this.getTag( px,_this.oZ.len  );
	    
	    _this.tag = objs.cluster;
//	    _this.lbl = objs.label;
	    
	    _this.tag.node.setAttribute("id","c_"+_this.oZ.id);
	    _this.tag.node.setAttribute("class","marker");

		_this.injectedTag = $j(_this.tag);
//		_this.label = $j(_this.lbl);

		/// EVENTS MANAGEMENT
	    $j(_this.tag.node).bind("mouseenter", function ()
	    {
	    	_this.over()
	    }).bind("mouseleave", function ()
	    {
	    	_this.out()
	    }).bind("mousedown", function (e)
	    {
	    	//DEBUG("MOUSE DOWN");
	    	//DEBUG(e);
		    	//e.stopPropagation();
		    	//_this.openPop(e);
	    	
	    }).bind("overMark", function(e,d){_this.listenToOverMark(e,d)}).bind("outMark", function(e,d){_this.listenToOutMark(e,d)});
}