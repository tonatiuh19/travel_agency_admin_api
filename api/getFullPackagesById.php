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

    if ($params['packID']) {
        $packID = $params['packID'];

        $sql = "SELECT 
    a.packID, 
    a.packTitle, 
    a.packDescription, 
    a.packLocationID, 
    a.packHotelID, 
    a.packHotelDescription, 
    a.packLimit, 
    a.packPrice, 
    a.packPriceMax,
    a.packTransportId, 
    a.packTransportDescription, 
    a.packDateRange, 
    a.packImage,
    b.hotLabel, 
    c.citName,
    (SELECT COUNT(*) FROM REVIEWS WHERE packID = a.packID) AS totalReviews,
    (SELECT AVG(rating) FROM REVIEWS WHERE packID = a.packID) AS averageRating
FROM 
    PACKAGES as a
INNER JOIN 
    HOTELS as b ON a.packHotelID = b.hotID
INNER JOIN 
    CITIES as c ON c.citID = a.packLocationID
WHERE 
    (a.status = 1 OR a.status = 2) 
    AND a.packID=" . $packID . "";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $packageDetails = $result->fetch_assoc();
            $packageDetails = array_map('utf8_encode', $packageDetails);

            // Query to get package images
            $sqlImages = "SELECT 
                a.packageImageID, 
                a.source 
            FROM 
                PACKAGES_IMAGES as a 
            WHERE 
                a.active=1 
                AND a.packagePackID=" . $packID;

            $resultImages = $conn->query($sqlImages);
            $images = [];
            if ($resultImages->num_rows > 0) {
                while ($row = $resultImages->fetch_assoc()) {
                    $images[] = array_map('utf8_encode', $row);
                }
            }

            // Add images array to package details
            $packageDetails['images'] = $images;

            $res = json_encode($packageDetails, JSON_NUMERIC_CHECK);
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
