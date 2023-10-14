<?php
/* this function will 
read one product / A user should be login to read one Product
read all products
update an existing product
Delete a product from the data-base */
class Product
{
    private $conn;
    private $table_name = 'product';

    // Constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all Products
    function read()
    {
        $query = "SELECT p.*, c.* FROM " . $this->table_name . " p INNER JOIN category
         c ON p.cat_id = c.cat_id ORDER BY p.p_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create a new product
    function create($p_name, $p_price, $cat_id)
    {
        $query = "INSERT INTO " . $this->table_name . " (p_name, p_price, cat_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$p_name, $p_price, $cat_id]);
        return $stmt;
    }

    // Update an product
    function update($p_name, $p_price, $cat_id, $p_id)
    {
        $query = "UPDATE " . $this->table_name . " SET p_name = ?, p_price = ?, cat_id = ? WHERE p_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$p_name, $p_price, $cat_id, $p_id]);
        return $stmt;
    }

    // Delete a product
    function delete($p_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE p_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$p_id]);
        return $stmt;
    }

    // Read one product
    function readOne($p_id)
    {
        // check if a user is login to read One product
        if (isset($_SESSION['customer']) && $_SESSION['customer'] === true) {
            $query = "SELECT * FROM " . $this->table_name . " WHERE p_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$p_id]);
            return $stmt;
        } else {
            // Handle the case where the user is not logged in
            http_response_code(401);
            echo json_encode(array('message' => 'You need to be logged in to view this product.'));
            exit;
        }
    }
}
