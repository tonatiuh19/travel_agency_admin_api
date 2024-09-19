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

    if ($params['userID']) {
        $userID = $params['userID'];

        $query = "
        SELECT 
            b.bookID,
            b.bookPaid,
            b.bookCustomerID,
            b.bookPackageID,
            b.bookDate,
            b.bookDateFor,
            b.bookPaidPrice,
            bc.contactName,
            bc.contactSurName,
            bc.contactEmail,
            bc.contactPhone,
            bp.passengerName,
            bp.passengerSurname,
            bp.passengerAge,
            A.packTitle,
            A.packPrice,
            A.packTransportId,
            A.packHotelID,
            A.packID
        FROM 
            BOOKINGS b
        INNER JOIN PACKAGES as A on A.packID = b.bookPackageID
        LEFT JOIN 
            BOOKINGS_CONTACT bc ON b.bookID = bc.contactBookID
        LEFT JOIN 
            BOOKINGS_PASSENGERS bp ON b.bookID = bp.passengerBookID
        ";

        if ($userID > 0) {
            $query .= " WHERE b.bookPaid=1 AND b.bookCustomerID = $userID";
        }

        $query .= " ORDER BY b.bookDateFor ASC";

        $result = $conn->query($query);
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
        echo "Not valid Body Data";
    }
} else {
    echo "Not valid Data";
}

$conn->close();
