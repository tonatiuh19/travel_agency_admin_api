<?php
require_once('db_cnn/cnn.php');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $requestBody = file_get_contents('php://input');
    $params = json_decode($requestBody);
    $params = (array) $params;

    $sql = "SELECT * FROM (SELECT a.citID, a.citName, a.status as 'cityStatus', a.id_country as 'ctryID', COUNT(b.packID) as 'packageCount'
            FROM CITIES as a
            LEFT JOIN PACKAGES as b ON a.citID = b.packLocationID
            GROUP BY a.citID, a.citName, a.status, a.id_country) as d
            WHERE d.packageCount > 0";
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
