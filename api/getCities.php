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

    $sql = "SELECT a.citID, a.citName, a.status as 'citStatus', b.ctryName, b.ctryID, b.status as 'ctryStatus', c.contID, c.contName FROM CITIES as a
INNER JOIN COUNTRIES as b on b.ctryID=a.id_country
INNER JOIN CONTINENTS as c on c.contID=b.id_cont";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $array[] = array_map('utf8_encode', $row);
        }
        $res = json_encode($array, JSON_NUMERIC_CHECK);
        header('Content-type: application/json; charset=utf-8');
        echo $res;
    } else {
        echo "No results";
    }

} else {
    echo "Not valid Data";
}

$conn->close();
?>