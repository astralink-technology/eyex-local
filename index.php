<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-local/layout/headers.php';
?>
<!DOCTYPE html>
<html lang="en" ng-app="eyexApp">
<head>
    <meta charset="utf-8">
    <title>Employees</title>
    <?php
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/eyex-local/layout/master.php');
    ?>
</head>
    <body class="landing" ng-controller="landingController"><div class="jumbotron">
        <h1>EyeX Local</h1>
        <p>Tracking changes from cloud using pusher</p>
    </div>
    <div class="col-md-12">
        <h2>Pusher Logs</h2>
        <div class="alert alert-success" role="alert" ng-repeat="log in pusherLogs">
            {{log.message}}
        </div>
    </div>
    </body>
</html>