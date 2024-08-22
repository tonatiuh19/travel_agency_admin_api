<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
require_once('db_cnn/cnn.php');
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST'){
	$requestBody=file_get_contents('php://input');
	$params= json_decode($requestBody);
	$params = (array) $params;

	if ($params['id_orders']) {
        $shipment_id = $params['shipment_id'];
        $shipment_price = $params['shipment_price'];
        $shipment_provider = $params['shipment_provider'];
        $shipment_tracking_number = $params['shipment_tracking_number'];
        $id_orders = $params['id_orders'];
        $todayVisit = date("Y-m-d H:i:s");
        $description = $params['description'].$todayVisit;
        $email_user = $params["email_user"];
        
        $sql = "UPDATE orders 
            SET shipment_id='$shipment_id', 
            shipment_price='$shipment_price', 
            shipment_provider='$shipment_provider', 
            track_id='$shipment_tracking_number',
            description='$description'
            WHERE id_orders=".$id_orders."";

        if ($conn->query($sql) === TRUE) {
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

$conn->close();
?>