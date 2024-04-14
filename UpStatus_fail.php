<?php
include 'conn_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestId = isset($_POST['requestId']) ? $_POST['requestId'] : '';

    // Update the status to "ไม่ยืนยัน"
    $stmt = $conn->prepare("UPDATE request SET Status = 'ไม่ยืนยัน' WHERE Request_id = ?");
    $stmt->bind_param("s", $requestId);

    if ($stmt->execute()) {
        // Fetch the updated status
        $updatedStatus = 'ไม่ยืนยัน';

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
