<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class sipDao{
    public function addSip(
        $sipId
        , $username = null
        , $password = null
        , $host = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pSipId = 'null';
        $pUsername = 'null';
        $pPassword = 'null';
        $pHost = 'null';

        if ($sipId != null) $pSipId = $dataHelper->convertDataString($sipId);
        if ($username != null) $pUsername = $dataHelper->convertDataString($username);
        if ($password != null) $pPassword = $dataHelper->convertDataString($password);
        if ($host != null) $pHost = $dataHelper->convertDataString($host);


        $sql = "CALL add_sip(" .
            $pSipId .
            ', ' . $pUsername .
            ', ' . $pPassword .
            ', ' . $pHost .
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

    public function deleteSip(
        $sipId
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $sipId == null
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

        $pSipId = 'null';

        if ($sipId != null) $pSipId = $dataHelper->convertDataString($sipId);

        $sql = "CALL delete_sip("
                . $pSipId .
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

    public function getSip(
        $sipId = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pSipId = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($sipId != null) $pSipId = $dataHelper->convertDataString($sipId);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_sip(" .
            $pSipId .
            ',' . $pPageSize .
            ',' . $pSkipSize .
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

    public function updateSip(
        $sipId
        , $username = null
        , $password = null
        , $host = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $sipId == null
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

        $pSipId = 'null';
        $pUsername = 'null';
        $pPassword = 'null';
        $pHost = 'null';

        if ($sipId != null) $pSipId = $dataHelper->convertDataString($sipId);
        if ($username != null) $pUsername = $dataHelper->convertDataString($username);
        if ($password != null) $pPassword = $dataHelper->convertDataString($password);
        if ($host != null) $pHost = $dataHelper->convertDataString($host);


        $sql = "CALL update_sip(" .
            $pSipId .
            ', ' . $pUsername .
            ', ' . $pPassword .
            ', ' . $pHost .
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
