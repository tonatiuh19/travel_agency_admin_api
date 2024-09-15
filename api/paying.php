<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
require_once('db_cnn/cnn.php');
require_once('vendor/autoload.php');
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $requestBody = file_get_contents('php://input');
    $params = json_decode($requestBody, true);

    if (isset($params['numPassengers']) && isset($params['passengers']) && isset($params['contactName']) && isset($params['contactSurname']) && isset($params['contactEmail']) && isset($params['contactPhone'])) {
        $numPassengers = $params['numPassengers'];
        $passengers = $params['passengers'];
        $contactName = $params['contactName'];
        $contactSurname = $params['contactSurname'];
        $contactEmail = $params['contactEmail'];
        $contactPhone = $params['contactPhone'];
        $bookCustomerID = $params['bookCustomerID'];
        $bookPackageID = $params['packID'];
        $bookPrice = $params['packPrice'];
        $custStripeID = $params['custStripeID'];
        $bookDateFor = $params['bookDateFor'];
        $token = $params['token'];

        $todayVisit = date("Y-m-d H:i:s");

        $stripe = new \Stripe\StripeClient(
            "sk_test_51PvXJtDK4RXtwLNrrcgFUnLopKmxm9bkTkjvSuREiNeuVdXvy6mIPcyVn3sKHjMWO0OHLT1kwTBitNx114ktxoEO00P96ae9oV"
        );

        try {

            $charge = $stripe->charges->create([
                'amount' => $bookPrice * 100,
                'currency' => 'eur',
                'source' => $token
            ]);
            $chargeId = $charge["id"];

            // Insert into BOOKING table
            $insertBookingQuery = "INSERT INTO BOOKINGS(bookCustomerID, bookPackageID, bookDate, bookStripeChargeID, bookPaid, bookDateFor, bookPaidPrice) 
                               VALUES ('$bookCustomerID', '$bookPackageID', '$todayVisit', '$chargeId', 1, '$bookDateFor', '$bookPrice')";

            if ($conn->query($insertBookingQuery) === TRUE) {
                $bookingID = $conn->insert_id;

                $insertContactQuery = "INSERT INTO BOOKINGS_CONTACT(contactBookID, contactName, contactSurName, contactEmail, contactPhone)
                               VALUES ('$bookingID', '$contactName', '$contactSurname', '$contactEmail', '$contactPhone')";

                if ($conn->query($insertContactQuery) !== TRUE) {
                    echo json_encode(["message" => "Error inserting contact: " . $conn->error, "paymentSuccess" => false, "errorCode" => 500]);
                    $conn->close();
                    exit();
                }

                // Insert into BOOKING_PASSENGERS table
                foreach ($passengers as $passenger) {
                    $passengerName = $passenger['nombre'];
                    $passengerSurname = $passenger['apellido'];
                    $passengerAge = $passenger['edad'];

                    $insertPassengerQuery = "INSERT INTO BOOKINGS_PASSENGERS (passengerBookID, passengerName, passengerSurname, passengerAge)
                                             VALUES ('$bookingID', '$passengerName', '$passengerSurname', $passengerAge)";

                    if ($conn->query($insertPassengerQuery) !== TRUE) {
                        http_response_code(500); // Set HTTP status code to 500
                        echo json_encode([
                            "message" => "Error inserting passenger: " . $conn->error,
                            "paymentSuccess" => false,
                            "errorCode" => 500,
                        ]);
                        $conn->close();
                        exit();
                    }
                }

                echo json_encode([
                    "bookingID" => $bookingID,
                    "message" => "Booking and passengers inserted successfully",
                    "paymentSuccess" => true,

                ]);
            } else {
                echo json_encode(["message" => "Error inserting booking: " . $conn->error, "paymentSuccess" => false, "errorCode" => 500]);
            }
        } catch (Exception $e) {
            $insertBadBookingQuery = "INSERT INTO BOOKINGS(bookCustomerID, bookPackageID, bookDate, bookPaid) 
                               VALUES ('$bookCustomerID', '$bookPackageID', '$todayVisit', 0)";
            if ($conn->query($insertBadBookingQuery) === TRUE) {

                echo json_encode(["message" => "Error payment: " . $e->getMessage(), "paymentSuccess" => false, "errorCode" => 500]);
            } else {
                echo json_encode(["message" => "Error inserting booking: " . $conn->error, "paymentSuccess" => false, "errorCode" => 500]);
            }
        }
    } else {
        echo json_encode(["message" => "Invalid input", "paymentSuccess" => false, "errorCode" => 400]);
    }
} else {
    echo json_encode(["message" => "Invalid request method", "paymentSuccess" => false, "errorCode" => 400]);
}

$conn->close();
