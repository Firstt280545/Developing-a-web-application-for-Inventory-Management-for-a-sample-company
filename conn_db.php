<?php
$serverName = "localhost";
$db_username = "root";
$db_password = "";
$dbName = "erp";
$conn = new mysqli($serverName, $db_username, $db_password, $dbName);
if($conn->connect_error){
    die("เชื่อมต่อฐานข้อมูลไม่ได้" . $conn->connect_error);
}else{
 //  echo " ";
}
?>
