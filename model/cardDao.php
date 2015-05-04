<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class cardDao{

    public function addCard(
        $cardId
        , $cardSerial = null
        , $entity = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pCardId = 'null';
        $pCardSerial = 'null';
        $pEntity = 'null';

        if ($cardId != null) $pCardId = $dataHelper->convertDataString($cardId);
        if ($cardSerial != null) $pCardSerial = $dataHelper->convertDataString($cardSerial);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);

        $sql = "CALL add_card(" .
            $pCardId .
            ', ' . $pCardSerial .
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

    public function deleteCard(
        $cardId
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $cardId == null
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

        $pCardId = 'null';

        if ($cardId != null) $pCardId = $dataHelper->convertDataString($cardId);

        $sql = "CALL delete_card("
                . $pCardId .
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

public function getCard(
        $cardId = null
        , $cardSerial = null
        , $entity = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pCardId = 'null';
        $pCardSerial = 'null';
        $pEntity = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($cardId != null) $pCardId = $dataHelper->convertDataString($cardId);
        if ($cardSerial != null) $pCardSerial = $dataHelper->convertDataString($cardSerial);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_card(" .
            $pCardId .
            ', ' . $pCardSerial .
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

    public function updateCard(
        $cardId
        , $cardSerial = null
        , $entity = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $cardId == null
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

        $pCardId = 'null';
        $pCardSerial = 'null';
        $pEntity = 'null';

        if ($cardId != null) $pCardId = $dataHelper->convertDataString($cardId);
        if ($cardSerial != null) $pCardSerial = $dataHelper->convertDataString($cardSerial);
        if ($entity != null) $pEntity = $dataHelper->convertDataString($entity);

        $sql = "CALL update_card(" .
                $pCardId .
                ', ' . $pCardSerial .
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
