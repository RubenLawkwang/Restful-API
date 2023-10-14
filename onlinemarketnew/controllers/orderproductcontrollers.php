<?php
/* This controller will
handle the create, update, delete, read all/read one function
from orderproduct models 
A user is required to be login to performe actions
*/
session_start();
// check if user is login
if (isset($_SESSION['customer'])) {
} else {
    echo "User ID not set in the session.";
}
// set respons content type to json
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once(__DIR__ . '/../config/database.php'); // Include data-base configuration
include_once(__DIR__ . '/../models/orderproduct.php'); // Include order-product configuration
include_once(__DIR__ . '/../models/userauth.php'); // Include user-login configuration
// data base connection
$database = new Database();
$db = $database->getConnection();

$orders = new OrderProduct($db); // New Order Product

$requestMethod = $_SERVER["REQUEST_METHOD"];
$raw_data = file_get_contents("php://input");

var_dump($raw_data); // check $raw_data data type

$data = json_decode($raw_data);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {

    http_response_code(400); // error 400 Bad Request
    echo json_encode(array('message' => 'Invalid JSON data'));
    exit;
}

// Handle different requests
switch ($requestMethod) {

    case 'GET': // Handle read one and read all request
        if (!empty($_GET["or_id"])) {
            $response = $orders->readOne($_GET["or_id"]);
        } else {
            $response = $orders->read();
        }

        break;

    case 'POST':  // Handle Create new order request request
        try {
            // check and retrieve product id from URL
            $p_id_from_url = isset($_GET['p_id']) ? $_GET['p_id'] : null;
            // check if user is login
            if (isset($_SESSION['customer']) && isset($p_id_from_url)) {
                $orders = new OrderProduct($db);
                $response = $orders->create($_SESSION['customer'], $p_id_from_url); // Pass session customer ID and the p_id from the URL
                if ($response) {
                    http_response_code(201); // successful request
                    echo json_encode(array('message' => 'Order created successfully'));
                } else {
                    http_response_code(500); // error 500 Internal Server Error
                    echo json_encode(array('message' => 'Order creation failed'));
                }
            } else {
                http_response_code(401);
                echo json_encode(array('message' => 'You need to be logged in and provide the product ID to create an order.'));
            }
        } catch (PDOException $e) {
            http_response_code(500); // error 500 Internal Server Error
            echo json_encode(array('message' => 'Database error: ' . $e->getMessage()));
        }
        break;



    case 'PUT': // Handle update order request request
        $response = $orders->update($data->or_date, $data->or_id);
        if ($response) {
            http_response_code(200); // successful update
            echo json_encode(array('message' => 'Order updated successfully'));
        } else {
            http_response_code(500); //error 500 Internal Server Error
            echo json_encode(array('message' => 'Order update failed'));
        }
        break;

    case 'DELETE': // Handle delete order request request
        $response = $orders->delete($data->or_id);

        if ($response) {
            http_response_code(200); // successful deletion
            echo json_encode(array('message' => 'Order deleted successfully'));
        } else {
            http_response_code(500); // error 500 Internal Server Error
            echo json_encode(array('message' => 'Order deletion failed'));
        }
        break;

    default:
        http_response_code(405);
        $response = array('message' => 'Method not allowed');
        echo json_encode($response);
        break;
}

// fetech data from data-base and encode it in json
echo json_encode($response->fetchAll(PDO::FETCH_ASSOC));
