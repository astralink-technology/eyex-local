<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/sqlconnection_helper.php');

    $dbCon = new cp_sqlConnection_helper();
    $con = $dbCon->initializeConnection();
    var_dump($con);

?>