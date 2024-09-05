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

    $sql = "SELECT a.reviewID, a.packID, a.custID, a.rating, a.comment, a.reviewDate, b.custName, b.custSurname
FROM REVIEWS as a
INNER JOIN CUSTOMERS as b ON b.custID=a.custID
ORDER BY RAND()
LIMIT 10";
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
