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
//use \Bcrypt; # todo
//use Guzzle\Http\Client; # todo

require_once 'vendor/autoload.php';
require_once 'models/lmtl.php'; # todo Aura Helper
// ***

// ### Main objects instantialization
$di = new Pimple();
$app = new Slim(array(
    'mode' => 'development' // selected mode, set with configureMode a/b
));

// #### A | Production mode config
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'log.level' => 4,
        'debug' => false
    ));
});

// #### B | Development mode config
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

// ##### Add Custom Cache Middleware
# $app->add(new \CQAtlas\Middlewares\Cache());

// ##### Hard to guess Cookie Salt
$app->add(new SessionCookie(array('secret' => 'nS#ul&1X4R7AOeIp+Q4T%nMX"4CS6~!')));

// #### App Wide Exception Handling (Production Mode)
$app->error(function (\Exception $e) use ($app) {
    $log = $app->getLog();
    echo 'UNE ERREUR >> ' . $e;
    $log->error($e);
});
// ***

// ### Dependency Injection

// #### >> Load Config (External with GitIgnore)
require_once('cq_atlas/config/config.php');
// #### AURA.SQL Factory
$di['db'] = $di->share(function () use ($di){
    $connectionFactory = new ConnectionFactory;
    $connection = $connectionFactory->newInstance(
        $di['dbDriver'],
        $di['dbFile']
    );
    $connection->connect();
    return $connection;
});
// ***

// ### Routes Middlewares & Hooks

// #### Authenticate *Route middleware*
$authenticate = function () use ($app, $di){
    return function () use ($app, $di) {
        if (!isset($_SESSION['user'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Autorisation requise');
            $app->redirect($di['baseUrl'].'login');
        }
    };
};

// #### Authenticate *Route middleware* for API
// ##### RESPONSE via JSON
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
            $app->stop();
        }
    };
};

// #### Cache *Route middleware* for API Calls
$apiCache = function () use ($app, $di){
    return function () use ($app, $di) {
        $Cache = new \CQAtlas\Helpers\Cache($app, $di);
        $Cache->call();
    };
};

// #### before.dispatch Hook
// ##### Pass User Infos to Templates
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

// ### HomePage -  */*
// ### Main Endpoint (GET)[**A**]
// Show the dashboard
# $app->get("/", $authenticate($app), function () use ($app, $di) {
$app->get("/",  function () use ($app, $di) {
    $app->render('index_v1.php');
});
// ***

// ### API -  */datasets*
// ### Datasets List (GET)
// #### *JSON* - *STATIC CACHE*
$app->get("/datasets", $apiCache($app, $di), function () use ($app, $di) {

    $from = 'datasets';
    processRequest( $app, $di, $from );
});
// ***

// ### API -  */datasets/:id*
// ### Get a Dataset  (GET)
// #### *JSON* - *STATIC CACHE*
$app->get("/datasets/:id", $apiCache($app, $di), function ($datasetId) use ($app, $di) {

    $from = 'datasets';
    $query = 'WHERE dataset_id='.(Integer)$datasetId.' LIMIT 1';
    processRequest( $app, $di, $from, $query );
});
// ***

// ### API -  */datasets/:id/places*
// ### Get a List of places for a Dataset  (GET)
// #### *JSON* - *STATIC CACHE*
$app->get("/datasets/:id/places", $apiCache($app, $di), function ($datasetId) use ($app, $di) {

    $from = 'places';
    $query =  'WHERE dataset_id='.(Integer)$datasetId;
    processRequest( $app, $di, $from, $query );
});
// ***

// ### API -  */datasets/:id/places*
// ### Add a Place to a dataset (PUT)[**A**]
// ### *JSON*
$app->put("/datasets/:datasetId/places", function ($datasetId) use ($app, $di) {

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

    if( $httpResponse['status'] !== 200 ){
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,'Error');
        $Response->show();
        $app->stop();
    }

    $Response = new \CQAtlas\Helpers\Response($app->response());

    $data = json_decode( $httpResponse['response'], true);
    $output = array_merge($Response->toArray(),$data);
    echo json_encode($output);
});
// ***

// ### API -  */places/:id*
// ### Get a Place  (GET)
// #### *JSON* - *STATIC CACHE*
//$app->get("/places/:id", $apiCache($app, $di), function ($placeId) use ($app, $di) {
$app->get("/places/:id", $apiCache($app, $di), function ($placeId) use ($app, $di) {

    $from = 'places';
    $query =  'WHERE place_id=' .(Integer)$placeId. ' LIMIT 1';
    processRequest( $app, $di, $from, $query );
});
// ***

// ### API -  */places/:id/near*
// ### Get Places Near a Place (GET)
// #### *Print JSON* - *STATIC CACHE*
$app->get("/places/:id/near", $apiCache($app, $di), function ($placeId) use ($app, $di) {

    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);

    try{
        $placeDetails = $CartoDB->getPlace($placeId);
        $lon = $placeDetails[0]['location']['longitude'];
        $lat = $placeDetails[0]['location']['latitude'];

        $placesNearby = $CartoDB->getPlacesNear($placeId,$lon,$lat);

    }catch (\Exception $e){
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$e->getMessage());
        $Response->show();
        $app->stop();
    }

    $Response = new \CQAtlas\Helpers\Response($app->response());
    $output = array_merge($Response->toArray(),array('timestamp'=>time(),'results'=>$placesNearby));
    $jsonOutput = json_encode($output);

    // #### Cache the Response
    $Cache = new \CQAtlas\Helpers\Cache($app, $di);
    $Cache->save($jsonOutput);

    echo $jsonOutput;
});
// ***

// ### API -  */categories*
// ### Categories List (GET)
// #### *JSON* - *STATIC CACHE*
$app->get("/categories", $apiCache($app, $di), function () use ($app, $di) {

    $from = 'categories';
    processRequest( $app, $di, $from );
});
// ***

// ### API -  ProcessRequest
// ### Process Simple API Request
// #### *Print JSON* - *Store in STATIC CACHE*
function processRequest(\Slim\Slim $app, \Pimple $di, $from='', $where=''){

    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);

    try{
        $Results = $CartoDB->selectAll($from,$where);

    }catch (\Exception $e){
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$e->getMessage());
        $Response->show();
        $app->stop();
    }

    $Response = new \CQAtlas\Helpers\Response($app->response());
    $output = array_merge($Response->toArray(),array('timestamp'=>time(),'results'=>$Results));
    $jsonOutput = json_encode($output);

    // #### Cache the Response
    $Cache = new \CQAtlas\Helpers\Cache($app, $di);
    $Cache->save($jsonOutput);

    $app->response()->body($jsonOutput); #echo $jsonOutput;
}
// ***

// ### /logout
// ### Logout Endpoint (GET)
// No Interface
$app->get("/logout", function () use ($app) {
    unset($_SESSION['user']);
    $app->view()->setData('user', null);
    $app->redirect('./');
});
// ***

// ### /login
// ### Login Endpoint (GET)
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

// ### /login
// ### Login Endpoint (POST)
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

// ### /admin/users
// ### Users management Endpoint (GET)[**A**]
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

// ### /publish
// ### Publish Dataset Endpoint (POST)[**A**]
// ### *JSON*
// Validate & publish datasets
# $app->post("/publish", $apiAuthenticate($app), function () use ($app, $di) {
$app->post("/publish",  function () use ($app, $di) {

    # Slim Request Object
    $reqBody = json_decode( $app->request()->getBody(), true );
    $meta = $reqBody['metadata'];
    $data = $reqBody['data'];
    $headers = $reqBody['headers'];


    echo '<pre><code>';
    print_r($meta);
    print_r($data);
    print_r($headers);
    print_r($reqBody['location']);
    print_r($reqBody['form']);
    echo '</code></pre>';
    $Response = new \CQAtlas\Helpers\Response($app->response());
    $Response->show();
    $app->stop();

    // #### Create an Excel Document (2007/.xlsx)
    $Excel = new \CQAtlas\Helpers\Excel($meta['name'],$meta['description']);

    $Excel->setSheet
          ->setMetas($meta)
          ->setDataHeaders($headers)
          ->setData($data)
          ->save($di['uploadDir'], '05featuredemo');

    // #### Upload to Google Drive
    $GoogleDrive = new \CQAtlas\Helpers\GoogleDrive();
    $driveFile = $GoogleDrive->upload($di['uploadDir'], '05featuredemo');

    // #### Google Drive Operation Via Server-Side Web App (AppScript::CQ Handler)
    // ##### >> Get Access Token From Spreadsheet API
    $SpreadsheetAPI = new \CQAtlas\Helpers\SpreadsheetApi();
    $SpreadsheetAPI->authenticate($di['google_user'], $di['google_password']);
    $SpreadsheetAPI->appScript('AKfycbzbFJq0hvgkQt6EYWJvgxS0hcnMNxbXGnee1crmCk0pxFhY2OKn',$driveFile->getId());
    // #### Publish JSON Response
    $Response = new \CQAtlas\Helpers\Response($app->response());
    $Response->show();
});
// ***

// ### /publish
// ### Publish Dataset Endpoint (POST)[**A**]
// ### *JSON*
// Validate & publish datasets
# $app->post("/publish", $apiAuthenticate($app), function () use ($app, $di) {
$app->get("/distribute", $apiAuthenticate($app), function () use ($app, $di) {

    // #### Google Drive Operation Via Server-Side Web App (AppScript::CQ-Check)
    // ##### >> Get Access Token From Spreadsheet API
/*    $SpreadsheetAPI = new \CQAtlas\Helpers\SpreadsheetApi();
    $SpreadsheetAPI->authenticate($di['google_user'], $di['google_password']);
    $scriptOutput = $SpreadsheetAPI->appScript('AKfycbwqmysJ6SdLNoq1_NZ_njnjC9hWMcRkVU9lYN1Di2U5ZbdE2VYZ');

    $appResponse = json_decode($scriptOutput['response']);
    if($appResponse->results->filesCount == 0){
        $app->stop();
    }

    //Download Sheets Files
    $validatedFiles = array();
    $pubPath = $di['storageDir'].'/publications';
    $fileExtension = 'xls';
    foreach ($appResponse->results->files as $file) {
        $validatedFiles[$file->name] = $SpreadsheetAPI->getFile($file->id,$pubPath,$fileExtension);
    }*/

    //$sourcePath = sprintf('%s.%s', $pubPath, $fileExtension);
    $sourcePath = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage/0ArDvk7BRZ_yjdHl4OUlfUEc0ZWxpRjZyaS14aEFBLVE.xlsx';
    $Excel = new \CQAtlas\Helpers\Excel();
    $Excel->getSheet($sourcePath);

    $metas = $Excel->getMetas();
    $metas['google_drive_id'] = '0ArDvk7BRZ_yjdHl4OUlfUEc0ZWxpRjZyaS14aEFBLVE';
    $metas['collection_id'] = 0;
    $metas['version'] = 1;
    $metas['status'] = 1;

    $metas['created_by'] = $app->view()->getData('user');
    //$metas['google_drive_id'] = $file->id;
    $datas = $Excel->getData();

/*    echo '<pre><code>';
    print_r($metas);
    print_r($datas);
    echo '</code></pre>';*/



    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);

    # CartoDB Add to Datasets
    //if($file->state === 'validated'){
        try{
            $dataset = $CartoDB->addDataset($metas,$datas);
        }catch (Exception $e){
            echo "OUPS $e";
        }
    //}

exit;
    # CartoDB Add to Places

    //$Reader = new \CQAtlas\Helpers\ExcelReader($sourcePath,0);
    //$objPHPExcel = \PHPExcel_IOFactory::load($sourcePath);
    //$objPHPExcel = \PHPExcel_IOFactory::createReader($sourcePath);

    # Slim Request Object
/*    $reqBody = json_decode( $app->request()->getBody(), true );
    $meta = $reqBody['metadata'];
    $data = $reqBody['data'];
    $headers = $reqBody['headers'];*/

    // ####Create an Excel Document (2007/.xlsx)
    # $Excel = new \CQAtlas\Helpers\Excel($meta['name'],$meta['description']);

/*    $Excel->setMetas($meta)
        ->setDataHeaders($headers)
        ->setData($data)
        ->save($di['uploadDir'], '05featuredemo');*/

    // ####Upload to Google Drive
    # $GoogleDrive = new \CQAtlas\Helpers\GoogleDrive();
    # $driveFile = $GoogleDrive->upload($di['uploadDir'], '05featuredemo');



    // ####Publish JSON Response
    # $Response = new \CQAtlas\Helpers\Response($app->response());
    # $Response->show();
});
// ***

$app->delete("/remove/:fileUri", $apiAuthenticate($app), function ($fileUri) use ($app, $di) {

    $Response = new \CQAtlas\Helpers\Response($app->response());
    if(file_exists($di['uploadDir'].'/'.$fileUri)){
        rename($di['uploadDir'].'/'.$fileUri, $di['uploadDir'].'/removed_'.$fileUri);
    }else{
        $Response->setStatus(403)->setMessage('Impossible de localiser le fichier.('.$di['uploadDir'].'/'.$fileUri.')')->show();
        $app->stop();
    }

    $Response->show();
});

// ### /upload
// ### Upload Endpoint (GET)[**A**]
// Show Upload Interface
$app->get("/upload", $authenticate($app), function () use ($app, $di) {
    $app->render('uploadTmpl.php');
});
// ***

// ### /upload v2
// ### Upload Endpoint (POST)[**A**]
// ##### *For Instance, a FileData Class.*
$app->post("/upload_v2", $apiAuthenticate($app), function () use ($app, $di) {

    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';
    $storage = new \Upload\Storage\FileSystem($di['uploadDir']);
    $file = new \Upload\File('file_upload', $storage);
    // #### Validation setup
    $file->addValidations(array(
        new \Upload\Validation\Mimetype($di['uploadMimetypes']),
        new \Upload\Validation\Extension(
            array_merge(
                $di['delimitedExtensions'],
                $di['excelExtensions'])
        ),
        new \Upload\Validation\Size($di['uploadMaxFileSize'])
    ));

    // ### File Upload
    try {
        $slugName = CqUtil::slugify($file->getName());
        $file->setName( $slugName.'_'.time() );
        $file->upload();

    } catch (\Exception $e) {
        $errors = $file->getErrors();
        if(empty($errors)){
            $errors[] = $e->getMessage();
        }
        $msg = $errors[0] . ' ['.$file->getMimetype().']';
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$msg);
        $Response->setContentType('text/html'); #iFrame Fix
        $Response->show();
        $app->stop();
    }

    // ### Dataset Process & Validation
    $Dataset = new \Dataset( $file, $di, $app->request()->post('optionsDelimiter') );

    $Dataset->addValidations(array(
        new \Dataset\Validation\Rows($di['datasetMinRows']), # Min 2 Rows
        new \Dataset\Validation\Headers(), #First Row = String
        new \Dataset\Validation\Spatial() #Check For Spatial Fields (lon, lat, address)
    ));

    try{
        $Dataset->process();
        # 1- Create Reader
        # 2- Validate
        # 3- Extract Meta setMetadata()
        # 4- Extract Fields Metadata setFieldsMeta()
        # 5- Extract Spatial Fields setSpatial()
        # 6- Json Dump Dataset->toJson(), ->toArray()

    }catch (\Exception $e){
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$e->getMessage());
        $Response->setContentType('text/html'); #iFrame Fix
        $Response->show();
        $app->stop();
    }

    $Response = new \CQAtlas\Helpers\Response($app->response());
    $Response->setContentType('text/html'); #iFrame Fix
    $output = array_merge( $Response->toArray(),$Dataset->toArray() );
    echo json_encode($output);
});
// ***


// ### /upload v1
// ### Upload Endpoint (POST)[**A**]
// #### *Todo: To Rewrite. Too Long & Hard to Test*
// ##### *For Instance, a FileData Class.*
$app->post("/upload", $apiAuthenticate($app), function () use ($app, $di) {

    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';
    $storage = new \Upload\Storage\FileSystem($di['uploadDir']);
    $file = new \Upload\File('file_upload', $storage);

    // #### Validation setup
    $file->addValidations(array(
        new \Upload\Validation\Mimetype($di['uploadMimetypes']),
        new \Upload\Validation\Size($di['uploadMaxFileSize'])
    ));

    // ### File Upload
    try {
        $slugName = CqUtil::slugify($file->getName());
        $file->setName( $slugName.'_'.time() );
        $fileMime = $file->getMimetype();
        $fileSize = ($file->getSize()/1000);
        $fileExtension = $file->getExtension();
        $file->upload();

    } catch (\Exception $e) {
        $errors = $file->getErrors();
        if(empty($errors)){
            $errors[] = $e->getMessage();
        }
        $msg = $errors[0] . ' ['.$file->getMimetype().']';
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$msg);
        $Response->setContentType('text/html'); #iFrame Fix
        $Response->show();
        $app->stop();
    }

    if( in_array($fileExtension, $di['delimitedExtensions']) ){

        $delimiter = $app->request()->post('optionsDelimiter');
        if ($delimiter === null){
            $delimiter = ',';
        }

        $sourcePath =  $di['uploadDir'].'/'.$file->getName().'.'.$fileExtension;
        $Reader = new \CQAtlas\Helpers\DelimitedReader($sourcePath,0,$delimiter);

    // #### Excel File
    }elseif( in_array($fileExtension, $di['excelExtensions']) ){

        $sourcePath =  $di['uploadDir'].'/'.$file->getName().'.'.$fileExtension;
        $Reader = new \CQAtlas\Helpers\ExcelReaderV2($sourcePath,0);

    }else{
        $msg = 'Format non compatible';
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$msg);
        $Response->setContentType('text/html'); #iFrame Fix
        $Response->show();
        return false;
    }

    // #### * Check for Minimum Rows
    if($Reader->getRowsCount()<2){
        $msg = 'Un document avec un minimum de 2 lignes est requis';
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$msg);
        $Response->setContentType('text/html'); #iFrame Fix
        $Response->show();
        return false;
    }

    $fileData = array();
    $fileData['header'] = $Reader->getHeaderRow();
    $fileData['rows'] = $Reader->getRows();

    $fileData['geoJson'] = array(
        'type' => 'FeatureCollection',
        'features' => array()
    );

    foreach ($fileData['rows'] as $row) {
        $fileData['geoJson']['features'][] = array(
            'geometry' => array(
                'type' => 'Point',
                'coordinates' => array()
            ),
                'properties' => $row
        );
    }

    $fileData['count'] = $Reader->getRowsCount();
    $fileData['metaFields'] = array();
    $fileData['geoFields'] = array(
        'lonField' => '',
        'latField' => '',
        'locField' => '',
        'geocoded' => 1
    );

    $flatData = array();
    for($i=0;$i< $fileData['count'];$i++){
        $flatData[] = array_values($fileData['rows'][$i]);
    }

    // #### Guessing Fields Type
    $fieldsCount = count($fileData['header']);
    for($i=0;$i<$fieldsCount-1;$i++){
        $fieldData = $fileData['rows'][1][ $fileData['header'][$i] ];
        $type = gettype($fieldData);
        // ##### >> IF STRING > CHECK FOR DATE
        if($type === 'string'){
            $type = (($timestamp = strtotime($fieldData)) && (strlen($fieldData)>7))? 'date' : $type;
        }

        $type = ($type === 'NULL')?'string':$type;

        $fileData['metaFields'][] = array(
            'title' => $fileData['header'][$i],
            'type' => $type,
            'desc' => ''
        );
    }

    // ### Spatial Fields Validation
    try{
        $lonHeader = CqUtil::matchKeys($fileData['header'], array('lon','lng','longitude'));
        $latHeader = CqUtil::matchKeys($fileData['header'], array('lat','latitude'));
        $locationHeader = CqUtil::matchKeys($fileData['header'], array('adresse','address','addr','location','localisation'));
        if( $lonHeader === false || $latHeader === false){
            if( $locationHeader === false ){
                // ##### >> STOP - No Location Field Detected
                throw new Exception('Aucune entête spatiale (lon,lat ou adresse)');
            }
            // ##### Data Need Geocoding! Now on the Client Side ...Easier on Quota
            $fileData['geoFields']['locField'] = $locationHeader;
            $fileData['geoFields']['geocoded'] = 0;
        }else
        {
            // #### Lng/Lat Validation
            require 'models/geo.php';
            $llErrors = 0;
            $llValids = 0;
            $rowCount = 0;
            $llErrorsObjs = array();

            foreach($fileData['geoJson']['features'] as &$row){

                if(Geo::geo_utils_is_valid_longitude($row['properties'][$lonHeader]) && Geo::geo_utils_is_valid_latitude($row['properties'][$latHeader])){
                    $row['geometry']['coordinates'] = array( (float)$row['properties'][$lonHeader], (float)$row['properties'][$latHeader]);
                    $llValids ++;
                }else{
                    $llErrors ++;
                    $llErrorsObjs[] = '('.$rowCount.': '.$row['properties'][$lonHeader].','.$row['properties'][$latHeader].')';
                }
                $rowCount ++;
            }
            if( $llErrors > 0 ){
                throw new \Exception('Certaines coordonnées (lon,lat) sont invalides. '.$llErrors.' erreurs.{'. implode(',',$llErrorsObjs).'}');
            }

            $fileData['geoFields']['lonField'] = $lonHeader;
            $fileData['geoFields']['latField'] = $latHeader;
        }

    }catch (\Exception $e){
        $msg = $e->getMessage(). ' :: ' . $e->getLine() . ' :: '. $e->getTraceAsString();
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$msg);
        $Response->setContentType('text/html'); #iFrame Fix
        $Response->show();
        return false;
    }

    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);
    $metaTmpl = $CartoDB->getSchema('datasets');


    $metadata = array(
        'name'=> '',
        'description' => '',
        'fileName'=> $file->getName(),
        'fileMime'=> $fileMime,
        'fileExtension'=> $file->getExtension(),
        'fileSize'=> $fileSize.' kb',
        'fileHash'=> md5_file($di['uploadDir'].'/'.$file->getName().'.'.$file->getExtension()),
        'fileId' => sha1($file->getName() . '_' . $_SESSION['user']),
        'fileCreated' => time(),
        'fileUri' => sprintf('%s.%s',$file->getName(),$file->getExtension()),
        'properties' => $fileData['metaFields']
    );

    $metadata['form'] = $metaTmpl['form'];
    $metadata = array_merge($metadata,$fileData['geoFields']);
    $metadata['form']['name']['value'] = $file->getName();

    $data = array(
        //'data' => $flatData,
        'geojson' =>$fileData['geoJson'],
        //'headers' =>$fileData['metaFields'],
        'metadata' => $metadata
    );


    $Response = new \CQAtlas\Helpers\Response($app->response());
    $Response->setContentType('text/html'); #iFrame Fix
    $output = array_merge($Response->toArray(),$data);
    echo json_encode($output);
});
// ***

// ### /private/about
// ### "TEST" ABOUT PRIVATE (GET)[**A**]
// Show Private/About Interface
$app->get("/private/about", $authenticate($app), function () use ($app) {
    $app->render('privateAbout.php');
});
// ***

// ### /buildTaxonomy
// ### Build Taxonomy Endpoint (GET)
// Build Categories Taxonomy on CartoDB via a JSON file
$app->get("/buildTaxonomy", function () use ($app, $di) {

    $jsonContent = file_get_contents($di['storageDir'].'/database/factual_taxonomy.json');
    $jsonContentUTF8 = mb_convert_encoding($jsonContent, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
    $taxonomy = json_decode($jsonContentUTF8, true);

    $sqlStatements = array();
    $insertText = "INSERT INTO categories (id, parent_id, en, fr) VALUES (";
    $count = 1;
    foreach ($taxonomy as $item) {
        $idParent = (count($item['parents']) > 0)?$item['parents'][0]:0;
        // ##### Escape Single Quotes With an Extra Quote ' => ''#####
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

// ### /buildCategories
// ### Build Categories Endpoint (GET)
// Build Categories on CartoDB via an Excel file
$app->get("/buildCategories", function () use ($app, $di) {

    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';
    $sourcePath ='./storage/database/categories.xlsx';
    $Reader = new \CQAtlas\Helpers\ExcelReader($sourcePath,0);
    $rows = $Reader->getRows();

    $sqlStatements = array();
    //$cache = array();
    $insertText = "INSERT INTO categories (id, parent_fr, fr, icon) VALUES (";

    $count = 1;
    foreach ($rows as $item) {
        // ##### Escape Single Quotes With an Extra Quote ' => ''#####
        $parent_fr = str_replace("'","''", $item['Regroupement']);
        $fr = str_replace("'","''", $item['Cat']);
        $icon = CqUtil::slugify($parent_fr).'/'.CqUtil::slugify($fr).'.png';
        $sqlStatements[] =  $insertText . $count.",'".$parent_fr."','".$fr."','".$icon."');";
/*        $cache[] = array(
            'id' => $count,
            'group' => $parent_fr,
            'cat' => $fr,
            'i' => $icon
        );*/
        $count ++;
    }

    $fields = array(
        'q' => implode('',$sqlStatements),
        'api_key' => $di['cartodb_api_key']
    );

    $url = sprintf('http://%s.%s', $di['cartodb_subdomain'],$di['cartodb_endpoint']);
    $curlResult = CqUtil::curlPost($url, json_encode($fields));

    echo '<pre><code>';
    print_r($curlResult);
    echo '</code></pre>';
});
// ***

// ### /montest###
// #### Playground ;)####
$app->get("/check_drive", function () use ($app, $di) {
    echo '<br>TEST DRIVE!';


});
// ***


// ### /montest###
// #### Playground ;)####
$app->get("/select_all", function () use ($app, $di) {
    echo '<br>TEST!';
    //$date = new DateTime();

    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);


    try{
        $results = $CartoDB->selectAll('places');
    }catch (Exception $e){
        echo "OUPS $e";
    }
    echo '<pre><code>';
    print_r($results);
    echo '</code></pre>';
    try{
        $results = $CartoDB->selectAll('places',"WHERE place_id=2");
    }catch (Exception $e){
        echo "OUPS $e";
    }

    echo '<pre><code>';
    print_r($results);
    echo '</code></pre>';

});
// ***

$app->get("/montest", function () use ($app, $di) {
    echo '<br>MonTest OK!';
    $date = new DateTime();

    echo '<br>'.$date->getTimestamp();
    echo '<br>'.time();

    $fakeDatasets = array(
        'fakeName' => array(
            'bbox_4326' => 'ST_MakeEnvelope(45.702888, -73.476921, 45.414650, -73.905762, 4326)',
            'the_geom'  => 'ST_MakeEnvelope(45.702888, -73.476921, 45.414650, -73.905762, 4326)',
            'collection_id' => 1,
            'created_by' => 1,
            //'dataset_id' => array('type' =>'number'),
            'dataset_extra_fields' => '{["champ_1","champ_2"]}',
            'desc_en'   => 'Description EN',
            'desc_fr'   => 'Description EN',
            'google_drive_id' =>  'wfqlwjrfl342324',
            'label'     => 'LABEL ....',
            'licence'   => 'Licence',
            'name_en'   => "L'autre École ç",
            'name_fr'   => 'Name FR',
            'privacy'   => 0,
            'slug'      => 'name_fr',
            'status'    => 1,
            'version'   => 1,
            'primary_category_id'   => 1,
            'secondary_category_id' => 2,
            'tertiary_category_id'  => 3//,
            //  'file_format'   => array('type' =>'string'),
            //  'file_hash'     => array('type' =>'string'),
            //  'file_mime'     => array('type' =>'string'),
            //  'file_size'      => array('type' =>'string'),
            //  'file_uri'      => array('type' =>'string')
        )
    );
    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);
    /*
        try{
            $curlDatasets = $CartoDB->batchInsert('datasets',$fakeDatasets);
        }catch (Exception $e){
            echo "OUPS $e";
        }

        echo '<pre><code>';
        print_r($curlDatasets);
        echo '</code></pre>';*/

    $fakePlaces = array(
        'fakeName' => array(
            //'bbox_4326' => array('type'=> 'geom'),
            'the_geom'  => 'ST_SetSRID(ST_Point(-73.50246,45.028293),4326)',
            'address'   => '5320 Saint-Denis',
            'city'   => 'Montreal',
            'longitude'   => -73.50246,
            'latitude'   => 45.628293,
            'postal_code'   => 'H2J 2M3',
            'tel_number'   => '514-273-2008',
            'website'   => 'http://steflef.com',
            'collection_id' => 1,
            'created_by' => 1,
            'dataset_id'   => 1,
            'desc_en'   => 'Description EN',
            'desc_fr'   => 'Description FR',
            'label'   => 'Label',
            'name_en'   => 'Home',
            'name_fr'   => 'Maison',
            //'place_id'   => 'Description EN',
            'privacy'   => 0,
            'slug'   => 'maison',
            'status'   => 1,
            'version'   => 1,
            'primary_category_id'   => 1,
            'secondary_category_id'   => 2,
            'tags'   => '{"champ_1":"Ma maison","champ_2","Stephane Lefebvre"}'
        )
    );
    try{
        $results = $CartoDB->batchInsert('places',$fakePlaces);
    }catch (Exception $e){
        echo "OUPS $e";
    }

    echo '<pre><code>';
    print_r($results);
    echo '</code></pre>';

});
// ***

// ### START THE ENGINE!
$app->run();