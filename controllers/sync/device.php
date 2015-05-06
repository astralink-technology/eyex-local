<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/eyex_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/deviceDao.php';
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

    //get the devices from remote
    $getRemoteDeviceParms = http_build_query(array(
        'DeviceId' => $deviceId
        , 'AppId' => $appId
        , 'Token' => $token
    ));
    $resGetRemoteDevice = $curlHelper->curlGet('/meyex/device/getSyncDevices', $getRemoteDeviceParms);
    $resGetRemoteDeviceData = $resGetRemoteDevice->Data; //object, please encode it later on

    //get the devices from local
    $deviceDb = new deviceDao();
    $resGetLocalDevice = $deviceDb->getDevice();
    $resGetLocalDeviceData = $resGetLocalDevice->Data; //object, please encode it later on

    //compare remote and local and sync (Add / Update)
    foreach ($resGetRemoteDeviceData as &$remoteDevice) {
        //remote variables
        $remoteDeviceId = $remoteDevice->_id;
        $remoteDeviceName = $remoteDevice->name;
        $remoteDeviceType = $remoteDevice->type;
        $remoteDeviceType2 = $remoteDevice->type2;
        $remoteDeviceIntPrefix = $remoteDevice->int_prefix;
        $remoteDeviceDoor= $remoteDevice->door->_id;

        //local variables
        $localDeviceId = null;
        $localDeviceName = null;
        $localDeviceType = null;
        $localDeviceType2 = null;
        $localDeviceIntPrefix = null;
        $localDeviceDoor= null;

        //sync flags
        $update = false;
        $add = true;

        foreach($resGetLocalDeviceData as &$localDevice){
            $localDeviceId = $localDevice->_id;
            $localDeviceName = $localDevice->name;
            $localDeviceType = $localDevice->type;
            $localDeviceType2 = $localDevice->type2;
            $localDeviceIntPrefix = $localDevice->int_prefix;
            $localDeviceDoor = $localDevice->door;

            //if both has, update
            if ($remoteDeviceId == $localDeviceId){
                $add = false;
                $update = true;
            }
        }

        //execute add
        if ($add == true){
            $addCount += 1;
            $resAddDevice = $deviceDb->addDevice(
                $remoteDeviceId
                , $remoteDeviceName
                , $remoteDeviceType
                , $remoteDeviceType2
                , $remoteDeviceIntPrefix
                , $remoteDeviceDoor
            );

            if ($resAddDevice->Error == true){
                echo json_encode($resAddDevice);
                return;
            }
        }

        //execute update
        if ($update == true){
            $updateCount += 1;
            $resUpdateDevice = $deviceDb->updateDevice(
                $remoteDeviceId
                , $remoteDeviceName
                , $remoteDeviceType
                , $remoteDeviceType2
                , $remoteDeviceIntPrefix
                , $remoteDeviceDoor
            );
            if ($resUpdateDevice->Error == true){
                echo json_encode($resUpdateDevice);
                return;
            }
        }
    }


    //get the devices from local again for delete check
    $resGetLocalDeviceForDel = $deviceDb->getDevice();
    $resGetLocalDeviceForDelData = $resGetLocalDeviceForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalDeviceForDelData as &$localDeviceDel){

        //local variables
        $localDeviceDelDeviceId = $localDeviceDel->_id;

        //sync flags
        $delete = true;

        //loop through all the entity ids
        if ($resGetRemoteDeviceData) {
            foreach ($resGetRemoteDeviceData as &$remoteDeviceDel) {
                //remote variables
                $remoteDeviceDelId = $remoteDeviceDel->_id;
                if ($localDeviceDelDeviceId == $remoteDeviceDelId) {
                    $delete = false;
                }
            }

            //execute delete
            if ($delete == true){
                $deleteCount += 1;
                $resDeleteDevice = $deviceDb->deleteDevice($localDeviceDelDeviceId);
                if ($resDeleteDevice->Error == true){
                    echo json_encode($resDeleteDevice);
                    return;
                }
            }
        }else{
            //execute clean up delete
            $deleteCount += 1;
            $resDeleteDevice = $deviceDb->deleteDevice($localDeviceDelDeviceId);
            if ($resDeleteDevice->Error == true){
                echo json_encode($resDeleteDevice);
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