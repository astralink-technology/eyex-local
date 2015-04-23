<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/data_helper.php';

class featuresDao{
    public function addFeatures(
        $featuresId
        , $remoteDoorControl = null
        , $localDoorControl = null
        , $voicemailPassword = null
        , $voicemailExtension = null
        , $pickup = null
        , $extra1 = null
        , $extra2 = null
        , $extra3 = null
        , $extra4 = null
        , $device = null
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pFeaturesId = 'null';
        $pRemoteDoorControl = 'null';
        $pLocalDoorControl = 'null';
        $pVoicemailPassword = 'null';
        $pVoicemailExtension = 'null';
        $pPickup = 'null';
        $pExtra1 = 'null';
        $pExtra2 = 'null';
        $pExtra3 = 'null';
        $pExtra4 = 'null';
        $pDevice = 'null';

        if ($featuresId != null) $pFeaturesId = $dataHelper->convertDataString($featuresId);
        if ($remoteDoorControl != null) $pRemoteDoorControl = $dataHelper->convertDataString($remoteDoorControl);
        if ($localDoorControl != null) $pLocalDoorControl = $dataHelper->convertDataString($localDoorControl);
        if ($voicemailPassword != null) $pVoicemailPassword = $dataHelper->convertDataString($voicemailPassword);
        if ($voicemailExtension != null) $pVoicemailExtension = $dataHelper->convertDataString($voicemailExtension);
        if ($pickup != null) $pPickup  = $dataHelper->convertDataString($pickup);
        if ($extra1 != null) $pExtra1 = $dataHelper->convertDataString($extra1);
        if ($extra2 != null) $pExtra2 = $dataHelper->convertDataString($extra2);
        if ($extra3 != null) $pExtra3 = $dataHelper->convertDataString($extra3);
        if ($extra4 != null) $pExtra4 = $dataHelper->convertDataString($extra4);
        if ($device != null) $pDevice = $dataHelper->convertDataString($device);


        $sql = "CALL add_features(" .
            $pFeaturesId .
            ', ' . $pRemoteDoorControl .
            ', ' . $pLocalDoorControl .
            ', ' . $pVoicemailPassword .
            ', ' . $pVoicemailExtension .
            ', ' . $pPickup .
            ', ' . $pExtra1 .
            ', ' . $pExtra2 .
            ', ' . $pExtra3 .
            ', ' . $pExtra4 .
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

    public function deleteFeatures(
        $featuresId
    ){
        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $featuresId == null
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

        $pFeaturesId = 'null';

        if ($featuresId != null) $pFeaturesId = $dataHelper->convertDataString($featuresId);

        $sql = "CALL delete_features("
                . $pFeaturesId .
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

    public function getFeatures(
        $featuresId = null
        , $remoteDoorControl = null
        , $localDoorControl = null
        , $voicemailExtension = null
        , $device = null
        , $pageSize = null
        , $skipSize = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        $pFeaturesId = 'null';
        $pRemoteDoorControl = 'null';
        $pLocalDoorControl = 'null';
        $pVoicemailExtension = 'null';
        $pDevice = 'null';
        $pPageSize = 'null';
        $pSkipSize = 'null';

        if ($featuresId != null) $pFeaturesId = $dataHelper->convertDataString($featuresId);
        if ($remoteDoorControl != null) $pRemoteDoorControl = $dataHelper->convertDataString($remoteDoorControl);
        if ($localDoorControl != null) $pLocalDoorControl = $dataHelper->convertDataString($localDoorControl);
        if ($voicemailExtension != null) $pVoicemailExtension= $dataHelper->convertDataString($voicemailExtension);
        if ($device != null) $pDevice = $dataHelper->convertDataString($device);
        if ($pageSize != null) $pPageSize = $dataHelper->convertDataInt($pageSize);
        if ($skipSize != null) $pSkipSize = $dataHelper->convertDataInt($skipSize);

        $sql = "CALL get_features(" .
            $pFeaturesId .
            ', ' . $pRemoteDoorControl .
            ', ' . $pLocalDoorControl .
            ', ' . $pVoicemailExtension .
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
            //parse profile images
            foreach ($resArray as $employee) {
                $employee->profile_picture = null;
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/' . $employee->id . '/profile-pic.jpg')){
                    $employee->profile_picture = '../../../data/' . $employee->id . '/profile-pic.jpg';
                }
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

    public function updateFeatures(
        $featuresId
        , $remoteDoorControl = null
        , $localDoorControl = null
        , $voicemailExtension = null
        , $voicemailPassword = null
        , $pickup = null
        , $extra1 = null
        , $extra2 = null
        , $extra3 = null
        , $extra4 = null
        , $device = null
    ){

        $dataHelper = new cp_data_helper();
        $dbHelper = new cp_sqlConnection_helper();

        if (
            $featuresId == null
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

        $pFeaturesId = 'null';
        $pRemoteDoorControl = 'null';
        $pLocalDoorControl = 'null';
        $pVoicemailExtension = 'null';
        $pVoicemailPassword = 'null';
        $pPickup = 'null';
        $pExtra1 = 'null';
        $pExtra2 = 'null';
        $pExtra3 = 'null';
        $pExtra4 = 'null';
        $pDevice = 'null';

        if ($featuresId != null) $pFeaturesId = $dataHelper->convertDataString($featuresId);
        if ($remoteDoorControl != null) $pRemoteDoorControl = $dataHelper->convertDataString($remoteDoorControl);
        if ($localDoorControl != null) $pLocalDoorControl = $dataHelper->convertDataString($localDoorControl);
        if ($voicemailExtension != null) $pVoicemailExtension = $dataHelper->convertDataString($voicemailExtension);
        if ($voicemailPassword != null) $pVoicemailPassword = $dataHelper->convertDataString($voicemailPassword);
        if ($pickup != null) $pPickup = $dataHelper->convertDataString($pickup);
        if ($extra1 != null) $pExtra1 = $dataHelper->convertDataString($extra1);
        if ($extra2 != null) $pExtra2 = $dataHelper->convertDataString($extra2);
        if ($extra3 != null) $pExtra3 = $dataHelper->convertDataString($extra3);
        if ($extra4 != null) $pExtra4 = $dataHelper->convertDataString($extra4);
        if ($device != null) $pDevice = $dataHelper->convertDataString($device);

        $sql = "CALL update_features(" .
                $pFeaturesId .
                ', ' . $pRemoteDoorControl .
                ', ' . $pLocalDoorControl .
                ', ' . $pVoicemailExtension .
                ', ' . $pVoicemailPassword .
                ', ' . $pPickup .
                ', ' . $pExtra1 .
                ', ' . $pExtra2 .
                ', ' . $pExtra3 .
                ', ' . $pExtra4 .
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
