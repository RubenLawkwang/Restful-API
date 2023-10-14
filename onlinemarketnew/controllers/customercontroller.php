<?php
/* This controller will
handle the update username function
and delete customer function */
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

include_once(__DIR__ . '/../config/database.php'); // Include data base configuration file

include_once(__DIR__ . '/../models/customer.php'); // Include customer configuration file

include_once(__DIR__ . '/../models/userauth.php'); // Include userlogin configuration file

// data base connection
$database = new Database();
$db = $database->getConnection();

$customer = new Customer($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];
$raw_data = file_get_contents("php://input");
var_dump($raw_data);


$data = json_decode($raw_data); // decode json data
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {

    http_response_code(400); // error 400 Bad Request
    echo json_encode(array('message' => 'Invalid JSON data'));
    exit;
}
//$responseArray = array();
// Handle different request methods
$userController = new Customer($db);
switch ($requestMethod) {
        // controllers
    case 'GET':
        if (!empty($_GET["c_id"])) {
            $response = $customer->readOne($_GET["c_id"]);
        } else {
            $response = $customer->read();
        }

        break;


    case 'PUT': // update customer username
        // check and assume that $data contain the user id and username
        if (!empty($data->u_id) && !empty($data->username)) {
            $response = $customer->update($data->username, $data->u_id);

            if ($response) {
                http_response_code(200);
                echo json_encode(array('message' => 'Username Updated'));
            } else {
                http_response_code(500); // error 500 Internal Server Error
                echo json_encode(array('message' => 'Username Update Failed'));
            }
        } else {
            // check if user_id is missing
            http_response_code(400);
            echo json_encode(array('message' => 'Invalid Request Data'));
        }
        break;

    case 'DELETE':
        // check and assume that $data contain the user id
        if (isset($data->c_id)) {
            $response = $customer->delete($data->c_id);

            if ($response) {
                http_response_code(200);
                echo json_encode(array('message' => 'Customer Deleted Successfully'));
            } else {
                http_response_code(500); // error 500 Internal Server Error
                echo json_encode(array('message' => 'Customer Deletion Failed'));
            }
        } else {
            http_response_code(400); // error 400 Bad Request
            echo json_encode(array('message' => 'Invalid Request Data'));
        }
        break;

    default:
        http_response_code(405);
        $response = array('message' => 'Method not allowed');
        echo json_encode($response);
        break;
}

echo json_encode($response->fetchAll(PDO::FETCH_ASSOC));
