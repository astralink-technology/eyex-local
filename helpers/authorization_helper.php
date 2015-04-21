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

class cp_authorization_helper{
    protected $userAuthorizationLevel = 100;
    protected $adminAuthorizationLevel = 500;

    //check if already authorized
    public function checkAlreadyAuthorized(){
        $sessionHelper = new cp_session_helper();
        $currentAuthorizationLevel = $sessionHelper->getSessionValue("AuthorizationLevel");
        if (session_id() != null     && $currentAuthorizationLevel == 300){
            header("Location: " .  '/eyex-lite/profile.php');
        }else if (session_id() != null && $currentAuthorizationLevel >= 400){
            header("Location: " .  '/eyex-lite/company.php');
        }else{
            return;
        }
    }

    //sets Authorization to a page / action
    public function setAuthorization($authorizationLevel, $redirectUrl){
        $sessionHelper = new cp_session_helper();
        $currentAuthorizationLevel = $sessionHelper->getSessionValue("AuthorizationLevel");

        $authorization = true;
        if ($authorizationLevel > $currentAuthorizationLevel){
            $authorization = false;
        }
        
        if ($authorization == false){
            header("Location: " .  $redirectUrl);
            return;
        }else{
            return; //proceed
        }
    }
    
    public function setAuthorizationRes($authorizationLevel){
        $sessionHelper = new cp_session_helper();
        $currentAuthorizationLevel = $sessionHelper->getSessionValue("AuthorizationLevel");
        $resDataHelper = new cp_resData_helper();
        
        $authorization = true;
        if ($authorizationLevel > $currentAuthorizationLevel){
            $authorization = false;
        }
        
        if ($authorization == false){
            return true;
            $resDataHelper->dataResponse(null, "401", "Unauthorized", true);
        }else{
            return false;
            $resDataHelper->dataResponse(null, null, null, false);
        }
    }
    
    //sets Authorization to a page for user
    public function setUserAuthorization($redirectUrl = null){
        $this->setAuthorization($this->userAuthorizationLevel, $redirectUrl);
        return;
    }
    
    
    //sets Authorization to a page for admin
    public function setAdminAuthorization($redirectUrl = null){
        return $this->setAuthorization($this->adminAuthorizationLevel, $redirectUrl);
        return;
    }
    
    //sets Authorization Response to an action / page for user
    public function setUserAuthorizationRes(){
        $this->setAuthorizationRes($this->userAuthorizationLevel);
    }
    
    //sets Authorization Response to an action / page for admin
    public function setAdminAuthorizationRes(){
        $this->setAuthorizationRes($this->adminAuthorizationLevel);
    }
    
}
?>
