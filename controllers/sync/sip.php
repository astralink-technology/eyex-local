<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/eyex_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/sipDao.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/config/webConfig.php';

    //app ID and token
    $webConfig = new webconfig();
    $webConfigObj = $webConfig->webconfig();
    $appId = $webConfigObj->appId;
    $token = $webConfigObj->token;

    //return variables
    $addCount = 0;
    $updateCount = 0;
    $deleteCount = 0;

    //instantiate helpers
    $curlHelper = new cp_curl_helper();
    $eyexHelper = new cp_eyex_helper();

    // Get the device ID for now
     $deviceId = $eyexHelper->getDeviceId();

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

    //get the sip from remote
    $getRemoteSipParms = http_build_query(array(
        'DeviceId' => $deviceId
        , 'AppId' => $appId
        , 'Token' => $token
    ));

    $resGetRemoteSip = $curlHelper->curlGet('/meyex/sip/getSyncSip', $getRemoteSipParms);
    $resGetRemoteSipData = $resGetRemoteSip->Data; //object, please encode it later on

    //get the sip from local
    $sipDb = new sipDao();
    $resGetLocalSip = $sipDb->getSip();
    $resGetLocalSipData = $resGetLocalSip->Data; //object, please encode it later on

    //compare remote and local and sync (Add / Update)
    foreach ($resGetRemoteSipData as &$remoteSip) {
        //remote variables
        $remoteSipId = $remoteSip->_id;
        $remoteSipHost = $remoteSip->host;
        $remoteSipUsername = $remoteSip->username;
        $remoteSipPassword = $remoteSip->password;

        //local variables
        $localSipId = null;
        $localSipHost = null;
        $localSipUsername = null;
        $localSipPassword = null;

        //sync flags
        $update = false;
        $add = true;

        foreach($resGetLocalSipData as &$localSip){
            $localSipId = $localSip->_id;
            $localSipHost = $localSip->host;
            $localSipUsername = $localSip->username;
            $localSipPassword = $localSip->password;

            //if both has, update
            if ($remoteSipId == $localSipId){
                $add = false;
                $update = true;
            }
        }

        //execute add
        if ($add == true){
            $addCount += 1;
            $resAddSip = $sipDb->addSip(
                $remoteSipId
                , $remoteSipUsername
                , $remoteSipPassword
                , $remoteSipHost
            );

            if ($resAddSip->Error == true){
                echo json_encode($resAddSip);
                return;
            }
        }

        //execute update
        if ($update == true){
            $updateCount += 1;
            $resUpdateSip = $sipDb->updateSip(
                $remoteSipId
                , $remoteSipUsername
                , $remoteSipPassword
                , $remoteSipHost
            );
            if ($resUpdateSip->Error == true){
                echo json_encode($resUpdateSip);
                return;
            }
        }
    }

    //get the phone from local again for delete check
    $resGetLocalSipForDel = $sipDb->getSip();
    $resGetLocalSipForDelData = $resGetLocalSipForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalSipForDelData as &$localSipDel){

        //local variables
        $localSipDelId = $localSipDel->_id;

        //sync flags
        $delete = true;

        //loop through all the phone ids
        if ($resGetRemoteSipData) {
            foreach ($resGetRemoteSipData as &$remoteSipDel) {
                //remote variables
                $remoteSipDelId = $remoteSipDel->_id;
                if ($localSipDelId == $remoteSipDelId) {
                    $delete = false;
                }
            }

            //execute delete
            if ($delete == true){
                $deleteCount += 1;
                $resDeleteSip = $sipDb->deleteSip($localSipId);
                if ($resDeleteSip->Error == true){
                    echo json_encode($resDeleteSip);
                    return;
                }
            }
        }else{
            //execute clean up delete
            $deleteCount += 1;
            $resDeleteSip = $sipDb->deleteSip($localSipId);
            if ($resDeleteSip->Error == true){
                echo json_encode($resDeleteSip);
                return;
            }
        }

    }

    //finally return
    $counter = new stdClass();
    $counter->added = $addCount;
    $counter->updated = $updateCount;
    $counter->deleted = $deleteCount;

    $jsonObject = new stdClass();
    $jsonObject->RowsReturned = 1;
    $jsonObject->Data = [$counter];
    $jsonObject->Error = false;
    $jsonObject->ErrorDesc = null;

    echo json_encode($jsonObject);
    return;
?>