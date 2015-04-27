<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class entityDoorRelationshipDao{
    public function addEntityDoorRelationship(
        $entity = null
        , $door = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pEntity = 'null';
        $pDoor = 'null';

        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);

        $sql = "CALL add_entity_door_relationship(" .
            $pEntity .
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

    public function deleteEntityDoorRelationship(
        $entityDoorRelationshipId = null
        , $doorId = null
        , $entityId = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $entityDoorRelationshipId == null
            && $doorId == null
            && $entityId == null
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

        $pEntityDoorRelationshipId = 'null';
        $pEntityId = 'null';
        $pDoorId = 'null';

        if ($entityDoorRelationshipId != null) $pEntityDoorRelationshipId = $dataHelper->convertDataInt($entityDoorRelationshipId);
        if ($doorId != null) $pDoorId = $dataHelper->convertDataString($doorId);
        if ($entityId != null) $pEntityId = $dataHelper->convertDataString($entityId);

        $sql = "CALL delete_entity_door_relationship("
                . $pEntityDoorRelationshipId .
                ', ' . $pEntityId .
                ', ' . $pDoorId .
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

    public function getEntityDoorRelationship(
        $entityDoorRelationshipId = null
        , $entity = null
        , $door = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pEntityDoorRelationshipId = 'null';
        $pEntity = 'null';
        $pDoor = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($entityDoorRelationshipId != null) $pEntityDoorRelationshipId = $dataHelper->convertDataInt($entityDoorRelationshipId);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_entity_door_relationship(" .
            $pEntityDoorRelationshipId .
            ', ' . $pDoor .
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

    public function updateEntityDoorRelationship(
        $entityDoorRelationshipId
        , $door = null
        , $entity = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $entityDoorRelationshipId == null
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

        $pEntityDoorRelationshipId = 'null';
        $pEntity = 'null';
        $pDoor = 'null';

        if ($entityDoorRelationshipId != null) $pEntityDoorRelationshipId = $dataHelper->convertDataInt($entityDoorRelationshipId);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);

        $sql = "CALL update_entity_door_relationship(" .
                $pEntityDoorRelationshipId .
                ', ' . $pDoor .
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
