<?php
// user-management.php

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_DATABASE', 'your_database');

// Create a database connection
function getDB() {
    $dbConnection = null;
    try {
        $dbConnection = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_DATABASE, DB_USERNAME, DB_PASSWORD);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Database connection error: " . $e->getMessage();
    }
    return $dbConnection;
}

// Get user information
function getUserInfo($db, $userId) {
    $stmt = $db->prepare("SELECT full_name, username, phone, address, gender, email, profile_image, (SELECT COUNT(*) FROM cart WHERE user_id = :user_id) AS cart_count FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update user information
function updateUserInfo($db, $userId, $fullName, $username, $phone, $password, $address, $gender, $email) {
    if ($password) {
        $password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET full_name = :full_name, username = :username, phone = :phone, address = :address, gender = :gender, email = :email, password = :password WHERE id = :user_id");
        $stmt->bindParam(':password', $password);
    } else {
        $stmt = $db->prepare("UPDATE users SET full_name = :full_name, username = :username, phone = :phone, address = :address, gender = :gender, email = :email WHERE id = :user_id");
    }
    $stmt->bindParam(':full_name', $fullName);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

// Update payment methods
function updatePaymentMethods($db, $userId, $naftyCoins, $cardNumber) {
    $stmt = $db->prepare("UPDATE users SET nafty_coins = :nafty_coins, card_number = :card_number WHERE id = :user_id");
    $stmt->bindParam(':nafty_coins', $naftyCoins);
    $stmt->bindParam(':card_number', $cardNumber);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    return $stmt->execute();
}

header('Content-Type: application/json');

$response = array();
$userId = 1; // Replace with the actual user ID from session or other source

try {
    $db = getDB();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get user information
        $response = getUserInfo($db, $userId);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'update-user-info') {
                // Update user information
                $fullName = $_POST['full-name'] ?? '';
                $username = $_POST['username'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $password = $_POST['password'] ?? '';
                $address = $_POST['address'] ?? '';
                $gender = $_POST['gender'] ?? '';
                $email = $_POST['email'] ?? '';

                $success = updateUserInfo($db, $userId, $fullName, $username, $phone, $password, $address, $gender, $email);
                $response['success'] = $success;
            } elseif ($_POST['action'] === 'update-payment-methods') {
                // Update payment methods
                $naftyCoins = $_POST['nafty-coins'] ?? '';
                $cardNumber = $_POST['card-number'] ?? '';

                $success = updatePaymentMethods($db, $userId, $naftyCoins, $cardNumber);
                $response['success'] = $success;
            }
        } else {
            $response['error'] = 'Invalid action specified';
        }
    }
} catch (PDOException $e) {
    $response['error'] = 'Database query error: ' . $e->getMessage();
}

echo json_encode($response);
?>
