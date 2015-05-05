<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class announcementDao{
    public function addAnnouncement(
        $announcementId
        , $message = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pAnnouncementId = 'null';
        $pMessage = 'null';

        if ($announcementId != null) $pAnnouncementId = $dataHelper->convertDataString($announcementId);
        if ($message != null) $pMessage = $dataHelper->convertDataString($message);

        $sql = "CALL add_announcement(" .
            $pAnnouncementId .
            ', ' . $pMessage .
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

    public function deleteAnnouncement()
    {
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $sql = "CALL delete_announcement()";

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

    public function getAnnouncement()
    {
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $sql = "CALL get_announcement()";

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

    public function updateAnnouncement(
        $message
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pMessage = 'null';

        if ($message != null || $message == '') $pMessage = $dataHelper->convertDataString($message);

        $sql = "CALL update_announcement(" .
            $pMessage .
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
