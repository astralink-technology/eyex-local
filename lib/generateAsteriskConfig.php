<?php
require_once ('conn.php');
class AsteriskConfig{
    

    function generate(){
        $sqlconnect = new conn();
        $conn = $sqlconnect->dbConnect();     
        $prefix = 9;//file_get_contents('../../../files/prefix.txt');  
        $operator = "10000";
        $remoteDoorCode =  "9";
        $localDoorCode =  "1234";
        $extDoorCode =  "888";
        $voicemailCode =  "123";
        $voicePassword =  "12345";
        $voicemailExtCode =  "99";
        $pickUpCode =  "8";
        $DroidPHPPath = "/data/data/org.opendroidphp";
        
     
     
        $sqloperator = "SELECT * FROM operator";
        
        $resultOperator = mysqli_query($conn, $sqloperator);
        if ($resultOperator) {
            $num_resultOperator = mysqli_num_rows($resultOperator);

            for($i=0;$i<$num_resultOperator;$i++)
            {
                $rowOperator=(mysqli_fetch_array($resultOperator, MYSQLI_ASSOC));
                $operator =  $rowFeatures['extension'];
            }

        }
        
        $sqlFeatures = "SELECT * FROM features";
        
        $resultFeatures = mysqli_query($conn, $sqlFeatures);
        if ($resultFeatures) {
            $num_resultFeatures = mysqli_num_rows($resultFeatures);

            for($i=0;$i<$num_resultFeatures;$i++)
            {
                $rowFeatures=(mysqli_fetch_array($resultFeatures, MYSQLI_ASSOC));
                $remoteDoorCode =  $rowFeatures['remote_door'];
                $localDoorCode =  $rowFeatures['local_door'];
                $extDoorCode =  $rowFeatures['extension_door'];
                $voicemailCode =  $rowFeatures['voicemail'];
                $voicePassword =  $rowFeatures['voicemail_password'];
                $voicemailExtCode =  $rowFeatures['voicemail_extension'];
                $pickUpCode =  $rowFeatures['pickup'];
            }

        } 
        
        
        $featuresContent = "[general]
pickupexten = *" . $pickUpCode . "              

[applicationmap]
localdooropen => #" . $localDoorCode . ",peer,Macro,localdooropen
remotedooropen => #" . $remoteDoorCode . ",peer,Macro,remotedooropen
";


     
        $sipContent = "#include \"sip_custom.conf\"
[general]\n";
     
        $extensionContent = "
#include \"extensions_custom.conf\"
[macro-localdooropen] 
exten => s,1,SET(FROM=\${MACRO_EXTEN})
;same => n,NoOp(\${SHELL(" . $DroidPHPPath . "/php-cgi -c " . $DroidPHPPath . "/htdocs/conf/php.ini " . $DroidPHPPath . "/htdocs/www/doorphpscript/phone_access.php PhoneNo=\${FROM})})
same => n,System(/sbin/sh /system/bin/am broadcast -a com.astralink.orcas.one.officeclient.OPEN_DOOR --es name door:open)

[macro-remotedooropen]
exten => s,1,SET(FROM=\${CALLERID(num)})
;same => n,NoOp(\${SHELL(" . $DroidPHPPath . "/php-cgi -c " . $DroidPHPPath . "/htdocs/conf/php.ini " . $DroidPHPPath . "/htdocs/www/doorphpscript/phone_access.php PhoneNo=\${FROM})})
same => n,System(/sbin/sh /system/bin/am broadcast -a com.astralink.orcas.one.officeclient.OPEN_DOOR --es name door:open)


[incoming]
exten => s,1,Answer()
same => n,Set(__DYNAMIC_FEATURES=remotedooropen)
";

        $sqlTimeoff = "SELECT * FROM timeoff";
    
        $resultTimeoff = mysqli_query($conn, $sqlTimeoff);
        if ($resultTimeoff) {
            $num_resultTimeoff = mysqli_num_rows($resultTimeoff);

            for($i=0;$i<$num_resultTimeoff;$i++)
            {
                $rowTimeoff=(mysqli_fetch_array($resultTimeoff, MYSQLI_ASSOC));
                $timezone =  $rowTimeoff['timezone'];
                $startDay =  $rowTimeoff['start_day'];
                $endDay =  $rowTimeoff['end_day'];
                $startTime =  $rowTimeoff['start_time'];
                $endTime =  $rowTimeoff['end_time'];                
                $extensionContent = $extensionContent . "same => n,GotoIfTime(" . $startTime . "-" . $endTime . "," . $startDay . "-" . $endDay . ",*,*," . $timezone . "?closed)\n";
            }

        }


        $extensionContent = $extensionContent . "same => n,Background(/data/asterisk/res/custom-ivr/voiceivr)
same => n,WaitExten(10)
same => n,Playback(/data/asterisk/res/custom-ivr/voiceivr-end)
same => n,Dial(SIP/" . $operator . ",60,r,t,)
same => n,VoiceMail(" . $operator . "@VoiceMail,u)
same => n,Hangup
same => n(closed),Playback(/data/asterisk/res/custom-ivr/awayivr)
same => n,Hangup


; operator
exten => 0,1,Dial(SIP/" . $operator . ",20)
exten => 0,n,VoiceMail(" . $operator . "@VoiceMail,u)
exten => 0,n,Hangup

exten => *" . $voicemailCode . ",1,VoiceMailMain(\${CALLERID(num)}@VoiceMail)\n\n
";


        $voicemailContent = "[general]
format=wav49|gsm|wav
serveremail=asterisk
attach=yes
skipms=3000
maxsilence=10
silencethreshold=128
maxlogins=3
emaildateformat=%A, %B %d, %Y at %r
pagerdateformat=%A, %B %d, %Y at %r
sendvoicemail=yes 
; Limit the maximum message length to 180 seconds
maxmessage=180           
; Limit the minimum message length to 3 seconds         
minmessage=3
; Limit the maximum voicemail to 50 voicemail         
maxmsg=50


[zonemessages]
eastern=America/New_York|'vm-received' Q 'digits/at' IMp
central=America/Chicago|'vm-received' Q 'digits/at' IMp
central24=America/Chicago|'vm-received' q 'digits/at' H N 'hours'
military=Zulu|'vm-received' q 'digits/at' H N 'hours' 'phonetic/z_p'
european=Europe/Copenhagen|'vm-received' a d b 'digits/at' HM 

[VoiceMail]
";
    
    
    
    
      
        $sqlProvider = "SELECT * FROM sip_provider";
        $resultProvider = mysqli_query($conn, $sqlProvider) or die(mysqli_error($conn));
        $num_results = mysqli_num_rows($resultProvider);

        for($i=0;$i<$num_results;$i++)
        {
            $rowProvider=(mysqli_fetch_array($resultProvider, MYSQLI_ASSOC));
            $prefix =  $rowProvider['id'];
            $sipContent = $sipContent . "register =>" . $rowProvider['username'] . ":" . $rowProvider['password'] . "@" . $rowProvider['host'] . "/s\n";
        }
        $sipContent = $sipContent . 
        "registertimeout=20
context=incoming
allowoverlap=no
bindport=5060
bindaddr=0.0.0.0
srvlookup=yes
subscribecontext=from-sip
sipdebug=yes
canreinvite=no
caninvite=no
session-timers=refuse
allowguest=no
insecure=invite,port
rtcachefriends=yes
callcounter=yes
useragent=Astralink eyeX
t1min=500
timert1=1000
timerb=32000
qualify=yes

";

        $sqlProvider1 = "SELECT * FROM sip_provider";
        $resultProvider1 = mysqli_query($conn, $sqlProvider1) or die(mysqli_error($conn));
        $num_results1 = mysqli_num_rows($resultProvider1);
        for($i=0;$i<$num_results1;$i++)
        {
            $rowProvider1=(mysqli_fetch_array($resultProvider1, MYSQLI_ASSOC));
            $sipContent = $sipContent . "[VoIPProvider]
canreinvite=no
username=" . $rowProvider1['username'] . "
authuser=" . $rowProvider1['username'] . "
fromuser=" . $rowProvider1['username'] . "
secret=" . $rowProvider1['password'] . "
context=incoming
type=friend
fromdomain=" . $rowProvider1['host'] . "
host=" . $rowProvider1['host'] . "
dtmfmode=rfc2833
disallow=all
allow=alaw
allow=ulaw


";
            
        }        
        
        $sqlExtension = "SELECT * FROM extension";
        $resultExtension = mysqli_query($conn, $sqlExtension) or die(mysqli_error($conn));
        $num_resultsExtension = mysqli_num_rows($resultExtension);    
    
        for($i=0;$i<$num_resultsExtension;$i++)
        {
            $rowExtension=(mysqli_fetch_array($resultExtension, MYSQLI_ASSOC));
            $sipContent = $sipContent . "[" . $rowExtension['extension_number'] . "]
type=friend
host=dynamic
defautluser=" . $rowExtension['extension_number'] . "
secret=" . $rowExtension['password'] . "
context=internal
mailbox=" . $rowExtension['extension_number'] . "@VoiceMail
callgroup=1
pickupgroup=1
dtmfmode=rfc2833
canreinvite=no
disallow=all
allow=alaw
allow=ulaw";
    
            if($rowExtension['video_call']){
                $sipContent = $sipContent .
              "
videosupport=yes
allow=h264
allow=h263p
allow=mpeg4";
              
            }
            $sipContent = $sipContent . "\n\n";
            if($rowExtension['voice_mail']){
                $voicemailContent = $voicemailContent . $rowExtension['extension_number'] . "=>" . $voicePassword . ",astralink technology, info@astralink.com.sg,, maxmsg=40\n";
            }
        
            $sqlContact = "SELECT * FROM user_name where id=" . $rowExtension['user_id'];
            $resultContact = mysqli_query($conn, $sqlContact) or die(mysqli_error($conn));
            $num_resultsContact = mysqli_num_rows($resultContact);    
    
            for($j=0;$j<$num_resultsContact;$j++){                                                  
                $rowContact=(mysqli_fetch_array($resultContact, MYSQLI_ASSOC));
                if(($rowContact['sip'] != null) AND ($rowContact['sip2'] != null) ){
             $extensionContent = $extensionContent . "exten => " . $rowExtension['extension_number'] . ",1,Dial(SIP/VoIPProvider/" .  $rowContact['sip'] . ",20)
exten => " . $rowExtension['extension_number'] . ",2,GotoIf($[ \"\${DIALSTATUS}\" = \"BUSY\" ]?busy)
exten => " . $rowExtension['extension_number'] . ",3,Dial(SIP/VoIPProvider/" . $rowContact['sip2'] . ",20)
exten => " . $rowExtension['extension_number'] . ",n,VoiceMail(" . $rowExtension['extension_number'] . "@VoiceMail,u)
exten => " . $rowExtension['extension_number'] . ",n(busy),Hangup

"; 
                }elseif (($rowContact['sip'] != null) AND ($rowContact['sip2'] == null) ){
                    $extensionContent = $extensionContent . "exten => " . $rowExtension['extension_number'] . ",1,Dial(SIP/VoIPProvider/" . $rowContact['sip'] . ",20)
exten => " . $rowExtension['extension_number'] . ",2,GotoIf($[ \"\${DIALSTATUS}\" = \"BUSY\" ]?busy)
exten => " . $rowExtension['extension_number'] . ",n,VoiceMail(" . $rowExtension['extension_number'] . "@VoiceMail,u)
exten => " . $rowExtension['extension_number'] . ",n(busy),Hangup

";             
            
                }elseif (($rowContact['sip'] == null) AND ($rowContact['sip2'] != null) ){
                    $extensionContent = $extensionContent . "exten => " . $rowExtension['extension_number'] . ",1,Dial(SIP/" . $rowExtension['extension_number'] . ",20)
exten => " . $rowExtension['extension_number'] . ",2,GotoIf($[ \"\${DIALSTATUS}\" = \"BUSY\" ]?busy)
exten => " . $rowExtension['extension_number'] . ",3,Dial(SIP/VoIPProvider/" . $rowContact['sip2'] . ",20)
exten => " . $rowExtension['extension_number'] . ",n,VoiceMail(" . $rowExtension['extension_number'] . "@VoiceMail,u)
exten => " . $rowExtension['extension_number'] . ",n(busy),Hangup

";
            
                }else{
                    $extensionContent = $extensionContent . "exten => " . $rowExtension['extension_number'] . ",1,Dial(SIP/" . $rowExtension['extension_number'] . ",20)
exten => " . $rowExtension['extension_number'] . ",2,GotoIf($[ \"\${DIALSTATUS}\" = \"BUSY\" ]?busy)
exten => " . $rowExtension['extension_number'] . ",n,VoiceMail(" . $rowExtension['extension_number'] . "@VoiceMail,u)
exten => " . $rowExtension['extension_number'] . ",n(busy),Hangup

";        
                }
            }                                    
        }
    
        $extensionContent = $extensionContent . "[internal]
exten => *" . $voicemailCode . ",1,VoiceMailMain(\${CALLERID(num)}@VoiceMail)

exten => #" . $extDoorCode . ",1,System(/sbin/sh /system/bin/am broadcast -a com.astralink.orcas.one.officeclient.OPEN_DOOR --es name door:open)
exten => #" . $extDoorCode . ",2,Hangup


exten => *" .$voicemailExtCode . ",1,VoiceMailMain(@VoiceMail)

exten => _" . $prefix . ".,1,Dial(SIP/VoIPProvider/\${EXTEN:1})

exten => 0,1,Set(__DYNAMIC_FEATURES=localdooropen)                           
exten => 0,2,Dial(SIP/" . $operator . ",20)
exten => 0,n,Hangup


";

        $sqlExtension1 = "SELECT * FROM extension";
        $resultExtension1 = mysqli_query($conn, $sqlExtension1) or die(mysqli_error($conn));
        $num_resultsExtension1 = mysqli_num_rows($resultExtension1); 
        for($i=0;$i<$num_resultsExtension1;$i++)
        {
            $rowExtension1=(mysqli_fetch_array($resultExtension1, MYSQLI_ASSOC));
            $sqlContact1 = "SELECT * FROM user_name where id=" . $rowExtension1['user_id'];
            $resultContact1 = mysqli_query($conn, $sqlContact1) or die(mysqli_error($conn));
            $num_resultsContact1 = mysqli_num_rows($resultContact1);    
            for($j=0;$j<$num_resultsContact1;$j++){
                $rowContact1=(mysqli_fetch_array($resultContact1, MYSQLI_ASSOC));
                if(($rowContact1['sip'] != null) AND ($rowContact1['sip2'] != null) ){
                    $extensionContent = $extensionContent . "exten => " . $rowExtension1['extension_number'] . ",1,Set(__DYNAMIC_FEATURES=localdooropen)
exten => " . $rowExtension1['extension_number'] . ",2,Dial(SIP/VoIPProvider/" . $rowContact1['sip'] . ",20)
exten => " . $rowExtension1['extension_number'] . ",3,GotoIf($[ \"\${DIALSTATUS}\" = \"BUSY\" ]?busy)
exten => " . $rowExtension1['extension_number'] . ",4,Dial(SIP/VoIPProvider/" . $rowContact1['sip2'] . ",20)
exten => " . $rowExtension1['extension_number'] . ",n,VoiceMail(" . $rowExtension1['extension_number'] . "@VoiceMail,u)
exten => " . $rowExtension1['extension_number'] . ",n(busy),Hangup

"; 
                }elseif (($rowContact1['sip'] != null) AND ($rowContact1['sip2'] == null) ){
                    $extensionContent = $extensionContent . "exten => " . $rowExtension1['extension_number'] . ",1,Set(__DYNAMIC_FEATURES=localdooropen)
exten => " . $rowExtension1['extension_number'] . ",2,Dial(SIP/VoIPProvider/" . $rowContact1['sip'] . ",20)
exten => " . $rowExtension1['extension_number'] . ",3,GotoIf($[ \"\${DIALSTATUS}\" = \"BUSY\" ]?busy)
exten => " . $rowExtension1['extension_number'] . ",n,VoiceMail(" . $rowExtension1['extension_number'] . "@VoiceMail,u)
exten => " . $rowExtension1['extension_number'] . ",n(busy),Hangup

";             
                
                }elseif (($rowContact1['sip'] == null) AND ($rowContact1['sip2'] != null) ){
                    $extensionContent = $extensionContent . "exten => " . $rowExtension1['extension_number'] . ",1,Set(__DYNAMIC_FEATURES=localdooropen)
exten => " . $rowExtension1['extension_number'] . ",2,Dial(SIP/" . $rowExtension1['extension_number'] . ",20)
exten => " . $rowExtension1['extension_number'] . ",3,GotoIf($[ \"\${DIALSTATUS}\" = \"BUSY\" ]?busy)
exten => " . $rowExtension1['extension_number'] . ",4,Dial(SIP/VoIPProvider/" . $rowContact1['sip2'] . ",20)
exten => " . $rowExtension1['extension_number'] . ",n,VoiceMail(" . $rowExtension1['extension_number'] . "@VoiceMail,u)
exten => " . $rowExtension1['extension_number'] . ",n(busy),Hangup

";
                
                }else{
                    $extensionContent = $extensionContent . "exten => " . $rowExtension1['extension_number'] . ",1,Set(__DYNAMIC_FEATURES=localdooropen)
exten => " . $rowExtension1['extension_number'] . ",2,Dial(SIP/" . $rowExtension1['extension_number'] . ",20)
exten => " . $rowExtension1['extension_number'] . ",3,GotoIf($[ \"\${DIALSTATUS}\" = \"BUSY\" ]?busy)
exten => " . $rowExtension1['extension_number'] . ",n,VoiceMail(" . $rowExtension1['extension_number'] . "@VoiceMail,u)
exten => " . $rowExtension1['extension_number'] . ",n(busy),Hangup

";        
                }
            }
        }

    
        $sqlconnect->dbDisconnect($conn);
        
        $sipConfigfile = '/data/asterisk/etc/asterisk/sip.conf';
        $extensionConfigfile = '/data/asterisk/etc/asterisk/extensions.conf';
        $voicemailConfigfile = '/data/asterisk/etc/asterisk/voicemail.conf';
        $featuresConfigfile = '/data/asterisk/etc/asterisk/features.conf';

    
        // Open the file to get existing content
        //$current = file_get_contents($file);
        // Append a new person to the file
        //$current .= "John Smith\n";
        // Write the contents back to the file
        file_put_contents($sipConfigfile, $sipContent);
        file_put_contents($extensionConfigfile, $extensionContent);
        file_put_contents($voicemailConfigfile, $voicemailContent);
        file_put_contents($featuresConfigfile, $featuresContent);

        $this->execShellCommand("/usr/sbin/asterisk -rx reload");
      

      
        
        }
        
        public function execShellCommand($Command){
            $pipeDesc =   array(
                array("pipe","r"),
                array("pipe","w"),
                    array("pipe","w")
                );

            $process = proc_open($Command, $pipeDesc, $pipes);
            proc_close($process);
     
            return;
        }
    }

?>

