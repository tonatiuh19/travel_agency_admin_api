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

	
	$sql = "SELECT a.id_product_f_sabor_types, a.value FROM product_f_sabor_types as a";
		
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$array[] = array_map('utf8_encode', $row);
		}
        $sql2 = "SELECT a.id_product_f_cuerpo_types, a.value FROM product_f_cuerpo_types as a";
        $result2 = $conn->query($sql2);

        if ($result2->num_rows > 0) {
        // output data of each row
            while($row2 = $result2->fetch_assoc()) {
                array_push($array,array_map('utf8_encode', $row2));
            }
            $sql3 = "SELECT a.id_product_f_acidez_types, a.value FROM product_f_acidez_types as a";
            $result3 = $conn->query($sql3);

            if ($result3->num_rows > 0) {
            // output data of each row
                while($row3 = $result3->fetch_assoc()) {
                    array_push($array,array_map('utf8_encode', $row3));
                }
                $res = json_encode($array, JSON_NUMERIC_CHECK);
                header('Content-type: application/json; charset=utf-8');
                echo $res;
            } else {
                echo "0 results";
            }
        } else {
            echo "0 results";
        }
		
	} else {
		echo "No results";
	}
	

}else{
	echo "Not valid Data";
}

$conn->close();
?>