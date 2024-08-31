<?php
// Database connection details
$host = "localhost"; // Change if your MySQL server is on a different host
$username = "root"; // Your database username
$password = ""; // Your database password
$database = "auto_spare_parts"; // Your database name

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $productName = filter_var($_POST['product_name'], FILTER_SANITIZE_STRING);
    $productID = filter_var($_POST['product_id'], FILTER_SANITIZE_STRING);
    $productPrice = filter_var($_POST['product_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $productDescription = filter_var($_POST['product_description'], FILTER_SANITIZE_STRING);
    
    // Handle the image upload
    $targetDir = "uploads/";
    $targetFile = $targetDir . uniqid() . "-" . basename($_FILES["productImage"]["name"]); // Unique filename
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if the file is an actual image
    if ($_FILES["productImage"]["error"] !== UPLOAD_ERR_OK) {
        die("Error uploading file.");
    }

    $check = getimagesize($_FILES["productImage"]["tmp_name"]);
    if ($check === false) {
        die("File is not an image.");
    }
    
    // Check file size (5MB max)
    if ($_FILES["productImage"]["size"] > 5000000) {
        die("Sorry, your file is too large.");
    }
    
    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }
    
    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
        die("Sorry, there was an error uploading your file.");
    }
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO products (product_name, product_id, product_price, product_description, product_image) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssss", $productName, $productID, $productPrice, $productDescription, $targetFile);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New product added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>