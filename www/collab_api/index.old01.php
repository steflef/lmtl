<?php
use \Slim\Slim;
use Slim\Middleware\SessionCookie;
use \Aura\Sql\ConnectionFactory;
use \Pimple;
use \Bcrypt;
use Guzzle\Http\Client;

require 'vendor/autoload.php';
require 'models/lmtl.php';

$di = new Pimple();
$app = new Slim(array(
    'mode' => 'development'
));

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'log.level' => 4,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});



// AURA.SQL
$di['db'] = $di->share(function (){
    $connectionFactory = new ConnectionFactory;
    $connection = $connectionFactory->newInstance(
        'sqlite',
        './storage/database/app2.sqlite'
    );
    $connection->connect();
    return $connection;
});
$di['baseUrl'] = 'http://localhost/lmtl/www/collab_api/';
$di['uploadDir'] = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage/uploads';
$di['ogre_host'] = 'http://ogre.adc4gis.com';
$di['ogre_convert_endpoint'] = '/convert';
$di['google_client_id'] = '509163819657.apps.googleusercontent.com';
$di['google_client_secret'] = 'notasecret';



$app->add(new SessionCookie(array('secret' => 'nS#ul&1X4R7AOeIp+Q4T%nMX"4CS6~!')));

$app->error(function (\Exception $e) use ($app) {
    $log = $app->getLog();
/*    $log->error(array(
        'label' => 'ERROR',
        'message' => $e
    ));
*/
    echo 'UNE ERREUR >> ' . $e;
    $log->error($e);
    //$app->render('error.php');
});


$authenticate = function () use ($app, $di){
    return function () use ($app, $di) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Autorisation requise');
            $app->redirect($di['baseUrl'].'login');
        }
    };
};

$apiAuthenticate = function () use ($app, $di){
    return function () use ($app, $di) {
        if (!isset($_SESSION['user'])) {
            $res = $app->response();
            $res->status(200);
            //$res['Content-Type'] = 'application/json';
            // iFrame FIX
            $res['Content-Type'] = 'text/html';
            $response['status'] = 403;
            $response['msg'] = "Session expirée";

            echo json_encode($response);
            exit;
        }
    };
};


$app->hook('slim.before.dispatch', function() use ($app, $di) {
    $user = null;

    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
    }
    $app->view()->setData(array(
        'user'=> $user,
        'base_url'=> $di['baseUrl']
    ));
});

$app->get("/", $authenticate($app), function () use ($app, $di) {

/*    require_once('models/GeoOgre.php');
    $geoJson = GeoOgre::geo_ogre_convert_file($di,'/Users/prof_lebrun/Downloads/feux-pietons.shp-1.zip');
    var_dump($geoJson);*/

    //$env = $app->environment();
    //$list = Lmtl::getUser($env['conn'],1);
    //$list = Lmtl::getUser($di['conn'],1);
/*    $list = Lmtl::getUser($di['db'],1);
    print_r($list);
    echo Bcrypt::salt(24);
    echo '<br>'. sha1('admin'.'$2y$24$sRWNKw7DRrtPJBSI9vALPA');*/

/*
    $client = new Client('http://ogre.adc4gis.com');

    $response = $client->post('/convert', null, array(
        'upload' => '@/Users/prof_lebrun/Downloads/feux-pietons.shp-1.zip'
    ))->send();*/
       // ->addPostFiles(array('upload' => '/Users/prof_lebrun/Downloads/feux-pietons.shp-1.zip'));

    //print_r($response);
    //echo '<br>'.$response;
    //echo '<br>'.$response->getBody();
/*    echo '<br>Content-Type: '.$response->getHeader('Content-Type');
    $b = $response->getHeader('Content-Length');
    $kb = round(trim($b)/1024,2);
    echo '<br>Content-Length: '. $kb . 'kb';
    echo '<br>Content-Length: '. $response->getHeader('Content-Length');
    echo '<br>Date: '.$response->getHeader('Date');
    echo '<br>Code: '.$response->getStatusCode();
    echo '<br>Reason: '.$response->getReasonPhrase();*/


    //echo '<br>'.$response->json();
/*    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://ogre.adc4gis.com/convert");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
// sets multipart/form-data content-type
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'upload' => '@/Users/prof_lebrun/Downloads/feux-pietons.shp-1.zip'//,
        //'file' => '/Users/prof_lebrun/Downloads/feux-pietons.shp-1.zip'
    ));

    $data = curl_exec($ch);
    print_r($data);*/



/*    $date = new DateTime();
        echo '<br>'.$date->format('U ');
        echo '<br>';*/


    $app->render('index_v1.php');
});


$app->get("/sheet", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");

    // username/password for google account
    $user = "prof.lebrun@gmail.com";
    $pass = "ocool007_";

    // connect to the spreadsheet
    $doc = new Spreadsheet();
    $doc->authenticate($user, $pass);
    $doc->setSpreadsheet("Spreadsheet_test"); // spreadsheet name
    $doc->setWorksheet("Worksheet_test"); // worksheet name

    // current date/time
    $date = date('Y-m-d G:i:s', time());
    $data = array(
        'Date' => $date,
        'Test' => 'StefLef',
        'Price' => 56.34
    );

    $doc->add($data);

    // query the database for entries that have not been put in the spreadsheet (googled lol)
/*    $query = "SELECT * FROM table WHERE googled = 0";

    $result = mysql_query($query);

    $count = 0;
    // loop through and if it's good add to spreadsheet
    while ($row = mysql_fetch_array($result)) {

        if ($doc->add($row)) {
            // if it's good, echo the id and update if it was added  (googled = 1)
            echo 'Entry ID: ' . $row['id'] . '<br />';
            mysql_query('UPDATE rsvp SET googled = 1 WHERE id = ' . $row['id']);
        } else {
            // fail
            echo 'Something went wrong, please try again.';
        }

        $count++;
    }*/

    //echo 'Entries added: ' . $count . '<br />';
    echo 'OK';

//}
});

$app->get("/list", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");

    // username/password for google account
    $user = "prof.lebrun@gmail.com";
    $pass = "ocool007_";

    // connect to the spreadsheet
    $doc = new Spreadsheet();
    $doc->authenticate($user, $pass);
    $rsp = $doc->listSheets();

    // current date/time
    $date = date('Y-m-d G:i:s', time());
    echo $date . '<br>';

    echo '<pre>';
    print_r($rsp);
    echo '</pre>';
});

$app->get("/add", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");

    // username/password for google account
    $user = "prof.lebrun@gmail.com";
    $pass = "ocool007_";

    // connect to the spreadsheet
    $doc = new Spreadsheet();
    $doc->authenticate($user, $pass);
    $doc->setSpreadsheet("0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc"); // spreadsheet name
    $doc->setWorksheet("tesWsiZPGjeWd2r-4BPSoig"); // worksheet name

    // current date/time
    $date = date('Y-m-d G:i:s', time());
    $data = array(
        'Src' => 'can',
        'Magnitude' => 5.2,
        'Region' => 'QC-'.$date
    );

    $rsp = $doc->add($data);
    echo '<pre>';
    print_r( $rsp );
    echo '</pre>';

});


$app->get("/title", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");

    // username/password for google account
    $user = "prof.lebrun@gmail.com";
    $pass = "ocool007_";

    // connect to the spreadsheet
    $doc = new Spreadsheet();
    $doc->authenticate($user, $pass);
    $doc->setSpreadsheet("0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc"); // spreadsheet name
    $rsp = $doc->updateTitle("S.TEST");

    echo '<pre>';
    print_r( $rsp );
    echo '</pre>';
});

$app->get("/cell", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");

    // username/password for google account
    $user = "prof.lebrun@gmail.com";
    $pass = "ocool007_";

    // connect to the spreadsheet
    $doc = new Spreadsheet();
    $doc->authenticate($user, $pass);
    $doc->setSpreadsheet("0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc"); // spreadsheet name
    $rsp = $doc->updateCell();

    echo '<pre>';
    print_r( $rsp );
    echo '</pre>';
});

$app->get("/geo", function () use ($app, $di) {



    $result = array();
    $adapter  = new \Geocoder\HttpAdapter\GuzzleHttpAdapter();

    $geocoder = new \Geocoder\Geocoder();
    $geocoder->registerProviders(array(
/*        new \Geocoder\Provider\OpenStreetMapsProvider(
            $adapter
        ),*/
        new \Geocoder\Provider\GoogleMapsProvider(
            $adapter,
            'AIzaSyBMrGmwQpwqRgO8LtMwf4J1S-GJThYpBLQ'
        ),
        new \Geocoder\Provider\CloudMadeProvider(
            $adapter,
            '3921666a8ed240d59478def473a7b18a'
        ),
        new \Geocoder\Provider\BingMapsProvider(
            $adapter,
            'Aj5-RoGzUR4-jai4Z0BSA8cNfPZUCS1jY3f0h-ZMxqagmu4umiXGq5UF78l8b_Ct'
        )
    ));


    $address = '20 Mont-Royal Est, Montréal, Canada';
    $zip = 'h2j2m3';

    foreach (array_keys($geocoder->getProviders()) as $provider) {
        try{
            $geocoder->using($provider);
            $result[$provider] = $geocoder->geocode($zip)->toArray();
        }catch(Exception $e){
            //echo $provider;
            echo "Error with >> " . $provider;
            $result[$provider] = array();
        }
    }

    echo '<pre>';
    print_r( $result );

    $geocoder->using('google_maps');
    $dumper = new \Geocoder\Dumper\GeojsonDumper();
    echo $dumper->dump($geocoder->geocode($zip));
    echo '</pre>';
});

// DRIVE
$app->get("/drive", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");
    require_once 'vendor/google-api-php-client/src/Google_Client.php';
    require_once "vendor/google-api-php-client/src/contrib/Google_DriveService.php";
    require_once "vendor/google-api-php-client/src/contrib/Google_Oauth2Service.php";

    $DRIVE_SCOPE = 'https://www.googleapis.com/auth/drive';
    $SERVICE_ACCOUNT_EMAIL = '509163819657@developer.gserviceaccount.com';
    $SERVICE_ACCOUNT_PKCS12_FILE_PATH = '/Users/prof_lebrun/Downloads/a56f4e11d50f76ede6c83f4c0fad55ea1741b20b-privatekey.p12';



    $key = file_get_contents($SERVICE_ACCOUNT_PKCS12_FILE_PATH);
   // echo ($key);
    $auth = new Google_AssertionCredentials(
        $SERVICE_ACCOUNT_EMAIL,
        array($DRIVE_SCOPE),
        $key);
    $client = new Google_Client();
    $client->setUseObjects(true);
    $client->setAssertionCredentials($auth);
    $GD_Service = new Google_DriveService($client);
    //File
    $mimeType = 'text/csv';
    $file = new Google_DriveFile();
    $file->setTitle('CQ ATLAS -- test');
    $file->setDescription('Ma Description ... test');
    $file->setMimeType($mimeType);
    $file->setOwnerNames(array('prof.lebrun@gmail.com'));
    $file->setParents(array(
        array(
            'kind'=> 'drive#fileLink',
            'id'=>'0B7Dvk7BRZ_yjT2hyd3JFOHoxWXM'
        )
    ));

    // Set the parent folder.
/*
        $parent = new Google_ParentReference();
        $parent->setId("CQATLAS");
        $file->setParents(array($parent));*/

    //$createdFile = $GD_Service->files->insert($file);

    $createdFile = $GD_Service->files->insert($file, array(
        'data' => file_get_contents('/Users/prof_lebrun/Downloads/eqs7day-M7.csv'),
        'mimeType' => $mimeType,
        'convert' => true
    ));

    //echo file_get_contents('/Users/prof_lebrun/Downloads/eqs7day-M7.csv');
    //print_r($createdFile);
    //return $createdFile;
    print "File ID: " . $createdFile->getId();
    print "File AlternateLink: " . $createdFile->getAlternateLink();
    echo '<pre>';
    print_r($createdFile);
    echo '</pre>';


/*    $file = new Google_DriveFile();
    $file->setTitle('Mon Titre ...');
    $file->setDescription('Ma Description ...');
    $file->setmimeType('application/vnd.google-apps.drive-sdk');
    $createdFile = $GD_Service->files->insert($file);
    print "File ID: " . $file->getId();*/
/*
    $url = 'https://www.googleapis.com/upload/drive/v2/files?uploadType=media';
    $headers = array(
        //"Authorization: GoogleLogin auth=" . $client->getAuth(),
        "GData-Version: 3.0",
        "Content-Type: application/vnd.google-apps.drive-sdk",
        "Authorization: ". $client->getAccessToken()
    );

    $fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006"><title>Mon titre ....</title><gs:rowCount>50</gs:rowCount><gs:colCount>10</gs:colCount></entry>';
    $headers[] = "Content-Length: ". strlen($fields);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
    $response = curl_exec($curl);


    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if($status == 200) {
       print_r($response);
    }
    echo 'Status >> '. $status . '<br>';
    print_r($response);*/
});


$app->get("/appscript", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");
    require_once 'vendor/google-api-php-client/src/Google_Client.php';
    require_once "vendor/google-api-php-client/src/contrib/Google_DriveService.php";
    require_once "vendor/google-api-php-client/src/contrib/Google_Oauth2Service.php";

    $SCOPES = array(
        'https://www.googleapis.com/auth/drive.file',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile'
    );
    $SERVICE_ACCOUNT_EMAIL = '509163819657@developer.gserviceaccount.com';
    $SERVICE_ACCOUNT_PKCS12_FILE_PATH = '/Users/prof_lebrun/Downloads/a56f4e11d50f76ede6c83f4c0fad55ea1741b20b-privatekey.p12';
    $key = file_get_contents($SERVICE_ACCOUNT_PKCS12_FILE_PATH);
    // echo ($key);
    $auth = new Google_AssertionCredentials(
        $SERVICE_ACCOUNT_EMAIL,
        $SCOPES,
        $key);


    $client = new Google_Client();
    $client->setUseObjects(true);
    $client->setAssertionCredentials($auth);
    $client->setAccessType('offline');
    //$client->setApprovalPrompt('force');
    try{
        $client->authenticate();
    }catch (Error $e){
        echo $e;

    }

    //$GD_Service = new Google_DriveService($client);


    //$apps = $GD_Service->files->listFiles();
    echo '>>>> '. $client->getAccessToken();
    if ($client->getAccessToken()) {
        echo '>>>> '. $client->getAccessToken();

        // We're not done yet. Remember to update the cached access token.
        // Remember to replace $_SESSION with a real database or memcached.
        $_SESSION['token'] = $client->getAccessToken();
    } else {
        $authUrl = $client->createAuthUrl();
        print "<a href='$authUrl'>Connect Me!</a>";
    }

    //$apps = $GD_Service->apps->listApps();
    echo '<pre>';
   print_r($client->getAuth());
    echo '</pre>';

    $headers = array(
        "Authorization: GoogleLogin auth=" . $client->getAccessToken()//,
        //"GData-Version: 3.0",
        //"Content-Type: application/json"//,
        //"Authorization: ". $client->getAccessToken()
    );

    $url = 'https://script.google.com/macros/s/AKfycbzbFJq0hvgkQt6EYWJvgxS0hcnMNxbXGnee1crmCk0pxFhY2OKn/exec';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);


    echo '<pre>';
    echo $status. ' :: ' ;
    echo $response;
  //  print_r($apps);
    echo '</pre>';
});

$app->get("/about", function () use ($app) {
    $app->render('about.php');
});

$app->get("/level/contact", function () use ($app) {
    $app->render('levelContact.php');
});

$app->get("/logout", function () use ($app) {
    unset($_SESSION['user']);
    $app->view()->setData('user', null);
    //$app->render('logout.php');
    $app->redirect('./');
});


// LOGIN
$app->get("/login", function () use ($app) {

    if (isset($_SESSION['slim.flash'])) {
        $flash = $_SESSION['slim.flash'];
    }

    $urlRedirect = './';

    if ($app->request()->get('r') && $app->request()->get('r') != '/logout' && $app->request()->get('r') != '/login') {
        $_SESSION['urlRedirect'] = $app->request()->get('r');
    }

    if (isset($_SESSION['urlRedirect'])) {
        $urlRedirect = '.'.$_SESSION['urlRedirect'];
    }

    $email_value = $email_error = $password_error = '';

    if (isset($flash['email'])) {
        $email_value = $flash['email'];
    }

    if (isset($flash['errors']['email'])) {
        $email_error = $flash['errors']['email'];
    }

    if (isset($flash['errors']['password'])) {
        $password_error = $flash['errors']['password'];
    }

    $app->render('login.php', array(
        //'error' => $error,
        'email_value' => $email_value,
        'email_error' => $email_error,
        'password_error' => $password_error,
        'urlRedirect' => $urlRedirect
    ));
});


$app->post("/login", function () use ($app, $di) {
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');

    $errors = array();
    echo '<br>'. $email. '<br>';
    $userInfos = Lmtl::getUser($di['db'],$email);
    //print_r($userInfos);
    //exit;

    if (count($userInfos) == 0) {
        $errors['email'] = "Courriel introuvable";
    } else if ( sha1($password.$userInfos[0]['salt']) != $userInfos[0]['password']) {
        $app->flash('email', $email);
        $errors['password'] = "Le mot de passe ne correspond pas";
    }

/*    if ($email != "admin") {
        $errors['email'] = "Courriel introuvable";
    } else if ($password != "aaaa") {
        $app->flash('email', $email);
        $errors['password'] = "Le mot de passe ne correspond pas";
    }*/

    if (count($errors) > 0) {
        $app->flash('errors', $errors);

        $app->redirect('login');
    }

    $_SESSION['user'] = $email;

    if (isset($_SESSION['urlRedirect'])) {
        $tmp = $_SESSION['urlRedirect'];
        unset($_SESSION['urlRedirect']);
        $app->redirect('.'.$tmp);
    }

    $app->redirect('./');
});


$app->get("/admin/users", $authenticate($app), function () use ($app, $di) {
    if( !Lmtl::isAdmin($di['db'],$_SESSION['user']) ){
        echo 'Admin Only!';
        exit();
    }

    $users = Lmtl::getUsers($di['db']);
    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    $res['X-Powered-By'] = 'Slim';

    echo json_encode($users);
/*    echo 'List Users <pre>';
    print_r($users);
    echo '</pre>';*/
});

$app->get("/upload", $authenticate($app), function () use ($app, $di) {
    $app->render('uploadTmpl.php');
});

$app->post("/upload", $apiAuthenticate($app), function () use ($app, $di) {

    $storage = new \Upload\Storage\FileSystem($di['uploadDir']);
    $file = new \Upload\File('file_upload', $storage);
    $errors = array();
    $metas = array();

    // RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    // iFrame fix
    $res['Content-Type'] = 'text/html';
    $response = array(
        "status" => "",
        "msg" => ""
    );

    // Validate file upload
    $file->addValidations(array(
        new \Upload\Validation\Mimetype(array('text/csv','text/plain')),
        // Ensure file is no larger than 5M (use "B", "K", M", or "G")
        new \Upload\Validation\Size('5M')
    ));

    // Try to upload file
    try {
        // Success!
        require_once 'vendor/cqatlas/cqatlas/CqUtil.php';
        $mime = $file->getMimetype();
        $slugName = CqUtil::slugify($file->getName());
        //$fingerprint = md5_file($_FILES['file_upload']['path']);
        $date = new DateTime();
        $file->setName($slugName.'_'.$date->getTimestamp() );

        $file->upload();

    } catch (\Exception $e) {
        // Fail!
        $errors = $file->getErrors();
        if(empty($errors)){
            $errors[] = $e->getMessage();
        }

        $response['status'] = 400;
        $response['msg'] = $errors[0];

        $res->status(400);
        echo json_encode($response);
        return false;
    }

    if(empty($errors)){
        //require_once 'vendor/File_CSV_DataSource/File_CSV_DataSource.php';
        ini_set('auto_detect_line_endings',TRUE);
        $reader = new \EasyCSV\Reader($di['uploadDir'].'/'.$file->getName().'.csv');
        $maxRows = 3;
        $quickViewData = array();

        while ($row = $reader->getRow()) {
            $quickViewData[] = $row;
            $maxRows --;
            if($maxRows == 0 ) break;
        }

        $quickViewFlatData = array();
        $maxRows = 3;
        while ($row = $reader->getFlatRow()) {
            $temp = array();
            foreach($row as $item){
                if(is_numeric($item)){
                    $temp[] = $item + 0;
                }else{
                    $temp[] = $item;
                }
            }
            $quickViewFlatData[] = $temp;
            $maxRows --;
            if($maxRows == 0 ) break;
        }
        ini_set('auto_detect_line_endings',FALSE);
        if(count($quickViewFlatData)<2){
            $response['status'] = 400;
            $response['msg'] = 'Un document avec un minimum de 2 lignes est requis';

            //Manual test
/*            ini_set('auto_detect_line_endings',TRUE);
            $handle = fopen($di['uploadDir'].'/'.$file->getName().'.csv','r');
            $count = 0;
            while ( ($data = fgetcsv($handle) ) !== FALSE ) {
                $count ++;
            }
            ini_set('auto_detect_line_endings',FALSE);*/


            $all = $reader->getAll();
            //$response['debug'] = $count;

            $res->status(400);
            echo json_encode($response);
            return false;
        }

        require 'models/geo.php';
        $csvHeaders = array_keys(array_change_key_case($quickViewData[0],CASE_LOWER));

        $csvTypes = array();
        $fieldsCount = count($csvHeaders);
        for($i=0;$i<$fieldsCount;$i++){
            // CHECK FOR TYPE
            $type = gettype($quickViewFlatData[1][$i]);
            // IF STRING > CHECK FOR DATE
            if($type === 'string'){
                $type = (($timestamp = strtotime($quickViewFlatData[1][$i])) && (strlen($quickViewFlatData[1][$i])>7))? 'date' : $type;
            }

            $csvTypes[] = array(
                'title' => $csvHeaders[$i],
                'type' => $type,
                'desc' => ''
            );
        }

        //Spatial fields / GEOCODING
        try{
            $lonHeader = CqUtil::matchKeys($csvHeaders, array('lon','lng','longitude'));
            $latHeader = CqUtil::matchKeys($csvHeaders, array('lat','latitude'));
            $addressHeader = CqUtil::matchKeys($csvHeaders, array('adresse','address','addr'));
            if( $lonHeader === false || $latHeader === false){
                // No Lng/Lat >> address geocoding
                if( $addressHeader === false ){
                    //KAPUT NO GEOCODING FIELDS
                    throw new Exception('Aucune entête spatiale (lon,lat ou adresse)');
                }
                // Need Geocoding !!
                // ========================================

                $geoResult = array();
                $adapter  = new \Geocoder\HttpAdapter\GuzzleHttpAdapter();
                $geocoder = new \Geocoder\Geocoder();
                $geocoder->registerProviders(array(
                    new \Geocoder\Provider\GoogleMapsProvider(
                        $adapter,
                        'AIzaSyBMrGmwQpwqRgO8LtMwf4J1S-GJThYpBLQ'
                    ),
                    new \Geocoder\Provider\CloudMadeProvider(
                        $adapter,
                        '3921666a8ed240d59478def473a7b18a'
                    ),
                    new \Geocoder\Provider\BingMapsProvider(
                        $adapter,
                        'Aj5-RoGzUR4-jai4Z0BSA8cNfPZUCS1jY3f0h-ZMxqagmu4umiXGq5UF78l8b_Ct'
                    )
                ));

                foreach($quickViewData as $row){
                    $address = $row[$addressHeader];

                    foreach (array_keys($geocoder->getProviders()) as $provider) {
                        try{
                            $geocoder->using($provider);
                            $geoResult[$provider] = $geocoder->geocode($address)->toArray();
                        }catch(Exception $e){
                            $geoResult[$provider] = array();
                        }
                    }
                    sleep(1);
                }

                $metas['address'] = $addressHeader;
                $metas['geocoding'] = $geoResult;
                /// ========================================
            }else{
                // Lng/Lat Ok >> Check if data is ok
                $llErrors = 0;
                $llValids = 0;
                $llErrorsObjs = array();

                foreach($quickViewData as $row){
                    $rowLower = array_change_key_case($row, CASE_LOWER);

                    if(Geo::geo_utils_is_valid_longitude($rowLower[$lonHeader]) && Geo::geo_utils_is_valid_latitude($rowLower[$latHeader])){
                        $llValids ++;
                    }else{
                        $llErrors ++;
                        $llErrorsObjs[] = '('.$rowLower[$lonHeader].','.$rowLower[$latHeader].')';
                    }
                }
                if( $llErrors > 0 ){
                    throw new \Exception('Certaines coordonnées (lon,lat) sont invalides. '.$llErrors.' erreurs.{'. implode(',',$llErrorsObjs).'}');
                }

                $metas['lon'] = $lonHeader;
                $metas['lat'] = $latHeader;
            }

        }catch (\Exception $e){
            $response['status'] = 400;
            $response['msg'] = $e->getMessage(). ' :: ' . $e->getLine() . ' :: '. $e->getTraceAsString();

            $res->status(400);
            echo json_encode($response);
            return false;
        }

        $metadata = array(
            'name'=> '',
            'description' => '',
            'fileName'=> $file->getName(),
            'fileMime'=> $mime,
            'fileExtension'=> $file->getExtension(),
            'fileSize'=> ($file->getSize()/1000).' kb',
            'fileHash'=> md5_file($di['uploadDir'].'/'.$file->getName().'.csv'),
            'fileId' => sha1($file->getName() . '_' . $_SESSION['user']),
            'fileCreated' => date('U')
        );

        $data = array(
            'data' =>  $quickViewData,
            'flatData' => $quickViewFlatData,
            'headers' => $csvHeaders,
            'headersType' =>$csvTypes,
            'metadata' => $metadata,
            'geoFields' => $metas
        );

        $response['status'] = 200;
        $response['msg'] = 'ok';

        $output = array_merge($response,$data);

        $res->status(200);
        echo json_encode($output);
    }
});

$app->get("/private/about", $authenticate($app), function () use ($app) {
    $app->render('privateAbout.php');
});

$app->get("/private/goodstuff", $authenticate($app), function () use ($app) {
    $app->render('privateGoodStuff.php');
});

$app->run();