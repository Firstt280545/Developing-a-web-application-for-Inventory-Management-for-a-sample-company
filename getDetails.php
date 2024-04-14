<?php
// getDetails.php

// Include your database connection file
include 'conn_db.php';

// Check if the Request_id parameter is set
if (isset($_GET['id'])) {
    $requestId = $_GET['id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM request WHERE Request_id = ?");
    $stmt->bind_param("i", $requestId);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if the result contains data
    if ($result->num_rows > 0) {
        // Fetch data
        $row = $result->fetch_assoc();
    
        // Create an associative array to store details
        $details = array(
            'Request_id' => $row['Request_id'],
            'Store' => $row['Store'],
            'Emp_id' => $row['Emp_id'],
            'Store_emp_name' => $row['Store_emp_name'],
            'Req_date' => $row['Req_date'],
            'Req_topic' => $row['Req_topic'],
            'Status' => $row['Status'],  // Fix the typo here
            'Req_detail' => $row['Req_detail']
        );
    
        // Return details as JSON
        echo json_encode(array('success' => true, 'details' => $details));
    } else {
        // If no data found
        echo json_encode(array('success' => false, 'message' => 'No data found'));
    }

    // Close the prepared statement
    $stmt->close();
} else {
    // If Request_id is not set
    echo json_encode(array('success' => false, 'message' => 'Request_id parameter is missing'));
}

// Close the database connection
$conn->close();
?>
