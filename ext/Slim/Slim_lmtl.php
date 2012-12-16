<?php
namespace Slim;
/**
 * Created by JetBrains PhpStorm.
 * User: prof_lebrun
 * Date: 12-12-15
 * Time: 4:24 PM
 * To change this template use File | Settings | File Templates.
 */
class Slim_lmtl extends Slim
{
    public function __construct($userSettings = array()){
        parent::__construct($userSettings);
    }

    /**
     * Redirect
     *
     * This method immediately redirects to a new URL. By default,
     * this issues a 302 Found response; this is considered the default
     * generic redirect response. You may also specify another valid
     * 3xx status code if you want. This method will automatically set the
     * HTTP Location header for you using the URL parameter.
     *
     * @param  string   $url        The destination URL
     * @param  int      $status     The HTTP redirect status code (optional)
     * @param  string   $base_url   The base URL (optional)
     */
    public function redirect($url, $status = 302, $base_url = 'http://localhost:85/index_test.php/')
    {
        $this->response->redirect($base_url.$url, $status);
        $this->halt($status);
    }
}