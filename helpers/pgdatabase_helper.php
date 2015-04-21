<?php
/*
 * Copyright Chilli Panda
 * Created on 30-09-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper to format correct data
 */

class cp_pgdatabase_helper{
    public function toBool($value){
        if ($value == true){
            $value = '1';
        }else{
            $value = '0';
        }
        return $value;
    }
}
?>
