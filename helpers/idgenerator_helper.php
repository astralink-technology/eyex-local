<?php
/*
 * Copyright Chilli Panda
 * Created on 01-03-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper to generate a random ID
 */

class cp_idGenerator_helper{
    
    protected function generateAlphaNumberic($length){
        $hexdecimal = "ABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $id = array(); //remember to declare $pass as an array
        $alphaLength = strlen($hexdecimal) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $id[] = $hexdecimal[$n];
        }

        return implode($id); //convert to string
    }

    protected function generateNumeric($length){
        $numeric = "0123456789";
        $id = array(); //remember to declare $pass as an array
        $alphaLength = strlen($numeric) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $id[] = $numeric[$n];
        }

        return implode($id); //convert to string
    }
    
    function generateId($feedback = NULL){
        $rand = $this->generateAlphaNumberic(8);
        $rand2 = $this->generateAlphaNumberic(8);
        $rand3 = $this->generateAlphaNumberic(8);
        
        $id = $rand . '-' . $rand2 . '-' . $rand3;
        if ($feedback == true){
            echo $id;
        }
        return $id;
    }

    function generateNumericId(){
        $rand = $this->generateNumeric(5);
        $id = $rand;
        return $id;
    }
}

?>
