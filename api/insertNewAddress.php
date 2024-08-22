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
		$street = $params['street'];
        $no = $params['no'];
        $noInt = $params['noInt'];
        $city = $params['city'];
        $state = $params['state'];
        $description = $params['description'];
        $cp = $params['cp'];
        $colony = $params['colony'];
        $todayVisit = date("Y-m-d H:i:s");

        $sql = "INSERT INTO adresses (type, street, number, number_interior, cp, colony, city, state, date, email_user, descripcion)
        VALUES ('2', '$street', '$no', '$noInt', '$cp', '$colony', '$city', '$state', '$todayVisit', '$email', '$description' )";

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