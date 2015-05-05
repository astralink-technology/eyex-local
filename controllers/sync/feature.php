<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/featuresDao.php';
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

    //get the feature from remote
    $resGetRemoteFeatureParms = http_build_query(array(
        'DeviceId' => $deviceId
        , 'AppId' => $appId
        , 'Token' => $token
    ));
    $resGetRemoteFeature = $curlHelper->curlGet('/meyex/feature/getSyncFeature', $resGetRemoteFeatureParms);
    $resGetRemoteFeatureData = $resGetRemoteFeature->Data; //object, please encode it later on

    //get the feature from local
    $featureDb = new featuresDao();
    $resGetLocalFeature = $featureDb->getFeatures();
    $resGetLocalFeatureData = $resGetLocalFeature->Data; //object, please encode it later on

    //compare remote and local and sync (Add / Update)
    foreach ($resGetRemoteFeatureData as &$remoteFeature) {
        //remote variables
        $remoteFeatureId = $remoteFeature->_id;
        $remoteFeatureRemoteDoorControl = $remoteFeature->remote_door_control;
        $remoteFeatureLocalDoorControl = $remoteFeature->local_door_control;
        $remoteFeatureVoicemailPassword = $remoteFeature->voicemail_password;
        $remoteFeatureVoicemailExtension = $remoteFeature->voicemail_extension;
        $remoteFeaturePickup = $remoteFeature->pickup;
        $remoteFeatureExtra1 = $remoteFeature->extra1;
        $remoteFeatureExtra2 = $remoteFeature->extra2;
        $remoteFeatureExtra3 = $remoteFeature->extra3;
        $remoteFeatureExtra4 = $remoteFeature->extra4;
        $remoteFeatureDevice = $remoteFeature->device;

        //local variables
        $localFeatureId = null;
        $localFeatureRemoteDoorControl = null;
        $localFeatureLocalDoorControl = null;
        $localFeatureVoicemailPassword = null;
        $localFeatureVoicemailExtension = null;
        $localFeatureVoicemailPickup = null;
        $localFeatureExtra1 = null;
        $localFeatureExtra2 = null;
        $localFeatureExtra3 = null;
        $localFeatureExtra4 = null;
        $localFeatureDevice = null;

        //sync flags
        $update = false;
        $add = true;

        foreach($resGetLocalFeatureData as &$localFeature){
            $localFeatureId = $localFeature->_id;
            $localFeatureRemoteDoorControl = $localFeature->remote_door_control;
            $localFeatureLocalDoorControl = $localFeature->local_door_control;
            $localFeatureVoicemailPassword = $localFeature->voicemail_password;
            $localFeatureVoicemailExtension = $localFeature->voicemail_extension;
            $localFeaturePickup = $localFeature->pickup;
            $localFeatureExtra1 = $localFeature->extra1;
            $localFeatureExtra2 = $localFeature->extra2;
            $localFeatureExtra3 = $localFeature->extra3;
            $localFeatureExtra4 = $localFeature->extra4;
            $localFeatureDevice = $localFeature->device;

            //if both has, update
            if ($remoteFeatureId == $localFeatureId){
                $add = false;
                $update = true;
            }
        }

        //execute add
        if ($add == true){
            $addCount += 1;
            $resAddFeature = $featureDb->addFeatures(
                $remoteFeatureId
                , $remoteFeatureRemoteDoorControl
                , $remoteFeatureLocalDoorControl
                , $remoteFeatureVoicemailPassword
                , $remoteFeatureVoicemailExtension
                , $remoteFeaturePickup
                , $remoteFeatureExtra1
                , $remoteFeatureExtra2
                , $remoteFeatureExtra3
                , $remoteFeatureExtra4
                , $remoteFeatureDevice
            );

            if ($resAddFeature->Error == true){
                echo json_encode($resAddFeature);
                return;
            }
        }

        //execute update
        if ($update == true){
            $updateCount += 1;
            $resUpdateFeature = $featureDb->updateFeatures(
                $remoteFeatureId
                , $remoteFeatureRemoteDoorControl
                , $remoteFeatureLocalDoorControl
                , $remoteFeatureVoicemailPassword
                , $remoteFeatureVoicemailExtension
                , $remoteFeaturePickup
                , $remoteFeatureExtra1
                , $remoteFeatureExtra2
                , $remoteFeatureExtra3
                , $remoteFeatureExtra4
                , $remoteFeatureDevice
            );
            if ($resUpdateFeature->Error == true){
                echo json_encode($resUpdateFeature);
                return;
            }
        }
    }


    //get the features from local again for delete check
    $resGetLocalFeatureForDel = $featureDb->getFeatures();
    $resGetLocalFeatureForDelData = $resGetLocalFeatureForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalFeatureForDelData as &$localFeatureDel){

        //local variables
        $localFeatureDelId = $localFeatureDel->_id;

        //sync flags
        $delete = true;

        //loop through all the features ids
        if ($resGetRemoteFeatureData) {
            foreach ($resGetRemoteFeatureData as &$remoteFeatureDel) {
                //remote variables
                $remoteFeatureDelId = $remoteFeatureDel->_id;
                if ($localFeatureDelId == $remoteFeatureDelId) {
                    $delete = false;
                }
            }

            //execute delete
            if ($delete == true){
                $deleteCount += 1;
                $resDeleteFeature = $featureDb->deleteFeatures($localFeatureDelId);
                if ($resDeleteFeature->Error == true){
                    echo json_encode($resDeleteFeature);
                    return;
                }
            }
        }else{
            //execute clean up delete
            $deleteCount += 1;
            $resDeleteFeature = $featureDb->deleteFeatures($localFeatureDelId);
            if ($resDeleteFeature->Error == true){
                echo json_encode($resDeleteFeature);
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