<?php
/**
 * Created by JetBrains PhpStorm.
 * User: prof_lebrun
 * Date: 13-02-21
 * Time: 3:21 PM
 * To change this template use File | Settings | File Templates.
 */

// ####App Attributes####
$di['baseUrl'] = 'http://localhost/lmtl/www/collab_api/';
$di['uploadDir'] = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage/uploads';
$di['storageDir'] = '/Applications/MAMP/htdocs/lmtl/www/collab_api/storage';
$di['uploadMimetypes'] = array(
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
);
$di['uploadMaxFileSize'] = '5M';
// ####App Attributes####
$di['dbDriver'] = 'sqlite';
$di['dbFile'] = './storage/database/app2.sqlite';
// ####Ogre Service attributes####
$di['ogre_host'] = 'http://ogre.adc4gis.com';
$di['ogre_convert_endpoint'] = '/convert';
// ####Google Services Attributes####
$di['google_client_id'] = '509163819657.apps.googleusercontent.com';
$di['google_client_secret'] = 'notasecret';
$di['google_user'] = 'prof.lebrun@gmail.com';
$di['google_password'] = 'ocool007_';
$di['google_drive_master_file_id'] = '0ArDvk7BRZ_yjdEI0S3R1Yks4NUxhaGlRTVlDTFNCd3c';
$di['google_drive_folder_id'] = '0B7Dvk7BRZ_yjT2hyd3JFOHoxWXM';
// ####CartoDB services attributes####
$di['cartodb_api_key'] = '6947088ad96d8efc9ffe906f1312d1568f3eafc1';
$di['cartodb_subdomain'] = 'steflef';
$di['cartodb_endpoint'] = 'cartodb.com/api/v2/sql';
// ####Upload attributes####
$di['delimitedExtensions'] = array('csv', 'txt');
$di['excelExtensions'] = array('xls', 'xlsx');
$di['serverSideGeocoding'] = false;
// ***