<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class deviceDao{
    public function addDevice(
        $deviceId
        , $name = null
        , $type = null
        , $type2 = null
        , $intPrefix = null
        , $door = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pDeviceId = 'null';
        $pName = 'null';
        $pType = 'null';
        $pType2 = 'null';
        $pIntPrefix = 'null';
        $pDoor = 'null';

        if ($deviceId != null) $pDeviceId = $dataHelper->convertDataString($deviceId);
        if ($name != null) $pName = $dataHelper->convertDataString($name);
        if ($type != null) $pType = $dataHelper->convertDataString($type);
        if ($type2 != null) $pType2 = $dataHelper->convertDataString($type2);
        if ($intPrefix != null) $pIntPrefix = $dataHelper->convertDataInt($intPrefix);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);

        $sql = "CALL add_device(" .
            $pDeviceId .
            ', ' . $pName .
            ', ' . $pType .
            ', ' . $pType2 .
            ', ' . $pIntPrefix .
            ', ' . $pDoor .
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

    public function deleteDevice(
        $deviceId
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $deviceId == null
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

        $pDeviceId = 'null';

        if ($deviceId != null) $pDeviceId = $dataHelper->convertDataString($deviceId);

        $sql = "CALL delete_device("
                . $pDeviceId .
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

    public function getDevice(
        $deviceId = null
        , $name = null
        , $type = null
        , $type2 = null
        , $intPrefix = null
        , $door = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pDeviceId = 'null';
        $pName = 'null';
        $pType = 'null';
        $pType2 = 'null';
        $pIntPrefix = 'null';
        $pDoor = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($deviceId != null) $pDeviceId = $dataHelper->convertDataString($deviceId);
        if ($name != null) $pName = $dataHelper->convertDataString($name);
        if ($type != null) $pType = $dataHelper->convertDataString($type);
        if ($type2 != null) $pType2 = $dataHelper->convertDataString($type2);
        if ($intPrefix != null) $pIntPrefix = $dataHelper->convertDataInt($intPrefix);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_device(" .
            $pDeviceId .
            ', ' . $pName .
            ', ' . $pType .
            ', ' . $pType2 .
            ', ' . $pIntPrefix .
            ', ' . $pDoor .
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

    public function updateDevice(
        $deviceId
        , $name = null
        , $type = null
        , $type2 = null
        , $intPrefix = null
        , $door = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $deviceId == null
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

        $pDeviceId = 'null';
        $pName = 'null';
        $pType = 'null';
        $pType2 = 'null';
        $pIntPrefix = 'null';
        $pDoor = 'null';

        if ($deviceId != null) $pDeviceId = $dataHelper->convertDataString($deviceId);
        if ($name != null) $pName = $dataHelper->convertDataString($name);
        if ($type != null) $pType = $dataHelper->convertDataString($type);
        if ($type2 != null) $pType2 = $dataHelper->convertDataString($type2);
        if ($intPrefix != null) $pIntPrefix = $dataHelper->convertDataInt($intPrefix);
        if ($door != null || $door == '') $pDoor = $dataHelper->convertDataString($door);

        $sql = "CALL update_device(" .
                $pDeviceId .
                ', ' . $pName .
                ', ' . $pType .
                ', ' . $pType2 .
                ', ' . $pIntPrefix .
                ', ' . $pDoor .
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
