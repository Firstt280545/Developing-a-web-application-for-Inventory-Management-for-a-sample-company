<?php
session_start();
include 'conn_db.php';

if (!isset($_SESSION['loggedin'])) { 
    header('Location: index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have the form fields in your HTML form with the same names as used below
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $product_price = $_POST['product_price'];
    $product_amount = $_POST['product_amount'];
    $size = $_POST['size'];
    $color = $_POST['color'];

    // Validate and sanitize the input data as needed

    // Perform the update in the database
    $sql = "UPDATE product SET Product_name = '$product_name', Category_id = '$category_id', Product_price = '$product_price', Product_amount = '$product_amount', Size = '$size', Color = '$color' WHERE Product_id = '$product_id'";
    
    if ($conn->query($sql) === TRUE) {
        header('Location: hq.php');
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    // If not a POST request, redirect to an error page or handle it accordingly
    header('Location: error_page.html');
    exit;
}
?>
