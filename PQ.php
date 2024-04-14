<?php
include 'conn_db.php';
session_start();

// ตรวจสอบ session และสิทธิ์การเข้าถึง
if (!isset($_SESSION['loggedin']) || 
    (!isset($_SESSION['Admin_account']) || $_SESSION['Admin_account'] !== $_SESSION['username'])) {
    echo '<script>alert("การเข้าถึงไม่ได้รับอนุญาต.");</script>';
    echo '<script>window.location.href = "index.html";</script>';
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Quotation (PQ)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style 2.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>


    <style media="print">
          body * {
        border: none !important;
        background-color: white !important;
    }

    .content {
        margin-left: 0;
    }

    .sidebar {
        display: none;
    }

    .navbar {
        display: none;
    }

    #printSubmitBtn,
    #submitBtn {
        display: none; /* Hide both buttons during printing */
    }
    </style>
    <style>
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        .collapsed {
            width: 0;
        }

        .sidebar {
            position: fixed;
            width: 250px;
            height: 100%;
            background:  #343a40;
            color: white;
            transition: all 0.3s;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-top:50px;
            z-index: 1; /* Set z-index to 1 to keep it on top */
        }

        .sidebar header {
            padding: 15px;
            text-align: center;
        }

        .sidebar nav {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
        }

        .content {
            margin-left: 250px;
            margin-top:50px;
            padding: 16px;
            flex: 1; /* Fill remaining space */
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .navbar {
        background: #343a40;
        padding: 10px;
        position: fixed;
        width: 100%;
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

        .navbar button {
            background-color: #343a40;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .table {
            width: 100%;
            overflow-x: auto; /* Add horizontal scroll on smaller screens */
        }

        .btn-toggle-nav.list-unstyled {
            display: flex;
            justify-content: center;
            flex-direction: column; /* Change to column direction */
            margin-left: 20px
        }

        .btn-toggle-nav.list-unstyled li {
            margin-bottom: 10px; /* Add margin bottom to create space between items */
        }
        .toggle-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #343a40;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            display: none; /* Initially hide the button on larger screens */
        }
        .sidebar a.active {
            background-color: #BFDAF7; /* Change to the desired highlight color */
            color: white;
            width: 100%;
            padding: 5px ;
        }

        @media (max-width: 992px) {
        .toggle-btn {
            display: block;/* Show the button on smaller screens */
            margin-left: 0; /* Add margin to create space between the button and search form */
        }

            .sidebar {
                width: 0; /* Hide the sidebar by default on smaller screens */
            }
            .sidebar.show {
                width: 250px;
                margin-top: 40px;
            }

            .content {
                margin-left: 0;
                margin-top: 50px;
            }

            /* Center-align the search form on smaller screens */
            .navbar {
                flex-direction: none;
                align-items: flex-end;
            }
        }
        @media print {
        /* ซ่อนส่วนอื่น ๆ ที่ไม่ต้องการพิมพ์ */
        body *, .navbar , 
         #printSubmitBtn,
        #calculateBtn,
        button[onclick="addInputBox()"] {
            display: none;
    }

    .content {
        position: absolute;
        left: 0;
        top: 0;
    }

    /* Display only the content you want to print */
    .content, .content * {
        display: block;
    }

    body {
        margin: 0;
        padding: 0;
    }

    @page {
        size: A4 ; /* Set page size to landscape */
        margin: 0;
    }

    .content {
        width: 100%;
        margin: 0;
        padding: 16px;
        box-sizing: border-box;
    }

    .row {
        margin-bottom: 10px;
        display: flex; /* Make rows flex containers */
    }

    input,
    textarea,
    select {
        flex: 1; /* Make form fields take equal width */
        margin-right: 5px; /* Add some spacing between form fields */
    }

    /* Adjust other styles as needed for horizontal layout */
    .container-lg {
        display: flex;
        flex-wrap: wrap;
    }

    /* Additional styling for specific elements if needed */
    .col-form-label {
        flex: 1;
        margin-right: 5px;
    }
    }
    </style>
    <div>

</div>

</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <div class="navbar">
        <!-- Add the button to toggle sidebar collapse -->
        <div class="toggle-btn">
            <button class="btn btn-primary" onclick="toggleCollapse('sidebar')" >☰</button>
        </div>

        <!-- Your search form -->
        <div class="d-flex justify-content-center align-items-center">
      
        <button class="btn btn-primary" >☰</button>    
        </div>
    </div>

    <div class="sidebar"id="sidebar">
        <header>
            <h1 style="color: white;">Warehouse</h1>
            <?php
            if (isset($_SESSION['username'])) {
                echo '<p style="color: white;">Logged in as: ' . $_SESSION['username'] . '</p>';
            }
        ?>
        </header>
        <nav>
        <ul>
            <div class="mb-1">
                <a href="#" onclick="toggleCollapse('home-collapse')" style="color: white; font-weight: bold;">
                    Home
                </a>
                <div class="collapse show" id="home-collapse" style="">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="hq.php" style="color: white; " >HQ</a></li>
                        <li><a href="Stores.php"  style="color: white; ">Store</a></li>
                    </ul>
                </div>
            </div>
            <a href="PQ.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded active"   style="color: white; font-weight: bold;">Purchase Quotation</a><br>
            <a href="accept.php" style="color: white; font-weight: bold;">Accept</a>
            <div class="mb-1">
                <a href="#" onclick="toggleCollapse('history-collapse')" style="color: white; font-weight: bold;">
                    History
                </a>
                <div class="collapse show" id="history-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="histrory_HQ.php" style="color: white; ">History HQ</a></li>
                        <li><a href="Store_his.php" style="color: white;">History Store</a></li>
                    </ul>
                </div>
            </div>
            <a href="signout.php" style="color: white; font-weight: bold;">Logout</a>
            
        </ul>
    </nav>
</div>

<script>
           function toggleCollapse(collapseId) {
            var collapseElement = document.getElementById(collapseId);
            var isCollapsed = collapseElement.classList.contains('show');

            if (isCollapsed) {
                collapseElement.classList.remove('show');
            } else {
                collapseElement.classList.add('show');
            }
        }
</script>
<div class="content">
    <h2 class="text-start">Purchase Quotation (PQ)</h2>
        <form action="process_pq.php" method="post">
        <!-- Add your input fields and form elements here based on the details you provided -->
       <div class="row mb-3">
            <label for="Store_address" class="col-sm-1 col-form-label ">Address</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="Store_address" rows="2" cols="50"></textarea><br>
            </div>
        </div>
        
  <div class="row mb-3">
    <label for="Emp_name" class="col-sm-1 col-form-label">Name</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" name="Emp_name">
    </div>
    <label for="Emp_id" class="col-sm-1 col-form-label">Id</label>
    <div class="col-sm-3">
        <input type="text" class="form-control" name="Emp_id">
    </div>
     <label for="PQ_No" class="col-sm-1 col-form-label">Doc No.</label>
         <div class="col-sm-2">
            <input type="int" class="form-control" name="PQ_No">
        </div>
    </div>
        
        <div class="row mb-3">
            <label for="storeAddress" class="col-sm-1 col-form-label">Address</label>
            <div class="col-sm-7">
            <textarea class="form-control" name="storeAddress" rows="2" cols="50"></textarea>
            </div>
    
            <label for="documentDate" class="col-sm-2 col-form-label">Date</label>
            <div class="col-sm-2">
            <input type="date" class="form-control" name="document_date">
            </div>
        </div>

       <div class="container-lg">
    <div class="row mb-3">
        <div class="col-sm-3">
            <label for="Vendors_No" class="col-form-label">Vendor Order No.</label>
            <input type="number" class="form-control" name="Vendors_No">
        </div>

        <div class="col-sm-3">
            <label for="Name" class="col-form-label">Name</label>
            <input type="text" class="form-control" name="VEN_Name">
        </div>

        <div class="col-sm-3">
            <label for="Emp_name" class="col-form-label">Customer</label>
            <input type="text" class="form-control" name="Cos_name">
        </div>

        <div class="col-sm-3">
            <label for="Emp_role" class="col-form-label">Department</label>
            <input type="text" class="form-control" name="Emp_role">
        </div>
    </div>
</div>
<div class="row mb-3" id="inputBoxesContainer">
    <!-- Your existing input boxes -->
    <div class="col-sm-1">
        <label for="Line_no" class="col-form-label">No.</label>
        <input type="int" class="form-control" name="Line_no[]"> <!-- เพิ่ม ID ที่ขาดไป -->
    </div>
    <div class="col-sm-2">
        <label for="Item_code" class="col-form-label">Code</label>
        <input type="int" class="form-control" name="Item_code[]">
    </div>
    <div class="col-sm-3">
        <label for="Item_name" class="col-form-label">Name</label>
        <input type="varchar" class="form-control" name="Item_name[]">
    </div>
    <div class="col-sm-2">
        <label for="Quantity" class="col-form-label">Qty.</label>
        <input type="varchar" class="form-control" name="Quantity[]" id="quantity"> <!-- เพิ่ม ID ที่ขาดไป -->
    </div>
    <div class="col-sm-2">
        <label for="Price" class="col-form-label">Unit/Price</label>
        <input type="int" class="form-control" name="Price[]" id="price"> <!-- เพิ่ม ID ที่ขาดไป -->
    </div>
    <div class="col-sm-2">
        <label for="Amount" class="col-form-label">Amount</label>
        <!-- <input type="int" class="form-control" name="Amount[]" readonly> -->
        <input type="int" step="0.01"  class="form-control" name="Amount[]" id="amount" readonly> <!-- เพิ่ม ID ที่ขาดไป -->
    </div>

    
</div>

<button type="button" onclick="addInputBox()">Add Input Box</button>


   <div class="row mb-3">
    <label for="Posting Description" class="col-sm-1 col-form-label">Etc.</label>
    <div class="col-sm-6">
        <textarea type="varchar" class="form-control" rows="2" cols="60" name="Posting Description"></textarea>
    </div>

    <div class="row mb-3 justify-content-end">
        <label for="VAT" class="col-sm-2 col-form-label text-end">Amt. bef. VAT</label>
        <div class="col-sm-2">
            <input type="text" class="form-control" name="VAT" readonly>
        </div>
    </div>

<div class="row mb-3 justify-content-end">
    <label for="VAT%" class="col-sm-1 col-form-label text-end">VAT Amount 7%</label>
    <div class="col-sm-2">
        <input type="int" class="form-control" name="VAT%" > <!-- Add ID here -->
    </div>
</div>

<div class="row mb-3 justify-content-end">
    <label for="Total_Amount" class="col-sm-2 col-form-label text-end">Total Amount</label>
    <div class="col-sm-2">
        <input type="int" class="form-control" name="Total_Amount" readonly> <!-- Add ID here -->
    </div>
</div>
<button type="button" id="calculateBtn" onclick="calculateTotalAmount()" style="width: 100px; height: 40px; float: left;">Calculate</button>

</div>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-sm-4">
            <label for="Emp_name1" class="col-form-label">Prepared by</label>
            <input type="text" class="form-control" name="Emp_name1">
        </div>

        <div class="col-sm-4">
            <label for="Emp_name2" class="col-form-label">Checked by</label>
            <input type="text" class="form-control" name="Emp_name2">
        </div>

        <div class="col-sm-4">
            <label for="Emp_name3" class="col-form-label">Authorized Signature</label>
            <input type="text" class="form-control" name="Emp_name3">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-4">
            <label for="Order_date" class="col-form-label">Date</label>
            <input type="date" class="form-control" name="Order_date">
        </div>

        <div class="col-sm-4">
            <label for="Order_date" class="col-form-label">Date</label>
            <input type="varchar" class="form-control" name="Order_date">
        </div>

        <div class="col-sm-4">
            <label for="Document_date" class="col-form-label">Date</label>
            <input type="varchar" class="form-control" name="Document_date1">
        </div>
    </div>
</div>


        <input type="hidden" name="printAction" id="printAction" value="0">

<button type="button" id="printSubmitBtn" onclick="printAndSubmit()">Print and Submit PQ</button>
<button type="submit" id="submitBtn" style="display: none;">Submit PQ</button>
</form>
</div>

<script>
function printAndSubmit() {
    // Set the hidden input value to indicate print action
    document.getElementById('printAction').value = '1';

    // Trigger print action
    printPQForm();

    // Reset the hidden input value
    document.getElementById('printAction').value = '0';

    // Hide the "Print and Submit PQ" button
    var printSubmitButton = document.getElementById('printSubmitBtn');
    printSubmitButton.innerHTML = ''; // Clear the button text
    printSubmitButton.style.display = 'none'; // Hide the button
    console.log('Print and Submit PQ button hidden');

    // Hide the "Add Input Box" button
    var addButton = document.querySelector('button[onclick="addInputBox()"]');
    addButton.style.display = 'none'; // Hide the button
    console.log('Add Input Box button hidden');

    // Hide the "Calculate" button
    var calculateButton = document.getElementById('calculateBtn');
    calculateButton.style.display = 'none'; // Hide the button
    console.log('Calculate button hidden');

    // Submit the form after printing
    document.getElementById('submitBtn').click();
}

function printPQForm() {
    // Check if it's a print action
    if (document.getElementById('printAction').value === '1') {
        // Use JavaScript to trigger the print action
        window.print();
    }
}
function addInputBox() {
    var inputBoxesContainer = document.getElementById('inputBoxesContainer');
    var newInputBox = document.createElement('div');
    newInputBox.className = 'row mb-3'; // Adjust class based on your styling

    // Create input elements for the new input box
    newInputBox.innerHTML = `
        <div class="col-sm-1">
            <label class="col-form-label"></label>
            <input type="int" class="form-control" name="Line_no[]">
        </div>
        <div class="col-sm-2">
            <label class="col-form-label"></label>
            <input type="int" class="form-control" name="Item_code[]">
        </div>
        <div class="col-sm-3">
            <label class="col-form-label"></label>
            <input type="varchar" class="form-control" name="Item_name[]">
        </div>
        <div class="col-sm-2">
            <label class="col-form-label"></label>
            <input type="varchar" class="form-control" name="Quantity[]">
        </div>
        <div class="col-sm-2">
            <label class="col-form-label"></label>
            <input type="int" class="form-control" name="Price[]">
        </div>
        <div class="col-sm-2">
            <label class="col-form-label"></label>
            <input type="int" class="form-control" name="Amount[]" id="amount" readonly> <!-- เพิ่ม ID ที่ขาดไป -->
        </div>
    `;

    inputBoxesContainer.appendChild(newInputBox);

    // Add event listeners to calculate amount when Quantity or Price changes
    var quantityInput = newInputBox.querySelector('[name="Quantity[]"]');
    var priceInput = newInputBox.querySelector('[name="Price[]"]');

    quantityInput.addEventListener('input', function() {
        calculateAmount(newInputBox);
    });

    priceInput.addEventListener('input', function() {
        calculateAmount1(newInputBox);
    });
}
function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); // ใส่ comma ทุก 3 ตำแหน่ง
}
function calculateAmount1(inputBox) {
    // Get the values of quantity and price from the input box
    var quantity = parseFloat(inputBox.querySelector('[name="Quantity[]"]').value);
    var price = parseFloat(inputBox.querySelector('[name="Price[]"]').value);

    // Calculate the amount
    var amount = quantity * price;

    // Format the amount with comma
    var formattedAmount = formatNumber(amount.toFixed(2));

    // Set the calculated amount to the corresponding input field
    inputBox.querySelector('[name="Amount[]"]').value = isNaN(amount) ? '' : formattedAmount;

    // Return an object containing the calculated values
    return {
        quantity: quantity,
        price: price,
        amount: formattedAmount
    };
}



function calculateAmount() {
    var quantity = $('#quantity').val();
    var price = $('#price').val();
    var amount = quantity * price;
    $('#amount').val(amount);
}

// หากคุณไม่มี jQuery
document.getElementById('quantity').addEventListener('input', calculateAmount);
document.getElementById('price').addEventListener('input', calculateAmount);

function calculateAmount() {
    var quantity = parseFloat(document.getElementById('quantity').value);
    var price = parseFloat(document.getElementById('price').value);
    var amount = quantity * price;
    document.getElementById('amount').value = formatNumber(amount.toFixed(2));
   
}

document.getElementById('calculateBtn').addEventListener('click', calculateTotalAmount);

// Function to calculate total amount
function calculateTotalAmount() {
    var totalAmount = 0; // กำหนดค่าเริ่มต้นของ Total Amount เป็น 0

    // ดึงค่าทั้งหมดของช่อง Amount มา
    var amountInputs = document.querySelectorAll('[name="Amount[]"]');

    // วนลูปผ่านทุกช่องและทำการคำนวณ Total Amount
    amountInputs.forEach(function(input) {
        var amount = parseFloat(input.value.replace(/,/g, '')) || 0; // ดึงค่า Amount และแปลงเป็นเลขทศนิยม หรือให้มีค่าเป็น 0 ถ้าไม่สามารถแปลงได้
        totalAmount += amount; // เพิ่มค่า Amount ไปยัง Total Amount
   
        document.querySelector('[name="VAT"]').value = formatNumber(totalAmount.toFixed(2));
    });

    // Calculate VAT 7%
    var vatPercentage = 7; // VAT percentage
    var vatAmount = totalAmount * (vatPercentage / 100);

    // Display the VAT amount in the "VAT" input field with 2 decimal places
    document.querySelector('[name="VAT%"]').value = formatNumber(vatAmount.toFixed(2));

    // Calculate total amount including VAT
    var totalAmountWithVAT = totalAmount + vatAmount;

    // Display the total amount with VAT in the "Total_Amount" input field
    document.querySelector('[name="Total_Amount"]').value = formatNumber(totalAmountWithVAT.toFixed(2));
}


function submitForm() {
    // Create an empty array to store all the input data
    var inputData = [];

    // Get all input boxes
    var inputBoxes = document.querySelectorAll('.row.mb-3');

    // Loop through each input box and collect the data
    inputBoxes.forEach(function(inputBox) {
        // Calculate the amount and get other values from the input box
        var calculatedValues = calculateAmount(inputBox);
        var lineNo = inputBox.querySelector('[name="Line_no[]"]').value;
        var itemCode = inputBox.querySelector('[name="Item_code[]"]').value;
        var itemName = inputBox.querySelector('[name="Item_name[]"]').value;
        var quantity = calculatedValues.quantity;
        var price = calculatedValues.price;
        var amount = calculatedValues.amount;

        // Create an object containing the data
        var data = {
            lineNo: lineNo,
            itemCode: itemCode,
            itemName: itemName,
            quantity: quantity,
            price: price,
            amount: amount
        };

        // Push the data object into the array
        inputData.push(data);
    });

    // Convert the array to JSON format
    var jsonData = JSON.stringify(inputData);

    // Send the JSON data to the PHP file for processing
    // You can use AJAX or a form submit to send the data
    // Example using AJAX:
    $.ajax({
        url: 'process_data.php',
        type: 'POST',
        data: {jsonData: jsonData},
        success: function(response) {
            // Handle the response from the server
            console.log('Data sent successfully');
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error('Error sending data:', error);
        }
    });
}

</script>