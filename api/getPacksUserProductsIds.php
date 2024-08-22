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
		$idProduct = $params['id_products'];

		$sql = "SELECT a.id_campaigns_products, a.id_products, a.quantity as 'stock', b.name, f.quantity
        FROm (SELECT a.id_products, a.id_campaign, a.id_campaigns_products, a.quantity FROM campaigns_product AS a WHERE date = ( SELECT MAX(date) FROM campaigns_product AS b WHERE a.id_products = b.id_products )) as a 
        INNER JOIN (SELECT a.id_stock, a.id_products, a.quantity FROM stock AS a WHERE date = ( SELECT MAX(date) FROM stock AS b WHERE a.id_products = b.id_products )) as f on f.id_products=a.id_products
        INNER JOIN products as b on a.id_products=b.id_products
        WHERE a.id_campaign=".$idProduct."
        ORDER BY a.quantity ASC";
		
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