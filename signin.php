<?php

session_start(); 
include 'conn_db.php'; 
$username = $_POST['username'];
$password = $_POST['password'];
$check = $conn->prepare('SELECT Emp_id, Emp_pwd, Emp_role, Store_id FROM employee WHERE Emp_user = ?');
$check->bind_param('s', $username); 
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->bind_result($Emp_id, $Emp_pwd, $Emp_role, $Store_id);
    $check->fetch();
    if ($password === $Emp_pwd) {
        $_SESSION['loggedin'] = TRUE; 
        $_SESSION['username'] = $username; 

        switch ($Emp_role) {
            case "CEO":
            case "Admin":
            case "HQ":
                $_SESSION[$Emp_role.'_account'] = $username;
                header('Location: hq.php');
                break;
            case "Store":
                $_SESSION['Store_account'] = $username;
                $_SESSION['Store_id'] = $Store_id; // Include Store_id in the session
                header('Location: Store_wh.php?id='. $Store_id);
                break;
            default:
                echo '<script>alert("Unauthorized access.");</script>';
                echo '<script>window.location.href = "index.html";</script>';
        }
    } else {
        echo '<script>alert("Username or password is incorrect.");</script>';
        echo '<script>window.location.href = "index.html";</script>';
    }
} else {
    echo '<script>alert("Username or password is incorrect.");</script>';
    echo '<script>window.location.href = "index.html";</script>';
}
?>
