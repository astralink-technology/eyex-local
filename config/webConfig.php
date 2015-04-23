<?php
    class webconfig{
        public function webconfig(){
            $webConfigObj = new stdClass();
            $webConfigObj->production = false;
//            $webConfigObj->cloudServer = 'http://www.eyexcess.com';
            $webConfigObj->cloudServer = 'http://localhost:4000';
            return $webConfigObj;
        }
    }
?>