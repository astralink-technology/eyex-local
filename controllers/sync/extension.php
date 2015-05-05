<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/extensionDao.php';
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

    // Get the device ID for now
     $deviceId = '90a783017007';

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

    //get the extensions from remote
    $getRemoteExtensionParms = http_build_query(array(
        'DeviceId' => $deviceId
        , 'AppId' => $appId
        , 'Token' => $token
    ));
    $resGetRemoteExtension = $curlHelper->curlGet('/meyex/extension/getSyncExtension', $getRemoteExtensionParms);
    $resGetRemoteExtensionData = $resGetRemoteExtension->Data; //object, please encode it later on

    //get the extensions from local
    $extensionDb = new extensionDao();
    $resGetLocalExtension = $extensionDb->getExtension();
    $resGetLocalExtensionData = $resGetLocalExtension->Data; //object, please encode it later on

    //compare remote and local and sync (Add / Update)
    foreach ($resGetRemoteExtensionData as &$remoteExtension) {
        //remote variables
        $remoteExtensionId = $remoteExtension->_id;
        $remoteExtensionNumber = $remoteExtension->number;
        $remoteExtensionPassword = $remoteExtension->extension_password;
        $remoteEmployee = '';
        if (isset($remoteExtension->employee)) $remoteEmployee = $remoteExtension->employee->_id;

        //local variables
        $localExtensionId = null;
        $localExtensionNumber = null;
        $localExtensionPassword = null;
        $localExtensionEmployee = null;

        //sync flags
        $update = false;
        $add = true;

        foreach($resGetLocalExtensionData as &$localExtension){
            $localExtensionId = $localExtension->_id;
            $localExtensionNumber = $localExtension->number;
            $localExtensionPassword = $localExtension->extension_password;
            $localExtensionEmployee = $localExtension->entity;

            //if both has, update
            if ($remoteExtensionId == $localExtensionId){
                $add = false;
                $update = true;
            }
        }

        //execute add
        if ($add == true){
            $addCount += 1;
            $resAddExtension = $extensionDb->addExtension(
                $remoteExtensionId
                , $remoteExtensionNumber
                , $remoteExtensionPassword
                , $remoteEmployee
            );
            if ($resAddExtension->Error == true){
                echo json_encode($resAddExtension);
                return;
            }
        }

        //execute update
        if ($update == true){
            $updateCount += 1;
            $resUpdateExtension = $extensionDb->updateExtension(
                $remoteExtensionId
                , $remoteExtensionNumber
                , $remoteExtensionPassword
                , $remoteEmployee
            );
            if ($resUpdateExtension->Error == true){
                echo json_encode($resUpdateExtension);
                return;
            }
        }
    }

    //get the extensions from local Again for delete check
    $resGetLocalExtensionForDel = $extensionDb->getExtension();
    $resGetLocalExtensionForDelData = $resGetLocalExtensionForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalExtensionForDelData as &$localExtensionDel){

        //local variables
        $localExtensionDelId = $localExtensionDel->_id;

        //sync flags
        $delete = true;

        if ($resGetRemoteExtensionData){
            foreach ($resGetRemoteExtensionData as &$remoteExtensionDel) {
                //remote variables
                $remoteExtensionDelId = $remoteExtensionDel->_id;
                if ($localExtensionDelId == $remoteExtensionDelId){
                    $delete = false;
                }
            }
            //execute delete
            if ($delete == true){
                $deleteCount += 1;
                $resDeleteExtension = $extensionDb->deleteExtension($localExtensionDelId);
                if ($resDeleteExtension->Error == true){
                    echo json_encode($resDeleteExtension);
                    return;
                }
            }
        }else{
            //execute cleanup delete
            $deleteCount += 1;
            $resDeleteExtension = $extensionDb->deleteExtension($localExtensionDelId);
            if ($resDeleteExtension->Error == true){
                echo json_encode($resDeleteExtension);
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