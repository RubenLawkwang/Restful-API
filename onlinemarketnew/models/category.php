<?php
/* this function will 
read one category
read all category
update an existing category
Delete a category from the data-base */
class Category
{
    private $conn;
    private $table_name = 'category';

    // Constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all Products
    function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY cat_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create a new category
    function create($cat_name, $cat_id)
    {
        $query = "INSERT INTO " . $this->table_name . " (cat_name, cat_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cat_name, $cat_id]);
        return $stmt;
    }

    // Update a category
    function update($cat_name, $cat_id)
    {
        $query = "UPDATE " . $this->table_name . " SET cat_name = ? WHERE cat_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cat_name, $cat_id]);
        return $stmt;
    }

    // Delete a category
    function delete($cat_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE cat_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cat_id]);
        return $stmt;
    }

    // Read one category
    function readOne($cat_id)
    {

        $query = "SELECT * FROM " . $this->table_name . " WHERE cat_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$cat_id]);
        return $stmt;
    }
}
