<?php
/*
 * Copyright Chilli Panda
 * Created on 03-01-2013
 * Created by Shi Wei Eamon
 */

/*
 *  A helper to initiate a session, add Items to session and remove session or destroy session
 */

class cp_socket_helper{
    function writeToSocket(){
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $isJsonObject = null;
        $text = null;
        $jsonObject = null;
        $socketMessage = "";

        if (isset($request->Host) && isset($request->Port)){
            $host = $request->Host;
            $port = $request->Port;

            if (isset($request->IsJsonObject)) { $isJsonObject = $request->IsJsonObject; };
            if (isset($request->Text)) { $text = $request->Text; };
            if (isset($request->JsonObject)) { $jsonObject = $request->JsonObject; };

            if ($isJsonObject != null && $isJsonObject == true){
                $socketMessage = $jsonObject;
            }else{
                $socketMessage = $text;
            }


            //connects to the socket
            $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die(json_encode("Could not create socket"));
            $result = socket_connect($socket, "127.0.0.1", 9998) or die(json_encode("Could not connect to server"));

            //hex the message
            $formattedMsg = sprintf("6d3103eb%04x", strlen($socketMessage));
            $binmsg = pack('H*', $formattedMsg);

            //writes to socket
            socket_write ($socket, $binmsg, strlen($binmsg))or die(json_encode("Could not send data to server"));
            socket_write ($socket, $socketMessage, strlen($socketMessage))or die(json_encode("Could not send data to server"));

            $result = socket_read ($socket, 1024) or die(json_encode("Could not read server response"));

            $dataResponse = array(
                "Data" => $result,
                "ErrorCode" => null,
                "ErrorDesc" => null,
                "Error" => false
            );

            echo json_encode($dataResponse);

        }else{
            $dataResponse = array(
                "Data" => null,
                "ErrorCode" => 500,
                "ErrorDesc" => "Fatal Error",
                "Error" => true
            );
            echo json_encode($dataResponse);
        }
    }
}
?>