<?php
// ตรวจสอบว่ามีการส่งข้อมูลมาโดยตรวจสอบว่ามีการกดปุ่ม Submit หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เชื่อมต่อกับฐานข้อมูล (ใช้ include ไฟล์ conn_db.php เพื่อเชื่อมต่อ)
    include 'conn_db.php';

    // ดึงข้อมูลที่ส่งมาจากฟอร์ม
    $storeAddress = $_POST['storeAddress'] ?? '';
    $documentDate = $_POST['document_Date'] ?? '';
    $vendorsNo = $_POST['Vendors_No'] ?? '';
    $name = $_POST['VEN_Name'] ?? '';
    $EMPname = $_POST['Cos_name'] ?? '';
    $postingDescription = $_POST['Posting_Description'] ?? '';
    $vat = $_POST['VAT'] ?? '';
    $vatPercent = $_POST['VAT%'] ?? '';
    $totalAmount = $_POST['Total_Amount'] ?? '';

    // ข้อมูลสินค้า
    $Lineno = $_POST['line_no'] ?? [];
    $itemCodes = $_POST['Item_code'] ?? [];
    $itemNames = $_POST['Item_name'] ?? [];
    $quantities = $_POST['Quantity'] ?? [];
    $prices = $_POST['Price'] ?? [];
    $amounts = $_POST['Amount'] ?? [];

    // ตรวจสอบว่ามีข้อมูลสินค้าหรือไม่
    if (!empty($itemCodes) && !empty($itemNames) && !empty($quantities) && !empty($prices) && !empty($amounts)) {
        // ทำการบันทึกข้อมูลลงในตาราง vendors
        $sql_vendors = "INSERT INTO vendors (`Vendors_No.`, `VEN_Name`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `VEN_Name` = VALUES(`VEN_Name`)";
        $stmt_vendors = mysqli_prepare($conn, $sql_vendors);
        if ($stmt_vendors) {
            mysqli_stmt_bind_param($stmt_vendors, "ss", $vendorsNo, $name);
            if (mysqli_stmt_execute($stmt_vendors)) {
                echo "บันทึกข้อมูล vendors สำเร็จ";

                // เรียกใช้ฟังก์ชัน savePurchaseQuotation เมื่อบันทึกข้อมูล vendors เรียบร้อยแล้ว
                savePurchaseQuotation($conn, $documentDate, $vendorsNo, $name, $EMPname, $postingDescription, $vatPercent, $totalAmount,$itemCodes, $itemNames, $quantities, $prices, $amounts, $vat, $vatPercent, $totalAmount);

            } else {
                echo "มีข้อผิดพลาดในการบันทึกข้อมูล vendors: " . mysqli_stmt_error($stmt_vendors);
            }
            mysqli_stmt_close($stmt_vendors);
        } else {
            echo "มีข้อผิดพลาดในการเตรียมคำสั่ง SQL vendors: " . mysqli_error($conn);
        }
    } else {
        echo "ไม่มีข้อมูลสินค้าที่จะบันทึก";
    }
    // ดูค่าข้อมูลที่ส่งมาจากฟอร์ม
}
print_r($_POST);

// เรียกใช้งานฟังก์ชัน savePurchaseQuotation()
savePurchaseQuotation($conn, $documentDate, $vendorsNo, $name, $name, $postingDescription, $vat, $vatPercent, $totalAmount, $itemCodes, $itemNames, $quantities, $prices, $amounts);

// ฟังก์ชันสำหรับบันทึกข้อมูลลงในตาราง purchase_quotation
function savePurchaseQuotation($conn, $documentDate, $vendorsNo, $name, $EMPname, $postingDescription, $vat, $vatPercent, $totalAmount, $itemCodes, $itemNames, $quantities, $prices, $amounts) {
    // ตรวจสอบว่าข้อมูลในอาร์เรย์ถูกต้องหรือไม่
    if (is_array($itemCodes) && is_array($itemNames) && is_array($quantities) && is_array($prices) && is_array($amounts)) {
        // วนลูปผ่านข้อมูลสินค้าแต่ละรายการ
        foreach ($itemCodes as $key => $itemCode) {
            $itemCodeValue = $itemCodes[$key];
            $itemNameValue = $itemNames[$key];
            $quantityValue = $quantities[$key];
            $priceValue = $prices[$key];
            $amountValue = $amounts[$key];

            // เพิ่มค่า vat, vatPercent, และ totalAmount ลงในอาร์เรย์
            $data = array(
                'vat' => $vat,
                'vatPercent' => $vatPercent,
                'totalAmount' => $totalAmount
            );
            // ทำการบันทึกข้อมูลลงในตาราง purchase_quotation
            $sql_purchase_quotation = "INSERT INTO purchase_quote
            (`document_date`, `Vendors_No`, `VEN_name`, `Cos_name`, `item_code`, `item_name`, `quantity`, `price`, `amount`, `VAT`, `VAT%`, `total_amount`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


            $stmt_purchase_quotation = mysqli_prepare($conn, $sql_purchase_quotation);
            if ($stmt_purchase_quotation) {
                mysqli_stmt_bind_param($stmt_purchase_quotation, "ssssdsdssdds", $documentDate, $vendorsNo, $name, $EMPname, $itemCodeValue, $itemNameValue, $quantityValue, $priceValue, $amountValue, $vat, $vatPercent, $totalAmount);

                if (mysqli_stmt_execute($stmt_purchase_quotation)) {
                    echo "บันทึกข้อมูล purchase_quotation สำเร็จ";
                    header("Location: PQ.php?message=บันทึกสำเร็จ");
                } else {
                    echo "มีข้อผิดพลาดในการบันทึกข้อมูล purchase_quotation: " . mysqli_stmt_error($stmt_purchase_quotation);
                }
                mysqli_stmt_close($stmt_purchase_quotation);
            } else {
                echo "มีข้อผิดพลาดในการเตรียมคำสั่ง SQL purchase_quotation: " . mysqli_error($conn);
            }

        }
    } else {
        // แสดงข้อความผิดพลาดหรือทำอย่างอื่นตามที่คุณต้องการ
        echo "";
    }
}


?>
