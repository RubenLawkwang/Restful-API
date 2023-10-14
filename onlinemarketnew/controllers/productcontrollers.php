<?php
/* This controller will
handle the create, update, delete, read all/read one function
from product models
*/
session_start(); // resume or start new session

// set respons content type to json
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once(__DIR__ . '/../config/database.php'); // include data-base configuration
include_once(__DIR__ . '/../models/products.php'); // include product configuration


$database = new Database(); // data base connection
$db = $database->getConnection();

$product = new Product($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];
$raw_data = file_get_contents("php://input");

var_dump($raw_data); // // check $raw_data data type

$data = json_decode($raw_data);
// handle json error
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // error 400 Bad Request
    echo json_encode(array('message' => 'Invalid JSON data'));
    exit;
}

// Handle different request
switch ($requestMethod) {

    case 'GET': // Handle read one and read all request
        if (!empty($_GET["p_id"])) {
            $response = $product->readOne($_GET["p_id"]);
        } else {
            $response = $product->read();
        }
        break;

    case 'POST': // Handle create request
        var_dump($data);
        $response = $product->create($data->p_name, $data->p_price, $data->cat_id);
        if ($response) {
            http_response_code(200);
            echo json_encode(array('message' => 'Product Created successfully'));
        } else {
            http_response_code(500); // error 500 Internal Server Error
            echo json_encode(array('message' => 'Product Creation failed'));
        }

        break;

    case 'PUT': // Handle update request
        $response = $product->update($data->p_name, $data->p_price, $data->cat_id, $data->p_id);
        if ($response) {
            http_response_code(200);
            echo json_encode(array('message' => 'Product updated successfully'));
        } else {
            http_response_code(500); // error 500 Internal Server Error
            echo json_encode(array('message' => 'Product update failed'));
        }
        break;

    case 'DELETE': // Handle delete request
        $response = $product->delete($data->p_id);
        if ($response) {
            http_response_code(200);
            echo json_encode(array('message' => 'Product deleted successfully'));
        } else {
            http_response_code(500); // error 500 Internal Server Error
            echo json_encode(array('message' => 'Product deletion failed'));
        }
        break;
    default:
        http_response_code(405);
        $response = array('message' => 'Method not allowed');
        break;
}

// fetech data from data-base and encode it in json
echo json_encode($response->fetchAll(PDO::FETCH_ASSOC));
