<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

/*
 * A helper to do syncing for eyexcess
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/helpers/curl_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/helpers/jwt_helper.php';
require_once ($_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/helpers/UTCconvertor_helper.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/model/cloudAccessDao.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/model/featureDao.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/model/announcementDao.php');

class cp_sync_helper{
    public function syncAnnouncements(){
        $cloudAccessDao = new cloudAccessDao();
        $announcementDao = new announcementDao();
        $cloudAccessRes = $cloudAccessDao->getCloudAccess();

        $jwtHelper = new cp_jwt_helper();
        $curlHelper = new cp_curl_helper();
        $utcHelper = new cp_UTCconvertor_helper();

        $token = null;
        $appId = null;
        $key = null;
        $ownerId = null;
        $cloudEnabled = false;

        if ($cloudAccessRes->RowsReturned > 0){
            $token = $cloudAccessRes->Data[0]->token;
            $appId = $cloudAccessRes->Data[0]->cloud_access_id;
            $enabled = $cloudAccessRes->Data[0]->enabled;
            $key = $cloudAccessRes->Data[0]->secret;
            if ($enabled == 't'){
                $cloudEnabled = true;
            }
            //decode the jwt to get the remote owner id
            $resJwtDecoded = $jwtHelper->decodeJWT($token, $key);
            $ownerId = $resJwtDecoded->OwnerId;
        }else{
            $cloudEnabled = false;
        }

        if ($cloudEnabled != true || $ownerId == null){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = 'Cloud Access not enabled';
            echo json_encode($jsonObject);
            return;
        }

        //check flags and variables
        $rAnnouncementId= null;
        $rLastUpdate = null;
        $rLastUpdateRaw = null;
        $rAnnouncement = '';

        $lAnnouncementId = null;
        $lLastUpdate = null;
        $lLastUpdateRaw = null;
        $lAnnouncement = '';

        //check if there is any record in remote, if there is record in remote, get the last update
        $getRemoteAnnouncementParms = http_build_query(array(
            'AppId' => $appId
            , 'Token' => $token
            , 'OwnerId' => $ownerId
        ));
        $resGetAnnouncements = $curlHelper->curlGet('/eyex/announcement/getAnnouncement', $getRemoteAnnouncementParms);
        if(isset($resGetAnnouncements) && $resGetAnnouncements->RowsReturned > 0){
            $rLastUpdate = $resGetAnnouncements->Data[0]->last_update;
            $rLastUpdate = $utcHelper->parseRemoteDateString($rLastUpdate);
            $rAnnouncement = $resGetAnnouncements->Data[0]->description;
            $rAnnouncementId = $resGetAnnouncements->Data[0]->media_id;
        }

        //check if there is any record in local, if there is record in local, get the last update
        $resGetLocalAnnouncement = $announcementDao->getAnnouncement();
        if (isset($resGetLocalAnnouncement) && $resGetLocalAnnouncement->RowsReturned > 0){
            $lLastUpdate = $resGetLocalAnnouncement->Data[0]->last_update;
            $lAnnouncement = $resGetLocalAnnouncement->Data[0]->message;
            $lAnnouncementId = $resGetLocalAnnouncement->Data[0]->id;
        }

        //convert the dates to raw for comparison
        $utcHelper->setDefaultUTC();
        if ($rLastUpdate != null){
            $rLastUpdateRaw= new DateTime($rLastUpdate);
        }
        if ($lLastUpdate!= null){
            $lLastUpdateRaw= new DateTime($lLastUpdate);
        }

        if ($rAnnouncementId == null && $lAnnouncementId != null){
            //record in local, no record in remote, add
            $params = array(
                'OwnerId' => urlencode($ownerId),
                'Description' => urlencode($lAnnouncement),
                'Token' => urlencode($token),
                'Sync' => urlencode(false), //do not sync again this time round
                'AppId' => urlencode($appId)
            );
            $resAddAnnouncement = $curlHelper->curlPost('/eyex/announcement/addAnnouncement', $params);
            echo json_encode($resAddAnnouncement);
        }else if ($lAnnouncementId == null && $rAnnouncementId != null){
            //record in remote, no record in local, add
            $resAddAnnouncement = $announcementDao->addAnnouncement($rAnnouncement);
            echo json_encode($resAddAnnouncement);
        }else if ($rLastUpdateRaw > $lLastUpdateRaw){
            //record in remote > local, update local
            $resUpdateAnnouncement = $announcementDao->updateAnnouncement($lAnnouncementId, $rAnnouncement);
            echo json_encode($resUpdateAnnouncement);
        }else if ($lLastUpdateRaw > $rLastUpdateRaw){
            //record in local > remote, update remote
            $params = array(
                'AnnouncementId' => urlencode($rAnnouncementId),
                'Description' => urlencode($lAnnouncement),
                'OwnerId' => urlencode($ownerId),
                'Sync' => urlencode(false), //do not sync again this time round
                'Token' => urlencode($token),
                'AppId' => urlencode($appId)
            );
            $resUpdateAnnouncement = $curlHelper->curlPost('/eyex/announcement/updateAnnouncement', $params);
            echo json_encode($resUpdateAnnouncement);
        }
    }

    public function syncFeatures(){
        $cloudAccessDao = new cloudAccessDao();
        $featureDao = new featuresDao();
        $cloudAccessRes = $cloudAccessDao->getCloudAccess();

        $jwtHelper = new cp_jwt_helper();
        $curlHelper = new cp_curl_helper();
        $utcHelper = new cp_UTCconvertor_helper();

        $token = null;
        $appId = null;
        $key = null;
        $ownerId = null;
        $cloudEnabled = false;

        if ($cloudAccessRes->RowsReturned > 0){
            $token = $cloudAccessRes->Data[0]->token;
            $appId = $cloudAccessRes->Data[0]->cloud_access_id;
            $enabled = $cloudAccessRes->Data[0]->enabled;
            $key = $cloudAccessRes->Data[0]->secret;
            if ($enabled == 't'){
                $cloudEnabled = true;
            }
            //decode the jwt to get the remote owner id
            $resJwtDecoded = $jwtHelper->decodeJWT($token, $key);
            $ownerId = $resJwtDecoded->OwnerId;
        }else{
            $cloudEnabled = false;
        }

        if ($cloudEnabled != true || $ownerId == null){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = 'Cloud Access not enabled';
            echo json_encode($jsonObject);
            return;
        }


        //check flags and variables
        $rFeatureId = null;
        $rLastUpdate = null;
        $rLastUpdateRaw = null;
        $rRemoteDoor = null;
        $rLocalDoor = null;
        $rExtensionDoor = null;
        $rVoicemail = null;
        $rVoicemailPassword = null;
        $rVoicemailExtension = null;
        $rPickup = null;
        $rExtra1 = null;
        $rExtra2 = null;
        $rExtra3 = null;
        $rExtra4 = null;

        $lFeatureId = null;
        $lLastUpdate = null;
        $lLastUpdateRaw = null;
        $lRemoteDoor = null;
        $lLocalDoor = null;
        $lExtensionDoor = null;
        $lVoicemail = null;
        $lVoicemailPassword = null;
        $lVoicemailExtension = null;
        $lPickup = null;
        $lExtra1 = null;
        $lExtra2 = null;
        $lExtra3 = null;
        $lExtra4 = null;

        //check if there is any record in remote, if there is record in remote, get the last update
        $getRemoteFeaturesParms = http_build_query(array(
            'AppId' => $appId
            , 'Token' => $token
            , 'OwnerId' => $ownerId
        ));
        $resGetFeatures = $curlHelper->curlGet('/eyex/feature/getFeature', $getRemoteFeaturesParms);
        if(isset($resGetFeatures) && $resGetFeatures->RowsReturned > 0){
            $rLastUpdate = $resGetFeatures->Data[0]->last_update;
            $rLastUpdate = $utcHelper->parseRemoteDateString($rLastUpdate);
            $rFeatureId = $resGetFeatures->Data[0]->feature_id;
            $rRemoteDoor = $resGetFeatures->Data[0]->remote_door;
            $rLocalDoor = $resGetFeatures->Data[0]->local_door;
            $rExtensionDoor = $resGetFeatures->Data[0]->extension_door;
            $rVoicemail= $resGetFeatures->Data[0]->voicemail;
            $rVoicemailPassword = $resGetFeatures->Data[0]->voicemail_password;
            $rVoicemailExtension = $resGetFeatures->Data[0]->voicemail_extension;
            $rPickup = $resGetFeatures->Data[0]->pickup;
            $rExtra1 = $resGetFeatures->Data[0]->extra1;
            $rExtra2 = $resGetFeatures->Data[0]->extra2;
            $rExtra3 = $resGetFeatures->Data[0]->extra3;
            $rExtra4 = $resGetFeatures->Data[0]->extra4;
        }


        //check if there is any record in local, if there is record in local, get the last update
        $resGetLocalFeature = $featureDao->getFeatures();
        if (isset($resGetLocalFeature) && $resGetLocalFeature->RowsReturned > 0){
            $lLastUpdate = $resGetLocalFeature->Data[0]->last_update;
            $lFeatureId = $resGetLocalFeature->Data[0]->id;
            $lRemoteDoor = $resGetLocalFeature->Data[0]->remote_door;
            $lLocalDoor = $resGetLocalFeature->Data[0]->local_door;
            $lExtensionDoor = $resGetLocalFeature->Data[0]->extension_door;
            $lVoicemail= $resGetLocalFeature->Data[0]->voicemail;
            $lVoicemailPassword = $resGetLocalFeature->Data[0]->voicemail_password;
            $lVoicemailExtension = $resGetLocalFeature->Data[0]->voicemail_extension;
            $lPickup = $resGetLocalFeature->Data[0]->pickup;
            $lExtra1 = $resGetFeatures->Data[0]->extra1;
            $lExtra2 = $resGetFeatures->Data[0]->extra2;
            $lExtra3 = $resGetFeatures->Data[0]->extra3;
            $lExtra4 = $resGetFeatures->Data[0]->extra4;
        }

        //convert the dates to raw for comparison
        $utcHelper->setDefaultUTC();
        if ($rLastUpdate != null){
            $rLastUpdateRaw = new DateTime($rLastUpdate);
        }
        if ($lLastUpdate!= null){
            $lLastUpdateRaw = new DateTime($lLastUpdate);
        }

        if ($rFeatureId == null && $lFeatureId != null){
            //record in local, no record in remote, add
            $params = array(
                'OwnerId' => urlencode($ownerId)
                , 'Token' => urlencode($token)
                , 'Sync' => urlencode(false) //do not sync again this time round
                , 'AppId' => urlencode($appId)
                , 'RemoteDoor' => urlencode($lRemoteDoor)
                , 'LocalDoor' => urlencode($lLocalDoor)
                , 'ExtensionDoor' => urlencode($lExtensionDoor)
                , 'Voicemail' => urlencode($lVoicemail)
                , 'VoicemailPassword' => urlencode($lVoicemailPassword)
                , 'VoicemailExtension' => urlencode($lVoicemailExtension)
                , 'Pickup' =>urlencode($lPickup)
                , 'Extra1' =>urlencode($lExtra1)
                , 'Extra2' =>urlencode($lExtra2)
                , 'Extra3' =>urlencode($lExtra3)
                , 'Extra4' =>urlencode($lExtra4)
            );
            $resAddRemoteFeature = $curlHelper->curlPost('/eyex/feature/addFeature', $params);
            echo json_encode($resAddRemoteFeature);
            echo json_encode('add remote');
        }else if ($lFeatureId == null && $rFeatureId != null){
            //record in remote, no record in local, add
            $resAddFeature = $featureDao->addFeatures(
                $rRemoteDoor
                , $rLocalDoor
                , $rExtensionDoor
                , $rVoicemail
                , $rVoicemailPassword
                , $lVoicemailExtension
                , $rPickup
                , $rExtra1
                , $rExtra2
                , $rExtra3
                , $rExtra4
            );
            echo json_encode($resAddFeature);
            echo json_encode('add local');
        }else if ($rLastUpdateRaw > $lLastUpdateRaw){
            //record in remote > local, update local
            $resUpdateFeature = $featureDao->updateFeatures(
                $lFeatureId
                , $rRemoteDoor
                , $rLocalDoor
                , $rExtensionDoor
                , $rVoicemail
                , $rVoicemailPassword
                , $rVoicemailExtension
                , $rPickup
                , $rExtra1
                , $rExtra2
                , $rExtra3
                , $rExtra4
            );
            echo json_encode($resUpdateFeature);
            echo json_encode('update local');
        }else if ($lLastUpdateRaw > $rLastUpdateRaw){
            //record in local > remote, update remote
            $params = array(
                'OwnerId' => urlencode($ownerId)
                , 'FeatureId' => urlencode($rFeatureId)
                , 'Token' => urlencode($token)
                , 'Sync' => urlencode(false) //do not sync again this time round
                , 'AppId' => urlencode($appId)
                , 'RemoteDoor' => urlencode($lRemoteDoor)
                , 'LocalDoor' => urlencode($lLocalDoor)
                , 'ExtensionDoor' => urlencode($lExtensionDoor)
                , 'Voicemail' => urlencode($lVoicemail)
                , 'VoicemailPassword' => urlencode($lVoicemailPassword)
                , 'VoicemailExtension' => urlencode($lVoicemailExtension)
                , 'Pickup' =>urlencode($lPickup)
                , 'Extra1' =>urlencode($lExtra1)
                , 'Extra2' =>urlencode($lExtra2)
                , 'Extra3' =>urlencode($lExtra3)
                , 'Extra4' =>urlencode($lExtra4)
            );
            $resUpdateFeature = $curlHelper->curlPost('/eyex/feature/updateFeature', $params);
            echo json_encode($resUpdateFeature);
            echo json_encode('update remote');
        }
    }

    public function syncDevices(){
        $cloudAccessDao = new cloudAccessDao();
        $deviceDao = new deviceDao();
        $cloudAccessRes = $cloudAccessDao->getCloudAccess();

        $jwtHelper = new cp_jwt_helper();
        $curlHelper = new cp_curl_helper();
        $utcHelper = new cp_UTCconvertor_helper();

        $token = null;
        $appId = null;
        $key = null;
        $ownerId = null;
        $cloudEnabled = false;

        if ($cloudAccessRes->RowsReturned > 0){
            $token = $cloudAccessRes->Data[0]->token;
            $appId = $cloudAccessRes->Data[0]->cloud_access_id;
            $enabled = $cloudAccessRes->Data[0]->enabled;
            $key = $cloudAccessRes->Data[0]->secret;
            if ($enabled == 't'){
                $cloudEnabled = true;
            }
            //decode the jwt to get the remote owner id
            $resJwtDecoded = $jwtHelper->decodeJWT($token, $key);
            $ownerId = $resJwtDecoded->OwnerId;
        }else{
            $cloudEnabled = false;
        }

        if ($cloudEnabled != true || $ownerId == null){
            //return the json object
            $jsonObject = new stdClass();
            $jsonObject->RowsReturned = null;
            $jsonObject->Data = false;
            $jsonObject->Error = true;
            $jsonObject->ErrorDesc = 'Cloud Access not enabled';
            echo json_encode($jsonObject);
            return;
        }
    }
}
 
?>