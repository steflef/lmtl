var ga = {};
var map, layer, startpoint, finalpoint, parser;
var clone ='';
var switchInterval;
var daysMin = ["D", "L", "M", "M", "J", "V", "S"];

Array.max = function( array ){
    return Math.max.apply( Math, array );
};
Array.min = function( array ){
    return Math.min.apply( Math, array );
};

/* !DOC READY */
$(document).ready(function(){
    g_atlas = {};
	g_atlas.analytic = {};
	
/*
	$( "#acc_2" ).accordion({
			//fillSpace: true
			autoHeight: false,
			navigation: true
		});	
*/
//
//	$( "#accordion" ).accordion({
//			//fillSpace: true
//			autoHeight: false,
//			navigation: true
//		});
//
//	$( "#accordion" ).delegate('a','click',function(event){
//		////console.log($(event.target));
//		//console.log($(event.target)[0].title);
//
//		var infos = $(event.target)[0].title;
//		if( infos.lastIndexOf('#') != -1 )
//		{
//			var aInfos = infos.split('#');
//			var title = aInfos[0];
//			var code = aInfos[1];
//			g_atlas.analytic.title = title;
//			g_atlas.analytic.code = code;
//			get_data(code);
//		}
//	});
    $("#code-select").chosen().change(function(){
        var code = $(this).val();
        var cText = $(this).find("option[value='"+code+"']").text();
        //console.log( code );
        //console.log( cText );

        g_atlas.analytic.title = cText;
        g_atlas.analytic.code = code;
        get_data( code );
    });

	// Création de la carte vectorielle
  	createMap("#map");
	
	// Création du graphique de la distribution 24/7
//  	r = Raphael("holder"),
//	    xs = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
//	    ys = [7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
//	    axisy = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
//	    axisx = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"];
//
//	r.g.txtattr.font = "12px 'Helvetica Neue',Helvetica,Arial,sans-serif";
	
	// Initialisation
	//$(".lvl_2:first").click();
	$(".opt:first").click(); 
	
	// Delegation
	$("#s_year").change(function(){
		get_data(g_atlas.analytic.code);
	});
	$("#s_month").change(function(){
		get_data(g_atlas.analytic.code);
	});
   // g_atlas.analytic.title = "1110";
   // g_atlas.analytic.code = "1110";
   // get_data("1110");

    $("#code-select").change();
});

// Retrieve data from call code-type
function get_data(code)
{
	$('div.loader').show();
	var attrs = {"fill": "#ddd"};
	arr.attr(attrs);
	
	$('span.active_title').hide();
	$('#geo_data').hide();
	//r.clear();
	
	var year = $("#s_year").val();
	var month =  $("#s_month").val();
	
	var url = BASE_URL+'analyses/rao/api/get_arr_data/'+year+'/'+month+'/'+code;
    console.log(url);
	$.getJSON(
				url,
				null,
				process_data
			);
}

// Process received data
function process_data(data,status,xhr)
{
	//console.log(data);
	////console.log(status);
	
	parseData(data['aStats']);
	draw_24h_graph(data['aGraph']);

	$("#legend_tmpl").find(".legend_text_1").text(data['aLegend'][1]);
	$("#legend_tmpl").find(".legend_text_2").text(data['aLegend'][2] + "%");
	$("#legend_tmpl").show();
	
	$('#geo_data').show();
	$('div.loader').hide();
}

// Draw 24/7 graph
function draw_24h_graph(data)
{
//    console.log(data);
//	r.clear();
//	r.g.dotchart(10, -20, 630, 190, xs, ys, data, {symbol: "o", max: 10, heat: true, axis: "0 0 1 1", axisxstep: 23, axisystep: 6, axisxlabels: axisx, axisxtype: " ", axisytype: " ", axisylabels: axisy}).hover(function () {
//	    this.tag = this.tag || r.g.tag(this.x, this.y, this.value, 0, this.r + 2).insertBefore(this);
//	    this.tag.show();
//	}, function () {
//	    this.tag && this.tag.hide();
//	});
//	 $("#holder").find("text>tspan").attr({"font-weight": 'bold',"color":"#333"});

    var _html = ['<table>'];
    var count = 0;
    var day = 0;
    var _td = [];

    var _max = Array.max(data);
    var _min = Array.min(data);
    var _divide = (_max - _min)/3;

    console.log(Array.max(data));
    $(data).each( function(index,element){
       // var cls = Math.round((element/_divide));
        var cls = (element === 0)?0: Math.round(((element-_min)/_divide)+0.3);
        _td.push('<td class="cls-'+cls+'">'+ element +'</td>');
        count++;

        if(count == 24){
            count = 0;
            var _tr = '<tr><td class="day-short">' + daysMin[day] +'</td>'+ _td.join('')+ '</tr>';
            _td = [];
            day ++;
            _html.push(_tr);
        }
    });


    for(var i=0;i<24;i++){
        _td.push('<td class="hour-short">'+ i +'H</td>');
    }
    _html.push('<tr><td class="day-short"></td>'+ _td.join('')+ '</tr>');
    _html.push('</table>');
    $("#table-24h-holder").html(_html.join(''));
}

// Build stats per geo
function parseData(data)
{
	var tmpl = $('#stats-tmpl').html();
	$(data).each(function(index){
	
		//this.cls_width = "width:"+this.arr_raw_percent+"%";
		//this.cls_width = this.arr_raw_percent;
		
	//	if( !g_atlas.isNull(this.arr_code))
        if( !(this.arr_code === null))
		{
			var attrs = {"fill": "#"+this.arr_cls_colors, "stroke":"#fff", "stroke-width":1, "fill-opacity":1}
        	$("#arr_"+this.arr_code)[0].raphael.attr(attrs);
		}


        //console.log(this);
	});	

    var view = {
	    	total: '',
		  	geom: data
		  };
	
	//Append to DOM
	$('#geo_data').html( Mustache.to_html(tmpl, view) );
	
	$('#geo_data').find("span.index").each( function(){
		var w = $(this).text();
		var c = $(this).parent().find("span.color").text();
		var t = $(this).parent().find("a").text();
		////console.log(w + " " + c);
		
		$(this).css('width', w);
		if( t.substring(0,1) != '*')
		{
			$(this).css('backgroundColor', "#"+c);
			$(this).parent().addClass("highlight");
		}
	});
	
	$("#geo_data").delegate('li.highlight','mouseenter',function(event){
		////console.log($(event.currentTarget)[0].id);
		var t = $(event.currentTarget)[0].id.substring(5);
		var attrs = {"stroke":"#66c", "stroke-width":1.4};
		$("#arr_"+t)[0].raphael.attr(attrs);
		$("#arr_"+t)[0].raphael.toFront();
	});
	$("#geo_data").delegate('li.highlight','mouseout',function(event){
		var t = $(event.currentTarget)[0].id.substring(5);
		var attrs = {"stroke":"#fff", "stroke-width":1}
		//var attrs = {"fill-opacity":1}
		$("#arr_"+t)[0].raphael.attr(attrs);
	});

	//$('span.active_title').html(g_atlas.analytic.title.split('/').join('<br />')).show();
    $('#selection-title h5').html(g_atlas.analytic.title).show();
    //console.log(g_atlas.analytic.title);
}

function createMap(target_div)
{
	//console.time("====== createVectors ======");
	//limits = quartiers;
	var pos = 0;
	var step = 30;
	var l = svg.length;
	
	// Création de l'environnement R
	//console.log("h:"+ $(target_div).height()+ " w:"+ $(target_div).width());
	var w =  $(target_div).width();
	var h =  $(target_div).height();
	
	$(target_div).append('<div id="r" style="height:'+h+'px;width:'+w+'px,position:absolute;left:0;top:0;"></div>');
	
	R = Raphael($("#r")[0], w, h);
	arr = R.set(); // Arrondissements

    (function(){
    	////console.info("// BATCH ("+pos+")//");
		var s = pos+step;
       (function(){
	    	for(var i= pos;i<s;i++)
			{
				if (i >= l)	break;
	           	var path = svg[i].geom;
	           	var id = svg[i].id;
	           	
	  			if(path == null) continue;
	  			
	  			var attrs = {"fill": "#ddd", "stroke":"#fff", "stroke-width":1, "fill-opacity":1};
				var c = R.path(path).attr(attrs);
				c.node.id="arr_"+id;
				arr.push(c);
				}
			})();
			
		pos = s;
		
        if (pos < l)
        {
            setTimeout(arguments.callee,10);
        }else
        {
        	//console.timeEnd("====== createVectors ======");
        	$("#map").delegate('path','mouseenter',function(event){
        		////console.log($(event.target)[0].raphael.node.id);
        		var arr_id = $(event.target)[0].raphael.node.id.substring(4);
        		////console.log(arr_id);
        		$("li#code_"+arr_id).css('backgroundColor',"#efefef");
        		var attrs = {"stroke":"#66c", "stroke-width":1.4};
        		$(event.target)[0].raphael.attr(attrs);
        		$(event.target)[0].raphael.toFront();
        	});
        	
        	$("#map").delegate('path','mouseout',function(event){
        		////console.log($(event.target)[0].raphael.node.id);
        		var arr_id = $(event.target)[0].raphael.node.id.substring(4);
        		$("li#code_"+arr_id).css('backgroundColor',"#fff");
        		var attrs = {"stroke":"#fff", "stroke-width":1};
				$(event.target)[0].raphael.attr(attrs);
        	});

        }
    })();
}