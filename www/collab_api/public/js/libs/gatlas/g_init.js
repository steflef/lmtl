// INITIALISATION DES VARIABLES
var $j = jQuery.noConflict(); // jQuery mode sans conflit
var g_atlas = {}; // OBJET G_ATLAS

//// ******************************************************************************
//// EXECUTE WHEN THE PAGE IS RENDERED ============== http://api.jquery.com/ready/
//// IE Too early for IE VML engine ... see window.onload = init; enbas de la page
//// ******************************************************************************

/* INITIALISE LES COUCHES VECTORIELLES POUR LA SÉLECTION. IE */
function init(){

	// *** FIN DES OPÉRATIONS ***
}

g_atlas.spiralStairs = function ()
{
    g_atlas.event.yaw += 45;
    g_atlas.event.streetview.panTo({
        yaw : g_atlas.event.yaw, pitch :- 2
    })
}

g_atlas.isNull = function (D) {
    return D === null
};

g_atlas.isUndefined = function (D) {
    return D === void 0
};

g_atlas.UrlExists = function(url) {
  var http = new XMLHttpRequest();
  http.open('HEAD', url, false);
  http.send();
  return http.status!=404;
}

Array.max = function( array ){
    return Math.max.apply( Math, array );
};

Array.min = function( array ){
    return Math.min.apply( Math, array );
};

/* !DEBUGER ============== */
var DEBUG_MODE = true;
var DEBUG = function (log) {
	if (DEBUG_MODE && typeof (console) !== 'undefined') { 
		console.log(log);
        //debugger; 
    } 
};

window.onload = init; // POUR IE.