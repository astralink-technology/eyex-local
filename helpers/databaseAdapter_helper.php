<?php
/*
 * Copyright Chilli Panda
 * Created on 05-03-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper to do up certain functions in calling the data from the database
 */

class cp_databaseAdapter_helper{
    public function retDataResponse($res, $data, $rowsRet, $errCode = null){
        if ($res === false){
            $totalRowsAvailable = $rowsRet;
            $data = "An error occured";
            die("Error in SQL query: " . pg_last_error());
            $errorMessage = pg_last_error();
            $error = true;
            if ($errCode != null){
                $errorCode = $errCode;
            }else{
                $errorCode = -1;
            }
        }else{
            if (is_null($rowsRet)){
                $totalRowsAvailable = "Not Applicable";
            }else{
                $totalRowsAvailable = $rowsRet;
            }
            $errorMessage = "No Error";
            $error = false;
            $errorCode = null;
        }
        
        $retData = array(
            "TotalRowsAvailable" => $totalRowsAvailable,
            "Data" => $data,
            "ErrorCode" => $errorCode,
            "ErrorMessage" => $errorMessage,
            "Error" => $error
        );
        
        return $retData;
    }
    
    public function hasDataNoError($dataRes){
        if ($dataRes["Error"] == false){
            return true;
        }else{
            return false;
        }
    }
    
    public function retLastInsertId($res, $id, $errCode = null){
        if ($res === false){
            $data = "An error occured";
            die("Error in SQL query: " . pg_last_error());
            $errorMessage = pg_last_error();
            $error = true;
            $id = null;
            if ($errCode != null){
                $errorCode = $errCode;
            }else{
                $errorCode = -1;
            }
        }else{
            $errorMessage = "No Error";
            $error = false;
            $errorCode = null;
        }

        $retData = array(
            "Id" => $id,
            "ErrorMessage" => $errorMessage,
            "ErrorCode" => $errorCode,
            "Error" => $error
        );

        return $retData;
    }
}
?>
