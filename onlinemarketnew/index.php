<?php
include_once 'config/database.php';
include_once 'models/products.php';
include_once 'models/readcustomer.php';
include_once 'models/userauth.php';
include_once 'models/orderproduct.php';
include_once 'models/customer.php';
include_once 'models/category.php';

$request = $_SERVER['REQUEST_URI'];

// Use a substring of the request, since your request has `/project/api` preceding the endpoint.
$endpoint = substr($request, strlen('/project/api'));

// Split the endpoint into its components
$endpoint_parts = explode('/', $endpoint);

switch ($endpoint_parts[1]) {
    case 'Products':
        include_once 'controllers/productcontrollers.php';
        break;
    case 'Readcustomers':
        include_once 'controllers/readcustomerscontroller.php';
        break;
    case 'Userauth':
        include_once 'controllers/userauthcontroller.php';
        break;
    case 'OrderProduct':
        include_once 'controllers/orderproductcontroller.php';
        break;
    case 'Customer':
        include_once 'controllers/customercontroller.php';
        break;
    case 'Category':
        include_once 'controllers/categorycontroller.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(
            array("message" => "No route found.")
        );
        break;
}
