<?php
/**
 * Created by JetBrains PhpStorm.
 * User: prof_lebrun
 * Date: 12-12-17
 * Time: 11:31 AM
 * To change this template use File | Settings | File Templates.
 */

$tokens = token_get_all(file_get_contents('index.php'));

/** A list:
 *  * item1
 *  * item2
 */
$comments = array();
//echo T_COMMENT . ' <> '. T_DOC_COMMENT . '<br>';
foreach($tokens as $token){
    if($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT){
        $comments[] = $token[1];
    }
    //echo $token[0].'<br>';
}
echo '<pre>';
print_r($comments);
echo '</pre>';