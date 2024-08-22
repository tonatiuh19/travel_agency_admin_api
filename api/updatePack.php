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

	if ($params['id_products']) {
        $email = $params['email'];
        $idProducts = $params['id_products'];
        $name = $params['name'];
        $description = $params['description'];
        $long_description = $params['long_description'];
        $quantity = $params['quantity'];
        $price = $params['price'];
        $todayVisit = date("Y-m-d H:i:s");

        $sql = "UPDATE products SET name='$name', description='$description', date='$todayVisit', long_description='$long_description' WHERE id_products=".$idProducts."";

        if ($conn->query($sql) === TRUE) {
            $sql2 = "SELECT id_products, name FROM products WHERE id_products=".$idProducts."";
            $result2 = $conn->query($sql2);

            if ($result2->num_rows > 0) {
            // output data of each row
                while($row2 = $result2->fetch_assoc()) {
                    $idProduct = $row2["id_products"]; 
                    $array[] = array_map('utf8_encode', $row2);
                }
                $sql3 = "INSERT INTO prices (id_products, price, date) VALUES ('$idProduct', '$price', '$todayVisit')";

                if ($conn->query($sql3) === TRUE) {
                    $sql7 = "INSERT INTO stock (id_products, quantity, date, email_user) VALUES ('$idProduct', '$quantity', '$todayVisit', '$email')";

                    if ($conn->query($sql7) === TRUE) {
                        $res = json_encode($array, JSON_NUMERIC_CHECK);
                        header('Content-type: application/json; charset=utf-8');
                        echo $res;
                    } else {
                        echo "Error: " . $sql7 . "<br>" . $conn->error;
                    }
                } else {
                    echo "Error: " . $sql3 . "<br>" . $conn->error;
                }
            } else {
                echo "0 results";
            }
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