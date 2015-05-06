<?php
/*
 * Copyright Chilli Panda
 * Created on 01-03-2013
 * Created by Shi Wei Eamon
 */

/*
 *  A helper to update employee status via door activity
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/ini_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/UTCconvertor_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/curl_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/config/webConfig.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/lib/generateAsteriskConfig.php';

class cp_eyex_helper{
    public function internetConnectivityCheck($webAddress, $port){
        $connected = @fsockopen($webAddress, $port);
        if ($connected){
            $is_conn = true;
            fclose($connected);
        }else{
            $is_conn = false;
        }
        return $is_conn;
    }

    public function checkInstallerExists(){
        $userDb = new userDao();
        $resGetUser = $userDb->getUser(null, null, null, null, null, null, null, null, null, null, null, null, null, null, 500);
        if ($resGetUser->Error == false && $resGetUser->RowsReturned > 0){
            return;
        }else{
            $addInstaller = $userDb->addUser(
                null
                , null
                , 'Authorized Installer'
                , 'installer@eyex.com'
                , 'admin'
                , 'installer'
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
                , 500
            );
            return;
        }
    }

    public function getDeviceId(){

        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;

        $iniHelper = new cp_ini_helper();

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/config/deviceId.ini';
        $deviceId = null;
        if ($production == true){
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/data/deviceId.ini';
        }

        $iniObject = $iniHelper->read_ini($dir);
        $deviceId = $iniObject['device_id'];

        return $deviceId;
    }

    public function clockInOut($deviceId, $userId){
        /*
         * I = Clock In
         * O = Clock Out
         * N = Enter
         * X = Exit
         * T = Tapped
         */
        $utcHelper = new cp_UTCconvertor_helper();
        $nowUTC = $utcHelper->getCurrentDateTime();

        //get the device type
        $deviceDb = new deviceDao();
        $accessDb = new accessDao();
        $userDb = new userDao();
        $doorRelationshipDb = new doorRelationshipDao();

        $vsDetect = false;
        $accessType = null;
        $deviceType = null;

        //detect if device id is a visitor station
        if ((substr($deviceId, -4) == '-888') ||(substr($deviceId, -4) == '-999')){
            $vsDetect = true;
        }

        if ($vsDetect == true){
            $deviceType = 'VS';
            if (substr($deviceId, -4) == '-888'){
                $accessType = 'I';
            }else if (substr($deviceId, -4) == '-999'){
                $accessType = 'O';
            }
            $deviceId = substr_replace($deviceId, "", -4);
        }else{
            //further check if the device is in or out
            $resGetDevice = $deviceDb->getDevice($deviceId);
            if ($resGetDevice->Error == false && $resGetDevice->RowsReturned > 0){
                $deviceType = $resGetDevice->Data[0]->device_type;
                if (empty($resGetDevice->Data[0]->type3)){
                    $accessType = 'T'; //Tapped the door
                }else{
                    $accessType = $resGetDevice->Data[0]->type3;
                }
            }
        }

        //using the device ID, get the door its controlling
        $resGetDoor = $doorRelationshipDb->getDoorRelationshipDetail(null, null, $deviceId);
        $doorName = null;
        $doorId = null;
        if ($resGetDoor->Error == false && $resGetDoor->RowsReturned > 0){
            $doorId = $resGetDoor->Data[0]->door_id;
            $doorName = $resGetDoor->Data[0]->door_name;
        }

        //add the access type
        $resAddAccess = $accessDb->addAccess(
            null
            , $nowUTC
            , null
            , null
            , null
            , $deviceId
            , $userId
            , $accessType
            , $doorId
            , $doorName
        );

        //change the user's status
        if ($accessType == 'I'){
            $resUpdateUser = $userDb->updateUser(
                $userId
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
                , null
                , 'I'
            );
        }else if ($accessType == 'O'){
            $resUpdateUser = $userDb->updateUser(
                $userId
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
                , null
                , 'A'
            );
        };

        return $resAddAccess;
    }

    public function checkFirstTimeSetup($redirect){
        $notcomplete = false;
        $dbHelper = new cp_sqlConnection_helper();

        //check for company account
        $sqlForAccount = "SELECT COUNT(*) AS company_count FROM user_name WHERE authorization_level = 400";
        $dbHelper->initializeConnection();
        // Perform Query
        $sqlQueryForAccount = mysql_query($sqlForAccount);
        $resArrayForAccount = array();
        while ($row = mysql_fetch_object($sqlQueryForAccount)) {
            array_push($resArrayForAccount, $row);
        }
        $dbHelper->dbDisconnect();

        //check for vistor station
        $sqlForDeviceType = "SELECT COUNT(*) AS device_count FROM device WHERE device_type = 'VS'";
        // Perform Query
        $dbHelper->initializeConnection();
        $sqlQueryForDeviceType = mysql_query($sqlForDeviceType);
        $resArrayForDeviceType = array();
        while ($row = mysql_fetch_object($sqlQueryForDeviceType)) {
            array_push($resArrayForDeviceType, $row);
        }
        $dbHelper->dbDisconnect();

        //check for office station
        $sqlForOsDeviceType = "SELECT COUNT(*) AS device_count FROM device WHERE device_type = 'OS'";
        // Perform Query
        $dbHelper->initializeConnection();
        $sqlQueryForOsDeviceType = mysql_query($sqlForOsDeviceType);
        $resArrayForOsDeviceType = array();
        while ($row = mysql_fetch_object($sqlQueryForOsDeviceType)) {
            array_push($resArrayForOsDeviceType, $row);
        }
        $dbHelper->dbDisconnect();

        if ((int)$resArrayForAccount[0]->company_count < 1){
            $notcomplete = 'company';
            if ($redirect == true){
                header('Location: /eyex-local/setup.php?step=company');
            }else{
                return $notcomplete;
            }
        }else if ((int)$resArrayForDeviceType[0]->device_count < 1){
            $notcomplete = 'visitor-station';
            if ($redirect == true){
                header( 'Location: /eyex-local/setup.php?step=visitor-station');
            }else{
                return $notcomplete;
            }
        }else if ((int)$resArrayForOsDeviceType[0]->device_count < 1){
            $notcomplete = 'office-station';
            return $notcomplete;
        }else{
            return $notcomplete;
        }
    }

    public function deviceBroadcast(){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;
        if ($production == true){
            $this->execShellCommand("/system/bin/am broadcast -a com.astralink.orcas.door.UPDATE_CONFIG --es name door:update");
        }
        return;
    }

    public function rs485Broadcast(){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;
        if ($production == true){
            $this->execShellCommand("/system/bin/am broadcast -a com.astralink.orcas.door.UPDATE_READER_CONFIG");
        }
    }

    public function rebootDeviceBroadcast(){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;
        if ($production == true){
            $this->execShellCommand("/system/bin/am broadcast  -a com.astralink.orcas.door.SYSTEM_REBOOT");
        }
    }

    public function doorSystemOverride($doorNode){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;
        if ($production == true){
            $this->execShellCommand("/system/bin/am broadcast -a com.astralink.orcas.door.SYSTEM_OVERRIDE --ei doorId " . $doorNode);
        }
    }

    public function enablePusher($enable){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;
        if ($production == true){
            if ($enable == true){
                $this->execShellCommand("/system/bin/am broadcast  -a droidphp.pusher --ei enable 1");
            }else if($enable == false){
                $this->execShellCommand('/system/bin/am broadcast  -a droidphp.pusher --ei enable 0');
            }
        }
    }

    public function updateStatus($status, $userId){
        $dbHelper = new cp_sqlConnection_helper();
        $dbHelper->initializeConnection();
        $sql = "UPDATE user_name SET  status = '%s' WHERE id = '%s'";

        $query = sprintf($sql
            , mysql_real_escape_string($status)
            , mysql_real_escape_string($userId));

        // Perform Query
        $sqlQuery = mysql_query($query);
        $dbHelper->dbDisconnect();
    }

    public function updateUserStatus($deviceId, $userId){
        if($deviceId == 888){
            updateStatus('I', $userId);
        }else if ($deviceId == 999){
            updateStatus('A', $userId);
        }
    }

    public function dbUpgrade(){
        error_reporting(0);
        $upgradeVersion = 1.0;

        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;

        $iniHelper = new cp_ini_helper();
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/config/version.ini';
        if ($production == true){
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/data/version.ini';
            if (!file_exists($dir)) {
                $versioniniFile = fopen($dir, "w");
                $dbVersionText = "db_version = 0.1";
                fwrite($versioniniFile, $dbVersionText);
                fclose($versioniniFile);
            }
        }

        $iniObject = $iniHelper->read_ini($dir);
        $currentVersion = $iniObject['db_version'];

        if ($currentVersion < $upgradeVersion){
            $dbHelper = new cp_sqlConnection_helper();

            $addAlterDir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/eyex-local-db/Alters/Add/';
            $modifyAlterDir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/eyex-local-db/Alters/Modify/';
            $dropAlterDir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/eyex-local-db/Alters/Drop/';
            $createTableDir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/eyex-local-db/Tables/';
            $spReadDir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/eyex-local-db/SP/Read/';
            $spUpdateDir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/eyex-local-db/SP/Update/';
            $spDeleteDir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/eyex-local-db/SP/Delete/';
            $spCreateDir = $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/eyex-local-db/SP/Create/';

            $addAlterFiles = null;
            $modifyAlterFiles = null;
            $dropAlterFiles = null;
            $createTableFiles = null;
            $spCreateFiles = null;
            $spReadFiles = null;
            $spUpdateFiles = null;
            $spDeleteFiles = null;

            if (is_dir($addAlterDir) == true) $addAlterFiles = scandir($addAlterDir);
            if (is_dir($modifyAlterDir) == true) $modifyAlterFiles = scandir($modifyAlterDir);
            if (is_dir($dropAlterDir) == true) $dropAlterFiles = scandir($dropAlterDir);
            if (is_dir($createTableDir) == true) $createTableFiles = scandir($createTableDir);
            if (is_dir($spCreateDir) == true) $spCreateFiles = scandir($spCreateDir);
            if (is_dir($spReadDir) == true) $spReadFiles = scandir($spReadDir);
            if (is_dir($spUpdateDir) == true) $spUpdateFiles = scandir($spUpdateDir);
            if (is_dir($spDeleteDir) == true) $spDeleteFiles = scandir($spDeleteDir);

            if ($createTableFiles != null){
                //get the create tables
                foreach ($createTableFiles as $key => $value)
                {
                    if (!in_array($value,array(".","..")))
                    {
                        if (is_dir($createTableFiles . DIRECTORY_SEPARATOR . $value))
                        {
                            $createTableScripts[$value] = dirToArray($createTableFiles . DIRECTORY_SEPARATOR . $value);
                        }
                        else
                        {
                            $createTableScripts[] = $value;
                        }
                    }
                }
                //execute all the create table scripts
                if (count($createTableScripts) > 0){
                    $conString = $dbHelper->initializeMysqliConnection();
                    foreach($createTableScripts as $createTableScript){

                        $file_content = file($createTableDir . $createTableScript);
                        $sql = implode($file_content);
                        $rank_query = mysqli_multi_query($conString, $sql);
                        if($rank_query) {
                            while(mysqli_more_results($conString) && mysqli_next_result($conString)) {
                                if($result = mysqli_store_result($conString)) {
                                    while($row = mysqli_fetch_row($result)){
                                        if (mysqli_error($conString) != null){
                                            print_r(mysqli_error($conString));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $dbHelper->dbDisconnectMysqli($conString);
                }
            }

            if ($addAlterFiles != null){
                //get the alters
                foreach ($addAlterFiles as $key => $value)
                {
                    if (!in_array($value,array(".","..")))
                    {
                        if (is_dir($addAlterFiles . DIRECTORY_SEPARATOR . $value))
                        {
                            $addAlterScripts[$value] = dirToArray($addAlterFiles . DIRECTORY_SEPARATOR . $value);
                        }
                        else
                        {
                            $addAlterScripts[] = $value;
                        }
                    }
                }
                //execute all the alter scripts
                if (count($addAlterScripts) > 0){
                    $conString = $dbHelper->initializeMysqliConnection();
                    foreach($addAlterScripts as $addAlterScript){
                        $file_content = file($addAlterDir . $addAlterScript);
                        $sql = implode($file_content);
                        $rank_query = mysqli_multi_query($conString, $sql);
                        if($rank_query) {
                            while(mysqli_more_results($conString) && mysqli_next_result($conString)) {
                                if($result = mysqli_store_result($conString)) {
                                    while($row = mysqli_fetch_row($result)){
                                        if (mysqli_error($conString) != null){
                                            print_r(mysqli_error($conString));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $dbHelper->dbDisconnectMysqli($conString);
                }
            }

            if ($modifyAlterFiles != null){
                //get the alters
                foreach ($modifyAlterFiles as $key => $value)
                {
                    if (!in_array($value,array(".","..")))
                    {
                        if (is_dir($modifyAlterFiles . DIRECTORY_SEPARATOR . $value))
                        {
                            $modifyAlterScripts[$value] = dirToArray($modifyAlterFiles . DIRECTORY_SEPARATOR . $value);
                        }
                        else
                        {
                            $modifyAlterScripts[] = $value;
                        }
                    }
                }
                //execute all the alter scripts
                if (count($modifyAlterScripts) > 0){
                    $conString = $dbHelper->initializeMysqliConnection();
                    foreach($modifyAlterScripts as $modifyAlterScript){
                        $file_content = file($modifyAlterDir . $modifyAlterScript);
                        $sql = implode($file_content);
                        $rank_query = mysqli_multi_query($conString, $sql);
                        if($rank_query) {
                            while(mysqli_more_results($conString) && mysqli_next_result($conString)) {
                                if($result = mysqli_store_result($conString)) {
                                    while($row = mysqli_fetch_row($result)){
                                        if (mysqli_error($conString) != null){
                                            print_r(mysqli_error($conString));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $dbHelper->dbDisconnectMysqli($conString);
                }
            }

            if ($spReadFiles != null){
                //get the read
                foreach ($spReadFiles as $key => $value)
                {
                    if (!in_array($value,array(".","..")))
                    {
                        if (is_dir($spReadFiles . DIRECTORY_SEPARATOR . $value))
                        {
                            $spReadScripts[$value] = dirToArray($spReadFiles . DIRECTORY_SEPARATOR . $value);
                        }
                        else
                        {
                            $spReadScripts[] = $value;
                        }
                    }
                }
                //execute all the read sp scripts
                if (count($spReadScripts) > 0){
                    $conString = $dbHelper->initializeMysqliConnection();
                    foreach($spReadScripts as $spReadScript){

                        $file_content = file($spReadDir . $spReadScript);
                        $sql = implode($file_content);
                        $rank_query = mysqli_multi_query($conString, $sql);
                        if($rank_query) {
                            while(mysqli_more_results($conString) && mysqli_next_result($conString)) {
                                if($result = mysqli_store_result($conString)) {
                                    while($row = mysqli_fetch_row($result)){
                                        if (mysqli_error($conString) != null){
                                            print_r(mysqli_error($conString));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $dbHelper->dbDisconnectMysqli($conString);
                }
            }

            if ($spCreateFiles != null){
                //get the create
                foreach ($spCreateFiles as $key => $value)
                {
                    if (!in_array($value,array(".","..")))
                    {
                        if (is_dir($spCreateFiles . DIRECTORY_SEPARATOR . $value))
                        {
                            $spCreateScripts[$value] = dirToArray($spCreateFiles . DIRECTORY_SEPARATOR . $value);
                        }
                        else
                        {
                            $spCreateScripts[] = $value;
                        }
                    }
                }
                //execute all the create sp scripts
                if (count($spCreateScripts) > 0){
                    $conString = $dbHelper->initializeMysqliConnection();
                    foreach($spCreateScripts as $spCreateScript){

                        $file_content = file($spCreateDir . $spCreateScript);
                        $sql = implode($file_content);
                        $rank_query = mysqli_multi_query($conString, $sql);
                        if($rank_query) {
                            while(mysqli_more_results($conString) && mysqli_next_result($conString)) {
                                if($result = mysqli_store_result($conString)) {
                                    while($row = mysqli_fetch_row($result)){
                                        if (mysqli_error($conString) != null){
                                            print_r(mysqli_error($conString));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $dbHelper->dbDisconnectMysqli($conString);
                }
            }

            if ($spDeleteFiles != null){
                //get the delete
                foreach ($spDeleteFiles as $key => $value)
                {
                    if (!in_array($value,array(".","..")))
                    {
                        if (is_dir($spDeleteFiles . DIRECTORY_SEPARATOR . $value))
                        {
                            $spDeleteScripts[$value] = dirToArray($spDeleteFiles . DIRECTORY_SEPARATOR . $value);
                        }
                        else
                        {
                            $spDeleteScripts[] = $value;
                        }
                    }
                }
                //execute all the delete sp scripts
                if (count($spDeleteScripts) > 0){
                    $conString = $dbHelper->initializeMysqliConnection();
                    foreach($spDeleteScripts as $spDeleteScript){

                        $file_content = file($spDeleteDir . $spDeleteScript);
                        $sql = implode($file_content);
                        $rank_query = mysqli_multi_query($conString, $sql);
                        if($rank_query) {
                            while(mysqli_more_results($conString) && mysqli_next_result($conString)) {
                                if($result = mysqli_store_result($conString)) {
                                    while($row = mysqli_fetch_row($result)){
                                        if (mysqli_error($conString) != null){
                                            print_r(mysqli_error($conString));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $dbHelper->dbDisconnectMysqli($conString);
                }
            }

            if ($spUpdateFiles != null){
                //get the update
                foreach ($spUpdateFiles as $key => $value)
                {
                    if (!in_array($value,array(".","..")))
                    {
                        if (is_dir($spUpdateFiles . DIRECTORY_SEPARATOR . $value))
                        {
                            $spUpdateScripts[$value] = dirToArray($spUpdateFiles . DIRECTORY_SEPARATOR . $value);
                        }
                        else
                        {
                            $spUpdateScripts[] = $value;
                        }
                    }
                }
                //execute all the update sp scripts
                if (count($spUpdateScripts) > 0){
                    $conString = $dbHelper->initializeMysqliConnection();
                    foreach($spUpdateScripts as $spUpdateScript){

                        $file_content = file($spUpdateDir . $spUpdateScript);
                        $sql = implode($file_content);
                        $rank_query = mysqli_multi_query($conString, $sql);
                        if($rank_query) {
                            while(mysqli_more_results($conString) && mysqli_next_result($conString)) {
                                if($result = mysqli_store_result($conString)) {
                                    while($row = mysqli_fetch_row($result)){
                                        if (mysqli_error($conString) != null){
                                            print_r(mysqli_error($conString));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $dbHelper->dbDisconnectMysqli($conString);
                }
            }

            if ($dropAlterFiles != null){
                //get the alters
                foreach ($dropAlterFiles as $key => $value)
                {
                    if (!in_array($value,array(".","..")))
                    {
                        if (is_dir($dropAlterFiles . DIRECTORY_SEPARATOR . $value))
                        {
                            $dropAlterScripts[$value] = dirToArray($dropAlterFiles . DIRECTORY_SEPARATOR . $value);
                        }
                        else
                        {
                            $dropAlterScripts[] = $value;
                        }
                    }
                }
                //execute all the alter scripts
                if (count($dropAlterScripts) > 0){
                    $conString = $dbHelper->initializeMysqliConnection();
                    foreach($dropAlterScripts as $dropAlterScript){
                        $file_content = file($dropAlterDir . $dropAlterScript);
                        $sql = implode($file_content);
                        $rank_query = mysqli_multi_query($conString, $sql);
                        if($rank_query) {
                            while(mysqli_more_results($conString) && mysqli_next_result($conString)) {
                                if($result = mysqli_store_result($conString)) {
                                    while($row = mysqli_fetch_row($result)){
                                        if (mysqli_error($conString) != null){
                                            print_r(mysqli_error($conString));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $dbHelper->dbDisconnectMysqli($conString);
                }
            }

            $newDbConfig = array();
            $newDbConfig['db_version'] = $upgradeVersion;
            $iniObject = $iniHelper->write_php_ini($newDbConfig, $dir);

            return 'DATABASE UPGRADED TO v' . $upgradeVersion;
        }else{
            return 'UP TO DATE v' . $currentVersion;
        }
    }

    public function getExternalIp(){
            $externalContent = file_get_contents('http://checkip.dyndns.com/');
            preg_match('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', $externalContent, $m);
            $externalIp = $m[0];

            return $externalIp;
    }

    public function execShellCommand($Command){
	    $pipeDesc =   array(
      		array("pipe","r"),
         	array("pipe","w"),
              	array("pipe","w")
            );

	    $process = proc_open($Command, $pipeDesc, $pipes);
	    proc_close($process);
     
            return;
    }

    public function reloadAsteriskConf(){
    	$astConfig = new AsteriskConfig();
    	$astConfig->generate(); 
        return;
    }
}
?>
