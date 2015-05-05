<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/doorDao.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/entityDoorRelationshipDao.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/config/webConfig.php';

    //app ID and token
    $webConfig = new webconfig();
    $webConfigObj = $webConfig->webconfig();
    $appId = $webConfigObj->appId;
    $token = $webConfigObj->token;

    //return variables
    $addDoorCount = 0;
    $updateDoorCount = 0;
    $deleteDoorCount = 0;
    $addEntityDoorRelationshipCount = 0;
    $deleteEntityDoorRelationshipCount = 0;

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

    //get the doors from remote
    $getRemoteDoorParms = http_build_query(array(
        'DeviceId' => $deviceId
        , 'AppId' => $appId
        , 'Token' => $token
    ));
    $resGetRemoteDoor = $curlHelper->curlGet('/meyex/door/getSyncDoor', $getRemoteDoorParms);
    $resGetRemoteDoorData = $resGetRemoteDoor->Data; //object, please encode it later on

    //get the doors from local
    $doorDb = new doorDao();
    $resGetLocalDoor = $doorDb->getDoor();
    $resGetLocalDoorData = $resGetLocalDoor->Data; //object, please encode it later on

    $entityDoorRelationshipDb = new entityDoorRelationshipDao();

    //DOORS
    //compare remote and local and sync (Add / Update)
    foreach ($resGetRemoteDoorData as &$remoteDoor) {
        //remote variables
        $remoteDoorId = $remoteDoor->_id;
        $remoteDoorName = $remoteDoor->door_name;
        $remoteDoorNode = $remoteDoor->door_node;

        //local variables
        $localDoorId = null;
        $localDoorNode = null;
        $localDoorName = null;

        //sync flags
        $update = false;
        $add = true;

        foreach($resGetLocalDoorData as &$localDoor){
            $localDoorId = $localDoor->_id;
            $localDoorName = $localDoor->door_name;
            $localDoorNode = $localDoor->door_node;

            //if both has, update
            if ($remoteDoorId == $localDoorId){
                $add = false;
                $update = true;
            }
        }

        //execute add
        if ($add == true){
            $addDoorCount += 1;
            $resAddDoor = $doorDb->addDoor(
                $remoteDoorId
                , $remoteDoorName
                , $remoteDoorNode
            );

            if ($resAddDoor->Error == true){
                echo json_encode($resAddDoor);
                return;
            }
        }

        //execute update
        if ($update == true){
            $updateDoorCount += 1;
            $resUpdateDoor = $doorDb->updateDoor(
                $remoteDoorId
                , $remoteDoorName
                , $remoteDoorNode
            );
            if ($resUpdateDoor->Error == true){
                echo json_encode($resUpdateDoor);
                return;
            }
        }
    }

    //get the doors from local again for delete check
    $resGetLocalDoorForDel = $doorDb->getDoor();
    $resGetLocalDoorForDelData = $resGetLocalDoorForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalDoorForDelData as &$localDoorDel){

        //local variables
        $localDoorDelDoorId = $localDoorDel->_id;

        //sync flags
        $delete = true;

        if ($resGetRemoteDoorData){
            foreach ($resGetRemoteDoorData as &$remoteDoorDel) {
                //remote variables
                $remoteDoorDelId = $remoteDoorDel->_id;
                if ($localDoorDelDoorId == $remoteDoorDelId){
                    $delete = false;
                }
            }
            //execute delete. If delete door, delete all the relationships too
            if ($delete == true){
                $deleteDoorCount += 1;
                $resDeleteDoor = $doorDb->deleteDoor($localDoorDelDoorId);
                $resDeleteEntityDoorRelationship = $entityDoorRelationshipDb->deleteEntityDoorRelationship(null, $localDoorDelDoorId);
                if ($resDeleteDoor->Error == true){
                    echo json_encode($resDeleteDoor);
                    return;
                }
                if ($resDeleteEntityDoorRelationship->Error == true){
                    echo json_encode($resDeleteEntityDoorRelationship);
                    return;
                }
            }
        }else{
            //execute cleanup delete. If delete door, delete all the relationships too
            $deleteDoorCount += 1;
            $resDeleteDoor = $doorDb->deleteDoor($localDoorDelDoorId);
            $resDeleteEntityDoorRelationship = $entityDoorRelationshipDb->deleteEntityDoorRelationship(null, $localDoorDelDoorId);
            if ($resDeleteDoor->Error == true){
                echo json_encode($resDeleteDoor);
                return;
            }
            if ($resDeleteEntityDoorRelationship->Error == true){
                echo json_encode($resDeleteEntityDoorRelationship);
                return;
            }
        }
    }


    //ENTITY DOOR RELATIONSHIP
    //get the door relationship by extracting from the doors
    $resGetRemoteDoorRelationshipData = array();
    foreach ($resGetRemoteDoorData as &$remoteDoor) {
        $remoteDoorRelationship = new stdClass();
        $remoteDoorRelationship->_id = $remoteDoor->_id;
        $remoteDoorRelationship->related_entities = array();
        //loop all the related entities of the door and organize them into an array
        if ($remoteDoor->related_entities){
            foreach($remoteDoor->related_entities as &$related_entity){
                array_push($remoteDoorRelationship->related_entities, $related_entity->_id);
            }
        }
        array_push($resGetRemoteDoorRelationshipData, $remoteDoorRelationship);
    }

    //get the door relationship from local
    $resGetLocalDoorRelationship = $entityDoorRelationshipDb->getEntityDoorRelationship();
    $resGetLocalDoorRelationshipData = $resGetLocalDoorRelationship->Data; //object, please encode it later on

    //compare remote and local and sync (Add)
    foreach ($resGetRemoteDoorRelationshipData as &$remoteDoorRelationship) {
        //top level remote variables
        $remoteDoorRelationshipDoorId = $remoteDoorRelationship->_id;
        $remoteDoorRelationshipEntityIds = $remoteDoorRelationship->related_entities;

        if ($remoteDoorRelationshipEntityIds){
            foreach($remoteDoorRelationshipEntityIds as $remoteDoorRelationshipEntityId){

                //remote door entity id = $remoteDoorRelationshipEntityId

                //local variables
                $localEntityId = null;
                $localDoorId = null;

                //sync flags
                $addDoorRelationship = true;

                foreach($resGetLocalDoorRelationshipData as $localDoorRelationship){
                    $localEntityId = $localDoorRelationship->entity;
                    $localDoorId = $localDoorRelationship->door;
                    if ($localEntityId == $remoteDoorRelationshipEntityId && $localDoorId == $remoteDoorRelationshipDoorId){
                        $addDoorRelationship = false;
                    }
                }

                //execute add
                if ($addDoorRelationship == true){
                    $addEntityDoorRelationshipCount += 1;
                    $resAddDoorRelationship = $entityDoorRelationshipDb->addEntityDoorRelationship(
                        $remoteDoorRelationshipEntityId
                        , $remoteDoorRelationshipDoorId
                    );
                    if ($resAddDoorRelationship ->Error == true){
                        echo json_encode($resDeleteEntityDoorRelationship);
                        return;
                    }
                }
            }
        }


    }

    //get the door relationship from local again for delete check
    $resGetLocalDoorRelationshipForDel = $entityDoorRelationshipDb->getEntityDoorRelationship();
    $resGetLocalDoorRelationshipForDelData = $resGetLocalDoorRelationshipForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalDoorRelationshipForDelData as &$localDoorRelationshipDel){

        //local variables
        $localDoorRelationshipDelEntityId = $localDoorRelationshipDel->entity;
        $localDoorRelationshipDelDoorId = $localDoorRelationshipDel->door;

        //top level remote variables
        foreach ($resGetRemoteDoorRelationshipData as &$remoteDoorRelationship) {

            $remoteDoorRelationshipDoorId = $remoteDoorRelationship->_id;
            $remoteDoorRelationshipEntityIds = $remoteDoorRelationship->related_entities;

            //make sure we are on the same door
            if ($localDoorRelationshipDelDoorId == $remoteDoorRelationshipDoorId){
                //loop through all the entity ids
                if ($remoteDoorRelationshipEntityIds) {

                    //sync flags
                    $deleteDoorRelationship = true;

                    foreach ($remoteDoorRelationshipEntityIds as $remoteDoorRelationshipEntityId) {

                        //remote door entity id = $remoteDoorRelationshipEntityId

                        if ($localDoorRelationshipDelEntityId == $remoteDoorRelationshipEntityId){
                            $deleteDoorRelationship = false;
                        }
                    }

                    //execute delete
                    if ($deleteDoorRelationship == true){
                        $deleteEntityDoorRelationshipCount += 1;
                        $resDeleteEntityDoorRelationship = $entityDoorRelationshipDb->deleteEntityDoorRelationship(null, $localDoorRelationshipDelDoorId, $localDoorRelationshipDelEntityId);
                        if ($resDeleteEntityDoorRelationship->Error == true){
                            echo json_encode($resDeleteEntityDoorRelationship);
                            return;
                        }
                    }
                }else{
                    //execute clean up delete
                    $deleteEntityDoorRelationshipCount += 1;
                    $resDeleteEntityDoorRelationship = $entityDoorRelationshipDb->deleteEntityDoorRelationship(null, $localDoorRelationshipDelDoorId);
                    if ($resDeleteEntityDoorRelationship->Error == true){
                        echo json_encode($resDeleteEntityDoorRelationship);
                        return;
                    }
                }
            }

        }
    }

    //finally return
    $counter = new stdClass();
    $counter->addedDoor = $addDoorCount;
    $counter->updatedDoor = $updateDoorCount;
    $counter->deletedDoor = $deleteDoorCount;
    $counter->addedEntityDoorRelationship = $addEntityDoorRelationshipCount;
    $counter->deletedEntityDoorRelationship = $deleteEntityDoorRelationshipCount;

    $jsonObject = new stdClass();
    $jsonObject->RowsReturned = 1;
    $jsonObject->Data = [$counter];
    $jsonObject->Error = false;
    $jsonObject->ErrorDesc = null;

    echo json_encode($jsonObject);
    return;
?>