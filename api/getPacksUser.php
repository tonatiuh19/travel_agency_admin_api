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

		$sql = "SELECT d.id_products, e.price, f.quantity, d.name, d.description, d.id_product_type, d.id_country, d.active, d.long_description, m.country FROM products as d 
        INNER JOIN (SELECT a.id_prices, a.id_products, a.price FROM prices AS a WHERE date = ( SELECT MAX(date) FROM prices AS b WHERE a.id_products = b.id_products )) as e on d.id_products=e.id_products 
        INNER JOIN (SELECT a.id_stock, a.id_products, a.quantity FROM stock AS a WHERE date = ( SELECT MAX(date) FROM stock AS b WHERE a.id_products = b.id_products )) as f on f.id_products=e.id_products
        INNER JOIN countries as m on m.id_country=d.id_country 
        WHERE d.id_product_type=3 and d.active<>0 and d.email_user='".$email."'
		ORDER BY f.quantity ASC";
		
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