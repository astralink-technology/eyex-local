<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class eventDao{
    public function addEvent(
        $eventTypeId = null
        , $accessMethod = null
        , $createDate = null
        , $door = null
        , $device = null
        , $entity = null
        , $doorName = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pEventTypeId = 'null';
        $pAccessMethod = 'null';
        $pCreateDate = 'null';
        $pDoor = 'null';
        $pDevice = 'null';
        $pEntity = 'null';
        $pDoorName = 'null';

        if ($eventTypeId != null) $pEventTypeId = $dataHelper->convertDataString($eventTypeId);
        if ($accessMethod != null) $pAccessMethod = $dataHelper->convertDataString($accessMethod);
        if ($createDate != null) $pCreateDate = $dataHelper->convertDataString($createDate);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);
        if ($device != null) $pDevice = $dataHelper->convertDataString($device);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);
        if ($doorName != null) $pDoorName = $dataHelper->convertDataString($doorName);

        $sql = "CALL add_event(" .
            $pEventTypeId .
            ', ' . $pAccessMethod .
            ', ' . $pCreateDate .
            ', ' . $pDoor .
            ', ' . $pDevice .
            ', ' . $pEntity .
            ', ' . $pDoorName .
            ")";

        // Perform Query
        $conString = $dbHelper->initializeConnection();
        $sqlQuery = mysql_query($sql);

        //error checking
        if (mysql_errno()) {
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = mysql_error();
        } else {
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

    public function deleteEvent(){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $sql = "CALL delete_event()";

        // Perform Query
        $conString = $dbHelper->initializeConnection();
        $sqlQuery = mysql_query($sql);

        //error checking
        if (mysql_errno()) {
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = mysql_error();
        } else {
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = null;
            $jsonObject->Error = false;
            $jsonObject->ErrorDesc = null;
        }
        $dbHelper->dbDisconnect();
        return $jsonObject;
    }

    public function getEvent(
        $eventId = null
        , $eventTypeId = null
        , $accessMethod = null
        , $door = null
        , $device = null
        , $entity = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pEventId = 'null';
        $pEventTypeId = 'null';
        $pAccessMethod = 'null';
        $pDoor = 'null';
        $pDevice = 'null';
        $pEntity = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($eventId != null) $pEventId = $dataHelper->convertDataInt($eventId);
        if ($eventTypeId != null) $pEventTypeId = $dataHelper->convertDataString($eventTypeId);
        if ($accessMethod != null) $pAccessMethod = $dataHelper->convertDataString($accessMethod);
        if ($door != null) $pDoor = $dataHelper->convertDataString($door);
        if ($device != null) $pDevice = $dataHelper->convertDataString($device);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_event(" .
            $pEventId .
            ', ' . $pEventTypeId .
            ', ' . $pAccessMethod .
            ', ' . $pDoor .
            ', ' . $pDevice .
            ', ' . $pEntity .
            ', ' . $pPageSize .
            ', ' . $pSkipSize .
            ")";

        $conString = $dbHelper->initializeConnection();
        $sqlQuery = mysql_query($sql);

        //error checking
        if (mysql_errno()) {
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = mysql_error();
        } else {
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
