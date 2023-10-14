<?php
session_start();
class UserAuth
{
    private $conn;
    private $table_customer = 'customer';

    // Constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Register a new customers
    // insert his username and password into table user

    public function register($username, $password, $c_fname, $c_lname, $c_email, $c_phonenumber, $c_address)
    {
        // Hash the password
        $hashedPassword = hash('md5', $password);

        try {
            // Insert into customer table
            $customerQuery = "INSERT INTO " . $this->table_customer . " (c_fname, c_lname, c_email, c_phonenumber, c_address) VALUES (?, ?, ?, ?, ?)";
            $customerStmt = $this->conn->prepare($customerQuery);

            if ($customerStmt->execute([$c_fname, $c_lname, $c_email, $c_phonenumber, $c_address])) {

                $customer_id = $this->conn->lastInsertId();

                //username and password into table user with reference with the customer id
                $userQuery = "INSERT INTO user (username, password, customer_id) VALUES (?, ?, ?)";
                $userStmt = $this->conn->prepare($userQuery);

                if ($userStmt->execute([$username, $hashedPassword, $customer_id])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Handle database errors here
            return false;
        }
    }

    // function for login
    function login($username, $password)
    {
        // Hash Password
        $hashedPassword = hash('md5', $password);
        // Check check of user already exist in table
        $userQuery = "SELECT * FROM user WHERE username = ? AND password = ?";
        $userStmt = $this->conn->prepare($userQuery);
        $userStmt->execute([$username, $hashedPassword]);
        if ($userStmt->rowCount() > 0) {
            // User exists
            $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

            // cookies to save session
            $userCookieValue = $userData['u_id'];

            // Store user data in the session
            $_SESSION['customer'] = $userCookieValue;

            // Set a user cookie
            $cookieName = 'user_cookie';
            $cookieValue = $userCookieValue;
            $cookieExpiration = time() + (60 * 60 * 24 * 30); // cookies will expire after 30 days
            $cookiePath = '/';
            setcookie($cookieName, $cookieValue, $cookieExpiration, $cookiePath);

            echo "Session ID: " . session_id() . "<br>";
            echo "Username in Session: " . $_SESSION['customer'] . "<br>";

            return 'Login successfully';
        }
        // if session does not match invalid user credentials
        else {
            return false;
        }
    }



    // logout function
    function logout()
    {
        // Resume or start user session
        session_start();

        // All session variables unset
        $_SESSION = array();

        // Session Destroy
        session_destroy();

        return 'Logged out successfully'; // susscessful message
    }
}
