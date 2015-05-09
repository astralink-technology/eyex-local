<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class phoneDao{
    public function addPhone(
        $phoneId
        , $phoneDigits = null
        , $countryCode = null
        , $code = null
        , $type = null
        , $entity = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pPhoneId = 'null';
        $pPhoneDigits = 'null';
        $pCountryCode = 'null';
        $pCode = 'null';
        $pType = 'null';
        $pEntity = 'null';

        if ($phoneId != null) $pPhoneId = $dataHelper->convertDataString($phoneId);
        if ($phoneDigits != null) $pPhoneDigits = $dataHelper->convertDataString($phoneDigits);
        if ($countryCode != null) $pCountryCode = $dataHelper->convertDataString($countryCode);
        if ($code != null) $pCode = $dataHelper->convertDataString($code);
        if ($type != null) $pType = $dataHelper->convertDataString($type);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);


        $sql = "CALL add_phone(" .
            $pPhoneId .
            ', ' . $pPhoneDigits .
            ', ' . $pCountryCode .
            ', ' . $pCode .
            ', ' . $pType .
            ', ' . $pEntity .
            ")";

        // Perform Query
        $conString = $dbHelper->initializeConnection();
        $sqlQuery = mysql_query($sql);

        //error checking
        if (mysql_errno()){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = mysql_error();
        }else{
            $resArray = array();
            while ($row = mysql_fetch_object($sqlQuery)) {
                array_push($resArray, $row);
            }
            $resultRows = count($resArray);
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = $resultRows;
            $jsonObject->Data = $resArray;
            $jsonObject->Error = false;
            $jsonObject->ErrorDesc = null;
        }
        $dbHelper->dbDisconnect();
        return $jsonObject;
    }

    public function deletePhone(
        $phoneId
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $phoneId == null
        ){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = 'Parameters Required';
            echo json_encode($jsonObject);
            return;
        }

        $pPhoneId = 'null';

        if ($phoneId != null) $pPhoneId = $dataHelper->convertDataString($phoneId);

        $sql = "CALL delete_phone("
                . $pPhoneId .
            ")";

        // Perform Query
        $conString = $dbHelper->initializeConnection();
        $sqlQuery = mysql_query($sql);

        //error checking
        if (mysql_errno()){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = mysql_error();
        }else{
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = null;
            $jsonObject->Error = false;
            $jsonObject->ErrorDesc = null;
        }
        $dbHelper->dbDisconnect();
        return $jsonObject;

    }

    public function getPhone(
        $phoneId = null
        , $countryCode = null
        , $code = null
        , $type = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pPhoneId = 'null';
        $pCountryCode = 'null';
        $pCode = 'null';
        $pType = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($phoneId != null) $pPhoneId = $dataHelper->convertDataString($phoneId);
        if ($countryCode != null) $pCountryCode = $dataHelper->convertDataString($countryCode);
        if ($code != null) $pCode = $dataHelper->convertDataString($code);
        if ($type != null) $pType = $dataHelper->convertDataString($type);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_phone(" .
            $pPhoneId .
            ', ' . $pCountryCode .
            ', ' . $pCode .
            ', ' . $pType .
            ', ' . $pPageSize .
            ', ' . $pSkipSize .
            ")";

        $conString = $dbHelper->initializeConnection();
        $sqlQuery = mysql_query($sql);

        //error checking
        if (mysql_errno()){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = mysql_error();
        }else{
            $resArray = array();
            while ($row = mysql_fetch_object($sqlQuery)) {
                array_push($resArray, $row);
            }
            $resultRows = count($resArray);
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = $resultRows;
            $jsonObject->Data = $resArray;
            $jsonObject->Error = false;
            $jsonObject->ErrorDesc = null;
        }
        $dbHelper->dbDisconnect();
        return $jsonObject;
    }

    public function updatePhone(
        $phoneId
        , $phoneDigits = null
        , $countryCode = null
        , $code = null
        , $type = null
        , $entity = null
    ){


        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $phoneId == null
        ){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = 'Parameters Required';
            echo json_encode($jsonObject);
            return;
        }

        $pPhoneId = 'null';
        $pPhoneDigits = 'null';
        $pCountryCode = 'null';
        $pCode = 'null';
        $pType = 'null';
        $pEntity = 'null';

        if ($phoneId != null) $pPhoneId = $dataHelper->convertDataString($phoneId);
        if ($phoneDigits != null) $pPhoneDigits = $dataHelper->convertDataString($phoneDigits);
        if ($countryCode != null) $pCountryCode = $dataHelper->convertDataString($countryCode);
        if ($code != null) $pCode = $dataHelper->convertDataString($code);
        if ($type != null) $pType = $dataHelper->convertDataString($type);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);

        $sql = "CALL update_phone(" .
            $pPhoneId .
            ', ' . $pPhoneDigits .
            ', ' . $pCountryCode .
            ', ' . $pCode .
            ', ' . $pType .
            ', ' . $pEntity .
            ")";

        // Perform Query
        $conString = $dbHelper->initializeConnection();
        $sqlQuery = mysql_query($sql);

        //error checking
        if (mysql_errno()){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = mysql_error();
        }else{
            $resArray = array();
            while ($row = mysql_fetch_object($sqlQuery)) {
                array_push($resArray, $row);
            }
            $resultRows = count($resArray);
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = $resultRows;
            $jsonObject->Data = $resArray;
            $jsonObject->Error = false;
            $jsonObject->ErrorDesc = null;
        }
        $dbHelper->dbDisconnect();
        return $jsonObject;
    }
}
?>
