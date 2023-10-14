<?php
class Customer
{
    private $conn;
    private $table_name = 'customer';
    private $table_name1 = 'user';

    // Constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all Customer
    function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY c_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }



    // Enables an existing customer to update his username
    function update($username, $u_id)
    {
        $query = "UPDATE " . $this->table_name1 . " SET username = ? WHERE u_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username, $u_id]);
        return $stmt;
    }



    // Delete a customer
    function delete($c_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE c_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$c_id]);
        return $stmt;
    }

    // Read one customer
    function readOne($c_id)
    {

        $query = "SELECT * FROM " . $this->table_name . " WHERE c_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$c_id]);
        return $stmt;
    }
}
