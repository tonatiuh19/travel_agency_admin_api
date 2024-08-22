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

		$sql = "SELECT a.id_orders, 
			a.email_user, 
			e.name as 'first_name',
			e.last_name,
			a.id_adress, 
			a.id_products,
			a.date, 
			a.complete, 
			a.track_id, 
			a.shipment_id,
			a.shipment_label_url,
			a.shipment_price,
			a.shipment_provider,
			a.description,
			b.id_subscriptions,
			b.start_date,
			b.active,
			b.type,
			b.subs_id
		FROM orders as a 
		INNER JOIN subscriptions as b on b.id_subscriptions = a.id_subscriptions
		LEFT JOIN users as e on e.email = a.email_user
		ORDER BY a.date ASC";
		
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$array[] = array_map('utf8_encode', $row);
			}
			$res = json_encode($array, JSON_NUMERIC_CHECK);
			header('Content-type: application/json; charset=utf-8');
			echo $res;
		} else {
			echo "0";
		}
	}else{
		echo "Not valid Body Data";
	}

}else{
	echo "Not valid Data";
}

$conn->close();
?>