<?php
$fromAddress=isset($_POST['fromAddress'])?$_POST['fromAddress']:'huangxf888@aliyun.com';
$password=isset($_POST['password'])?$_POST['password']:'hxf888';
$fromName=isset($_POST['fromName'])?$_POST['fromName']:'管理员';
$toAddress=isset($_POST['toAddress'])?$_POST['toAddress']:'';
$toName=isset($_POST['toName'])?$_POST['toName']:'';
$subject=isset($_POST['subject'])?$_POST['subject']:'';
$body=isset($_POST['body'])?$_POST['body']:'';

require 'PHPMailerAutoload.php';

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.aliyun.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->CharSet  = 'UTF-8';	//设置邮件内容编码
$mail->Username = $fromAddress;                 // SMTP username
$mail->Password = $password;                           // SMTP password
//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
//$mail->Port = 587;                                    // TCP port to connect to

$mail->setFrom($fromAddress, $fromName);
$mail->addAddress($toAddress, $toName);     // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addReplyTo($fromAddress, $fromName);
//$mail->addCC('cc@example.com');	//抄送
//$mail->addBCC('bcc@example.com');	//密送

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $subject;
$mail->Body    = $body;
//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}