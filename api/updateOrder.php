<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require "./phpmailer/phpmailer/src/Exception.php";
require "./phpmailer/phpmailer/src/PHPMailer.php";
require "./phpmailer/phpmailer/src/SMTP.php";
require_once "db_cnn/cnn.php";
$method = $_SERVER["REQUEST_METHOD"];

if ($method == "POST") {
    $requestBody = file_get_contents("php://input");
    $params = json_decode($requestBody);
    $params = (array) $params;

    if ($params["shipment_id"]) {
        $shipment_id = $params["shipment_id"];
        $shipment_price = $params["shipment_price"];
        $shipment_provider = $params["shipment_provider"];
        $shipment_tracking_number = $params["shipment_tracking_number"];
        $id_orders = $params["id_orders"];
        $todayVisit = date("Y-m-d H:i:s");
        $description = $params["description"] . $todayVisit;
        $type = $params["type"];
        $email_user = $params["email_user"];

        if ($type == "1") {
            $sql2 = "SELECT d.id_products, e.price, d.name, d.description, d.peso, d.long_description, d.id_country, i.country, y.quantity, y.id_stock, h.id_product_f_acidez_types, k.id_product_f_cuerpo_types, m.id_product_f_sabor_types, d.id_product_type, d.active
            FROM products as d INNER JOIN countries as i on i.id_country=d.id_country 
            INNER JOIN (SELECT a.id_products, a.price FROM prices AS a INNER JOIN (SELECT id_products, MAX(Date) as TopDate FROM prices GROUP BY id_products) AS EachItem ON EachItem.TopDate = a.date AND EachItem.id_products = a.id_products ORDER BY `a`.`id_products` ASC) as e on d.id_products=e.id_products 
            INNER JOIN (SELECT a.id_products, a.quantity, a.id_stock FROM stock AS a INNER JOIN (SELECT id_products, MAX(Date) as TopDate FROM stock GROUP BY id_products) AS EachItem ON EachItem.TopDate = a.date AND EachItem.id_products = a.id_products ORDER BY `a`.`id_products` ASC) as y on y.id_products=d.id_products 
            LEFT JOIN product_f_acidez as h on h.id_product=d.id_products
            LEFT JOIN product_f_cuerpo as k on k.id_product=d.id_products
            LEFT JOIN product_f_sabor as m on m.id_product=d.id_products
            WHERE (d.active=2 or d.active=1 or d.active=3) and d.email_user='tg@tienditacafe.com' and y.quantity>=0 and d.id_country NOT IN ( SELECT id_country FROM countries WHERE id_country=10 ) AND e.price < 60
            ORDER BY e.price ASC LIMIT 1";
        } elseif ($type == "2") {
            $sql2 = "SELECT d.id_products, e.price, d.name, d.description, d.peso, d.long_description, d.id_country, i.country, y.quantity, y.id_stock, h.id_product_f_acidez_types, k.id_product_f_cuerpo_types, m.id_product_f_sabor_types, d.id_product_type, d.active
            FROM products as d INNER JOIN countries as i on i.id_country=d.id_country 
            INNER JOIN (SELECT a.id_products, a.price FROM prices AS a INNER JOIN (SELECT id_products, MAX(Date) as TopDate FROM prices GROUP BY id_products) AS EachItem ON EachItem.TopDate = a.date AND EachItem.id_products = a.id_products ORDER BY `a`.`id_products` ASC) as e on d.id_products=e.id_products 
            INNER JOIN (SELECT a.id_products, a.quantity, a.id_stock FROM stock AS a INNER JOIN (SELECT id_products, MAX(Date) as TopDate FROM stock GROUP BY id_products) AS EachItem ON EachItem.TopDate = a.date AND EachItem.id_products = a.id_products ORDER BY `a`.`id_products` ASC) as y on y.id_products=d.id_products 
            LEFT JOIN product_f_acidez as h on h.id_product=d.id_products
            LEFT JOIN product_f_cuerpo as k on k.id_product=d.id_products
            LEFT JOIN product_f_sabor as m on m.id_product=d.id_products
            WHERE (d.active=2 or d.active=1 or d.active=3) and d.email_user='tg@tienditacafe.com' and y.quantity>=0 and d.id_country NOT IN ( SELECT id_country FROM countries WHERE id_country=10 ) AND (e.price < 150 and d.peso >=500)
            ORDER BY e.price ASC LIMIT 1";
        } else {
            $sql2 = "SELECT d.id_products, e.price, d.name, d.description, d.peso, d.long_description, d.id_country, i.country, y.quantity, y.id_stock, h.id_product_f_acidez_types, k.id_product_f_cuerpo_types, m.id_product_f_sabor_types, d.id_product_type, d.active
            FROM products as d INNER JOIN countries as i on i.id_country=d.id_country 
            INNER JOIN (SELECT a.id_products, a.price FROM prices AS a INNER JOIN (SELECT id_products, MAX(Date) as TopDate FROM prices GROUP BY id_products) AS EachItem ON EachItem.TopDate = a.date AND EachItem.id_products = a.id_products ORDER BY `a`.`id_products` ASC) as e on d.id_products=e.id_products 
            INNER JOIN (SELECT a.id_products, a.quantity, a.id_stock FROM stock AS a INNER JOIN (SELECT id_products, MAX(Date) as TopDate FROM stock GROUP BY id_products) AS EachItem ON EachItem.TopDate = a.date AND EachItem.id_products = a.id_products ORDER BY `a`.`id_products` ASC) as y on y.id_products=d.id_products 
            LEFT JOIN product_f_acidez as h on h.id_product=d.id_products
            LEFT JOIN product_f_cuerpo as k on k.id_product=d.id_products
            LEFT JOIN product_f_sabor as m on m.id_product=d.id_products
            WHERE (d.active=2 or d.active=1 or d.active=3) and d.email_user='tg@tienditacafe.com' and y.quantity>=0 and d.id_country NOT IN ( SELECT id_country FROM countries WHERE id_country=10 ) AND (e.price < 250 and d.peso >=900)
            ORDER BY e.price ASC LIMIT 1";
        }

        $result2 = $conn->query($sql2);

        if ($result2->num_rows > 0) {
            while ($row2 = $result2->fetch_assoc()) {
                $idProduct = $row2["id_products"];
                $idStock = $row2["id_stock"];
            }
            $sqlj =
                "UPDATE stock SET quantity = quantity - 1 WHERE id_stock='" .
                $idStock .
                "'";
            if ($conn->query($sqlj) === true) {
                $sql =
                    "UPDATE orders 
                    SET shipment_id='$shipment_id', 
                    shipment_price='$shipment_price', 
                    shipment_provider='$shipment_provider', 
                    track_id='$shipment_tracking_number',
                    description='$description',
                    id_products='$idProduct',
                    complete='2'
                    WHERE id_orders=" .
                    $id_orders .
                    "";

                if ($conn->query($sql) === true) {
                    sendmail(
                        $email_user,
                        $shipment_provider,
                        $shipment_tracking_number
                    );
                    echo "1";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Error: " . $sqlj . "<br>" . $conn->error;
            }
        } else {
            echo "0 results";
        }
    } else {
        echo "Not valid Body Data";
    }
} else {
    echo "Not valid Data";
}

function sendmail($addReplyToEmail, $provider, $trackId)
{
    $todayVisit = date("Y-m-d");
    $mail = new PHPMailer(true);
    $mess =
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Paquete enviado</title><style type="text/css">img{max-width:600px;outline:0;text-decoration:none;-ms-interpolation-mode:bicubic}a{border:0;outline:0}a img{border:none}h1,h2,h3,td{font-family:Helvetica,Arial,sans-serif;font-weight:400}td{font-size:13px;line-height:150%;text-align:left}body{-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100%;height:100%;color:#37302d;background:#fff}table{border-collapse:collapse!important}h1,h2,h3{padding:0;margin:0;color:#444;font-weight:400;line-height:110%}h1{font-size:35px}h2{font-size:30px}h3{font-size:24px}h4{font-size:18px;font-weight:400}.important-font{color:#21beb4;font-weight:700}.hide{display:none!important}.force-full-width{width:100%!important}td.desktop-hide{font-size:0;height:0;display:none;color:#fff}</style><style type="text/css" media="screen">@media screen{@import url(http://fonts.googleapis.com/css?family=Open+Sans:400);h1,h2,h3,td{font-family:"Open Sans","Helvetica Neue",Arial,sans-serif!important}}</style><style type="text/css" media="only screen and (max-width:600px)">@media only screen and (max-width:600px){table[class=w320]{width:320px!important}table[class=w300]{width:300px!important}table[class=w290]{width:290px!important}td[class=w320]{width:320px!important}td[class~=mobile-padding]{padding-left:14px!important;padding-right:14px!important}td[class*=mobile-padding-left]{padding-left:14px!important}td[class*=mobile-padding-right]{padding-right:14px!important}td[class*=mobile-block]{display:block!important;width:100%!important;text-align:left!important;padding-left:0!important;padding-right:0!important;padding-bottom:15px!important}td[class*=mobile-no-padding-bottom]{padding-bottom:0!important}td[class~=mobile-center]{text-align:center!important}table[class*=mobile-center-block]{float:none!important;margin:0 auto!important}[class*=mobile-hide]{display:none!important;width:0!important;height:0!important;line-height:0!important;font-size:0!important}td[class*=mobile-border]{border:0!important}td[class*=desktop-hide]{display:block!important;font-size:13px!important;height:61px!important;padding-top:10px!important;padding-bottom:10px!important;color:#444!important}body{background-color:#fff}}</style></head><body class="body" style="padding:0;margin:0;display:block;background:#fff;-webkit-text-size-adjust:none" bgcolor="#ffffff"><table align="center" cellpadding="0" cellspacing="0" width="100%" height="100%"><tr><td align="center" valign="top" bgcolor="#ffffff" width="100%"><table cellspacing="0" cellpadding="0" width="100%"><tr><td style="background:#1f1f1f" width="100%"><center><table cellspacing="0" cellpadding="0" width="600" class="w320"><tr><td valign="top" class="mobile-block mobile-no-padding-bottom mobile-center" width="270" style="background:#1f1f1f;padding:10px 10px 10px 20px"><img src="https://bolsadecafe.com/images/logo.jpg" width="142" height="30" alt="Your Logo"></td><td valign="top" class="mobile-block mobile-center" width="270" style="background:#1f1f1f;padding:10px 15px 10px 10px"></td></tr></table></center></td></tr><tr><td style="border-bottom:1px solid #e7e7e7"><center><table cellpadding="0" cellspacing="0" width="600" class="w320"><tr><td align="left" class="mobile-padding" style="padding:20px"><br class="mobile-hide"><p><h2>Tu paquete ha sido Enviado 🚚</h2></p><div><br><b>&iexcl;Tenemos Buenas noticias para ti! Tu BolsadeCafe ha sido empacada y enviada satisfactoriamente.</b></div><br><table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff"><tr><td style="width:100px;background:#d84a38"><div></div></td><td width="281" style="background-color:#fff;font-size:0;line-height:0">&nbsp;</td></tr></table></td><td class="mobile-hide" style="padding-top:20px;padding-bottom:0;vertical-align:bottom" valign="bottom"><table cellspacing="0" cellpadding="0" width="100%"><tr></tr></table></td></tr></table></center></td></tr><tr><td valign="top" style="background-color:#f8f8f8;border-bottom:1px solid #e7e7e7"><center><table border="0" cellpadding="0" cellspacing="0" width="600" class="w320" style="height:100%"><tr><td valign="top" class="mobile-padding" style="padding:20px"><table cellspacing="0" cellpadding="0" width="100%"><tr><td style="padding-right:20px"><b>Paqueter&iacute;a</b></td><td style="padding-right:20px"><b>ID de Seguimiento</b></td></tr><tr><td style="padding-top:5px;padding-right:20px;border-top:1px solid #e7e7e7">' .
        $provider .
        '</td><td style="padding-top:5px;padding-right:20px;border-top:1px solid #e7e7e7">' .
        $trackId .
        '</td></tr></table><table cellspacing="0" cellpadding="0" width="100%"><tr><td style="padding-top:35px"><table cellpadding="0" cellspacing="0" width="100%"><tr><td width="350" class="mobile-hide" style="vertical-align:top">Cualquier duda que tengas, ponte en contacto con nosotros.<br><h4>Equipo BolsaDeCafe<h4></td><td style="padding:0 0 15px 30px" class="mobile-block"></td></tr><tr><td style="vertical-align:top" class="desktop-hide">Cualquier duda que tengas no dudes en contestar este correo.<br><h4>Equipo BolsaDeCafe<h4></td></tr></table></td></tr></table></td></tr></table></center></td></tr><tr><td style="background-color:#1f1f1f"><center><table border="0" cellpadding="0" cellspacing="0" width="600" class="w320" style="height:100%;color:#fff" bgcolor="#1f1f1f"><tr><td align="right" valign="middle" class="mobile-padding" style="font-size:12px;padding:20px;background-color:#1f1f1f;color:#fff;text-align:left"></td></tr></table></center></td></tr></table></td></tr></table></body></html>';

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
            "Paquete enviado - BolsaDeCafe"
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
        $mail->Subject = "Paquete enviado | BolsaDeCafe";
        $mail->Body = $mess;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

$conn->close();
?>
