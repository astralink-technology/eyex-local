<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/eyex-local/helpers/eyex_helper.php');

    $eyexHelper = new cp_eyex_helper();
    $deviceId = $eyexHelper->getDeviceId();

    echo $deviceId;
?>