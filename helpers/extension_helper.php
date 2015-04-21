<?php
/*
 * Copyright Chilli Panda
 * Created on 05-03-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper on extensions
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/config/webConfig.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/helpers/sqlconnection_helper.php';

class cp_extension_helper{

    public function checkIfExceedLimit(){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;

        $PBXFile = '/data/data/com.github.DroidPHP/files/EyeXIcf.xml';
        if ($production == false){
            $PBXFile = $_SERVER['DOCUMENT_ROOT'] . '/data/data/com.github.DroidPHP/files/EyeXIcf.xml';
        }

        $ExtensionLimit = null;
        if (file_exists($PBXFile)) {
            $PBXContent = file_get_contents($PBXFile);
            if (strpos($PBXContent,'StartUpConfig') === false) {
                $PBXContent = '<?xml version="1.0" encoding="utf-8"?><start></start>';
            }

            try {
                $GetPBX = new SimpleXMLElement($PBXContent);
                $ExtensionLimit = $GetPBX->GetStatus[0]->Limit;
                $PClass = $GetPBX->GetStatus[0]->SIP;
            } catch (Exception $e) {
                $ExtensionLimit = 10;
                }

            if ($ExtensionLimit == null){
                return false;
            }else{

                $sqlHelper = new cp_sqlConnection_helper();
                $sqlHelper->initializeConnection();

                $sql = "SELECT COUNT(*) AS extension_count FROM extension";
                $query = sprintf($sql);
                $sqlQuery = mysql_query($query);

                $resArray = array();
                while ($row = mysql_fetch_object($sqlQuery)) {
                    array_push($resArray, $row);
                }

                $existingExtensions = (int) $resArray[0]->extension_count;
                if($existingExtensions >= $ExtensionLimit){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            return true;
        }
    }
}
?>
