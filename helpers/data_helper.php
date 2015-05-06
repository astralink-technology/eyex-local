<?php
/*
 * Copyright Chilli Panda
 * Created on 01-03-2013
 * Created by Shi Wei Eamon
 */

/*
 *  A helper to figure out what you passed in to the SP
 */

class cp_data_helper{
    public function convertDataString($data){
        return '"' . (string) $data . '"';
    }
    public function convertDataStringArray($data){
        return  '"' . (string) $data . '"';
    }
    public function convertDataInt($data){
        if ($data === ''){
            return '""';
        }else{
            return (int) $data;
        }
    }
    public function convertDataBool($data){
        if ($data == true){
            return 1;
        }else if ($data == false){
            return 0;
        }else{
            return null;
        }
    }
}
?>
