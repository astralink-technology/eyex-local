<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class extensionDao{
    public function addExtension(
        $extensionId
        , $number = null
        , $extensionPassword = null
        , $entity = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pExtensionId = 'null';
        $pNumber = 'null';
        $pExtensionPassword = 'null';
        $pEntity = 'null';

        if ($extensionId != null) $pExtensionId = $dataHelper->convertDataString($extensionId);
        if ($number != null) $pNumber = $dataHelper->convertDataInt($number);
        if ($extensionPassword != null) $pExtensionPassword = $dataHelper->convertDataString($entity);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);

        $sql = "CALL add_extension(" .
            $pExtensionId .
            ', ' . $pNumber .
            ', ' . $pExtensionPassword .
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

    public function deleteExtension(
        $extensionId
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $extensionId == null
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

        $pExtensionId = 'null';

        if ($extensionId != null) $pExtensionId = $dataHelper->convertDataString($extensionId);

        $sql = "CALL delete_extension("
                . $pExtensionId .
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

    public function getExtension(
        $extensionId = null
        , $number = null
        , $entity = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pExtensionId = 'null';
        $pNumber = 'null';
        $pEntity = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($extensionId != null) $pExtensionId = $dataHelper->convertDataString($extensionId);
        if ($number != null) $pNumber = $dataHelper->convertDataInt($number);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_extension(" .
            $pExtensionId .
            ', ' . $pNumber .
            ', ' . $pEntity .
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

    public function updateExtension(
        $extensionId
        , $number = null
        , $extensionPassword = null
        , $entity = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $extensionId == null
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

        $pExtensionId = 'null';
        $pNumber = 'null';
        $pExtensionPassword = 'null';
        $pEntity = 'null';

        if ($extensionId != null) $pExtensionId = $dataHelper->convertDataString($extensionId);
        if ($number != null) $pNumber = $dataHelper->convertDataInt($number);
        if ($extensionPassword != null) $pExtensionPassword = $dataHelper->convertDataString($entity);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);

        $sql = "CALL update_extension(" .
                $pExtensionId .
                ', ' . $pNumber .
                ', ' . $pExtensionPassword .
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
