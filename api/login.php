<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
require_once('db_cnn/cnn.php');
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
	$requestBody = file_get_contents('php://input');
	$params = json_decode($requestBody);
	$params = (array) $params;

	if ($params['email']) {
		$email = $params['email'];
		$pwd = $params['pwd'];

		$sql = "SELECT a.empID, a.empName, a.empSurname, a.position_id, a.empEmail FROM EMPLOYEES as a WHERE a.position_id=1 and a.empEmail='$email' and a.empPassword='$pwd'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$array[] = array_map('utf8_encode', $row);
			}
			$res = json_encode($array, JSON_NUMERIC_CHECK);
			header('Content-Type: application/json');
			echo $res;
		} else {
			echo "0";
		}
	} else {
		echo "Not valid Body Data";
	}
} else {
	echo "Not valid Data";
}

$conn->close();
