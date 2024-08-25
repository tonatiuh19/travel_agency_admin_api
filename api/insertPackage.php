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

	if ($params['id_user']) {
        $id_user = $params['id_user'];
		$name = $params['name'];
        $price = $params['price'];
        $location = $params['location'];

        $startDate = $params['startDate'];
        $date = DateTime::createFromFormat('d-m-Y', $startDate);
        $startDate = $date->format('m-d-Y');

        $endDate = $params['endDate'];
        $date = DateTime::createFromFormat('d-m-Y', $endDate);
        $endDate = $date->format('m-d-Y');

        $dateRange = $params['date'];

        $transport = $params['transport'];
        $hosting = $params['hosting'];
        $limit = $params['limit'];
        $transportType = $params['transportType'];
        $transportDescription = $params['transportDescription'];
        $hostingType = $params['hostingType'];
        $hotelDescription = $params['hotelDescription'];
        $generalDescription = $params['generalDescription'];
        

        $todayVisit = date("Y-m-d H:i:s");

        $sql = "INSERT INTO PACKAGES (empID, packTitle, packDescription, packLocationID, packHotelID, packHotelDescription, packLimit, packPrice, packStartDate, packEndDate, packTransportId, packTransportDescription, inputDate, packDateRange) VALUES ('$id_user', '$name', '$generalDescription', '$location', '$hostingType', '$hotelDescription', '$limit', '$price', '$startDate', '$endDate', '$transportType', '$transportDescription', '$todayVisit', '$dateRange')";

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