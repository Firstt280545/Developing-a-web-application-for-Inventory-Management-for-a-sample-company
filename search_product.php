<?php
include 'conn_db.php';

$response = []; // Initialize response array

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Use prepared statement to prevent SQL Injection
    $sql = "SELECT DISTINCT Product_name FROM product WHERE Product_name LIKE ? AND Store_id = ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $search . "%";
    $stmt->bind_param("si", $searchParam, $_SESSION['current_store_id']);

    $stmt->execute();

    // Check for errors
    if ($stmt->error) {
        $response['error'] = $stmt->error;
    } else {
        $result = $stmt->get_result();
    
        $suggestions = [];

        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row['Product_name'];
        }

        $response['suggestions'] = $suggestions;
    }
} else {
    $response['error'] = "Search parameter not provided";
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
