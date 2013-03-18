<?php
// # LMTL User Interface #
// PHP backend supporting the API.

// ## Namespaces
//
// **Slim Framework** & middleware libraries as a base,
// **Aura.SQL** as an adapter for SQL database access,
// **Pimple** to manage Dependency injection,
// **Bcrypt** to help with salting and hash generation &
// **Guzzle** as a HTTP Client
use \Slim\Slim;
use Slim\Middleware\SessionCookie;
use \Aura\Sql\ConnectionFactory;
use \Pimple;
use \Bcrypt;
use Guzzle\Http\Client;

require 'vendor/autoload.php';
require 'models/lmtl.php';
// ***

// ### Main objects instantialization
$di = new Pimple();
$app = new Slim(array(
    'mode' => 'development' // selected mode, set with configureMode a/b
));

// **a)** Production mode config
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'log.level' => 4,
        'debug' => false
    ));
});

// **b)** Development mode config
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});
// Hard to guest Cookie salt
$app->add(new SessionCookie(array('secret' => 'nS#ul&1X4R7AOeIp+Q4T%nMX"4CS6~!')));
// Exception handling
$app->error(function (\Exception $e) use ($app) {
    $log = $app->getLog();
    /*    $log->error(array(
            'label' => 'ERROR',
            'message' => $e
        ));
    */
    echo 'UNE ERREUR >> ' . $e;
    $log->error($e);
});
// ***

// ### Dependency Injection
// Movable config & objects ...

// AURA.SQL Factory
$di['db'] = $di->share(function (){
    $connectionFactory = new ConnectionFactory;
    $connection = $connectionFactory->newInstance(
        'sqlite',
        './storage/database/app2.sqlite'
    );
    $connection->connect();
    return $connection;
});
// App attributes
$di['baseUrl'] = 'http://localhost/lmtl/www/collab_api/';
$di['uploadDir'] = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage/uploads';
$di['storageDir'] = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage';
// Ogre service attributes
$di['ogre_host'] = 'http://ogre.adc4gis.com';
$di['ogre_convert_endpoint'] = '/convert';
// Google services attributes
$di['google_client_id'] = '509163819657.apps.googleusercontent.com';
$di['google_client_secret'] = 'notasecret';
$di['google_user'] = 'prof.lebrun@gmail.com';
$di['google_password'] = 'ocool007_';
$di['google_drive_master_file_id'] = '0ArDvk7BRZ_yjdEI0S3R1Yks4NUxhaGlRTVlDTFNCd3c';
$di['google_drive_folder_id'] = '0B7Dvk7BRZ_yjT2hyd3JFOHoxWXM';
// CartoDB services attributes
$di['cartodb_api_key'] = '6947088ad96d8efc9ffe906f1312d1568f3eafc1';
$di['cartodb_subdomain'] = 'steflef';
$di['cartodb_endpoint'] = 'cartodb.com/api/v2/sql';
// Upload attributes
$di['delimitedExtensions'] = array('csv', 'txt');
$di['excelExtensions'] = array('xls', 'xlsx');
$di['serverSideGeocoding'] = false;
// ***

// ### Authentication

// Authenticate *route middleware*
$authenticate = function () use ($app, $di){
    return function () use ($app, $di) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Autorisation requise');
            $app->redirect($di['baseUrl'].'login');
        }
    };
};

// Authenticate *route middleware* for API
# RESPONSE via JSON
$apiAuthenticate = function () use ($app, $di){
    return function () use ($app, $di) {
        if (!isset($_SESSION['user'])) {
            $res = $app->response();
            $res->status(200);
          # >> iFrame FIX
            $res['Content-Type'] = 'text/html'; # $res['Content-Type'] = 'application/json';
            $response['status'] = 403;
            $response['msg'] = "Session expirée";
            echo json_encode($response);
            exit;
        }
    };
};

// Before dispatch hook to pass user infos to templates
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
// ***

// ###/ aka HomePage###
// ###Main Endpoint (GET)[**A**]###
// Show the dashboard
//$app->get("/", $authenticate($app), function () use ($app, $di) {
$app->get("/",  function () use ($app, $di) {
    $app->render('index_v1.php');
});
// ***

// ##API##
// ###/datasets###
// ###Datasets List (GET)###
// ###*JSON*###
$app->get("/datasets",  function () use ($app, $di) {
    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    $httpResponse = CqUtil::getDatasets($di);

    # RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    $res['Encoding'] = ' UTF-8';

    $response = array(
        "status" => "",
        "msg" => ""
    );

    if( $httpResponse['status'] !== 200 ){
        $response['status'] = 400;
        $response['msg'] = 'Error';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    $response['status'] = 200;
    $response['msg'] = 'ok';

    $data = json_decode( $httpResponse['response'], true);
    $output = array_merge($response,$data);

    $res->status(200);
    echo json_encode($output);
});
// ***

// ##API##
// ###/datasets/:id###
// ###Get a Dataset  (GET)###
// ###*JSON*###
$app->get("/datasets/:id",  function ($datasetId) use ($app, $di) {
    //echo "/datasets/:id ($id)";
    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    $httpResponse = CqUtil::getDataset($di, $datasetId);

    # RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    $res['Encoding'] = ' UTF-8';

    $response = array(
        "status" => "",
        "msg" => ""
    );

    if( $httpResponse['status'] !== 200 ){
        $response['status'] = 400;
        $response['msg'] = 'Error';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    $response['status'] = 200;
    $response['msg'] = 'ok';

    $data = json_decode( $httpResponse['response'], true);
    $output = array_merge($response,$data);

    $res->status(200);
    echo json_encode($output);
});
// ***

// ##API##
// ###/datasets/:id/places###
// ###Get a List of places for a Dataset  (GET)###
// ###*JSON*###
$app->get("/datasets/:id/places",  function ($datasetId) use ($app, $di) {

    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    $httpResponse = CqUtil::getDatasetPlaces($di, $datasetId);

    # RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    $res['Encoding'] = ' UTF-8';

    $response = array(
        "status" => "",
        "msg" => ""
    );

    if( $httpResponse['status'] !== 200 ){
        $response['status'] = 400;
        $response['msg'] = 'Error';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    $response['status'] = 200;
    $response['msg'] = 'ok';

    $data = json_decode( $httpResponse['response'], true);
    $output = array_merge($response,$data);

    $res->status(200);
    echo json_encode($output);
});
// ***

// ##API##
// ###/datasets/:datasetId/places/:placeId###
// ###Get a Place From a dataset (GET)###
// ###*JSON*###
$app->get("/datasets/:datasetId/places/:placeId",  function ($datasetId,$placeId) use ($app, $di) {

    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    $httpResponse = CqUtil::getDatasetPlaceId($di, $datasetId, $placeId);

    # RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    $res['Encoding'] = ' UTF-8';

    $response = array(
        "status" => "",
        "msg" => ""
    );

    if( $httpResponse['status'] !== 200 ){
        $response['status'] = 400;
        $response['msg'] = 'Error';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    $response['status'] = 200;
    $response['msg'] = 'ok';

    $data = json_decode( $httpResponse['response'], true);
    $output = array_merge($response,$data);

    $res->status(200);
    echo json_encode($output);
});
// ***

// ##API##
// ###/datasets/:datasetId/places###
// ###Add a Place to a dataset (PUT)[**A**]###
// ###*JSON*###
$app->put("/datasets/:datasetId/places",  function ($datasetId) use ($app, $di) {
    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    #1 validate fields
    $reqBody = json_decode( $app->request()->getBody(), true );
    $fields = array();
    $values = array();
    foreach ($reqBody as $field=>$value) {
        $field[]=$field;
        $values[]=$value;
    }

    $sql = 'INSERT INTO places ('.implode(',',$fields).') VALUES ('.implode(',',$values).');';
    $fields = array(
        'q' => $sql,
        'api_key' => $di['cartodb_api_key']
    );

    $url = 'http://'.$di['cartodb_subdomain'].$di['cartodb_endpoint'];

    $httpResponse = CqUtil::curlPost($url, json_encode($fields));

    # RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    $res['Encoding'] = ' UTF-8';

    $response = array(
        "status" => "",
        "msg" => ""
    );

    if( $httpResponse['status'] !== 200 ){
        $response['status'] = 400;
        $response['msg'] = 'Error';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    $response['status'] = 200;
    $response['msg'] = 'ok';

    $data = json_decode( $httpResponse['response'], true);
    $output = array_merge($response,$data);

    $res->status(200);
    echo json_encode($output);
});
// ***

// ##API##
// ###/places/:id###
// ####*shortcut: without a dataset*####
// ###Get a Place  (GET)###
// ###*JSON*###
$app->get("/places/:id",  function ($placeId) use ($app, $di) {

    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    $httpResponse = CqUtil::getPlace($di, $placeId);

    # RESPONSE
    $res = $app->response();
    $res['Content-Type'] = 'application/json';
    $res['Encoding'] = ' UTF-8';

    $response = array(
        "status" => "",
        "msg" => ""
    );

    if( $httpResponse['status'] !== 200 ){
        $response['status'] = 400;
        $response['msg'] = 'Error';
        $res->status(400);
        echo json_encode($response);
        return false;
    }

    $response['status'] = 200;
    $response['msg'] = 'ok';

    $data = json_decode( $httpResponse['response'], true);
    $output = array_merge($response,$data);

    $res->status(200);
    echo json_encode($output);
});
// ***

// ###/logout###
// ###Logout Endpoint (GET)###
// No Interface
$app->get("/logout", function () use ($app) {
    unset($_SESSION['user']);
    $app->view()->setData('user', null);
    $app->redirect('./');
});
// ***

// ###/login###
// ###Login Endpoint (GET)###
// Show login interface
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
        # 'error' => $error,
        'email_value' => $email_value,
        'email_error' => $email_error,
        'password_error' => $password_error,
        'urlRedirect' => $urlRedirect
    ));
});
// ***

// ###/login###
// ###Login Endpoint (POST)###
// Validate login.
// Then, show login interface or redirect to destination
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
// ***

// ###/admin/users###
// ###Users management Endpoint (GET)[**A**]###
// Show users page to admin
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
// ***

// ###/publish###
// ###Publish Dataset Endpoint (GET)###
// ###*JSON*###
// Validate & publish datasets
// ##>> TO REFACTOR <<##
$app->post("/publish",  function () use ($app, $di) {
    # REQUEST
    $reqBody = json_decode( $app->request()->getBody(), true );
    $meta = $reqBody['metadata'];
    $data = $reqBody['data'];
    $headers = $reqBody['headers'];

    // ####Create an Excel Document####
    $objPHPExcel = new \PHPExcel();
    $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Meta');
    $objPHPExcel->addSheet($myWorkSheet, 0);


    // #####>> Set File Properties#####
    $objPHPExcel->getProperties()->setCreator("CQ ROBOT");
    $objPHPExcel->getProperties()->setLastModifiedBy("CQ ROBOT");
    $objPHPExcel->getProperties()->setTitle($meta['name']);
    $objPHPExcel->getProperties()->setSubject("Created by CQ");
    $objPHPExcel->getProperties()->setDescription($meta['description']);
    $objPHPExcel->getProperties()->setKeywords("");
    $objPHPExcel->getProperties()->setCategory("");

    // #####>> Build Metadata Worksheet#####
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Description');
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Valeur');

    // #####>> Add Data to Metadata Worksheet#####
    $rowCount =2;
    foreach ($meta as $desc=>$val) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $desc);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $val);
        $rowCount++;
    }

    // #####>> Build Data Worksheet#####
    $myDataWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Data');
    $objPHPExcel->addSheet($myDataWorkSheet, 1);
    $objPHPExcel->setActiveSheetIndex(1);

    // #####>> Sets Haeders to Data Worksheet#####
    $colCount = 0;
    foreach ($headers as $header) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($colCount, 1, $header['title']);
        $colCount++;
    }

    // #####>> Add Data to Data Worksheet#####
    for($r=0;$r<count($data);$r++){
        for($c=0;$c<count($data[$r]);$c++){
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($c, $r+2, $data[$r][$c]);
        }
    }

    // #####>>Save File IO#####
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($di['uploadDir'].'/05featuredemo.xlsx');

    // ####Upload to Google Drive####
    // #####>> Instantiate Google Drive Service#####
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

    // #####>> Set Google Drive File Properties#####
    $mimeType = 'application/vnd.ms-excel';

    $file = new Google_DriveFile();
    $file->setTitle('CQ ATLAS -- UPLOAD EXCEL');
    $file->setDescription('Ma Description ... test');
    $file->setMimeType($mimeType);
    $file->setOwnerNames(array('prof.lebrun@gmail.com'));
    $file->setParents(array(
        array(
            'kind'=> 'drive#fileLink',
          #  'id'=>'0B7Dvk7BRZ_yjT2hyd3JFOHoxWXM' // CQATLAS
            'id'=>'0B7Dvk7BRZ_yjdnZXNDBwR2xRbHM' // UPLOADED
        )
    ));

    // #####>> Upload File to Google Drive#####
    $createdFile = $GD_Service->files->insert($file, array(
        'data' => file_get_contents($di['uploadDir'].'/05featuredemo.xlsx'),
        'mimeType' => $mimeType,
        'convert' => true
    ));

    // ####Google Drive Operation Via Server-Side AppScript####
    // #####>> Get Token From Spreadsheet API#####
    require_once("models/Spreadsheet.php");
    $doc = new Spreadsheet();
    $doc->authenticate($di['google_user'], $di['google_password']);

    // #####>> Trigger App Script (copy MasterFle etc.)#####
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
    $curlresponse = curl_exec($curl);
    $curl_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    # $xml = simplexml_load_string($response);

    print_r($curlresponse);


    // ####Publish JSON Response####
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
// ***

// ###/upload###
// ###Upload Endpoint (GET)###
// Show Upload Interface
$app->get("/upload", $authenticate($app), function () use ($app, $di) {
    $app->render('uploadTmpl.php');
});
// ***

// ###/upload###
// ###Upload Endpoint (POST)###
// Validate upload
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

    # RESPONSE
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
// ***

// ###/private/about###
// ###TEST ABOUT PRIVATE (GET)[**A**]###
// Show Private/About Interface
$app->get("/private/about", $authenticate($app), function () use ($app) {
    $app->render('privateAbout.php');
});
// ***

// ###/buildTaxonomy###
// ###Build Taxonomy Endpoint (GET)###
// Build Categories Taxonomy on CartoDB via a JSON file
// TEST.
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
// ***

// ###/cartodb###
// ###CartoDB Endpoint (GET)###
// Bunch of tests with cartoDB
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
// ***

// ###/cartodb_v2###
// ###CartoDB V2 API INTERFACE Endpoint (GET)###
// Test the SQL API V2 with CURL. Good Results.
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
// ***

// ###/pr_0###
$app->get("/montest", function () use ($app, $di) {
   // require_once 'vendor/cqatlas/cqatlas/CqUtil.php';
    echo 'OK1';
    $Reader = new \CQAtlas\Helpers\Reader('test');
    //echo 'OK2';
});
// ***

// ### START THE ENGINE! ###
$app->run();