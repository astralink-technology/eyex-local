<?php
/*
 * Copyright Chilli Panda
 * Created on 05-03-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper on encoding / decoding jwt
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/lib/php-jwt-master/Authentication/JWT.php';

class cp_jwt_helper{
    function decodeJWT($token, $key){
        $JWT = new JWT();
        $jsonObject = $JWT->decode($token, $key);

        return $jsonObject;
    }
}
?>
