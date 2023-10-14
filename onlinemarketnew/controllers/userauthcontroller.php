<?php
/* This controller will
the creation of a customer profile from userauth models 
the customer registration and login credentials will be insert in
customer and user table accordingly
*/
session_start(); // start or resume user session

// set respons content type to json
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once(__DIR__ . '/../config/database.php'); // Include data-base configuration
include_once(__DIR__ . '/../models/userauth.php'); // Include userauth configuration

// data-base connection
$database = new Database();
$db = $database->getConnection();

$user = new UserAuth($db); // New User

$requestMethod = $_SERVER["REQUEST_METHOD"];
$raw_data = file_get_contents("php://input");

$data = json_decode($raw_data); // encode data in json

// handle json error
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // error 400 Bad Request
    echo json_encode(array('message' => 'Invalid JSON data'));
    exit;
}

// handle different requests methods
switch ($requestMethod) {

    case 'POST': // Handle create request for both customer and user table
        if (
            isset($data->username) &&
            isset($data->password) &&
            isset($data->fname) &&
            isset($data->lname) &&
            isset($data->number) &&
            isset($data->email) &&
            isset($data->address)
        ) {
            $response = $user->register(
                $data->username,
                $data->password,
                $data->fname,
                $data->lname,
                $data->number,
                $data->email,
                $data->address
            );

            if ($response) {
                http_response_code(201); // Successful request
                echo json_encode(array('message' => 'Registration successful'));
            } else {
                http_response_code(500); // error 500 Internal Server Error
                echo json_encode(array('message' => 'Registration failed'));
            }
        } else {
            http_response_code(400); // error 400 Bad Request
            echo json_encode(array('message' => 'Missing required fields'));
        }
        break;

    case 'GET': // Handle Login request
        if (isset($data->username) && isset($data->password)) {
            $loginResponse = $user->login($data->username, $data->password);

            if ($loginResponse === 'Login successfully') {
                // Store user's login status in the session
                $_SESSION['customer'] = true;

                http_response_code(200); // successful request
                echo json_encode(array('message' => 'Login successful'));
            } else {
                http_response_code(401); // error 401 Unauthorized
                echo json_encode(array('message' => 'Invalid credentials'));
            }
        } else {
            http_response_code(400); // error 400 Bad Request
            echo json_encode(array('message' => 'Missing required fields'));
        }
        break;

    case 'DELETE': // handle Logout Request
        if (isset($_SESSION['customer']) && $_SESSION['customer'] === true) {

            $_SESSION = array();

            // Destroy the session
            session_destroy();

            http_response_code(200); // successful request
            echo json_encode(array('message' => 'Logged out successfully'));
        } else {
            http_response_code(400); // error 400 Bad Request
            echo json_encode(array('message' => 'User not logged in'));
        }
        break;
}
