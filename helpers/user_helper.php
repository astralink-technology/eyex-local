<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

class cp_user_helper{
    function checkIfUserImageExists($authLevel, $userId){
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/doorphpscript/comp_logo/logo.jpg";
        if ($authLevel == 300){
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/data/" . $userId . "/profile-pic.jpg";
        }

        if (file_exists($targetDir) == true){
            if ($authLevel == 300){
                return '../../../data/' . $userId . "/profile-pic.jpg";
            }else{
                return '../../../doorphpscript/comp_logo/logo.jpg';
            }
        }else{
            return false;
        }
    }
}
 
?>

