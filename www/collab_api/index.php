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
//use \Pimple;
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

// ### Dashboard -  */*
// ### Main Endpoint (GET)[**A**]
// Show the dashboard
# $app->get("/", $authenticate($app), function () use ($app, $di) {
$app->get("/tb",  function () use ($app, $di) {
    $app->render('tbTmpl.php');
});
// ***

// ### API -  */datasets*
// ### Datasets List (GET)
// #### *JSON* - *STATIC CACHE*
$app->get("/datasets", $apiCache($app, $di), function () use ($app, $di) {

    $from = 'datasets';
    $query = 'ORDER BY updated_at DESC';
    processRequest( $app, $di, $from, $query );
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
/*$app->get("/datasets/:id/places", $apiCache($app, $di), function ($datasetId) use ($app, $di) {

    $from = 'places';
    $query =  'WHERE dataset_id='.(Integer)$datasetId;
    processRequest( $app, $di, $from, $query );
});*/
// ***

// ### API -  */datasets/:id/places*
// ### Get a List of places for a Dataset  (GET)
// #### *JSON* - *STATIC CACHE*
$app->get("/datasets/:id/places", $apiCache($app, $di), function ($datasetId) use ($app, $di) {

    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);

    try{
        $places = $CartoDB->getPlaces($datasetId);

    }catch (\Exception $e){
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$e->getMessage());
        $Response->addContent(array('trace' => $e->getTrace()[0]));
        $Response->show();
        $app->stop();
    }

    $Response = new \CQAtlas\Helpers\Response($app->response());
    $Response->addContent(array('timestamp'=>time(),'results'=>$places));
    $output = $Response->toArray();
    $jsonOutput = json_encode($output);

    // #### Cache the Response
    $Cache = new \CQAtlas\Helpers\Cache($app, $di);
    $Cache->save($jsonOutput);

    $Response->show();
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

    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);

    try{
        $placeDetails = $CartoDB->getPlace($placeId);

    }catch (\Exception $e){
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$e->getMessage());
        $Response->show();
        $app->stop();
    }

    $Response = new \CQAtlas\Helpers\Response($app->response());
    $Response->addContent(array('timestamp'=>time(),'results'=>$placeDetails[0]));
    $jsonOutput = json_encode($Response->toArray());

    // #### Cache the Response
    $Cache = new \CQAtlas\Helpers\Cache($app, $di);
    $Cache->save($jsonOutput);

    $Response->show();
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

// ### API -  */places/:id/near*
// ### Get Places Near a Place (GET)
// #### *Print JSON* - *STATIC CACHE*
$app->get("/test/:id", $apiCache($app, $di), function ($datasetId) use ($app, $di) {

    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);

    try{
        $placesCount = $CartoDB->getPlacesCount($datasetId);

    }catch (\Exception $e){
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$e->getMessage());
        $Response->addContent(array('trace' => $e->getTrace()[0]));
        $Response->show();
        $app->stop();
    }
    echo $placesCount;
/*    $Response = new \CQAtlas\Helpers\Response($app->response());
    $output = array_merge($Response->toArray(),array('timestamp'=>time(),'results'=>$placesNearby));
    $jsonOutput = json_encode($output);

    // #### Cache the Response
    $Cache = new \CQAtlas\Helpers\Cache($app, $di);
    $Cache->save($jsonOutput);

    echo $jsonOutput;*/
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
        $Response->addContent(array('trace' => $e->getTrace()[0]));
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
    ini_set('memory_limit', '256M');
    require_once 'vendor/cqatlas/cqatlas/CqUtil.php';

    # Slim Request Object
    $reqBody = json_decode( $app->request()->getBody() );
    $meta = $reqBody->metadata;
    $meta->created_by = $app->view()->getData('user');
    $data = $reqBody->geojson;
    $properties = $reqBody->properties;

/*    echo '<pre><code>';
    print_r($meta);
    print_r($data);
    print_r($properties);
    echo '</code></pre>';
    $Response = new \CQAtlas\Helpers\Response($app->response(),400);
    $Response->show();
    $app->stop();*/

    // #### Create an Excel Document (2007/.xlsx)
    try{
        $Excel = new \CQAtlas\Helpers\Excel($meta->nom,$meta->description);

        $Excel->setSheet()
              ->setMetas($meta)
              ->setDataHeaders($properties, $data->features)
              ->setData($data->features, $properties)
              ->setProperties($properties)
              ->save($di['pubDir'], $meta->nom);

    } catch (\Exception $e) {

        $msg = $e->getMessage() . ' ['.$e->getLine().']';
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$msg);
        $Response->addContent(array('trace' => $e->getTrace()[0]));
        $Response->show();
        $app->stop();
    }
    // #### Upload to CartoDB

    $dataset = new stdClass;

    $dataset->google_drive_id = '';
    $dataset->collection_id = 0;
    $dataset->version = 1;
    $dataset->privacy = 1;
    $dataset->status = 0;
    $dataset->created_by = $meta->created_by;
    $dataset->attributions = $meta->source;
    $dataset->licence = $meta->licence;
    $dataset->description = $meta->description;
    $dataset->name = $meta->nom;
    $dataset->label = $meta->etiquette;
    $dataset->tertiary_category_id = 0;
    $dataset->secondary_category_id = 0;
    $dataset->primary_category_id = 0;

    $categories = explode(',',$meta->categories);
    $l = count($categories);

    switch($l){
        case ($l >= 3):
            $dataset->tertiary_category_id = $categories[2];
        case 2:
            $dataset->secondary_category_id = $categories[1];
        case 1:
            $dataset->primary_category_id = $categories[0];
            break;
    }
    $dataset->slug = CqUtil::slugify($dataset->name);
    $dataset->file_uri = $meta->uri;

    // User Defined Fields
    $dataset_extra_fields = [];
    foreach ($properties as $field) {
        $dataset_extra_fields[] = array(
            'field' => $field->title,
            'type'  => $field->type,
            'desc'  => $field->desc
        );
    }
    $dataset->dataset_extra_fields = json_encode($dataset_extra_fields);

    $CartoDB = new \CQAtlas\Helpers\CartoDB($di);

    # CartoDB Add to Datasets
    try{
        $datasetId = $CartoDB->createDataset($dataset);
    }catch (Exception $e){
        $msg = $e->getMessage() . ' ['.$e->getLine().']';
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$msg);
        $Response->addContent(array('trace' => $e->getTrace()[0]));
        $Response->show();
        $app->stop();
    }

    // Flush Cache
    $Cache = new \CQAtlas\Helpers\Cache($app, $di);
    $Cache->bust('datasets');

    // BUILD PLACES INSERTS
    $batchInserts = array();
    foreach ($data->features as $place) {
        $attributes = new stdClass;
        $attributes->the_geom = "ST_GeomFromText('POINT(".str_replace(',', ' ',$place->_geo->lon.' '.$place->_geo->lat).")',4326)";
        $attributes->address = $place->_geo->formatted_address;
        $attributes->city = trim($place->_geo->city);
        $attributes->postal_code = trim($place->_geo->postal_code);
        $attributes->created_by = $dataset->created_by;
        $attributes->dataset_id = $datasetId;
        $attributes->label = $place->properties->{$dataset->label};
        $attributes->latitude = $place->_geo->lat;
        $attributes->longitude = $place->_geo->lon;

        //$attributes->name_fr = (array_key_exists('nom',$place->properties))?$place->properties->nom:'';
        $attributes->name_fr = $place->properties->{$meta->att_name};
        // (array_key_exists('description',$place->properties))?$place->properties->description:'';
        $attributes->description = $place->properties->{$meta->att_description};
        $attributes->tel_number = (array_key_exists('telephone',$place->properties))?$place->properties->telephone:'';
        $attributes->website = (array_key_exists('web',$place->properties))?$place->properties->web:'';

        if(trim($meta->c_categorie) !== ''){
            $attributes->primary_category_id = $place->properties->{$meta->c_categorie};
        }else{
            $attributes->primary_category_id = $dataset->primary_category_id;
            $attributes->secondary_category_id = $dataset->secondary_category_id;
        }

        //tags
        $tags = array();
        foreach ($properties as $field) {
            $tags[$field->title] = $place->properties->{$field->title};
        }
        $attributes->tags = json_encode($tags);

        foreach ($attributes as $key=>&$val) {
            if($val === ''){
                $val = 'NULL';
            }
        }

        $batchInserts[] = $attributes;
    }

    # CartoDB Add to Places
    try{
        $placesOutput = $CartoDB->addPlaces($batchInserts);
    }catch (Exception $e){
        $msg = $e->getMessage() . ' ['.$e->getLine().']';
        $Response = new \CQAtlas\Helpers\Response($app->response(),400,$msg);
        $Response->addContent(array('trace' => $e->getTrace()[0]));
        $Response->show();
        $app->stop();
    }

    $Response = new \CQAtlas\Helpers\Response($app->response(),200,'ok');
    $Response->addContent($placesOutput);
    $Response->show();
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