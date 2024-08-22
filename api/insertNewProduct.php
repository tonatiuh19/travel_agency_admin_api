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
        $name = $params['name'];
        $peso = $params['peso'];
        $description = $params['description'];
        $id_product_type = $params['id_product_type'];
        $id_country = $params['id_country'];
        $long_description = $params['long_description'];
        $quantity = $params['quantity'];
        $price = $params['price'];
        $todayVisit = date("Y-m-d H:i:s");

        if($id_product_type == "1"){
            $id_product_f_acidez_types = $params['id_product_f_acidez_types'];
            $id_product_f_cuerpo_types = $params['id_product_f_cuerpo_types'];
            $id_product_f_sabor_types = $params['id_product_f_sabor_types'];

            $sql = "INSERT INTO products (name, description, peso, id_product_type, id_country, email_user, date, active, long_description) VALUES ('$name', '$description', '$peso', '$id_product_type', '$id_country', '$email', '$todayVisit', '2', '$long_description')";

            if ($conn->query($sql) === TRUE) {
                $sql2 = "SELECT id_products, name FROM products WHERE email_user='".$email."' AND id_country=".$id_country." AND date='".$todayVisit."' AND id_product_type=".$id_product_type."";
                $result2 = $conn->query($sql2);

                if ($result2->num_rows > 0) {
                // output data of each row
                    while($row2 = $result2->fetch_assoc()) {
                        $idProduct = $row2["id_products"]; 
                        $array[] = array_map('utf8_encode', $row2);
                    }
                    $sql3 = "INSERT INTO prices (id_products, price, date) VALUES ('$idProduct', '$price', '$todayVisit')";

                    if ($conn->query($sql3) === TRUE) {
                        $sql4 = "INSERT INTO product_f_acidez (id_product, id_product_f_acidez_types) VALUES ('$idProduct', '$id_product_f_acidez_types')";

                        if ($conn->query($sql4) === TRUE) {
                            $sql5 = "INSERT INTO product_f_cuerpo (id_product, id_product_f_cuerpo_types) VALUES ('$idProduct', '$id_product_f_cuerpo_types')";

                            if ($conn->query($sql5) === TRUE) {
                                $sql6 = "INSERT INTO product_f_sabor (id_product, id_product_f_sabor_types) VALUES ('$idProduct', '$id_product_f_sabor_types')";

                                if ($conn->query($sql6) === TRUE) {
                                    $sql7 = "INSERT INTO stock (id_products, quantity, date, email_user) VALUES ('$idProduct', '$quantity', '$todayVisit', '$email')";

                                    if ($conn->query($sql7) === TRUE) {
                                        $res = json_encode($array, JSON_NUMERIC_CHECK);
                                        header('Content-type: application/json; charset=utf-8');
                                        echo $res;
                                    } else {
                                        echo "Error: " . $sql7 . "<br>" . $conn->error;
                                    }
                                } else {
                                    echo "Error: " . $sql6 . "<br>" . $conn->error;
                                }
                            } else {
                                echo "Error: " . $sql5 . "<br>" . $conn->error;
                            }
                        } else {
                            echo "Error: " . $sql4 . "<br>" . $conn->error;
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
            $sql = "INSERT INTO products (name, description, id_product_type, id_country, email_user, date, active, long_description) VALUES ('$name', '$description', '$id_product_type', '$id_country', '$email', '$todayVisit', '2', '$long_description')";

            if ($conn->query($sql) === TRUE) {
                $sql2 = "SELECT id_products, name FROM products WHERE email_user='".$email."' AND id_country=".$id_country." AND date='".$todayVisit."' AND id_product_type=".$id_product_type."";
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
        }

		
	}else{
		echo "Not valid Body Data";
	}

}else{
	echo "Not valid Data";
}

$conn->close();
?>