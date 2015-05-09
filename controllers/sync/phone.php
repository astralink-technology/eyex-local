<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/eyex_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/phoneDao.php';
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

    //get the feature from remote
    $resGetRemotePhoneParms = http_build_query(array(
        'DeviceId' => $deviceId
        , 'AppId' => $appId
        , 'Token' => $token
    ));
    $resGetRemotePhone = $curlHelper->curlGet('/meyex/phone/getSyncPhone', $resGetRemotePhoneParms);
    $resGetRemotePhoneData = $resGetRemotePhone->Data; //object, please encode it later on

    //get the phone from local
    $phoneDb = new phoneDao();
    $resGetLocalPhone = $phoneDb->getPhone();
    $resGetLocalPhoneData = $resGetLocalPhone->Data; //object, please encode it later on

    //compare remote and local and sync (Add / Update)
    foreach ($resGetRemotePhoneData as &$remotePhone) {
        //remote variables
        $remotePhoneId = $remotePhone->_id;
        $remotePhonePhoneDigits = $remotePhone->phone_digits;
        $remotePhoneCountryCode = $remotePhone->country_code;
        $remotePhoneCode = $remotePhone->code;
        $remotePhoneType = $remotePhone->type;
        $remotePhoneEntity = $remotePhone->entity;

        //local variables
        $localPhoneId = null;
        $localPhonePhoneDigits = null;
        $localPhoneCountryCode = null;
        $localPhoneCode = null;
        $localPhoneType = null;
        $localPhoneEntity = null;

        //sync flags
        $update = false;
        $add = true;

        foreach($resGetLocalPhoneData as &$localPhone){
            $localPhoneId = $localPhone->_id;
            $localPhonePhoneDigits = $localPhone->phone_digits;
            $localPhoneCountryCode = $localPhone->country_code;
            $localPhoneCode = $localPhone->code;
            $localPhoneType = $localPhone->type;
            $localPhoneEntity = $localPhone->entity;

            //if both has, update
            if ($remotePhoneId == $localPhoneId){
                $add = false;
                $update = true;
            }
        }

        //execute add
        if ($add == true){
            $addCount += 1;
            $resAddPhone = $phoneDb->addPhone(
                $remotePhoneId
                , $remotePhonePhoneDigits
                , $remotePhoneCountryCode
                , $remotePhoneCode
                , $remotePhoneType
                , $remotePhoneEntity
            );

            if ($resAddPhone->Error == true){
                echo json_encode($resAddPhone);
                return;
            }
        }

        //execute update
        if ($update == true){
            $updateCount += 1;
            $resUpdatePhone = $phoneDb->updatePhone(
                $remotePhoneId
                , $remotePhonePhoneDigits
                , $remotePhoneCountryCode
                , $remotePhoneCode
                , $remotePhoneType
                , $remotePhoneEntity
            );
            if ($resUpdatePhone->Error == true){
                echo json_encode($resUpdatePhone);
                return;
            }
        }
    }

    //get the phone from local again for delete check
    $resGetLocalPhoneForDel = $phoneDb->getPhone();
    $resGetLocalPhoneForDelData = $resGetLocalPhoneForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalPhoneForDelData as &$localPhoneDel){

        //local variables
        $localPhoneDelId = $localPhoneDel->_id;

        //sync flags
        $delete = true;

        //loop through all the phone ids
        if ($resGetRemotePhoneData) {
            foreach ($resGetRemotePhoneData as &$remotePhoneDel) {
                //remote variables
                $remotePhoneDelId = $remotePhoneDel->_id;
                if ($localPhoneDelId == $remotePhoneDelId) {
                    $delete = false;
                }
            }

            //execute delete
            if ($delete == true){
                $deleteCount += 1;
                $resDeletePhone = $phoneDb->deletePhone($localPhoneId);
                if ($resDeletePhone->Error == true){
                    echo json_encode($resDeletePhone);
                    return;
                }
            }
        }else{
            //execute clean up delete
            $deleteCount += 1;
            $resDeletePhone = $phoneDb->deletePhone($localPhoneId);
            if ($resDeletePhone->Error == true){
                echo json_encode($resDeletePhone);
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