<?php

/*
 * Copyright Chilli Panda
 * Created on 17-04/2014
 * Created by Shi Wei Eamon
 */

/*
 * A helper on encryption
 */
class cp_log_helper{
    public function errorLog(){
        if (isset($_POST['Messsage'])){
            $message = $_POST['Message'];
            error_log($message);
            echo json_encode("Logged: " . $message);
        }else{
            echo json_encode("Logging Failed");
        }
    }
}
 
?>

