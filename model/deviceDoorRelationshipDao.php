<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class deviceDoorRelationshipDao{

    public function addDeviceDoorRelationship(
        $device = null
        , $door = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pDeviceDoorRelationshipId = 'null';
        $pDevice = 'null';
        $pDoor = 'null';

        if ($device != null) $pDevice = $dataHelper->convertDataString($device);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);

        $sql = "CALL add_device_door_relationship(" .
            $pDeviceDoorRelationshipId .
            ', ' . $pDevice .
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

    public function deleteDeviceDoorRelationship(
        $deviceDoorRelationshipId
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $deviceDoorRelationshipId == null
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

        $pDeviceDoorRelationshipId = 'null';

        if ($deviceDoorRelationshipId != null) $pDeviceDoorRelationshipId = $dataHelper->convertDataInt($deviceDoorRelationshipId);

        $sql = "CALL delete_device_door_relationship("
                . $pDeviceDoorRelationshipId .
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

    public function getDeviceDoorRelationship(
        $deviceDoorRelationshipId = null
        , $device = null
        , $door = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pDeviceDoorRelationshipId = 'null';
        $pDevice = 'null';
        $pDoor = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($deviceDoorRelationshipId != null) $pDeviceDoorRelationshipId = $dataHelper->convertDataInt($deviceDoorRelationshipId);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);
        if ($device != null) $pDevice = $dataHelper->convertDataString($device);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_door_device_relationship(" .
            $pDeviceDoorRelationshipId .
            ', ' . $pDoor .
            ', ' . $pDevice .
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

    public function updateDeviceDoorRelationship(
        $deviceDoorRelationshipId
        , $door = null
        , $device = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $deviceDoorRelationshipId == null
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

        $pDeviceDoorRelationshipId = 'null';
        $pDevice = 'null';
        $pDoor = 'null';

        if ($deviceDoorRelationshipId != null) $pDeviceDoorRelationshipId = $dataHelper->convertDataInt($deviceDoorRelationshipId);
        if ($device != null) $pDevice = $dataHelper->convertDataString($device);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);

        $sql = "CALL update_door_device_relationship(" .
                $pDeviceDoorRelationshipId .
                ', ' . $pDoor .
                ', ' . $pDevice .
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
