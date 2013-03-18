<?php

use Guzzle\Http\Client;

/**
 * Created by JetBrains PhpStorm.
 * User: prof_lebrun
 * Date: 13-01-27
 * Time: 3:47 PM
 * To change this template use File | Settings | File Templates.
 */
class GeoOgre
{

    public function __construct(){}

    static function geo_ogre_convert_file($di, $path)
    {

        if (!file_exists($path)) {
            return array(
                'ok' => 0,
                'error' => 'Le fichier est introuvable.',
            );
        }

        $client = new Client($di['ogre_host']);

        $response = $client->post($di['ogre_convert_endpoint'], null, array(
            'upload' => '@'.$path
        ))->send();

/*
        Guzzle Response exemple:
        echo '<br>Content-Type: '.$response->getHeader('Content-Type');
        $body = $response->getBody()
        $b = $response->getHeader('Content-Length');
        $kb = round(trim($b)/1024,2);
        echo '<br>Content-Length: '. $kb . 'kb';
        echo '<br>Content-Length: '. $response->getHeader('Content-Length');
        echo '<br>Date: '.$response->getHeader('Date');
        echo '<br>Code: '.$response->getStatusCode();
        echo '<br>Reason: '.$response->getReasonPhrase();*/

        if ($response->getStatusCode() != 200) {
            return $response->getStatusCode();
        }

        if ((int)trim($response->getHeader('Content-Length')) < 1024) {
            return $response->getHeader('Content-Length');
        }

        $json = json_decode($response->getBody(), TRUE);

        if (!$json) {
            return array(
                'ok' => 0,
                'error' => 'failed to decode JSON',
            );
        }

        if (isset($json['error'])) {
            return array(
                'ok' => 0,
                'error' => $json['message'],
            );
        }

        return array(
            'ok' => 1,
            'data' => $json,
        );
    }
}