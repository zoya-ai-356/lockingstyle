<?php 
require_once 'common/header.php'; 
require_once 'common/auth-logic.php';
checkUserAuth();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $rating = (int)$_POST['rating'];
    $comment = sanitize($_POST['comment']);

    // Check if user actually bought the product
    $check_purchase = mysqli_query($conn, "SELECT oi.id FROM order_items oi 
                                          JOIN orders o ON oi.order_id = o.id 
                                          WHERE o.user_id = '$user_id' 
                                          AND oi.product_id = '$product_id' 
                                          AND o.order_status = 'delivered'");
    
    if (mysqli_num_rows($check_purchase) > 0) {
        // Ensure reviews table exists
        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            product_id INT,
            rating TINYINT,
            comment TEXT,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $sql = "INSERT INTO reviews (user_id, product_id, rating, comment) 
                VALUES ('$user_id', '$product_id', '$rating', '$comment')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "Feedback submitted for moderation.";
            redirect("product-details.php?id=$product_id");
        }
    } else {
        $_SESSION['error'] = "Only verified buyers can leave reviews.";
        redirect("product-details.php?id=$product_id");
    }
}
?>