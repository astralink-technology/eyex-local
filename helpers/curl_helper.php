<?php

class cp_curl_helper{
    public function curlPost($postUrl, $params){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $cloudServer = $webConfigObj->cloudServer;

        //set POST variables
        $url = $cloudServer . $postUrl;

        $params_string = '';
        //url-ify the data for the POST
        foreach($params as $key=>$value) { $params_string .= $key.'='.$value.'&'; }
        rtrim($params_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($params));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false); //disable ssl
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);

        if(curl_errno($ch))
        {
            return curl_errno($ch);
        }else{
            //close connection
            curl_close($ch);
        }

        return json_decode($result);
    }

    public function curlGet($getUrl, $params){
        $webConfig = new webconfig();
        $webConfigObj = $webConfig->webconfig();
        $cloudServer = $webConfigObj->cloudServer;

        //set POST variables
        if ($params != null){
            $url = $cloudServer . $getUrl . '?' . $params;
        }else{
            $url = $cloudServer . $getUrl;
        }

        //open connection
        $ch = curl_init();

        //set the url and the GET vars
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);
        return json_decode($result);
    }
}
?>
