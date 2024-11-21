<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//require_once "PHPMailer/autoload.php";
require_once "PHPMailer/Exception.php";
require "PHPMailer/OAuth.php";
require_once "PHPMailer/PHPMailer.php";
require "PHPMailer/POP3.php";
require "PHPMailer/SMTP.php";

function mail_smtp($From,$SenderName,$To,$ReceiverName,$Subject,$Content,$AltContent,$Host,$Username,$Password,$Secure="tls",$Port="587")
{

    $mail = new PHPMailer(true);
//Enable SMTP debugging.
    $mail->SMTPDebug = 0;
//Set PHPMailer to use SMTP.
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
//Set SMTP host name
    $mail->Host = $Host;
//Set this to true if SMTP host requires authentication to send email
    $mail->SMTPAuth = true;
//Provide username and password
    $mail->Username = $Username;
    $mail->Password = $Password;
//If SMTP requires TLS encryption then set it
    $mail->SMTPSecure = $Secure;
//$mail->SMTPSecure = "ssl"/"tls";
//Set TCP port to connect to
    $mail->Port = $Port;
//$mail->Port = 465/587;

    $mail->From = $From;
    $mail->FromName = $SenderName;

        $mail->addAddress($To, $ReceiverName);
    // Add cc or bcc
    //    $mail->addCC('CC@EXAMPLE.COM');
    //    $mail->addBCC('BCC@EXAMPLE.COM');

        $mail->isHTML(true);

        $mail->Subject = $Subject;
        $mail->Body = $Content;
        $mail->AltBody = $AltContent;

        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        }

}
function mail_php($To,$Subject,$Content){
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    mail($To, $Subject, $Content, $headers);
}
