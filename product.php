<?php
// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'auto_spare_parts';

// Connect to database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get all products
function getProducts($conn) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

// Function to get product by ID
function getProductById($id, $conn) {
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM products WHERE id = '$id'";
    $result = $conn->query($sql);
    $product = $result->fetch_assoc();
    return $product;
}

// Function to add product to cart
function addToCart($productId, $userId, $conn) {
    $productId = $conn->real_escape_string($productId);
    $userId = $conn->real_escape_string($userId);
    $sql = "INSERT INTO cart (user_id, product_id) VALUES ('$userId', '$productId')";
    $conn->query($sql);
}

// Function to get cart count
function getCartCount($userId, $conn) {
    $userId = $conn->real_escape_string($userId);
    $sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = '$userId'";
    $result = $conn->query($sql);
    $count = $result->fetch_assoc()['count'];
    return $count;
}

// Function to search products
function searchProducts($term, $conn) {
    $term = $conn->real_escape_string($term);
    $sql = "SELECT * FROM products WHERE name LIKE '%$term%' OR description LIKE '%$term%'";
    $result = $conn->query($sql);
    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

// Handle add to cart request
if (isset($_POST['product_id']) && isset($_SESSION['user_id'])) {
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'];
    addToCart($productId, $userId, $conn);
    echo 'Product added to cart';
    exit;
}

// Handle get cart count request
if (isset($_GET['get_cart_count']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $count = getCartCount($userId, $conn);
    echo $count;
    exit;
}

// Handle search products request
if (isset($_GET['term'])) {
    $term = $_GET['term'];
    $products = searchProducts($term, $conn);
    $html = '';
    foreach ($products as $product) {
        $html .= '<div class="product-card">
            <img src="images/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">
            <div class="product-info">
                <h2>' . htmlspecialchars($product['name']) . '</h2>
                <p>' . htmlspecialchars($product['description']) . '</p>
                <p>Price: ' . htmlspecialchars($product['price']) . '</p>
                <button class="add-to-cart" data-product-id="' . htmlspecialchars($product['id']) . '">Add to Cart</button>
            </div>
        </div>';
    }
    echo $html;
    exit;
}

// Get all products
$products = getProducts($conn);

// Display products
$html = '';
foreach ($products as $product) {
    $html .= '<div class="product-card">
        <img src="images/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">
        <div class="product-info">
            <h2>' . htmlspecialchars($product['name']) . '</h2>
            <p>' . htmlspecialchars($product['description']) . '</p>
            <p>Price: ' . htmlspecialchars($product['price']) . '</p>
            <button class="add-to-cart" data-product-id="' . htmlspecialchars($product['id']) . '">Add to Cart</button>
        </div>
    </div>';
}

// Display cart count
$cartCount = isset($_SESSION['user_id']) ? getCartCount($_SESSION['user_id'], $conn) : 0;
?>

<!-- HTML code here -->

<header>
    <nav>
        <ul>
            <li><a href="home page.html">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="contact us.html">Contact</a></li>
            <li><a href="#" class="cart-link">Cart (<span class="cart-count"><?= htmlspecialchars($cartCount) ?></span>)</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Our Products</h1>
    <div class="products-container">
        <?= $html ?>
    </div>
    <form id="search-form">
        <!-- Search form content here -->
    </form>
</main>

<?php $conn->close(); // Close the database connection ?>
