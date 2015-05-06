<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class entityDao{
    public function addEntity(
        $entityId
        , $authenticationString = null
        , $authenticationStringLower = null
        , $firstName = null
        , $lastName = null
        , $name = null
        , $extension = null
        , $card = null
        , $pin = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pEntityId = 'null';
        $pAuthenticationString = 'null';
        $pAuthenticationStringLower = 'null';
        $pFirstName = 'null';
        $pLastName = 'null';
        $pName = 'null';
        $pExtension = 'null';
        $pCard = 'null';
        $pPin = 'null';

        if ($entityId != null) $pEntityId = $dataHelper->convertDataString($entityId);
        if ($authenticationString != null) $pAuthenticationString = $dataHelper->convertDataString($authenticationString);
        if ($authenticationStringLower != null) $pAuthenticationStringLower = $dataHelper->convertDataString($authenticationStringLower);
        if ($firstName != null) $pFirstName = $dataHelper->convertDataString($firstName);
        if ($lastName != null) $pLastName = $dataHelper->convertDataString($lastName);
        if ($name != null) $pName = $dataHelper->convertDataString($name);
        if ($extension != null) $pExtension = $dataHelper->convertDataString($extension);
        if ($card != null) $pCard = $dataHelper->convertDataString($card);
        if ($pin != null) $pPin = $dataHelper->convertDataString($pin);


        $sql = "CALL add_entity(" .
            $pEntityId .
            ', ' . $pAuthenticationString .
            ', ' . $pAuthenticationStringLower .
            ', ' . $pFirstName .
            ', ' . $pLastName .
            ', ' . $pName .
            ', ' . $pExtension .
            ', ' . $pCard .
            ', ' . $pPin .
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

    public function deleteEntity(
        $entityId
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $entityId == null
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

        $pEntityId = 'null';

        if ($entityId != null) $pEntityId = $dataHelper->convertDataString($entityId);

        $sql = "CALL delete_entity("
                . $pEntityId .
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

    public function getEntity(
        $entityId = null
        , $pin = null
        , $card = null
        , $extension = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pEntityId = 'null';
        $pPin = 'null';
        $pCard = 'null';
        $pExtension = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($entityId != null) $pEntityId = $dataHelper->convertDataString($entityId);
        if ($pin != null) $pPin = $dataHelper->convertDataString($pin);
        if ($card != null) $pCard = $dataHelper->convertDataString($card);
        if ($extension != null) $pExtension = $dataHelper->convertDataString($extension);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_entity(" .
            $pEntityId .
            ', ' . $pPin .
            ', ' . $pCard .
            ', ' . $pExtension .
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

    public function updateEntity(
        $entityId
        , $authenticationString = null
        , $authenticationStringLower = null
        , $firstName = null
        , $lastName = null
        , $name = null
        , $extension = null
        , $card = null
        , $pin = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $entityId == null
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

        $pEntityId = 'null';
        $pAuthenticationString = 'null';
        $pAuthenticationStringLower = 'null';
        $pFirstName = 'null';
        $pLastName = 'null';
        $pName = 'null';
        $pExtension = 'null';
        $pCard = 'null';
        $pPin = 'null';

        if ($entityId != null) $pEntityId = $dataHelper->convertDataString($entityId);
        if ($authenticationString != null) $pAuthenticationString = $dataHelper->convertDataString($authenticationString);
        if ($authenticationStringLower != null) $pAuthenticationStringLower = $dataHelper->convertDataString($authenticationStringLower);
        if ($firstName != null) $pFirstName = $dataHelper->convertDataString($firstName);
        if ($lastName != null) $pLastName = $dataHelper->convertDataString($lastName);
        if ($name != null) $pName = $dataHelper->convertDataString($name);
        if ($extension != null) $pExtension = $dataHelper->convertDataString($extension);
        if ($card != null) $pCard = $dataHelper->convertDataString($card);
        if ($pin != null) $pPin = $dataHelper->convertDataString($pin);

        $sql = "CALL update_entity(" .
                $pEntityId .
                ', ' . $pAuthenticationString .
                ', ' . $pAuthenticationStringLower .
                ', ' . $pFirstName .
                ', ' . $pLastName .
                ', ' . $pName .
                ', ' . $pExtension .
                ', ' . $pCard .
                ', ' . $pPin .
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
