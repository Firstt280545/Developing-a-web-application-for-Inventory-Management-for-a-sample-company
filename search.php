<?php
include 'conn_db.php';

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM product WHERE Product_name LIKE '%$search%'";
    $result = $conn->query($sql);

    $suggestions = array();

    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['Product_name'];
    }

    echo json_encode($suggestions);
}
?>
