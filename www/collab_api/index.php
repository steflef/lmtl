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
$app->post("/publish", $apiAuthenticate($app), function () use ($app, $di) {
//$app->post("/publish",  function () use ($app, $di) {

    # Slim Request Object
    $reqBody = json_decode( $app->request()->getBody() );
    $meta = $reqBody->metadata;
    $meta->created_by = $app->view()->getData('user');
    $data = $reqBody->geojson;
    $properties = $reqBody->properties;
/*
    echo '<pre><code>';
    print_r($meta);
    print_r($data);
    print_r($properties);
    echo '</code></pre>';
    $Response = new \CQAtlas\Helpers\Response($app->response());
    $Response->show();
    $app->stop();*/

    // #### Create an Excel Document (2007/.xlsx)
    $Excel = new \CQAtlas\Helpers\Excel($meta->nom,$meta->description);

    $Excel->setSheet()
          ->setMetas($meta)
          ->setDataHeaders($properties, $data->features)
          ->setData($data->features, $properties)
          ->setProperties($properties)
          ->save($di['uploadDir'], '05featuredemo');


    // #### Upload to Google Drive
    $GoogleDrive = new \CQAtlas\Helpers\GoogleDrive();
    $driveFile = $GoogleDrive->upload($di['uploadDir'], '05featuredemo');

    // #### Google Drive Operation Via Server-Side Web App (AppScript::CQ Handler)
    // ##### >> Get Access Token From Spreadsheet API
    $SpreadsheetAPI = new \CQAtlas\Helpers\SpreadsheetApi();
    $SpreadsheetAPI->authenticate($di['google_user'], $di['google_password']);
    $resp = $SpreadsheetAPI->appScript('AKfycbzbFJq0hvgkQt6EYWJvgxS0hcnMNxbXGnee1crmCk0pxFhY2OKn',$driveFile->getId());
    // #### Publish JSON Response
    $Response = new \CQAtlas\Helpers\Response($app->response());

    $output = array_merge($Response->toArray(),array('google'=>$resp));
    echo $jsonOutput = json_encode($output);
   //$Response->show();
});
// ***

// ### /publish
// ### Publish Dataset Endpoint (POST)[**A**]
// ### *JSON*
// Validate & publish datasets
# $app->post("/distribute", $apiAuthenticate($app), function () use ($app, $di) {
$app->get("/distribute", function () use ($app, $di) {

    // #### Google Drive Operation Via Server-Side Web App (AppScript::CQ-Check)
    // ##### >> Get Access Token From Spreadsheet API
    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';
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
    foreach ($appResponse->results->files as &$file) {
        //$validatedFiles[$file->name] = $SpreadsheetAPI->getFile($file->id,$pubPath,$fileExtension);
        $getResults = $SpreadsheetAPI->getFile($file->id,$pubPath,$fileExtension);
        $file['status'] = $getResults['status'];
        $file['path'] = $getResults['file'];
    }*/
//    echo '<pre><code>';
//    print_r($appResponse);
//    print_r($validatedFiles);
//    echo '</code></pre>';
//    $app->stop();

    // FAKE
    $results = array();

    $ressource = new stdClass;
    $ressource->id = '0ArDvk7BRZ_yjdHl4OUlfUEc0ZWxpRjZyaS14aEFBLVE';
    $ressource->name  =  '*V*_SUPER_BATCH';
    $ressource->state ='validated';
    $ressource->lastUpdated =  '2013-02-28T16:40:41.181Z';
    $ressource->status= 200;
    $ressource->path  = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage/publications/0ArDvk7BRZ_yjdHl4OUlfUEc0ZWxpRjZyaS14aEFBLVE.xlsx';

    //$results[] = $ressource;

    $ressource = new stdClass;
    $ressource->id = '0ArDvk7BRZ_yjdG9LNjI4TDQxSGpjaWVpWkJCMUxkeWc';
    $ressource->name  = '*V*_worsthousing-no-lat-loc-sub_1363806463';
    $ressource->state ='validated';
    $ressource->lastUpdated =  '2013-02-28T16:40:41.181Z';
    $ressource->status= 200;
    $ressource->path  = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage/publications/0ArDvk7BRZ_yjdG9LNjI4TDQxSGpjaWVpWkJCMUxkeWc.xlsx';

    $results[] = $ressource;


    //$sourcePath = sprintf('%s.%s', $pubPath, $fileExtension);
    //$sourcePath = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage/0ArDvk7BRZ_yjdHl4OUlfUEc0ZWxpRjZyaS14aEFBLVE.xlsx';
    foreach($results as $file){

        $Excel = new \CQAtlas\Helpers\Excel();
        $Excel->getSheet($file->path);

        $metadatas = $Excel->getMetas();
        $data = $Excel->getData();
        $customFields = $Excel->getProperties();

        $metas = new stdClass;

        $metas->google_drive_id = $file->id;
        $metas->collection_id = 0;
        $metas->version = 1;
        $metas->status = 1;
        // todo: real user ID (Uploader)
        $metas->created_by = $metadatas['created_by'];

        $metas->attributions = $metadatas['source'];
        $metas->licence = $metadatas['licence'];
        $metas->description = $metadatas['description'];
        $metas->name = $metadatas['nom'];
        $metas->label = $metadatas['etiquette'];

        $categories = explode(',',$metadatas['categories']);
        $l = count($categories);

        switch($l){
            case 3:
                $metas->tertiary_category_id = $categories[2];
            case 2:
                $metas->secondary_category_id = $categories[1];
            case 1:
                $metas->primary_category_id = $categories[0];
            break;
        }
        $metas->slug = CqUtil::slugify($metadatas['nom']);
        $metas->file_uri = $metadatas['URI'];

        // User Defined Fields
        $dataset_extra_fields = [];
        foreach ($customFields as $field) {
            //if( substr($key,0,1) !== '_' ){
                $dataset_extra_fields[] = array(
                    'field' => $field['Champ'],
                    'type'  => $field['Type'],
                    'desc'  => $field['Description']
                );
            //}
        }
        $metas->dataset_extra_fields = json_encode($dataset_extra_fields);


        $CartoDB = new \CQAtlas\Helpers\CartoDB($di);

        # CartoDB Add to Datasets
        try{
            $datasetId = $CartoDB->createDataset($metas);
        }catch (Exception $e){
            echo "OUPS $e";
        }

/*        echo "<br>Dataset Created!<br>";
        echo '<pre><code>';
       // print_r($datasetId);
       // print_r($customFields);
       // print_r($metadatas);
       // print_r($metas);
       // print_r($data);
        echo '</code></pre>';*/

        // BUILD INSERTS
        $batchInserts = array();
        foreach ($data as $place) {
            $attributes = new stdClass;
            $attributes->the_geom = "ST_GeomFromText('POINT(".str_replace(',', ' ',$place['_lonlat']).")',4326)";
            $attributes->address = $place['_formatted_address'];
            $attributes->city = $place['_city'];
            $attributes->postal_code = $place['_postal_code'];
            $attributes->created_by = $metas->created_by;
            $attributes->dataset_id = $datasetId;
            $attributes->label = $place[$metas->label];
            $attributes->latitude = $place['_lat'];
            $attributes->longitude = $place['_lon'];
            $attributes->latitude = $place['_lat'];

            $attributes->name_fr = (array_key_exists('nom',$place))?$place['nom']:'';
           // $attributes->desc = (array_key_exists('description',$place))?$place['description']:'';
            $attributes->tel_number = (array_key_exists('telephone',$place))?$place['telephone']:'';
            $attributes->website = (array_key_exists('web',$place))?$place['web']:'';

            if($metadatas['c_categorie'] === ''){
                $attributes->primary_category_id = $place[$metadatas['c_categorie']];
            }else{
                $attributes->primary_category_id =$metas->primary_category_id;
                $attributes->secondary_category_id =$metas->secondary_category_id;
            }

            //tags
            $tags = array();
            foreach ($customFields as $field) {
                $tags [] = array(
                    $field['Champ'] => $place[$field['Champ']]
                );
            }
            $attributes->tags = json_encode($tags);
            $batchInserts[] = $attributes;
        }

        # CartoDB Add to Datasets
        try{
            $response = $CartoDB->addPlaces($batchInserts);
        }catch (Exception $e){
            echo "AddPlaces :: $e";
        }
        echo '<pre><code>';
        print_r($response);
        echo '</code></pre>';
    }


    $app->stop();

});
// ***

// ### /remove/:fileUri
// ### Remove Endpoint (DELETE)[**A**]
// Rewrite Uploaded File
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
// ***

// ### /upload
// ### Upload Endpoint (GET)[**A**]
// Show Upload Interface
$app->get("/upload", $authenticate($app), function () use ($app, $di) {
    $app->render('uploadTmpl.php');
});
// ***

// ### /upload
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
        //'name'=> '',
        //'description' => '',
        'fileName'=> $file->getName(),
        'fileMime'=> $fileMime,
        'fileExtension'=> $file->getExtension(),
        'fileSize'=> $fileSize.' kb',
        //'fileHash'=> md5_file($di['uploadDir'].'/'.$file->getName().'.'.$file->getExtension()),
        //'fileId' => sha1($file->getName() . '_' . $_SESSION['user']),
        //'fileCreated' => time(),
        'fileUri' => sprintf('%s.%s',$file->getName(),$file->getExtension()),
        'properties' => $fileData['metaFields']
    );

    $metadata['form'] = $metaTmpl['form'];
    $metadata = array_merge($metadata,$fileData['geoFields']);
    $metadata['form']['name']['value'] = $file->getName();

    $data = array(
        'geojson' =>$fileData['geoJson'],
        'metadata' => $metadata
    );


    $Response = new \CQAtlas\Helpers\Response($app->response());
    $Response->setContentType('text/html'); #iFrame Fix
    $output = array_merge($Response->toArray(),$data);
    echo json_encode($output);
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

// ### START THE ENGINE!
$app->run();