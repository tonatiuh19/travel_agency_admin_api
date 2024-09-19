<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
require_once('db_cnn/cnn.php');
require_once('vendor/autoload.php');
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $requestBody = file_get_contents('php://input');
    $params = json_decode($requestBody);
    $params = (array) $params;

    if ($params['custEmail']) {

        $stripe = new \Stripe\StripeClient(
            "sk_test_51OtL2vI0AVtzugqlOig4E1ACAVjBX28q4H3PtW5AWEeICiAi6USnIgtDTB4SkQ2cg2FhWReBjT4sVqqNJ321lxHq00ApVEJXcL"
        );

        $custEmail = $params['custEmail'];
        $custName = $params['custName'];
        $custSurname = $params['custSurname'];
        $picture = $params['picture'];
        $custEmailVerified = $params['custEmailVerified'];


        $customer = $stripe->customers->create([
            'name' => $custName . " " . $custSurname,
            'email' => $custEmail,
        ]);
        $custStripeID = $customer["id"];

        $checkUserQuery = " SELECT 
        a.custID, 
        a.custEmail, 
        a.custEmailVerified, 
        a.custName, 
        a.custSurname, 
        a.picture, 
        a.custStripeID,
        CASE 
            WHEN b.bookCustomerID IS NOT NULL THEN 1 
            ELSE 0 
        END AS hasBookings
    FROM 
        CUSTOMERS as a
    LEFT JOIN 
        BOOKINGS as b 
    ON 
        a.custID = b.bookCustomerID
    WHERE a.custEmail='$custEmail' LIMIT 1";

        $result = $conn->query($checkUserQuery);

        if ($result->num_rows > 0) {
            // User exists, fetch the user data
            $user = $result->fetch_assoc();
            echo json_encode($user);
        } else {
            // User does not exist, insert the user
            $insertUserQuery = "INSERT INTO CUSTOMERS(custEmail, custEmailVerified, custName, custSurname, picture, custStripeID) 
            VALUES ('$custEmail', '$custEmailVerified', '$custName', '$custSurname', '$picture', '$custStripeID')";

            if ($conn->query($insertUserQuery) === TRUE) {
                // Fetch the newly inserted user
                $newUserId = $conn->insert_id;
                $newUserQuery = "SELECT a.custID, a.custEmail, a.custEmailVerified, a.custName, a.custSurname, a.picture, a.custStripeID 
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
