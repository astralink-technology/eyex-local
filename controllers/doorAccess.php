<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/entityDao.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/doorDao.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/deviceDao.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/entityDoorRelationshipDao.php';

    //check if the
    $entityDb = new entityDao();
    $doorDb = new doorDao();
    $deviceDb = new deviceDao();
    $entityDoorRelationshipDb = new entityDoorRelationshipDao();

    $deviceId = null;
    $pinNo = null;
    $cardId = null;
    $vsAccessType = null;

    //GET parameters
    if (isset($_GET["DeviceId"])) $deviceId = $_GET["DeviceId"];
    if (isset($_GET["Card"])) $cardId = $_GET["Card"];
    if (isset($_GET["Pin"])) $pinNo = $_GET["Pin"];
    if (isset($_GET["VsAccessType"])) $vsAccessType = $_GET["VsAccessType"];

    //Required Parameters
    if (
        $deviceId == null ||
        ($pinNo == null && $cardId == null)
    ){
        //return the json object
        $jsonObject = new stdClass();
        $jsonObject->RowsReturned = null;
        $jsonObject->Data = false;
        $jsonObject->Error = true;
        $jsonObject->ErrorDesc = 'Device ID, either PIN or Card ID Required';
        echo json_encode($jsonObject);
        return;
    }

    //Check device type
    $resCheckDeviceType = $deviceDb->getDevice(
        $deviceId
    );

    $dualAuthentication = false;
    $deviceType = null;
    $devicePrefix = null;

    if ($resCheckDeviceType->RowsReturned > 0){
        $deviceType = $resCheckDeviceType->Data[0]->type;
        $devicePrefix = $resCheckDeviceType->Data[0]->int_prefix;
        if ($deviceType == 'PCR'){
            $dualAuthentication = true; //dual access
        }
    }else{
        //return the json object
        $jsonObject = new stdClass();
        $jsonObject->RowsReturned = null;
        $jsonObject->Data = false;
        $jsonObject->Error = true;
        $jsonObject->ErrorDesc = 'Device Not Found';
        echo json_encode($jsonObject);
        return;
    }

    //get the door the device is connected
    $doorId = null;
    $doorNode = null;
    $doorName = null;

    $resGetDevice = $deviceDb->getDevice($deviceId);
    if ($resGetDevice->RowsReturned > 0){
        $doorId = $resGetDevice->Data[0]->door;
    }

    if ($doorId != null){
        $resGetDoor = $doorDb->getDoor($doorId);
        if ($resGetDoor->RowsReturned > 0){
            $doorNode = $resGetDoor->Data[0]->door_node;
            $doorName = $resGetDoor->Data[0]->door_name;
        }
    }

    //Door DB & Door Relationship DB to check for the Doors controlled by Reader Device.
    //prepare the object for return
    $returnObject = new stdClass();
    $returnObject->entityId = null;
    $returnObject->name = null;
    $returnObject->deviceId = $deviceId; //Device Id
    $returnObject->devicePrefix = $devicePrefix; //Device Prefix
    $returnObject->doorNode = $doorNode;
    $returnObject->doorName = $doorName;
    $returnObject->dualAuthentication = $dualAuthentication; //singular or dual access. 1 or 2
    $returnObject->authentication = false;

    if ($dualAuthentication == false){ // Singular Authentication
        if ($pinNo){
            //User DB to look for user using PIN
            $resEntity = $entityDb->getEntity(null, $pinNo);
            if ($resEntity->RowsReturned > 0){
                //user found
                $entityId = $resEntity->Data[0]->_id;
                $entityName = $resEntity->Data[0]->name;
                $returnObject->entityId = $entityId;
                $returnObject->name = $entityName;

                //get the doors users are able to authenticate
                $resGetEntityDoorRelationship = $entityDoorRelationshipDb->getEntityDoorRelationship(null, $entityId, $doorId);
                if ($resGetEntityDoorRelationship->RowsReturned > 0){
                    $returnObject->authentication = true;
                }
            }else{
                //return the json object
                $returnObject->authentication = false;
                echo json_encode($returnObject);
                return;
            }
        }else if ($cardId){
            //User DB to look for user using Card Id
            $resEntity = $entityDb->getEntity(null, null, $cardId);
            if ($resEntity->RowsReturned > 0){
                //user found
                $entityId = $resEntity->Data[0]->_id;
                $entityName = $resEntity->Data[0]->name;
                $returnObject->entityId = $entityId;
                $returnObject->name = $entityName;

                //get the doors users are able to authenticate
                $resGetEntityDoorRelationship = $entityDoorRelationshipDb->getEntityDoorRelationship(null, $entityId, $doorId);
                if ($resGetEntityDoorRelationship->RowsReturned > 0){
                    $returnObject->authentication = true;
                }
            }else{
                //return the json object
                $returnObject->authentication = false;
                echo json_encode($returnObject);
                return;
            }
        }
    } else if ($dualAuthentication == true){ // Dual Authentication
        if ($cardId && $pinNo){
            //User DB to look for user using Card Id and Pin No
            $resEntity = $entityDb->getEntity(null, $pinNo, $cardId);
            if ($resEntity->RowsReturned > 0) {
                //user found
                $entityId = $resEntity->Data[0]->_id;
                $entityName = $resEntity->Data[0]->name;
                $returnObject->entityId = $entityId;
                $returnObject->name = $entityName;

                //get the doors users are able to authenticate
                $resGetEntityDoorRelationship = $entityDoorRelationshipDb->getEntityDoorRelationship(null, $entityId, $doorId);
                if ($resGetEntityDoorRelationship->RowsReturned > 0){
                    $returnObject->authentication = true;
                }
            }else{
                //return the json object
                $returnObject->authentication = false;
                echo json_encode($returnObject);
                return;
            }
        }else{
            $returnObject->authentication = false;
        }
    }
    echo json_encode($returnObject);

?>