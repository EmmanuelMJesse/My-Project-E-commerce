<?php
session_start();

// Get the JSON data sent by the JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Validate and sanitize the input data
$fullName = filter_var($data['fullName'], FILTER_SANITIZE_STRING);
$phone = filter_var($data['phone'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$address = filter_var($data['address'], FILTER_SANITIZE_STRING);
$apartment = filter_var($data['apartment'], FILTER_SANITIZE_STRING);
$city = filter_var($data['city'], FILTER_SANITIZE_STRING);
$region = filter_var($data['region'], FILTER_SANITIZE_STRING);
$paymentMethod = filter_var($data['paymentMethod'], FILTER_SANITIZE_STRING);
$paymentNumber = filter_var($data['paymentNumber'], FILTER_SANITIZE_STRING);

$subtotal = filter_var($data['subtotal'], FILTER_VALIDATE_FLOAT);
$deliveryFee = filter_var($data['deliveryFee'], FILTER_VALIDATE_FLOAT);
$discount = filter_var($data['discount'], FILTER_VALIDATE_FLOAT);
$salesTax = filter_var($data['salesTax'], FILTER_VALIDATE_FLOAT);
$total = filter_var($data['total'], FILTER_VALIDATE_FLOAT);

// Example of saving the order to the database
// $conn = new mysqli('host', 'user', 'pass', 'dbname');
// $stmt = $conn->prepare("INSERT INTO orders (full_name, phone, email, address, apartment, city, region, payment_method, payment_number, subtotal, delivery_fee, discount, sales_tax, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
// $stmt->bind_param('ssssssssdddddd', $fullName, $phone, $email, $address, $apartment, $city, $region, $paymentMethod, $paymentNumber, $subtotal, $deliveryFee, $discount, $salesTax, $total);
// $stmt->execute();

// Here you would process the payment using the appropriate gateway
if ($paymentMethod === 'mpesa') {
    // Process M-Pesa payment
    $paymentSuccess = true;  // Set based on actual payment processing
} elseif ($paymentMethod === 'card') {
    // Process Card payment
    $paymentSuccess = true;  // Set based on actual payment processing
} else {
    $paymentSuccess = false;
}

// Respond back to the frontend
if ($paymentSuccess) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Payment failed']);
}
?>
