<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/entityDao.php';
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

    //get the entities from remote
    $getRemoteEntityParms = http_build_query(array(
        'DeviceId' => $deviceId
        , 'AppId' => $appId
        , 'Token' => $token
    ));
    $resGetRemoteEntity = $curlHelper->curlGet('/meyex/entity/getSyncEmployees', $getRemoteEntityParms);
    $resGetRemoteEntityData = $resGetRemoteEntity->Data; //object, please encode it later on

    //get the entities from local
    $entityDb = new entityDao();
    $resGetLocalEntity = $entityDb->getEntity();
    $resGetLocalEntityData = $resGetLocalEntity->Data; //object, please encode it later on

    //compare remote and local and sync (Add / Update)
    foreach ($resGetRemoteEntityData as &$remoteEntity) {
        //remote variables
        $remoteEntityId = $remoteEntity->related_entity->_id;
        $remoteEntityName = $remoteEntity->related_entity->name;
        $remoteEntityFirstName = $remoteEntity->related_entity->first_name;
        $remoteEntityLastName = $remoteEntity->related_entity->last_name;
        $remoteEntityAuthenticationString = $remoteEntity->related_entity->authentication_string;
        $remoteEntityAuthenticationStringLower = $remoteEntity->related_entity->authentication_string_lower;
        $remoteEntityPin = $remoteEntity->related_entity->pin;
        $remoteEntityCard = $remoteEntity->related_entity->card->_id;
        $remoteEntityExtension = $remoteEntity->related_entity->extension->_id;

        //local variables
        $localEntityId = null;
        $localEntityFirstName = null;
        $localEntityLastName = null;
        $localEntityName = null;
        $localEntityAuthenticationString = null;
        $localEntityAuthenticationStringLower = null;
        $localEntityPin = null;
        $localEntityCard = null;
        $localEntityExtension = null;

        //sync flags
        $update = false;
        $add = true;

        foreach($resGetLocalEntityData as &$localEntity){
            $localEntityId = $localEntity->_id;
            $localEntityFirstName = $localEntity->first_name;
            $localEntityLastName = $localEntity->last_name;
            $localEntityName = $localEntity->name;
            $localEntityAuthenticationString = $localEntity->authentication_string;
            $localEntityAuthenticationStringLower = $localEntity->authentication_string_lower;
            $localEntityPin = $localEntity->pin;
            $localEntityCard = $localEntity->card;
            $localEntityExtension = $localEntity->extension;

            //if both has, update
            if ($remoteEntityId == $localEntityId){
                $add = false;
                $update = true;
            }
        }

        //execute add
        if ($add == true){
            $addCount += 1;
            $resAddEntity = $entityDb->addEntity(
                $remoteEntityId
                , $remoteEntityAuthenticationString
                , $remoteEntityAuthenticationStringLower
                , $remoteEntityFirstName
                , $remoteEntityLastName
                , $remoteEntityName
                , $remoteEntityExtension
                , $remoteEntityCard
                , $remoteEntityPin
            );
            if ($resAddEntity->Error == true){
                echo json_encode($resAddEntity);
                return;
            }
        }

        //execute update
        if ($update == true){
            $updateCount += 1;
            $resUpdateEntity = $entityDb->updateEntity(
                $remoteEntityId
                , $remoteEntityAuthenticationString
                , $remoteEntityAuthenticationStringLower
                , $remoteEntityFirstName
                , $remoteEntityLastName
                , $remoteEntityName
                , $remoteEntityExtension
                , $remoteEntityCard
                , $remoteEntityPin
            );
            if ($resUpdateEntity->Error == true){
                echo json_encode($resUpdateEntity);
                return;
            }
        }
    }


    //get the entities from local Again for delete check
    $resGetLocalEntityForDel = $entityDb->getEntity();
    $resGetLocalEntityForDelData = $resGetLocalEntityForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalEntityForDelData as &$localEntityDel){

        //local variables
        $localEntityDelEntityId = $localEntityDel->_id;

        //sync flags
        $delete = true;

        if ($resGetRemoteEntityData){
            foreach ($resGetRemoteEntityData as &$remoteEntityDel) {
                //remote variables
                $remoteEntityDelId = $remoteEntityDel->related_entity->_id;
                if ($localEntityDelEntityId == $remoteEntityDelId){
                    $delete = false;
                }
            }
            //execute delete
            if ($delete == true){
                $deleteCount += 1;
                $resDeleteEntity = $entityDb->deleteEntity($localEntityDelEntityId);
                if ($resDeleteEntity->Error == true){
                    echo json_encode($resDeleteEntity);
                    return;
                }
            }
        }else{
            //execute cleanup delete
            $deleteCount += 1;
            $resDeleteEntity = $entityDb->deleteEntity($localEntityDelEntityId);
            if ($resDeleteEntity->Error == true){
                echo json_encode($resDeleteEntity);
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