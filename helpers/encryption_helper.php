<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/Libraries/password_compat/lib/password.php';

/*
 * Copyright Chilli Panda
 * Created on 03-05-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper on encryption
 */
class cp_encryption_helper{
    public function verify(){
        if (isset($_POST['InputPassword']) && isset($_POST['HashedPassword'])){
            $password = $_POST['InputPassword'];
            $hash = $_POST['HashedPassword'];
            echo json_encode(password_verify($password, $hash));
        }else{
            echo json_encode(false);
        }
    }
}
 
?>

