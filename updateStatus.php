<?php
include 'conn_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestId = isset($_POST['requestId']) ? $_POST['requestId'] : '';

    // Update the status to "ยืนยันแล้ว"
    $stmt = $conn->prepare("UPDATE request SET Status = 'ยืนยันแล้ว' WHERE Request_id = ?");
    $stmt->bind_param("s", $requestId);

    if ($stmt->execute()) {
        // Fetch the updated status
        $updatedStatus = 'ยืนยันแล้ว';

        // Success response with updated status
        echo json_encode(['success' => true, 'updatedStatus' => $updatedStatus]);
    } else {
        // Failure response without any comment
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
} else {
    // Invalid request response without any comment
    echo json_encode(['success' => false]);
}
?>
