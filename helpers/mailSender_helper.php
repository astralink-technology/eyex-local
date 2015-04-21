<?php
/*
 * Copyright Chilli Panda
 * Created on 2013-10-02
 * Created by Shi Wei Eamon
 */

/*
 *  A helper to send a mail for you 
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/Libraries/PHPMailer/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Config/webConfig.php';

class cp_mailSender_helper extends webConfig{
    public $host;
    public $username;
    public $password;
    public $from;
    public $fromName;
    public $addReplyTo;
    public $addReplyName;
    public $addCC;
    public $addBCC;
    public $smtpSecure;

    protected function initializeMailSender(){
        $webConfig = new webConfig();
        $webConfig->mailConfig();
        $this->host = $webConfig->mailHost;
        $this->username = $webConfig->mailUsername;
        $this->password = $webConfig->mailPassword;
        $this->from = $webConfig->from;
        $this->fromName = $webConfig->fromName;
        $this->addReplyTo = $webConfig->addReplyTo;
        $this->addReplyName = $webConfig->addReplyName;
        $this->addCC = $webConfig->addCC;
        $this->addBCC = $webConfig->addBCC;
        $this->smtpSecure = $webConfig->smtpSecure;
    }

    public function sendMail(
        $receiverName
        , $receiverEmail
        , $isHtml
        , $subject
        , $htmlBody
        , $plainTextBody
        , $pFrom = null
        , $pFromName = null
        , $pAddReplyTo = null
        , $pAddReplyName = null
        , $pAddCC = null
        , $pAddBCC = null
    ){
        $mail = new PHPMailer;
        $this->initializeMailSender();

        if ($receiverEmail == null){
            return 'Please specify a recepient before sending';
        }
        if ($this->host == null){
            return 'Please specify a Host';
        }else{
            $mail->Host = $this->host;
        }
        if ($this->username == null || $this->password == null){
            return 'Please specify username and password for host';
        }else{
            $mail->Username = $this->username;
            $mail->Password = $this->password;
        }

        if ($pFrom != null && $pFromName != null){
            $mail->setFrom($pFrom, $pFromName);
        }else{
            if ($this->from == null){
                return 'Please specify a sender';
            }else{
                if ($this->fromName == null){
                    $mail->FromName = "(Unknown)";
                }else{
                    $mail->setFrom($this->from, $this->fromName);
                }
            }
        }
        if ($pAddReplyTo != null && $pAddReplyName != null){
            $mail->addReplyTo($pAddReplyTo, $pAddReplyName);
        }else{
            if ($this->addReplyTo != null){
                if ($this->addReplyName == null){
                    return 'Please specify the name of the replied receiver if you are using reply to function';
                }else{
                    $mail->addReplyTo($this->addReplyTo, $this->addReplyName);
                }
            }
        }

        if ($pAddCC != null){
            $mail->addCC($pAddCC);
        }else{
            if ($this->addCC != null){
                $mail->addCC($this->addCC);
            }
        }

        if ($pAddBCC != null){
            $mail->addBCC($pAddBCC);
        }else{
            if ($this->addBCC != null){
                $mail->addBCC($this->addBCC);
            }
        }

        if ($this->smtpSecure != null){
            $mail->SMTPSecure = $this->smtpSecure;
        }

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->SMTPAuth = true;                               // Enable SMTP authentication

        //Set the subject
        if ($subject != null){
            $mail->Subject = $subject;
        }else{
            $mail->Subject = "(No Subject)";
        }
        //Set receipient
        if ($receiverName != null){
            $mail->addAddress($receiverEmail, $receiverName);  // Add a recipient
        }else{
            $mail->addAddress($receiverEmail);  // Add a recipient
        }
        //Determine message type
        if ($isHtml == true){
            //sending as html body
            $mail->isHTML(true);
            $mail->Body = $htmlBody; //html body
        }else{
            //sending as plain text body
            $mail->isHTML(false);
            $mail->Body = $plainTextBody;
        }

        if(!$mail->send()) {
            return 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }
        return 'Message has been sent';
    }
}
?>
