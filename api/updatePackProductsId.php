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

        $sql = "UPDATE campaigns_product SET quantity='$quantity', date='$todayVisit' WHERE id_products=".$id_products." AND id_campaign=".$id_campaign."";

        if ($conn->query($sql) === TRUE) {
            echo "1";
        } else {
            echo "Error updating record: " . $conn->error;
        }
		
	}else{
		echo "Not valid Body Data";
	}

}else{
	echo "Not valid Data";
}

$conn->close();
?>