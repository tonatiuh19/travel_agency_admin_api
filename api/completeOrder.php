<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require "./phpmailer/phpmailer/src/Exception.php";
require "./phpmailer/phpmailer/src/PHPMailer.php";
require "./phpmailer/phpmailer/src/SMTP.php";
require_once('db_cnn/cnn.php');
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST'){
	$requestBody=file_get_contents('php://input');
	$params= json_decode($requestBody);
	$params = (array) $params;

	if ($params['id_orders']) {
        $id_orders = $params['id_orders'];
        $todayVisit = date("Y-m-d H:i:s");
        $description = "COMPLETE: ".$todayVisit;
        $email_user = $params["email_user"];
        
        $sql = "UPDATE orders 
            SET complete='3', 
            description='$description'
            WHERE id_orders=".$id_orders."";

        if ($conn->query($sql) === TRUE) {
            sendmail($email_user);
            echo "1";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
		
	}else{
		echo "Not valid Body Data";
	}

}else{
	echo "Not valid Data";
}

function sendmail($addReplyToEmail)
{
    $todayVisit = date("Y-m-d");
    $mail = new PHPMailer(true);
    $mess =
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Bienvenido</title><style type="text/css">img{max-width:600px;outline:0;text-decoration:none;-ms-interpolation-mode:bicubic}a{border:0;outline:0}a img{border:none}h1,h2,h3,td{font-family:Helvetica,Arial,sans-serif;font-weight:400}td{font-size:13px;line-height:150%;text-align:left}body{-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100%;height:100%;color:#37302d;background:#fff}table{border-collapse:collapse!important}h1,h2,h3{padding:0;margin:0;color:#444;font-weight:400;line-height:110%}h1{font-size:35px}h2{font-size:30px}h3{font-size:24px}h4{font-size:18px;font-weight:400}.important-font{color:#21beb4;font-weight:700}.hide{display:none!important}.force-full-width{width:100%!important}</style><style type="text/css" media="screen">@media screen{@import url(http://fonts.googleapis.com/css?family=Open+Sans:400);h1,h2,h3,td{font-family:"Open Sans","Helvetica Neue",Arial,sans-serif!important}}</style><style type="text/css" media="only screen and (max-width:600px)">@media only screen and (max-width:600px){table[class=w320]{width:320px!important}table[class=w300]{width:300px!important}table[class=w290]{width:290px!important}td[class=w320]{width:320px!important}td[class~=mobile-padding]{padding-left:14px!important;padding-right:14px!important}td[class*=mobile-padding-left]{padding-left:14px!important}td[class*=mobile-padding-right]{padding-right:14px!important}td[class*=mobile-block]{display:block!important;width:100%!important;text-align:left!important;padding-left:0!important;padding-right:0!important;padding-bottom:15px!important}td[class*=mobile-no-padding-bottom]{padding-bottom:0!important}td[class~=mobile-center]{text-align:center!important}table[class*=mobile-center-block]{float:none!important;margin:0 auto!important}[class*=mobile-hide]{display:none!important;width:0!important;height:0!important;line-height:0!important;font-size:0!important}td[class*=mobile-border]{border:0!important}}</style></head><body class="body" style="padding:0;margin:0;display:block;background:#fff;-webkit-text-size-adjust:none" bgcolor="#ffffff"><table align="center" cellpadding="0" cellspacing="0" width="100%" height="100%"><tr><td align="center" valign="top" bgcolor="#ffffff" width="100%"><table cellspacing="0" cellpadding="0" width="100%"><tr><td style="background:#1f1f1f" width="100%"><center><table cellspacing="0" cellpadding="0" width="600" class="w320"><tr><td valign="top" class="mobile-block mobile-no-padding-bottom mobile-center" width="270" style="background:#1f1f1f;padding:10px 10px 10px 20px"><a href="#" style="text-decoration:none"><img src="https://bolsadecafe.com/images/logo_mailer.svg" width="142" alt="Your Logo"></a></td></tr></table></center></td></tr><tr><td style="border-bottom:1px solid #e7e7e7"><center><table cellpadding="0" cellspacing="0" width="600" class="w320"><tr><td align="left" class="mobile-padding" style="padding:20px"><br class="mobile-hide"><h2>Es momento de disfrutar tu BolsaDeCafe 🙌</h2><br><b>¡Felicidades!</b>Tu paquete ha sido entregado con exito.<br><br><table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff"><tr><td style="width:100px;background:#d84a38"><div><!--[if mso]><v:rect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:33px;v-text-anchor:middle;width:100px" stroke="f" fillcolor="#D84A38"><w:anchorlock><center><![endif]--><!--[if mso]><![endif]--></div></td><td width="281" style="background-color:#fff;font-size:0;line-height:0">&nbsp;</td></tr></table></td><td class="mobile-hide" style="padding-top:20px;padding-bottom:0;vertical-align:bottom" valign="bottom"><table cellspacing="0" cellpadding="0" width="100%"><tr><td align="right" valign="bottom" style="padding-bottom:0;vertical-align:bottom"></td></tr></table></td></tr></table></center></td></tr><tr><td valign="top" style="background-color:#f8f8f8;border-bottom:1px solid #e7e7e7"><center><table border="0" cellpadding="0" cellspacing="0" width="600" class="w320" style="height:100%"><tr><td valign="top" class="mobile-padding" style="padding:20px"><table cellspacing="0" cellpadding="0" width="100%"><tr><td style="padding-top:50px"><table cellpadding="0" cellspacing="0" width="100%"><tr><td width="350" style="vertical-align:top">Si tienes alguna pregunta, no dudes en ponerte en contacto con nosotros.<br><h4>Equipo BolsaDeCafe<h4></td></tr></table></td></tr></table></td></tr></table></center></td></tr><tr><td style="background-color:#1f1f1f"><center><table border="0" cellpadding="0" cellspacing="0" width="600" class="w320" style="height:100%;color:#fff" bgcolor="#1f1f1f"><tr><td align="right" valign="middle" class="mobile-padding" style="font-size:12px;padding:20px;background-color:#1f1f1f;color:#fff;text-align:left"></td></tr></table></center></td></tr></table></td></tr></table></body></html>';

    try {
        //Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        // $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host = "mail.bolsadecafe.com"; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = "dihola@bolsadecafe.com"; // SMTP username
        $mail->Password = "JulioBanda93"; // SMTP password
        $mail->SMTPSecure = "ssl"; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 469; // TCP port to connect to

        //Recipients
        $mail->setFrom(
            "noreply@bolsadecafe.com",
            "Tu Paquete ha sido Entregado - BolsaDeCafe"
        );
        $mail->addAddress("" . $addReplyToEmail . "", "Coffee Lover"); // Add a recipient

        $mail->addReplyTo("dihola@bolsadecafe.com", "Informacion");
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('info@agromotics.com', 'Info');

        // Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = "Tu Paquete ha sido Entregado | BolsaDeCafe";
        $mail->Body = $mess;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

$conn->close();
?>