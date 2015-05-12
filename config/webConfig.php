<?php
    class webconfig{
        public function webconfig(){
            $webConfigObj = new stdClass();

            //production / development mode
            $webConfigObj->production = true;

            //cloud server configuration
            $webConfigObj->cloudServer = 'http://www.eyexcess.com';
            if ($webConfigObj->production == false){
                $webConfigObj->cloudServer = 'http://localhost:4000';
            }

            //Astralink AppID and Token
            $webConfigObj->appId = 'C81R582S-WKKYXTBA-LG4CI8AI';
            $webConfigObj->token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJFbnRlcnByaXNlSWQiOiJUOFJITEJGMS1FVlc4TTFDRi1IRENGVkdPViJ9.LG1_IlF-RezAw_IFAHqG2i6z1kcp6UdPHbq84aBSW14';

            return $webConfigObj;
        }
    }
?>