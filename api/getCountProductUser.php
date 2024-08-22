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

	if ($params['email']) {
		$email = $params['email'];

		$sql = "SELECT COUNT(a.id_products) as 'count' FROM products as a WHERE a.email_user='".$email."' AND (a.active=1 or a.active=3)";

		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$array[] = array_map('utf8_encode', $row);
			}
			$res = json_encode($array, JSON_NUMERIC_CHECK);
			header('Content-type: application/json; charset=utf-8');
			echo $res;
		} else {
			echo "No results";
		}
	}else{
		echo "Not valid Body Data";
	}

}else{
	echo "Not valid Data";
}

$conn->close();
?>