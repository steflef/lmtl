<!doctype html>
<html class="no-js">
<head>
    <meta http-equiv="Content-Type" content="charset=utf-8"/>
    <title>Connexion [ng]</title>
    <base href="<?=$base_url?>" />

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="./public/style/bootstrap.css">

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../public/imgs/favicon.png">
    <link rel="apple-touch-icon" href="../public/imgs/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../public/imgs/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../public/imgs/apple-touch-icon-114x114.png">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="./public/js/libs/bootstrap/bootstrap-tooltip.js"></script>
</head>
<body>
<div class="container" style="padding-top: 45px;">
    <div class="row">
        <div class="span12">
            <div class="hero-unit">

                <h1>LMTL - Lieux montréalais</h1>
                <h2>Se Connecter</h2>
                <p>Veuillez vous connecter à l'aide de votre adresse courriel et de votre mot de passe.</p>

                <form action="login" method="POST">
                    <p>Courriel:<br><input class="input-xlarge" type="text" name="email" id="email" placeholder="admin@admin.com" value="<?=$email_value?>" /> <span class="help-inline label label-important"><?=$email_error?></span></p>
                    <p>Mot de passe:<br><input class="input-xlarge" type="password" name="password" id="password" /> <span class="help-inline label label-important"><?=$password_error?></span></p>
                    <p><input type="submit" class="btn" value="Connexion" />
                </form>

                <footer>
                    <p>&copy; Collectif Quartier 2013</p>
                </footer>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#identity').focus();
</script>
</body>
</html>