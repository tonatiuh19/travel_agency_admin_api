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

	if ($params['id_products']) {
		$id_campaign = $params['id_campaign'];
        $quantity = $params['quantity'];
        $id_products = $params['id_products'];
        $todayVisit = date("Y-m-d H:i:s");

        $sql = "INSERT INTO campaigns_product (id_products, id_campaign, quantity, date) VALUES ('$id_products', '$id_campaign', '$quantity', '$todayVisit')";

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