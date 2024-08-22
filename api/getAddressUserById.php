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

	if ($params['id_adresses']) {
		$id_adresses = $params['id_adresses'];

        $sql = "SELECT b.id_adresses, b.street, b.number, b.number_interior, b.cp, b.colony, b.city, b.state, b.descripcion
                FROM adresses as b 
                WHERE b.id_adresses='$id_adresses'";
                
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			//echo 'Hola';
			//echo $result;
			while($row = $result->fetch_assoc()) {
				$array[] = array_map('utf8_encode', $row);
			}
			$res = json_encode($array, JSON_NUMERIC_CHECK);
			header('Content-Type: application/json');
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