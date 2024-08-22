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
        $id_products = $params['id_products'];
        
		$sql = "UPDATE products SET active='0' WHERE id_products=".$id_products."";

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