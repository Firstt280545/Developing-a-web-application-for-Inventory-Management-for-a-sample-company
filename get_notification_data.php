<?php
// Include your database connection file
include 'conn_db.php';

// Fetch notifications from the database for today
$sql = "SELECT Request_id, Store_emp_name, Req_topic, Status FROM request WHERE DATE(Req_date) = CURDATE() ORDER BY Req_date DESC LIMIT 10";
$result = $conn->query($sql);

$notifications = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = array(
            'Request_id' => $row['Request_id'],
            'Store_emp_name' => $row['Store_emp_name'],
            'Req_topic' => $row['Req_topic'],
            'Status' => $row['Status']
        );
    }
}

// Close the database connection
$conn->close();

// Output JSON-encoded notifications
header('Content-Type: application/json');
echo json_encode($notifications);
?>
