<?php
/*
 * Copyright Chilli Panda
 * Created on 05-03-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper on reading and writing ini file
 */

class cp_ini_helper{
    public function safefilerewrite($fileName, $dataToSave){
        if ($fp = fopen($fileName, 'w')){
            $startTime = microtime();
            do
            {            $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if(!$canWrite) usleep(round(rand(0, 100)*1000));
            } while ((!$canWrite)and((microtime()-$startTime) < 1000));

            //file was locked so now we can store information
            if ($canWrite)
            {            fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }

    }
    public function read_ini($fileDir){
        $ini_array = null;
        if(file_exists($fileDir) == true){
            $ini_array = parse_ini_file($fileDir);
        }
        return $ini_array;
    }
    public function write_php_ini($array, $file)
    {
        $res = array();
        foreach($array as $key => $val)
        {
            if(is_array($val))
            {
                $res[] = "[$key]";
                foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
            }
            else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
        }
        $this->safefilerewrite($file, implode("\r\n", $res));
    }
}
?>
