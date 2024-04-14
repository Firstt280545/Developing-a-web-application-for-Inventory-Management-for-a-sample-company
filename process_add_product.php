<?php
include 'conn_db.php';

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $store_id = 66000;
    $category_id = $_POST['category_id'];
    $product_price = $_POST['product_price'];
    $product_amount = $_POST['product_amount'];
    $size = $_POST['size'];
    $color = $_POST['color'];

     // Check for empty fields
     if (empty($product_id) || empty($product_name) || empty($category_id) || empty($product_price) || empty($product_amount) || empty($size) || empty($color)) {
        echo '<script>alert("บึนทึกไม่สำเร็จกรุณากรอกข้อมูลให้ครบทุกช่อง.");</script>';
        echo '<script>window.location.href = "hq.php";</script>';
        exit(); 
    }
    // เขียน SQL Query เพื่อเพิ่มข้อมูล
    $sql = "INSERT INTO product (Product_id, Product_name, Store_id, Category_id, Product_price, Product_amount, Size, Color) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // ใช้ Prepared Statements เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiiiiss", $product_id, $product_name, $store_id, $category_id, $product_price, $product_amount, $size, $color);

    // ทำการ execute และตรวจสอบว่าสำเร็จหรือไม่
    if ($stmt->execute()) {
        // ถ้าสำเร็จให้ redirect หรือทำอย่างอื่นตามที่คุณต้องการ
        header("Location: hq.php");
    } else {
        // ถ้าไม่สำเร็จให้แสดงข้อความหรือทำอย่างอื่นตามที่คุณต้องการ
        echo '<script>alert("บึนทึกไม่สำเร็จ.");</script>' . $sql . "<br>" . $conn->error;
    }
    
    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();
}
?>

