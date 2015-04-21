<?php
/*
 * Copyright Chilli Panda
 * Created on 03-01-2013
 * Created by Shi Wei Eamon
 */

/*
 *  A helper to initiate a session, add Items to session and remove session or destroy session
 */

class cp_session_helper{
    public function startNewSession(){
        if (session_id() == null){
            session_start();
        }
        $sessionId = session_id();
        $this->addSessionValue("sessionId", $sessionId);
    }

    public function addSessionValue($sessionName, $value){
        if (session_id() == null){
            session_start();
        }
        $_SESSION[$sessionName]= $value;
    }
    
    public function removeSessionValue($sessionName){
        if (session_id() == null){
            session_start();
        }
        if(isset($_SESSION[$sessionName])){
            unset($_SESSION[$sessionName]); 
        }
    }
    
    public function getSessionValue($sessionValueName){
        if (session_id() == null){
            session_start();
        }
        if(isset($_SESSION[$sessionValueName])){
            return $_SESSION[$sessionValueName];
        }else{
            return false;
        }
    }
    
    public function destroySession(){
        if (session_id() == null){
            session_start();
        }
        session_unset();
        // destroy the session 
        session_destroy();  
    }
}
?>
