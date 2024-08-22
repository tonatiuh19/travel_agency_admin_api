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

		$sql = "SELECT d.id_products, e.price, d.name, d.description, d.peso, d.long_description, d.id_country, i.country, y.quantity, h.id_product_f_acidez_types, k.id_product_f_cuerpo_types, m.id_product_f_sabor_types, d.id_product_type, d.active
		FROM products as d INNER JOIN countries as i on i.id_country=d.id_country 
		INNER JOIN (SELECT a.id_products, a.price FROM prices AS a INNER JOIN (SELECT id_products, MAX(Date) as TopDate FROM prices GROUP BY id_products) AS EachItem ON EachItem.TopDate = a.date AND EachItem.id_products = a.id_products ORDER BY `a`.`id_products` ASC) as e on d.id_products=e.id_products 
		INNER JOIN (SELECT a.id_products, a.quantity FROM stock AS a INNER JOIN (SELECT id_products, MAX(Date) as TopDate FROM stock GROUP BY id_products) AS EachItem ON EachItem.TopDate = a.date AND EachItem.id_products = a.id_products ORDER BY `a`.`id_products` ASC) as y on y.id_products=d.id_products 
		LEFT JOIN product_f_acidez as h on h.id_product=d.id_products
		LEFT JOIN product_f_cuerpo as k on k.id_product=d.id_products
		LEFT JOIN product_f_sabor as m on m.id_product=d.id_products
		WHERE (d.active=2 or d.active=1 or d.active=3) and d.id_products=".$id_products." and y.quantity>=0 and d.id_country NOT IN ( SELECT id_country FROM countries WHERE id_country=10 ) 
		ORDER BY y.quantity ASC";
		
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