<?php
/*
 * Copyright Chilli Panda
 * Created on 01-03-2013
 * Created by Shi Wei Eamon
 */

/*
 *  A helper to connect you to the database
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/config/webConfig.php';

class cp_sqlConnection_helper{

    private $prodHost = "192.168.1.86";
    private $prodUser = "cety";
    private $prodPassword = "astralink";
    private $prodDb = "VS";

//    private $host = "192.168.1.86";
//    private $db = "VS";
//    private $user = "cety";
//    private $password = "astralink";

    private $host = "192.168.1.86";
    private $user = "cety";
    private $db = "VS";
    private $password = "astralink";

    protected $connectionString = NULL;

    public function initializeMysqliConnection(){

        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;

        $host = $this->host;
        $user = $this->user;
        $db = $this->db;
        $password = $this->password;

        if ($production == true){
            $host = $this->prodHost;
            $user = $this->prodUser;
            $db = $this->prodDb;
            $password = $this->prodPassword;
        }

        $conString = new mysqli($host, $user, $password, $db);
        if ($conString->connect_errno) {
            echo "Failed to connect to MySQL: (" . $conString->connect_errno . ") " . $conString->connect_error;
            return;
        }
        return $conString;
    }

    public function dbDisconnectMysqli($conString){
        mysqli_close($conString);
    }

    public function initializeConnection(){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $production = $webConfigObj->production;

        $host = $this->host;
        $user = $this->user;
        $db = $this->db;
        $password = $this->password;

        if ($production == true){
            $host = $this->prodHost;
            $user = $this->prodUser;
            $db = $this->prodDb;
            $password = $this->prodPassword;
            error_reporting(0);
        }

        $link = mysql_connect($host,$user,$password);
        if (!$link) {
            die('Could not connect: ' . mysql_error());
        }
        if (!mysql_select_db($db)) {
            die('Could not select database: ' . mysql_error());
        }

        $this->connectionString = $link;

        return $link;
    }
    
    function dbDisconnect(){
        mysql_close($this->connectionString);
    }

}
?>
