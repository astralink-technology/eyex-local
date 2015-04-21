<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 */

/*
 * A helper to render a view from a file
 */
class cp_resData_helper{
    public function scriptGenTypeResponse($parameters, $scriptType, $errorCode, $errorDesc, $error){
        $dataResponse = array(
            "parameters" => $parameters,
            "scriptType" => $scriptType,
            "errorCode" => $errorDesc,
            "errorDesc" => $errorDesc,
            "error" => $error
        );

        echo json_encode($dataResponse);
    }

    public function dataResponse($data, $errorCode, $errorDesc, $error, $totalRowsAvailable = null){
        //no rows specified fallback plan
        if ($totalRowsAvailable === null){
            $dataResponse = array(
                "data" => $data,
                "errorCode" => $errorCode,
                "errorDesc" => $errorDesc,
                "error" => $error
            );
        }else {
            if ($totalRowsAvailable < 1){
                $data = array();
                $dataResponse = array(
                    "totalRowsAvailable" => 0,
                    "data" => $data,
                    "errorCode" => $errorCode,
                    "errorDesc" => $errorDesc,
                    "error" => $error
                );
            }else{
                $dataResponse = array(
                    "totalRowsAvailable" => $totalRowsAvailable,
                    "data" => $data,
                    "errorCode" => $errorCode,
                    "errorDesc" => $errorDesc,
                    "error" => $error
                );
            }
        }

        echo json_encode($dataResponse);
    }
}
?>

