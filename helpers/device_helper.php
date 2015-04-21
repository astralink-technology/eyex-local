<?php
/*
 * Copyright Chilli Panda
 * Created on 05-03-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper on devices
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/config/webConfig.php';

class cp_device_helper{

    public function getDeviceId(){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;

        $PBXFile = '/data/data/com.github.DroidPHP/files/EyeXIcf.xml';
        if ($production == false){
            $PBXFile = $_SERVER['DOCUMENT_ROOT'] . '/data/data/com.github.DroidPHP/files/EyeXIcf.xml';
        }

        $MacAddr = null;
        if (file_exists($PBXFile)) {
            $PBXContent = file_get_contents($PBXFile);
            if (strpos($PBXContent, 'StartUpConfig') === false) {
                $PBXContent = '<?xml version="1.0" encoding="utf-8"?><start></start>';
            }
            try {
                $GetPBX = new SimpleXMLElement($PBXContent);
                $MacAddr = $GetPBX->GetStatus[0]->MacAddr;
            } catch (Exception $e) {
                $MacAddr = "Not Found";
            }
        }

        return $MacAddr;
    }
}
?>
