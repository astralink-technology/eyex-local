<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/eyex_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/eventDao.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/config/webConfig.php';

    //declare models
    $eventDb = new eventDao();

    //app ID and token
    $webConfig = new webconfig();
    $webConfigObj = $webConfig->webconfig();
    $appId = $webConfigObj->appId;
    $token = $webConfigObj->token;

    //return variables
    $transferCount = 0;

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

    //get local events
    $resGetLocalEvent = $eventDb->getEvent();
    $resGetLocalEventData = $resGetLocalEvent->Data;

    if ($resGetLocalEvent->RowsReturned > 0){
        //curl all to the server
        //create a cloud account
        $params = array(
            'AppId' => urlencode($appId)
            , 'DeviceId' => urlencode($deviceId)
            , 'Token' => urlencode($token)
            , 'Events' => urlencode(json_encode($resGetLocalEventData))
        );
        $resPostEvents = $curlHelper->curlPost('/meyex/event/syncLocalEvents', $params);
        if ($resPostEvents->Error == false){
            $transferCount = $resGetLocalEvent->RowsReturned;
            //delete all the transferred
            $eventDb->deleteEvent();
        }
    }

    //finally return
    $counter = new stdClass();
    $counter->transferred = $transferCount;

    $jsonObject = new stdClass();
    $jsonObject->RowsReturned = 1;
    $jsonObject->Data = [$counter];
    $jsonObject->Error = false;
    $jsonObject->ErrorDesc = null;

    echo json_encode($jsonObject);
    return;
?>