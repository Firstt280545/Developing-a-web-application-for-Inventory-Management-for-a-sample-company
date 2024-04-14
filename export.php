<?php
session_start();
include 'conn_db.php';

// ตรวจสอบ SESSION ล็อกอิน
if (!isset($_SESSION['loggedin'])) { 
    header('Location: index.html');
    exit;
}

// ตรวจสอบการทำงานของฟังก์ชัน Export
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM product WHERE Product_name LIKE '%$search%'";
    $result = $conn->query($sql);

    // Export as CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="warehouse_data.csv"');
    $output = fopen('php://output', 'w');

    // Output CSV header
    fputcsv($output, array('Product_id', 'Product_name', 'Category_id', 'Product_price', 'Product_amount', 'Size', 'Color'));

    // Output data
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);

    exit;
}
?>
