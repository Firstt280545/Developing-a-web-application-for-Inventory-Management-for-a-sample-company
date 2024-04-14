<?php
include 'conn_db.php';

if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];

    $sql = "SELECT * FROM Store WHERE NOT Store_id = 66000 AND Store_name LIKE '%$searchQuery%'";
    $result = $conn->query($sql);

    $stores = array();

    while ($row = $result->fetch_assoc()) {
        $stores[] = array(
            'Store_id' => $row['Store_id'],
            'Store_name' => $row['Store_name'],
            'Store_address' => $row['Store_address']
        );
    }

    echo json_encode($stores);
}
?>
