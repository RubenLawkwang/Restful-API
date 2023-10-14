<?php
/* this function will 
A User should be login to perform any actions here
read one product order
read all product order
update an existing product order
Delete a product order from the data-base */

// check if user is login
if (isset($_SESSION['customer'])) {
    $userId = $_SESSION['customer'];
} else {
    // user is not login redirection is set on login page
    header("Location: login.php"); // Redirect to the login page
    exit();
}

class OrderProduct
{
    private $conn;
    private $table_name = 'customerorder';

    // Constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all Order Products
    function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY or_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    // Create a new order product
    public function create()
    {
        try {
            // retrieve user cookie
            $userCookie = $_SERVER['HTTP_COOKIE'];
            $userCookieValue = null;
            // This code will extract user cookies
            if (preg_match('/user_cookie=([^;]+)/', $userCookie, $matches)) {
                $userCookieValue = $matches[1];
            }
            if (!$userCookieValue) {
                http_response_code(401);
                echo json_encode(array('message' => 'Authentication required'));
                return false;
            }
            // Retrieve Product ID From URL
            $p_id = isset($_GET['p_id']) ? $_GET['p_id'] : null;
            if (!$p_id) {
                http_response_code(400);
                echo json_encode(array('message' => 'Product ID (p_id) is missing in the URL'));
                return false;
            }
            // Prepare and execute the query
            $query = "INSERT INTO " . $this->table_name . " (or_date, u_id, p_id) VALUES (NOW(), ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userCookieValue, $p_id]);

            if ($stmt->rowCount() > 0) {
                http_response_code(201);
                echo json_encode(array('message' => 'Order created successfully'));
                return true;
            } else {
                http_response_code(500);
                echo json_encode(array('message' => 'Order creation failed'));
                return false;
            }
        } catch (PDOException $e) {

            http_response_code(500);
            echo json_encode(array('message' => 'Database error: ' . $e->getMessage()));
            return false;
        }
    }




    // Update an order product
    function update($or_date, $or_id)
    {
        // check if a user is login
        if (isset($_SESSION['customer']) && $_SESSION['customer'] === true) {
            $query = "UPDATE " . $this->table_name . " SET or_date = ? WHERE or_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$or_date, $or_id]);
            return $stmt;
        } else {
            http_response_code(401);
            echo json_encode(array('message' => 'You need to be logged in to update this product.'));
            exit;
        }
    }

    // Delete order product
    function delete($or_id)
    {
        // check if a user is login
        if (isset($_SESSION['customer']) && $_SESSION['customer'] === true) {
            $query = "DELETE FROM " . $this->table_name . " WHERE or_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$or_id]);
            return $stmt;
        } else {

            http_response_code(401);
            echo json_encode(array('message' => 'You need to be logged in to delete this product.'));
            exit;
        }
    }

    // Read one order product
    function readOne($or_id)
    {
        // check if user is login
        if (isset($_SESSION['customer']) && $_SESSION['customer'] === true) {
            $query = "SELECT customerorder.or_date, user.username, product.p_name 
            FROM " . $this->table_name . " INNER JOIN user ON customerorder.u_id = user.u_id
            INNER JOIN product ON customerorder.p_id = product.p_id WHERE or_id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$or_id]);
            return $stmt;
        } else {

            http_response_code(401);
            echo json_encode(array('message' => 'You need to be logged in to view this product.'));
            exit;
        }
    }
}
