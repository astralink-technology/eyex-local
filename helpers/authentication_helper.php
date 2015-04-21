<?php
/*
 * Copyright Chilli Panda
 * Created on 05-03-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper on authentication
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/helpers/session_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/model/userDao.php';

class cp_authentication_helper{

    public function checkUser($identificationString){
        $userDb = new userDao();
        $resGetUser = $userDb->getUser(
            null
            , null
            , $identificationString
        );

        if ($resGetUser->Error == false AND $resGetUser->RowsReturned > 0){
            return true;
        }else{
            return false;
        }
    }

    public function authenticateUser($identificationString, $password){

        $userDb = new userDao();
        $resGetUser = $userDb->getUser(
            null
            , null
            , $identificationString
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , null
            , $password
            , 0
        );

        if ($resGetUser->Error == false AND $resGetUser->RowsReturned > 0){
            $authLevel = $resGetUser->Data[0]->authorization_level;
            $username = $resGetUser->Data[0]->username;
            $name = $resGetUser->Data[0]->name;
            $id = $resGetUser->Data[0]->id;
            $sessionHelper = new cp_session_helper();
            $sessionHelper->startNewSession();
            $sessionHelper->addSessionValue('AuthorizationLevel', $authLevel);
            $sessionHelper->addSessionValue('Name', $name);
            $sessionHelper->addSessionValue('Username', $username);
            $sessionHelper->addSessionValue('UserId', $id);

            return $resGetUser->Data;
        }else{
            return false;
        }

    }
}
?>
