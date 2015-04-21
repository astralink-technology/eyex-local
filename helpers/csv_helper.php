<?php
/*
 * Copyright Chilli Panda
 * Created on 01-03-2013
 * Created by Shi Wei Eamon
 */

/*
 *  A helper to connect you csv everything under the sun
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/eyex-lite/helpers/sqlconnection_helper.php';
class cp_csv_helper{
    public function csvHeader($fileName){
        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $fileName . '.csv');
    }
}
?>
