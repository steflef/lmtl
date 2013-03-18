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
$di['storageDir'] = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage';
$di['ogre_host'] = 'http://ogre.adc4gis.com';
$di['ogre_convert_endpoint'] = '/convert';

$di['google_client_id'] = '509163819657.apps.googleusercontent.com';
$di['google_client_secret'] = 'notasecret';
$di['google_user'] = 'prof.lebrun@gmail.com';
$di['google_password'] = 'ocool007_';
$di['google_drive_master_file_id'] = '0ArDvk7BRZ_yjdEI0S3R1Yks4NUxhaGlRTVlDTFNCd3c';
$di['google_drive_folder_id'] = '0B7Dvk7BRZ_yjT2hyd3JFOHoxWXM';

$di['cartodb_api_key'] = '6947088ad96d8efc9ffe906f1312d1568f3eafc1';
$di['cartodb_subdomain'] = 'steflef';
$di['cartodb_endpoint'] = 'cartodb.com/api/v2/sql';


$di['delimitedExtensions'] = array('csv', 'txt');
$di['excelExtensions'] = array('xls', 'xlsx');
$di['serverSideGeocoding'] = false;

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

// ADD NEW WORKSHEET
$app->get("/add_worksheet", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");

    // username/password for google account
    $user = "prof.lebrun@gmail.com";
    $pass = "ocool007_";

    // connect to the spreadsheet
    $doc = new Spreadsheet();
    $doc->authenticate($user, $pass);
    $doc->setSpreadsheetId("0ApilBOlHO2BDdHZvdlNlaWdFNi15d2J0dHpUZ2lJV2c"); // spreadsheet name

    $worksheetId = $doc->addSheet('Meta');

   // $xml = simplexml_load_string($rsp['response']);
    //echo $xml->id;


    $wid =explode('/', $worksheetId['response']->id);
    $sheetId = array_pop($wid);
/*    echo '<pre><code>';
    print_r($wid);
    print_r($sheetId);
    echo '</code></pre>';*/
    $doc->setWorksheetId( substr($sheetId, 0,3));
    $col = $doc->addColumn(array('Metadata', 'Valeurs'));
    print_r($col);
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

$app->get("/zend", function() use ($app) {

    //require_once 'vendor/Zend/Loader.php';
    $user = "prof.lebrun@gmail.com";
    $pass = "ocool007_";
    $fileId = '0ApilBOlHO2BDdHZvdlNlaWdFNi15d2J0dHpUZ2lJV2c';
    // Init Zend Gdata service

    $service =  ZendGData\Spreadsheets::AUTH_SERVICE_NAME;
    $client =  ZendGData\ClientLogin::getHttpClient($user, $pass, $service);
    $spreadSheetService = new ZendGData\Spreadsheets($client);

    $query = new ZendGData\Spreadsheets\DocumentQuery();
    $query->setSpreadsheetKey($fileId);
/*    echo '>>> '. $query->getWorksheetId();
    echo '<pre>';
    print_r( $query );
    echo '</pre>';*/

    $feed = $spreadSheetService->getWorksheetFeed($query);
    $entries = $feed->entries[0]->getContentsAsRows();

    echo '>>>>>' . $feed->entries[0]->id . "<br>";
    echo '>>>>>' . $feed->entries[0]->title->text;
    $wid =explode('/', $feed->entries[0]->id);
    $sheetId = array_pop($wid);

/*    echo '<pre>';
    print_r( $feed->entries );
    echo '</pre>';*/


/*    echo '<pre>';
    print_r( $entries );
    echo '</pre>';*/
//----------------------------------------------------------------------------------
// Example 2: Get column information

    $query = new ZendGData\Spreadsheets\CellQuery();
    $query->setSpreadsheetKey($fileId);
    $feed = $spreadSheetService->getCellFeed($query);
    $columnCount = $feed->getColumnCount()->getText();
    $columns = array();
    for ($i = 0; $i < $columnCount; $i++) {
        $columnName = $feed->entries[$i]->getCell()->getText();
        $columns[$i] = strtolower(str_replace(' ', '', $columnName));
    }
    echo "<hr><h3>Example 2: Get column information</h3>";
    echo "Nr of columns: $columnCount";
    echo "<br>Columns: ";
    echo var_export($columns, true);

    echo '<pre>';
    print_r( $columns );
    echo '</pre>';

//-------------------------------------------------------------------------------------------------
// Example 3: Add cell data

/*    $query = new ZendGData\Spreadsheets\ListQuery();
    $query->setSpreadsheetKey($fileId);
    $query->setWorksheetId($wid);
    $query->setSpreadsheetQuery('name=John and age>25');
    $listFeed = $spreadSheetService->getListFeed($query);*/

    $testData = array();
    foreach ($columns as $col) {
        $testData[$col] = "Dynamically added " . date("Y-m-d H:i:s") . " in column " . $col;
    }

    echo '<pre>';
    print_r( $testData );
    echo '</pre>';
    $startTime = microtime(true);
/*    $ret = $spreadSheetService->insertRow($testData, $fileId, $sheetId);
    $spreadSheetService->insertRow($testData, $fileId, $sheetId);
    $spreadSheetService->insertRow($testData, $fileId, $sheetId);
    $spreadSheetService->insertRow($testData, $fileId, $sheetId);*/

    require_once("models/Spreadsheet.php");
    $user = "prof.lebrun@gmail.com";
    $pass = "ocool007_";

    // connect to the spreadsheet
    $doc = new Spreadsheet();
    $doc->authenticate($user, $pass);
    $doc->setSpreadsheetId($fileId);
    $doc->setWorksheetId($sheetId);
    $doc->setWorksheetId('od5');
    //$rsp = $doc->addRow($testData);
    $rsp = $doc->addColumn(array('stef1','lef','test'));
    print_r($rsp);

    echo 'Timer: '.(microtime(true) - $startTime);
    //$up = $spreadSheetService->updateCell(1,1,"test",$fileId);
   // echo var_export($ret, true);

});

// COPY DRIVE FILE
$app->get("/copy", function () use ($app, $di) {
    require_once("models/Spreadsheet.php");
    require_once 'vendor/google-api-php-client/src/Google_Client.php';
    require_once "vendor/google-api-php-client/src/contrib/Google_DriveService.php";
    require_once "vendor/google-api-php-client/src/contrib/Google_Oauth2Service.php";

    $DRIVE_SCOPE = 'https://www.googleapis.com/auth/drive';
    $SERVICE_ACCOUNT_EMAIL = '509163819657@developer.gserviceaccount.com';
    $SERVICE_ACCOUNT_PKCS12_FILE_PATH = '/Users/prof_lebrun/Downloads/a56f4e11d50f76ede6c83f4c0fad55ea1741b20b-privatekey.p12';

    // GOOGLE DRIVE SERVICE
    $key = file_get_contents($SERVICE_ACCOUNT_PKCS12_FILE_PATH);
    $auth = new Google_AssertionCredentials(
        $SERVICE_ACCOUNT_EMAIL,
        array($DRIVE_SCOPE),
        $key);
    $client = new Google_Client();
    $client->setUseObjects(true);
    $client->setAssertionCredentials($auth);
    $GD_Service = new Google_DriveService($client);

    // NEW FILE METADATA
    $mimeType = 'text/csv';
    $driveFile = new Google_DriveFile();
    $driveFile->setTitle('**COPY** CQ ATLAS -- CLONE OF MASTER FILE');
    $driveFile->setDescription('Ma Description ... test');
    $driveFile->setMimeType($mimeType);
    $driveFile->setOwnerNames(array('prof.lebrun@gmail.com'));
    $driveFile->setParents(array(
        array(
            'kind'=> 'drive#fileLink',
            'id'=>$di['google_drive_folder_id']
        )
    ));

    // COPY FILE
    try {
       $clone = $GD_Service->files->copy($di['google_drive_master_file_id'], $driveFile);
       return $clone->getId();

    } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
        return false;
    }
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

$app->get("/about", function () use ($app) {
    $app->render('about.php');
});

$app->get("/level/contact", function () use ($app) {
    $app->render('levelContact.php');
});

// LOGOUT
$app->get("/logout", function () use ($app) {
    unset($_SESSION['user']);
    $app->view()->setData('user', null);
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

    if (count($userInfos) == 0) {
        $errors['email'] = "Courriel introuvable";
    } else if ( sha1($password.$userInfos[0]['salt']) != $userInfos[0]['password']) {
        $app->flash('email', $email);
        $errors['password'] = "Le mot de passe ne correspond pas";
    }

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
});

//$app->post("/publish", $apiAuthenticate($app), function () use ($app, $di) {
$app->post("/publish1",  function () use ($app, $di) {
    // REQUEST
    $reqBody = json_decode( $app->request()->getBody(), true );
    $meta = $reqBody['metadata'];
    $data = $reqBody['data'];
    $headers = $reqBody['headers'];
    //$csvFile = new \Ddeboer\DataImport\Writer\CsvWriter( $di['uploadDir'].'/'.$meta['fileName'].'.csv', 'w', ',' );

/*    print_r($reqBody);
    exit;*/

    // COPY MASTER_FILE_CQ
    //require_once("models/Spreadsheet.php");
    require_once 'vendor/google-api-php-client/src/Google_Client.php';
    require_once "vendor/google-api-php-client/src/contrib/Google_DriveService.php";
    require_once "vendor/google-api-php-client/src/contrib/Google_Oauth2Service.php";

    $DRIVE_SCOPE = 'https://www.googleapis.com/auth/drive';
    $SERVICE_ACCOUNT_EMAIL = '509163819657@developer.gserviceaccount.com';
    $SERVICE_ACCOUNT_PKCS12_FILE_PATH = '/Users/prof_lebrun/Downloads/a56f4e11d50f76ede6c83f4c0fad55ea1741b20b-privatekey.p12';

    // GOOGLE DRIVE SERVICE
    $key = file_get_contents($SERVICE_ACCOUNT_PKCS12_FILE_PATH);
    $auth = new Google_AssertionCredentials(
        $SERVICE_ACCOUNT_EMAIL,
        array($DRIVE_SCOPE),
        $key);
    $client = new Google_Client();
    $client->setUseObjects(true);
    $client->setAssertionCredentials($auth);
    $GD_Service = new Google_DriveService($client);

    // NEW FILE METADATA
    $mimeType = 'text/csv';
    $driveFile = new Google_DriveFile();
    $driveFile->setTitle($meta['name']);
    $driveFile->setDescription($meta['name']);
    $driveFile->setMimeType($mimeType);
    $driveFile->setOwnerNames(array('prof.lebrun@gmail.com'));
    $driveFile->setParents(array(
        array(
            'kind'=> 'drive#fileLink',
            'id'=>$di['google_drive_folder_id']
        )
    ));

    // COPY FILE
    try {
        $clone = $GD_Service->files->copy($di['google_drive_master_file_id'], $driveFile);
        $cloneId = $clone->getId();

    } catch (Exception $e) {

        $res = $app->response();
        $res['Content-Type'] = 'application/json'; // iFrame fix
        $response = array(
            "status" => "400",
            "msg" => "An error occurred: " . $e->getMessage()
        );
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    // CREATE META WORKSHEET
    require_once("models/Spreadsheet.php");

    // connect to the spreadsheet
    $doc = new Spreadsheet();
    $doc->authenticate($di['google_user'], $di['google_password']);
    $doc->setSpreadsheetId($cloneId);

    $worksheet = $doc->addSheet('Meta',count($meta)+1,2);
    $worksheetFullId =explode('/', $worksheet['response']->id);
    $worksheetId = array_pop($worksheetFullId);
    $doc->setWorksheetId( substr($worksheetId, 0,3) );

    // ADD META ROWS
    $metaPrepared = array();
    $metaPrepared[] = array('Description' => 'Description', 'Valeur' =>'Valeur');
    foreach ($meta as $row=>$value) {
        if($row === 'fileCreated'){
            $timestamp = strtotime($value);
            $value = date('c');
        }
        $metaPrepared[] = array(
            'Description' => $row,
            'Valeur' => $value
        );
    }

    $results = $doc->addCellRows($metaPrepared, '1');

    $worksheet = $doc->addSheet('Data',count($data)+1,count($headers));
    $worksheetFullId =explode('/', $worksheet['response']->id);
    $worksheetId = array_pop($worksheetFullId);
    $doc->setWorksheetId( substr($worksheetId, 0,3) );

    // ADD META ROWS
    $dataPrepared = array();
    $headersPrepared= array();
    $headersRow= array();
    foreach ($headers as $header) {
        $headersPrepared[]=$header['title'];
        $headersRow[$header['title']] = $header['title'];
    }
  /*  for($i=0;$i<count($headersPrepared);$i++){
        $rowData[$headersPrepared[$i]] = $row[$i];
    }*/

    $dataPrepared[] = $headersRow;
    foreach ($data as $row) {
        $rowData = array();
        for($i=0;$i<count($headersPrepared);$i++){
            $rowData[$headersPrepared[$i]] = $row[$i];
        }
        $dataPrepared[] = $rowData;
    }
//print_r($dataPrepared);

    $results_2 = $doc->addCellRows($dataPrepared, '2');

  //  print_r($results_2);
    //$results = $doc->addBatchCellCols($colsPrepared);

    $worksheet = $doc->addSheet('Champs',count($headers),3);
    $worksheetFullId =explode('/', $worksheet['response']->id);
    $worksheetId = array_pop($worksheetFullId);
    $doc->setWorksheetId( substr($worksheetId, 0,3) );
    $results_3 = $doc->addCellRows($headers, '3');

    print_r($results_3);

    // RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'text/html'; // iFrame fix
    $response = array(
        "status" => "",
        "msg" => ""
    );

    //print_r($reqBody);

    $response['status'] = 200;
    $response['msg'] = 'ok';
    //$response['culr_rep'] = $results['response'];
    //$response['culr_status'] = $results['status'];
    $response['data'] = $reqBody['data'];
    //$response['meta'] = $reqBody['metadata'];
    $res->status(200);
    echo json_encode($response);
    return false;
});

$app->post("/publish",  function () use ($app, $di) {
    // REQUEST
    $reqBody = json_decode( $app->request()->getBody(), true );
    $meta = $reqBody['metadata'];
    $data = $reqBody['data'];
    $headers = $reqBody['headers'];

    $objPHPExcel = new \PHPExcel();
    $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Meta');
    $objPHPExcel->addSheet($myWorkSheet, 0);


    // FILE PROPERTIES
    $objPHPExcel->getProperties()->setCreator("CQ ROBOT");
    $objPHPExcel->getProperties()->setLastModifiedBy("CQ ROBOT");
    $objPHPExcel->getProperties()->setTitle($meta['name']);
    $objPHPExcel->getProperties()->setSubject("Created by CQ");
    $objPHPExcel->getProperties()->setDescription($meta['description']);
    $objPHPExcel->getProperties()->setKeywords("");
    $objPHPExcel->getProperties()->setCategory("");

    // METADATA WORKSHEET
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Description');
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Valeur');

    $rowCount =2;
    foreach ($meta as $desc=>$val) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $desc);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $val);
        $rowCount++;
    }

    // DATA WORKSHEET
    $myDataWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Data');
    $objPHPExcel->addSheet($myDataWorkSheet, 1);
    $objPHPExcel->setActiveSheetIndex(1);

    // SET DATA HEADERS
    $colCount = 0;
    foreach ($headers as $header) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($colCount, 1, $header['title']);
        $colCount++;
    }

    // ADDING DATA
    for($r=0;$r<count($data);$r++){
        for($c=0;$c<count($data[$r]);$c++){
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $r+2, $data[$r][$c]);
        }
    }

    //WRITE TO FILE
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($di['uploadDir'].'/05featuredemo.xlsx');

    // UPLOAD TO GGDRIVE
    require_once 'vendor/google-api-php-client/src/Google_Client.php';
    require_once "vendor/google-api-php-client/src/contrib/Google_DriveService.php";
    require_once "vendor/google-api-php-client/src/contrib/Google_Oauth2Service.php";

    $DRIVE_SCOPE = 'https://www.googleapis.com/auth/drive';
    $SERVICE_ACCOUNT_EMAIL = '509163819657@developer.gserviceaccount.com';
    $SERVICE_ACCOUNT_PKCS12_FILE_PATH = '/Users/prof_lebrun/Downloads/a56f4e11d50f76ede6c83f4c0fad55ea1741b20b-privatekey.p12';

    $key = file_get_contents($SERVICE_ACCOUNT_PKCS12_FILE_PATH);
    $auth = new Google_AssertionCredentials(
        $SERVICE_ACCOUNT_EMAIL,
        array($DRIVE_SCOPE),
        $key);

    $client = new Google_Client();
    $client->setUseObjects(true);
    $client->setAssertionCredentials($auth);

    $GD_Service = new Google_DriveService($client);
    //File
    //$mimeType = 'text/csv';
    $mimeType = 'application/vnd.ms-excel';

    $file = new Google_DriveFile();
    $file->setTitle('CQ ATLAS -- UPLOAD EXCEL');
    $file->setDescription('Ma Description ... test');
    $file->setMimeType($mimeType);
    $file->setOwnerNames(array('prof.lebrun@gmail.com'));
    $file->setParents(array(
        array(
            'kind'=> 'drive#fileLink',
          //  'id'=>'0B7Dvk7BRZ_yjT2hyd3JFOHoxWXM' // CQATLAS
            'id'=>'0B7Dvk7BRZ_yjdnZXNDBwR2xRbHM' // UPLOADED
        )
    ));

    $createdFile = $GD_Service->files->insert($file, array(
        'data' => file_get_contents($di['uploadDir'].'/05featuredemo.xlsx'),
        'mimeType' => $mimeType,
        'convert' => true
    ));

    require_once("models/Spreadsheet.php");
    $doc = new Spreadsheet();
    $doc->authenticate($di['google_user'], $di['google_password']);

    $url ='https://script.google.com/macros/s/AKfycbzbFJq0hvgkQt6EYWJvgxS0hcnMNxbXGnee1crmCk0pxFhY2OKn/exec';
    $headers = array(
        "Content-Type: application/json",
        "Authorization: GoogleLogin auth=" . $doc->getToken()
    );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url . '?id='.$createdFile->getId());
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_POST, false);
    //curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
    $curlresponse = curl_exec($curl);
    $curl_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    //$xml = simplexml_load_string($response);

    print_r($curlresponse);


    // RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'text/html'; // iFrame fix
    $response = array(
        "status" => "",
        "msg" => ""
    );


    $response['status'] = 200;
    $response['msg'] = 'ok';
    $res->status(200);
    echo json_encode($response);
    return false;
});


$app->get("/upload", $authenticate($app), function () use ($app, $di) {
    $app->render('uploadTmpl.php');
});

$app->post("/upload", $apiAuthenticate($app), function () use ($app, $di) {

    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';
    $storage = new \Upload\Storage\FileSystem($di['uploadDir']);
    $file = new \Upload\File('file_upload', $storage);

    // Validate file upload
    $file->addValidations(array(
        new \Upload\Validation\Mimetype(array(
            'text/csv',
            'text/plain',
            'application/vnd.ms-excel',
            'application/msexcel',
            'application/x-msexcel',
            'application/x-ms-excel',
            'application/x-excel',
            'application/x-dos_ms_excel',
            'application/xls',
            'application/x-xls',
            'application/msword',
            'composite document file v2 document'
        )),
        new \Upload\Validation\Size('5M')
    ));

    $errors = array();
    $metas = array();
    $date = new DateTime();

    // RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'text/html'; // iFrame fix
    $response = array(
        "status" => "",
        "msg" => ""
    );

    // Try to upload file
    try {
        $slugName = CqUtil::slugify($file->getName());
        $file->setName( $slugName.'_'.$date->getTimestamp() );
        $fileMime = $file->getMimetype();
        $fileSize = ($file->getSize()/1000);
        $file->upload();

    } catch (\Exception $e) {
        // === UPLOAD FAIL ===
        $errors = $file->getErrors();
        if(empty($errors)){
            $errors[] = $e->getMessage();
        }

        $response['status'] = 400;
        $response['msg'] = $errors[0] . ' ['.$file->getMimetype().']';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    // CHECK FILE FORMAT TO SELECT THE READER
    $fileExtension = $file->getExtension();

    // DELIMITED TEXT (CSV, TXT)
    if( in_array($fileExtension, $di['delimitedExtensions']) ){

        // SELECT DELIMITER
        $req = $app->request();
        $delimiter = $req->post('optionsDelimiter');
        if ($delimiter === null){
            $delimiter = ',';
        }

        // READ FILE
        $source = new \Ddeboer\DataImport\Source\Stream( $di['uploadDir'].'/'.$file->getName().'.'.$fileExtension );
        $source->addFilter(new \Ddeboer\DataImport\Source\Filter\NormalizeLineBreaks());
        $delimitedFile = $source->getFile();
        $reader = new \Ddeboer\DataImport\Reader\CsvReader($delimitedFile, $delimiter);
        $reader->setHeaderRowNumber(0);

        $fileData = array();
        $fileData['header'] = $reader->getColumnHeaders();
        $fileData['rows'] = array();

        $count = 0;
        foreach($reader as $item){
            $fileData['rows'][] = $item;
            $count ++;
        }

        $fileData['count'] = $count;

    }elseif( in_array($fileExtension, $di['excelExtensions']) ){

        $source = new \Ddeboer\DataImport\Source\Stream( $di['uploadDir'].'/'.$file->getName().'.'.$fileExtension );
        $xlsFile = $source->getFile();
        $xlsReader = new \Ddeboer\DataImport\Reader\ExcelReader($xlsFile);
        $xlsReader->setHeaderRowNumber(0);

        $fileData = array();
        $fileData['header'] = $xlsReader->getColumnHeaders();
        $fileData['rows'] = array();

        $count = 0;
        foreach($xlsReader as $item){
            $fileData['rows'][] = $item;
            $count ++;
        }

        $fileData['count'] = $count;
    }else{
        $response['status'] = 400;
        $response['msg'] = 'Format non compatible';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    // CHECK FOR ROWS
    if($fileData['count']<2){
        $response['status'] = 400;
        $response['msg'] = 'Un document avec un minimum de 2 lignes est requis';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    // Sub Set for Partial rendering
    $subsetRows = $fileData['count'];
    $quickViewFlatData = array();

    for($i=0;$i< $subsetRows;$i++){
        $quickViewFlatData[] = array_values($fileData['rows'][$i]);
    }

    $fileData['metaFields'] = array();
    $fieldsCount = count($fileData['header']);
    for($i=0;$i<$fieldsCount;$i++){
        // CHECK FOR TYPE
        $fieldData = $fileData['rows'][1][ $fileData['header'][$i] ];
        $type = gettype($fieldData);
        // IF STRING > CHECK FOR DATE
        if($type === 'string'){
            $type = (($timestamp = strtotime($fieldData)) && (strlen($fieldData)>7))? 'date' : $type;
        }

        $fileData['metaFields'][] = array(
            'title' => $fileData['header'][$i],
            'type' => $type,
            'desc' => ''
        );
    }

    //Spatial fields / GEOCODING
    require 'models/geo.php';
    $fileData['geoFields'] = array(
        'lonField' => '',
        'latField' => '',
        'locField' => '',
        'geocoded' => 1
    );

    try{
        $lonHeader = CqUtil::matchKeys($fileData['header'], array('lon','lng','longitude'));
        $latHeader = CqUtil::matchKeys($fileData['header'], array('lat','latitude'));
        $locationHeader = CqUtil::matchKeys($fileData['header'], array('adresse','address','addr','location','localisation'));
        if( $lonHeader === false || $latHeader === false){
            // No Lng/Lat >> address geocoding
            if( $locationHeader === false ){
                //KAPUT NO GEOCODING FIELDS
                throw new Exception('Aucune entête spatiale (lon,lat ou adresse)');
            }

            $fileData['geoFields']['locField'] = $locationHeader;
            $fileData['geoFields']['geocoded'] = 0;
            // Need Geocoding !!
            // DO IT CLIENT SIDE ... EASIER ON QUOTA
            // ========================================
            if($di['serverSideGeocoding']){

                $geoResult = array();
                $adapter  = new \Geocoder\HttpAdapter\GuzzleHttpAdapter();
                $geocoder = new \Geocoder\Geocoder();
                $geocoder->registerProviders(array(
                    new \Geocoder\Provider\GoogleMapsProvider(
                        $adapter,
                        'AIzaSyBMrGmwQpwqRgO8LtMwf4J1S-GJThYpBLQ'
                    )//,
                    /*
                    new \Geocoder\Provider\CloudMadeProvider(
                        $adapter,
                        '3921666a8ed240d59478def473a7b18a'
                    ),
                    new \Geocoder\Provider\BingMapsProvider(
                        $adapter,
                        'Aj5-RoGzUR4-jai4Z0BSA8cNfPZUCS1jY3f0h-ZMxqagmu4umiXGq5UF78l8b_Ct'
                    )
                    */
                ));

                $count = 0;
                set_time_limit(300);
                $time_start = microtime(true);
                foreach( $fileData['rows'] as $row ){
                    $address = $row[$locationHeader];

                    foreach (array_keys($geocoder->getProviders()) as $provider) {
                        try{
                            $time_start_request = microtime(true);
                            $geocoder->using($provider);
                            $geoResult[$count][$provider] = $geocoder->geocode($address)->toArray();
                            $geoResult[$count][$provider]['timer'] = microtime(true) - $time_start_request;
                        }catch(Exception $e){
                            $geoResult[$count][$provider] = array();
                        }
                    }
                    $count ++;
                    usleep(300000);
                }

                $geoResult['timer'] = microtime(true) - $time_start;
                $fileData['geoFields']['geocoded'] = 1;
                $fileData['geoFields']['geocoding'] = $geoResult;
            }
            /// ========================================
        }else{
            // Lng/Lat Ok >> Check if data is ok
            $llErrors = 0;
            $llValids = 0;
            $llErrorsObjs = array();

            foreach($fileData['rows'] as $row){
                if(Geo::geo_utils_is_valid_longitude($row[$lonHeader]) && Geo::geo_utils_is_valid_latitude($row[$latHeader])){
                    $llValids ++;
                }else{
                    $llErrors ++;
                    $llErrorsObjs[] = '('.$row[$lonHeader].','.$row[$latHeader].')';
                }
            }
            if( $llErrors > 0 ){
                throw new \Exception('Certaines coordonnées (lon,lat) sont invalides. '.$llErrors.' erreurs.{'. implode(',',$llErrorsObjs).'}');
            }

            $fileData['geoFields']['lonField'] = $lonHeader;
            $fileData['geoFields']['latField'] = $latHeader;
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
        'fileMime'=> $fileMime,
        'fileExtension'=> $file->getExtension(),
        'fileSize'=> $fileSize.' kb',
        'fileHash'=> md5_file($di['uploadDir'].'/'.$file->getName().'.'.$file->getExtension()),
        'fileId' => sha1($file->getName() . '_' . $_SESSION['user']),
        'fileCreated' => time()
    );

    $metadata = array_merge($metadata,$fileData['geoFields']);

    $data = array(
        'data' => $quickViewFlatData,
        'headers' =>$fileData['metaFields'],
        'metadata' => $metadata
    );

    $response['status'] = 200;
    $response['msg'] = 'ok';

    $output = array_merge($response,$data);
    $res->status(200);
    echo json_encode($output);
});

$app->get("/private/about", $authenticate($app), function () use ($app) {
    $app->render('privateAbout.php');
});

$app->get("/private/goodstuff", $authenticate($app), function () use ($app) {
    $app->render('privateGoodStuff.php');
});

$app->get("/dd", function () use ($app, $di) {

    $source = new \Ddeboer\DataImport\Source\Stream($di['uploadDir'].'/cq-test-bad-lon-noadress_1360275406.csv');
    $source->addFilter(new \Ddeboer\DataImport\Source\Filter\NormalizeLineBreaks());
    $ddFile = $source->getFile();
    $reader = new \Ddeboer\DataImport\Reader\CsvReader($ddFile);
    $reader->setHeaderRowNumber(0);

    print_r( $reader->getColumnHeaders() );
    $count = 0;
    foreach($reader as $item){
        echo '<pre>';
        print_r($item);
        echo '</pre>';
        echo $count;
        $count ++;
    }

/*    $source_2 = new \Ddeboer\DataImport\Source\Stream('/Applications/MAMP/htdocs/lmtl/www/collab_api/storage/uploads/worsthousing.xls');
    $xlsFile = $source_2->getFile();
    $xlsReader = new \Ddeboer\DataImport\Reader\ExcelReader($xlsFile);
    $xlsReader->setHeaderRowNumber(0);

    print_r( $xlsReader->getColumnHeaders() );

    $count = 0;
    foreach($xlsReader as $item){
        echo '<pre>';
        print_r($item);
        echo '</pre>';
        echo $count;
        $count ++;
    }*/

});

$app->get("/buildTaxonomy", function () use ($app, $di) {

    $jsonContent = file_get_contents($di['storageDir'].'/database/factual_taxonomy.json');
    $jsonContentUTF8 = mb_convert_encoding($jsonContent, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
    $taxonomy = json_decode($jsonContentUTF8, true);

    $sqlStatements = array();
    $insertText = "INSERT INTO categories (id, parent_id, en, fr) VALUES (";
    $count = 1;
    foreach ($taxonomy as $item) {
        $idParent = (count($item['parents']) > 0)?$item['parents'][0]:0;
        // ESCAPE ' > ''
        $en = str_replace("'","''", $item['labels']['en']);
        $fr = str_replace("'","''", $item['labels']['fr']);
        $sqlStatements[] =  $insertText . $count.",".$idParent.",'".$en."','".$fr."');";
        $count ++;
    }

    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    $fields = array(
        'q' => implode('',$sqlStatements),
        'api_key' => $di['cartodb_api_key']
    );

    $url = 'http://'.$di['cartodb_subdomain'].$di['cartodb_endpoint'];
    $curlResult = CqUtil::curlPost($url, json_encode($fields));

    echo '<pre><code>';
    print_r($curlResult);
    echo '</code></pre>';
});

$app->get("/cartodb", function () use ($app, $di) {

    require_once 'vendor/cartodb/cartodb.class.php';
    require_once 'vendor/cartodb/cartodb.config.php';

    $config = getConfig();
    $cartodb =  new CartoDBClient($config);

    #Check if the $key and $secret work fine and you are authorized
    if (!$cartodb->authorized) {
        error_log("uauth");
        print 'There is a problem authenticating, check the key and secret.';
        exit();
    }

    #Now we can perform queries straigh away. The second param indicates if you want
    #the result to be json_decode (true) or just return the raw json string

    $schema = array(
        'col1' => 'text',
        'col2' => 'integer'
    );

/*
      id serial NOT NULL ,
  user_id integer NOT NULL ,
  collection_id integer NOT NULL ,
  name character varying(255) NOT NULL ,
  description text NOT NULL ,
  label varying (255) NOT NULL ,
  privacy smallint,
  mime_type varying(64) NOT NULL,
  -- points hstore,
  tmpl hstore,
  -- points_json text,
  tmpl_json text,
  bbox_26918 geography(POLYGON,26918),
  bbox_4326 geography(POLYGON,4326),
  bbox_3857 geography(POLYGON,3857),
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  downloaded_count integer DEFAULT 0,
  features_count integer DEFAULT 0,
  status smallint,*/
    $datasetsSchema = array(
        'id' => 'serial',
        'user_id' => 'integer',
        'collection_id' => 'integer',
        'name' => 'character varying(255)',
        'slug' => 'character varying(255)',
        'description' => 'text',
        'label' => 'character varying(255)',
        'privacy' => 'integer',
        'mime_type' => 'character varying(64)',
        'bbox_26918' => 'geography(POLYGON,26918)',
        'bbox_4326' => 'geography(POLYGON,4326)',
        'bbox_3857' => 'geography(POLYGON,3857)',
        'downloaded_count' => 'integer DEFAULT 0',
        'features_count' => 'integer DEFAULT 0',
        'file_uri' => 'character varying(255)',
        'cat_01' => 'integer',
        'cat_02' => 'integer',
        'cat_03' => 'integer',
        'status' => 'integer',
        'version' => 'integer',
        'licence' => 'integer',
        'attributions' => 'text'
    );

    $categoriesSchema = array(
        'id' => 'serial',
        'parent_id' => 'integer',
        'name_fr' => 'character varying(255)',
        'desc_fr' => 'character varying(255)',
        'name_en' => 'character varying(255)',
        'desc_en' => 'character varying(255)',
        'icon_prefix' => 'character varying(255)',
        'icon_suffix' => 'character varying(4)'
    );

/*    $sql = "SELECT count(*) FROM test_2";
    $response = $cartodb->runSql($sql);
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre> ===========';*/

    $test = array(
        'field_1' => 'test l\'école',
        'field_2' => 463
    );


/*    $json =   json_encode($test, JSON_UNESCAPED_UNICODE);
    echo 'JSON:'.$json . '<br>';

    $insertSql = 'INSERT INTO test_2 (tags) VALUES';
    $insertSql .= '($$'.$json.'$$);';

    try{
        $response = $cartodb->runSql($insertSql);
    }catch (Exception $e){
        echo '<pre><code>';
        print_r($e);
        echo '</code></pre>';
    }*/

/*    $alterTableSql = 'ALTER TABLE places ADD COLUMN cat_01 INTEGER;ALTER TABLE places ADD COLUMN cat_02 INTEGER;ALTER TABLE places ADD COLUMN cat_03 INTEGER;';

    try{
        $response = $cartodb->runSql($alterTableSql);
    }catch (Exception $e){
        echo '<pre><code>';
        print_r($e);
        echo '</code></pre>';
    }*/

/*    echo '<pre><code>';
    print_r($response);
    echo '</code></pre> ===========';*/

/*    $json = utf8_decode( json_decode($test, JSON_UNESCAPED_UNICODE) );
    echo 'JSON:'.$json . '<br>';

    $insertSql = 'INSERT INTO test_2 (description, dataset_id, the_geom, point_4326, tags) VALUES';
    $insertSql .= '(\'this is a string\', 13, ST_SetSRID(ST_Point(-110, 43),4326),ST_SetSRID(ST_Point(-110, 43),4326),\''.$json.'\');';
    $insertSql .= 'INSERT INTO test_2 (description, dataset_id, the_geom, point_4326) VALUES';
    $insertSql .= '(\'this is a string\', 14, ST_SetSRID(ST_Point(-110, 43),4326),ST_SetSRID(ST_Point(-110, 43),4326));';

        try{
            $response = $cartodb->runSql($insertSql);
        }catch (Exception $e){
            echo '<pre><code>';
            print_r($e);
            echo '</code></pre>';
        }

    echo '<pre><code>';
    print_r($response);
    echo '</code></pre> ===========';*/
/*
    $sql = "SELECT count(*) FROM test_2";
    $response = $cartodb->runSql($sql);
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';*/


    try{
        $response = $cartodb->createTable('categories', $categoriesSchema);
    }catch (Exception $e){
        echo '<pre><code>';
        print_r($e);
        echo '</code></pre>';
    }
/*    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';*/
/*
    $response = $cartodb->addColumn('example', 'col3', 'text');
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';*/



/*
    $response = $cartodb->dropColumn('example', 'col2');
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';

    $data = array(
        'col1' => "'row1 - col1'",
        'col3' => "'row1 - col3'",
    );
    $response = $cartodb->insertRow('example', $data);
    $row = array_pop($response['return']['rows']);
    print_r($row);

    $data['col1'] = "'row1 - col1 new'";
    $data['col3'] = "'row1 - col3 new'";
    $response = $cartodb->updateRow('example', $row->id, $data);
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';*/

/*

    $response = $cartodb->getRow('example', 1);
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';

    $response = $cartodb->deleteRow('example', 1);
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';

    $response = $cartodb->getRecords('example', array('rows_per_page' => 0));
    $total_rows = $response['return']['total_rows'];
    $response = $cartodb->getRecords('example', array('rows_per_page' => $total_rows));
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';*/

/*
    $response = $cartodb->dropTable('example');
    echo '<pre><code>';
    print_r($response);
    echo '</code></pre>';*/
});

$app->get("/cartodb_v2", function () use ($app, $di) {
    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    $sql = "SELECT count(*) FROM places;SELECT count(*) FROM categories;";
    $fields = array(
        'q' => $sql,
        'api_key' => $di['cartodb_api_key']
    );

    $url = 'http://'.$di['cartodb_subdomain'].$di['cartodb_endpoint'];

    $curlResult = CqUtil::curlPost($url, json_encode($fields));
    echo '<pre><code>';
    print_r($curlResult);
    echo '</code></pre>';
});

$app->run();