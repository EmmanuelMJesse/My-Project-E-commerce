<?php
header('Content-Type: application/json');

// Include config file for database connection settings
include("config.php");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "auto_spare_parts";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $country_code = $_POST['country_code'];
    $phone_number = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $location = !empty($_POST['location']) ? $_POST['location'] : NULL;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : NULL;
    $contact_method = !empty($_POST['contact_method']) ? $_POST['contact_method'] : NULL;

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO  customer_registration (full_name, username, country_code, phone_number, password, location, gender, contact_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => "Prepare failed: " . $conn->error]));
    }

    $stmt->bind_param("ssssssss", $full_name, $username, $country_code, $phone_number, $password, $location, $gender, $contact_method);

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = "Error: " . $stmt->error;
    }

    echo json_encode($response);

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
