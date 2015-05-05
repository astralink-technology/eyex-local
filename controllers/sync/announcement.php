<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/announcementDao.php';
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

    //get the announcement from remote
    $getRemoteAnnouncementParms = http_build_query(array(
        'DeviceId' => $deviceId
        , 'AppId' => $appId
        , 'Token' => $token
    ));

    $resGetRemoteAnnouncement = $curlHelper->curlGet('/meyex/message/getSyncAnnouncement', $getRemoteAnnouncementParms);
    $resGetRemoteAnnouncementData = $resGetRemoteAnnouncement->Data; //object, please encode it later on

    //get the card from local
    $announcementDb = new announcementDao();
    $resGetLocalAnnouncement = $announcementDb->getAnnouncement();
    $resGetLocalAnnouncementData = $resGetLocalAnnouncement->Data; //object, please encode it later on

    //compare remote and local and sync (Add / Update / Delete)
    $remoteAnnouncementId = null;
    $localAnnouncementId = null;
    $remoteAnnouncement = null;
    $localAnnouncement = null;

    if (isset($resGetRemoteAnnouncementData)){
        $remoteAnnouncementId = $resGetRemoteAnnouncementData[0]->_id;
        $remoteAnnouncement = $resGetRemoteAnnouncementData[0]->message;
    }

    if (isset($resGetLocalAnnouncementData)){
        $localAnnouncementId = $resGetLocalAnnouncementData[0]->_id;
        $localAnnouncement = $resGetLocalAnnouncementData[0]->message;
    }

    $add = true;
    $update = false;
    $delete = false;

    if ($remoteAnnouncementId != null && $localAnnouncementId != null){
        //update
        $announcementDb->updateAnnouncement($remoteAnnouncement);
        $updateCount += 1;
    } else if ($remoteAnnouncementId == null && $localAnnouncementId != null){
        //delete
        $announcementDb->deleteAnnouncement();
        $deleteCount += 1;
    } else if ($remoteAnnouncementId != null && $localAnnouncementId == null){
        //add
        $announcementDb->addAnnouncement($remoteAnnouncementId, $remoteAnnouncement);
        $addCount += 1;
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