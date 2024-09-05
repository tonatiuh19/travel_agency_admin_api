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

    if ($params['custEmail']) {
        $custEmail = $params['custEmail'];
        $custName = $params['custName'];
        $custSurname = $params['custSurname'];
        $picture = $params['picture'];
        $custEmailVerified = $params['custEmailVerified'];

        $checkUserQuery = "SELECT a.custID, a.custEmail, a.custEmailVerified, a.custName, a.custSurname, a.picture FROM CUSTOMERS as a WHERE a.custEmail='$custEmail'";

        $result = $conn->query($checkUserQuery);

        if ($result->num_rows > 0) {
            // User exists, fetch the user data
            $user = $result->fetch_assoc();
            echo json_encode($user);
        } else {
            // User does not exist, insert the user
            $insertUserQuery = "INSERT INTO CUSTOMERS(custEmail, custEmailVerified, custName, custSurname, picture) 
            VALUES ('$custEmail', '$custEmailVerified', '$custName', '$custSurname', '$picture')";

            if ($conn->query($insertUserQuery) === TRUE) {
                // Fetch the newly inserted user
                $newUserId = $conn->insert_id;
                $newUserQuery = "SELECT a.custID, a.custEmail, a.custEmailVerified, a.custName, a.custSurname, a.picture 
                FROM CUSTOMERS as a 
                WHERE custID = $newUserId";

                $newUserResult = $conn->query($newUserQuery);
                if ($newUserResult->num_rows > 0) {
                    $newUser = $newUserResult->fetch_assoc();
                    echo json_encode($newUser);
                } else {
                    echo json_encode(["message" => "User inserted but not found"]);
                }
            } else {
                echo json_encode(["message" => "Error inserting user: " . $conn->error]);
            }
        }
    } else {
        echo "Not valid Body Data";
    }
} else {
    echo "Not valid Data";
}

$conn->close();
