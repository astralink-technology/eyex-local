<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/model/cardDao.php';

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

    //get the card from remote
    $getRemoteCardParms = http_build_query(array(
        'DeviceId' => $deviceId
    ));
    $resGetRemoteCard = $curlHelper->curlGet('/meyex/card/getSyncCard', $getRemoteCardParms);
    $resGetRemoteCardData = $resGetRemoteCard->Data; //object, please encode it later on

    //get the card from local
    $cardDb = new cardDao();
    $resGetLocalCard = $cardDb->getCard();
    $resGetLocalCardData = $resGetLocalCard->Data; //object, please encode it later on

    //compare remote and local and sync (Add / Update)
    foreach ($resGetRemoteCardData as &$remoteCard) {
        //remote variables
        $remoteCardId = $remoteCard->_id;
        $remoteCardSerial = $remoteCard->card_serial;
        $remoteCardEntity = '';
        if (isset($remoteCard->employee)) $remoteCardEntity = $remoteCard->employee->_id;

        //local variables
        $localCardId = null;
        $localCardSerial = null;
        $localCardEntity = null;

        //sync flags
        $update = false;
        $add = true;

        foreach($resGetLocalCardData as &$localCard){
            $localCardId = $localCard->_id;
            $localCardSerial = $localCard->card_serial;
            $localCardEntity = $localCard->entity;

            //if both has, update
            if ($remoteCardId == $localCardId){
                $add = false;
                $update = true;
            }
        }

        //execute add
        if ($add == true){
            $addCount += 1;
            $resAddCard = $cardDb->addCard(
                $remoteCardId
                , $remoteCardSerial
                , $remoteCardEntity
            );
            if ($resAddCard->Error == true){
                echo json_encode($resAddCard);
                return;
            }
        }

        //execute update
        if ($update == true){
            $updateCount += 1;
            $resUpdateCard = $cardDb->updateCard(
                $remoteCardId
                , $remoteCardSerial
                , $remoteCardEntity
            );
            if ($resUpdateCard->Error == true){
                echo json_encode($resUpdateCard);
                return;
            }
        }
    }


    //get the cards from local again for delete check
    $resGetLocalCardForDel = $cardDb->getCard();
    $resGetLocalCardForDelData = $resGetLocalCardForDel->Data; //object, please encode it later on

    //compare local and remote (Delete)
    foreach($resGetLocalCardForDelData as &$localCardDel){

        //local variables
        $localCardDelId = $localCardDel->_id;

        //sync flags
        $delete = true;

        //loop through all the entity ids
        if ($resGetRemoteCardData) {
            foreach ($resGetRemoteCardData as &$remoteCardDel) {
                //remote variables
                $remoteCardDelId = $remoteCardDel->_id;
                if ($localCardDelId == $remoteCardDelId) {
                    $delete = false;
                }
            }

            //execute delete
            if ($delete == true){
                $deleteCount += 1;
                $resDeleteCard = $cardDb->deleteCard($localCardDelId);
                if ($resDeleteCard->Error == true){
                    echo json_encode($resDeleteCard);
                    return;
                }
            }
        }else{
            //execute clean up delete
            $deleteCount += 1;
            $resDeleteCard = $cardDb->deleteCard($localCardDelId);
            if ($resDeleteCard->Error == true){
                echo json_encode($resDeleteCard);
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