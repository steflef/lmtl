<!doctype html>
<html class="no-js" ng-app="appMain">
<head>
    <meta http-equiv="Content-Type" content="charset=utf-8"/>
    <title>Tableau de bord [lmtl]</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="<?=$base_url?>" />
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="./public/style/bootstrap.css">
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4.4/leaflet.css"/>
    <link rel="stylesheet" href="./public/js/libs/leaflet/markercluster/MarkerCluster.css"/>
    <link rel="stylesheet" href="./public/js/libs/leaflet/markercluster/MarkerCluster.Default.css"/>

    <style type="text/css">
/*        .leaflet-left .leaflet-control {
            margin-left: 400px;
        }*/
    </style>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4.4/leaflet.ie.css"/>
    <!--<link rel="stylesheet" href="/public/js/libs/leaflet/markercluster/MarkerCluster.Default.ie.css"/>-->
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="./public/imgs/favicon.png">
    <link rel="apple-touch-icon" href="./public/imgs/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="./public/imgs/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="./public/imgs/apple-touch-icon-114x114.png">

    <script type="text/javascript">
        var BASE_URL = '';
        // Montreal
        var origin={
            lon:-73.59246,
            lat:45.528293
        };
        <?if (!empty($user)):?>
        var USER_NAME = '<?=$user?>';
        <?endif;?>
    </script>

    <script type="text/javascript" src="//code.angularjs.org/1.0.1/angular-1.0.1.min.js"></script>
    <script type="text/javascript" src="http://code.angularjs.org/1.0.1/angular-sanitize-1.0.1.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="//documentcloud.github.com/underscore/underscore-min.js"></script>
    <script type="text/javascript" src="./public/js/ng/ctrl/map.js"></script>
    <script type="text/javascript" src="./public/js/ng/ctrl/toolbar.js"></script>
    <script type="text/javascript" src="./public/js/ng/app-tb-datasets.js"></script>
    <script type="text/javascript" src="http://cdn.leafletjs.com/leaflet-0.4.4/leaflet.js"></script>
    <script type="text/javascript" src="./public/js/libs/leaflet/markercluster/leaflet.markercluster.js"></script>
    <script type="text/javascript" src="./public/js/libs/bootstrap/bootstrap-dropdown.js"></script>
</head>
<body>
<header class="app_header">
    <nav>
        <div ng-controller="ToolBarCtrl"><toolbar></toolbar></div>
    </nav>

    <noscript>
        <div class="translate" id="noscript">
            <h3>Tu dois activer JavaScript pour naviguer sur cette application</h3>
            <p>Nous utilisons les meilleures technologies de pointe disponibles pour offrir à nos utilisateurs une expérience Web optimale.
                <br/>Il est recommandé d'activer JavaScript dans les paramètres du navigateur pour continuer.
            </p>
            <p class="small">Contactez un administrateur pour plus d'informations</p>
        </div>
    </noscript>
</header>
