<?php
include 'conn_db.php';
session_start();

// ตรวจสอบ session และสิทธิ์การเข้าถึง
if (!isset($_SESSION['loggedin']) || 
    (!isset($_SESSION['HQ_account']) || $_SESSION['HQ_account'] !== $_SESSION['username']) &&
    (!isset($_SESSION['Admin_account']) || $_SESSION['Admin_account'] !== $_SESSION['username']) &&
    (!isset($_SESSION['CEO_account']) || $_SESSION['CEO_account'] !== $_SESSION['username'])) {
    echo '<script>alert("การเข้าถึงไม่ได้รับอนุญาต.");</script>';
    echo '<script>window.location.href = "index.html";</script>';
    exit();
}

$showExportButton = true; // กำหนดค่าเริ่มต้นเป็น true
if (isset($_SESSION['HQ_account'])|| isset($_SESSION['CEO_account'])) {
    $showExportButton  = false;
}

$showPQButton = true; // กำหนดค่าเริ่มต้นเป็น true
if (isset($_SESSION['HQ_account'])|| isset($_SESSION['CEO_account'])) {
    $showPQButton  = false;
}

$searchFilter = "";

// Check if filter parameters are provided in the URL
if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
    $searchFilter .= " AND Product_id = '" . $_GET['product_id'] . "'";
}

if (isset($_GET['size']) && !empty($_GET['size'])) {
    $searchFilter .= " AND Size = '" . $_GET['size'] . "'";
}

if (isset($_GET['color']) && !empty($_GET['color'])) {
    $searchFilter .= " AND Color = '" . $_GET['color'] . "'";
}

// Modify the SQL query to include the filter conditions
$sql = "SELECT * FROM product WHERE Store_id = 66000" . $searchFilter;
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Warehouse</title>

    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">

    <!-- Your custom styles -->
    <link rel="stylesheet" type="text/css" href="style 2.css">

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    
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
    .btn-toggle-nav.list-unstyled {
    display: flex;
    justify-content: center;
    flex-direction: column; /* Change to column direction */
    margin-left: 20px
}

.btn-toggle-nav.list-unstyled li {
    margin-bottom: 10px; /* Add margin bottom to create space between items */
}
        .sidebar {
        position: fixed;
        width: 250px;
        height: 100%;
        background: #343a40; /* Change the background color to white */
        color: white; /* Change the text color to black */
        transition: all 0.3s;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        margin-top: 50px;
        z-index: 1;
    }

        .sidebar header {
            padding: 15px;
            color: white;
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

        #suggestionsDropdown {
            position: absolute;
            z-index: 1000;
            background-color:  #343a40;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
        }
        .sidebar a.active {
            background-color: #9EB8D9; /* Change to the desired highlight color */
            color: white;
            width: 100%;
            padding: 5px ;
        }
        .navbar form input{
        margin-top: 1%; /* หรือ margin-top: 10px; ตามที่คุณต้องการ */
    }

        /* Adjust the button display for smaller screens */
        @media (max-width: 768px) {

    .navbar form input{
        margin-top: 1%; /* หรือ margin-top: 10px; ตามที่คุณต้องการ */
    }

        .toggle-btn {
            display: block; /* Show the button on smaller screens */
        }

        .sidebar {
            width: 0; /* Hide the sidebar by default on smaller screens */
        }
        .sidebar.show {
            width: 250px;
            margin-top: 60px;
        }

            .content {
                margin-left: 0;
                margin-top: 60px;
            }

            /* Center-align the search form on smaller screens */
            .navbar {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
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
            <form class="d-flex" role="search" method="get">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search" id="searchInput">
            <datalist id="suggestionsDropdown"></datalist>
            <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <button type="button" class="btn btn-primary" id="filterButton">Filter</button>
            <button type="button" class="btn btn-primary align-items: flex-end" id="addProductButton" >เพิ่มสินค้า</button>
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
                        <li><a href="hq.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded" style="color: white; " >HQ</a></li>
                        <li><a href="Stores.php"  style="color: white; ">Store</a></li>
                    </ul>
                </div>
            </div>
            <?php if ($showPQButton) { ?>
            <a href="PQ.php" style="color: white; font-weight: bold;">Purchase Quotation</a><br>
            <?php } ?>
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
       <!-- Export CSV Button -->
       <?php if ($showExportButton) { ?>
<a href="export.php?search=<?php echo isset($_GET['search']) ? urlencode($_GET['search']) : ''; ?>" class="btn btn-success ">Export CSV</a>
<?php } ?>
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
        <!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Products</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal3()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="get">
                    <div class="mb-3">
                        <label for="filterProductID" class="form-label">Product ID:</label>
                        <input type="text" class="form-control" id="filterProductID" name="product_id">
                    </div>
                    <div class="mb-3">
                        <label for="filterSize" class="form-label">Size:</label>
                        <input type="text" class="form-control" id="filterSize" name="size">
                    </div>
                    <div class="mb-3">
                        <label for="filterColor" class="form-label">Color:</label>
                        <input type="text" class="form-control" id="filterColor" name="color">
                    </div>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal3()">Close</button>
            </div>
        </div>
    </div>
</div>

        <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">เพิ่มสินค้า</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal1()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="process_add_product.php" method="post">
                    <div class="form-group">
                        <label for="add_product_id">รหัสสินค้า:</label>
                        <input type="text" class="form-control" id="add_product_id" name="product_id">
                    </div>
                    <div class="form-group">
                        <label for="add_product_name">ชื่อสินค้า:</label>
                        <input type="text" class="form-control" id="add_product_name" name="product_name">
                    </div>
                        <div class="form-group">
                            <label for="category_id">รหัสหมวดหมู่:</label>
                            <input type="text" class="form-control" id="category_id" name="category_id">
                        </div>
                        <div class="form-group">
                            <label for="product_price">ราคา:</label>
                            <input type="text" class="form-control" id="product_price" name="product_price">
                        </div>
                        <div class="form-group">
                            <label for="product_amount">จำนวน:</label>
                            <input type="text" class="form-control" id="product_amount" name="product_amount">
                        </div>
                        <div class="form-group">
                            <label for="size">ขนาด:</label>
                            <input type="text" class="form-control" id="size" name="size">
                        </div>
                        <div class="form-group">
                            <label for="color">สี:</label>
                            <input type="text" class="form-control" id="color" name="color">
                        </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal1()">ปิด</button>
                <button type = "submit" class="btn btn-primary">เพิ่มสินค้า</button>
            </div>
                </form>
            </div>
        </div>
        
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
    $('#addProductButton').click(function () {
        // Reset the border color for all form fields
        $('.form-control').css('border-color', '');

        $('#addProductModal').modal('show');
    });

    $('#addProductModal form').submit(function (event) {
        // Check for empty fields
        var emptyFields = checkEmptyFields();

        // If there are empty fields, prevent form submission and highlight them
        if (emptyFields.length > 0) {
            event.preventDefault();
            highlightEmptyFields(emptyFields);
        }
    });

    function checkEmptyFields() {
        var emptyFields = [];

        // Add your form field IDs here
        var fieldIds = ['add_product_id', 'add_product_name', 'category_id', 'product_price', 'product_amount', 'size', 'color'];

        // Check if any of the fields are empty
        fieldIds.forEach(function (fieldId) {
            var fieldValue = $('#' + fieldId).val().trim();
            if (fieldValue === '') {
                emptyFields.push(fieldId);
            }
        });

        return emptyFields;
    }

    function highlightEmptyFields(emptyFields) {
        // Add a red border to the empty fields
        emptyFields.forEach(function (fieldId) {
            $('#' + fieldId).css('border-color', 'red');
        });
    }

});

    $(document).ready(function () {
        // Show filter modal when filter button is clicked
        $('#filterButton').click(function () {
            $('#filterModal').modal('show');
        });
    });
    function closeModal1() {
        // Reset the border color for all form fields when closing the modal
        $('.form-control').css('border-color', '');

        $('#addProductModal').modal('hide');
    }
    function closeModal3() {
    $('#filterModal').modal('hide');
}

    
    </script>

    <div class="table-responsive">
            <?php
                if (isset($_GET['search'])) {
                    $search = $_GET['search'];
                    $sql = "SELECT * FROM product WHERE Product_name like '%$search%'";
                    $result = $conn->query($sql);
                }

                // Debug ส่วนที่แสดงข้อมูล
                if ($result->num_rows > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<table class='table'>";
                    echo "<thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Amount</th><th>Size</th><th>Color</th></tr></thead>";
                    echo "<tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["Product_id"] . "</td>";
                        echo "<td>" . $row["Product_name"] . "</td>";
                        echo "<td>" . $row["Category_id"] . "</td>";
                        echo "<td>" . $row["Product_price"] . "</td>";
                        echo "<td>" . $row["Product_amount"] . "</td>";
                        echo "<td>" . $row["Size"] . "</td>";
                        echo "<td>" . $row["Color"] . "</td>";

                        // เพิ่มปุ่มแก้ไขพร้อมกับแอตทริบิวต์ข้อมูลสำหรับแต่ละแถว
                        echo "<td><button class='btn btn-primary edit-btn' data-toggle='modal' data-target='#editProductModal' data-product-id='" . $row["Product_id"] . "' data-product-name='" . $row["Product_name"] . "' data-category-id='" . $row["Category_id"] . "' data-product-price='" . $row["Product_price"] . "' data-product-amount='" . $row["Product_amount"] . "' data-size='" . $row["Size"] . "' data-color='" . $row["Color"] . "'>แก้ไข</button></td>";
                        echo "</div>"; // Close the table-responsive div
                }}

            ?>
    <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel"aria-hidden="true">
    <!-- Modal content for Edit Product Modal -->
    <div class="modal-dialog" role="document">    
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editProductModalLabel">แก้ไขสินค้า</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal2()"ห >
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="process_edit_product.php" method="post">
                <div class="form-group">
                    <label for="product_id">รหัสสินค้า:</label>
                    <input type="text" class="form-control" id="product_id" name="product_id" readonly>
                </div>
                <div class="form-group">
                    <label for="product_name">ชื่อสินค้า:</label>
                    <input type="text" class="form-control" id="product_name" name="product_name">
                </div>
                <div class="form-group">
                    <label for="category_id">รหัสหมวดหมู่:</label>
                    <input type="text" class="form-control" id="category_id" name="category_id">
                </div>
                <div class="form-group">
                    <label for="product_price">ราคา:</label>
                    <input type="text" class="form-control" id="product_price" name="product_price">
                </div>
                <div class="form-group">
                    <label for="product_amount">จำนวน:</label>
                    <input type="text" class="form-control" id="product_amount" name="product_amount">
                </div>
                <div class="form-group">
                    <label for="size">ขนาด:</label>
                    <input type="text" class="form-control" id="size" name="size">
                </div>
                <div class="form-group">
                    <label for="color">สี:</label>
                    <input type="text" class="form-control" id="color" name="color">
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal2()">ปิด</button>
            <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
        </div>
        </form>
</div>
</div>

</div>
<script>
    function closeModal2() {
    $('#editProductModal').modal('hide');
}
$(document).ready(function () {
        // Show the editProductModal when the edit button is clicked
        $('body').on('click', '.edit-btn', function () {
            var productId = $(this).data('product-id');
            var productName = $(this).data('product-name');
            var categoryId = $(this).data('category-id');
            var productPrice = $(this).data('product-price');
            var productAmount = $(this).data('product-amount');
            var size = $(this).data('size');
            var color = $(this).data('color');

            // Set the values in the modal fields
            $('#editProductModal #product_id').val(productId);
            $('#editProductModal #product_name').val(productName);
            $('#editProductModal #category_id').val(categoryId);
            $('#editProductModal #product_price').val(productPrice);
            $('#editProductModal #product_amount').val(productAmount);
            $('#editProductModal #size').val(size);
            $('#editProductModal #color').val(color);

            // Reset the border color for all form fields
            $('.form-control').css('border-color', '');

            // Show the editProductModal
            $('#editProductModal').modal('show');
        });

        // Add a listener for the modal hidden event to clear the values when the modal is closed
        $('#editProductModal').on('hidden.bs.modal', function () {
            // Clear the input values
            $('#editProductModal input').val('');
        });

        $('#editProductModal form').submit(function (event) {
            // Check for empty fields
            var emptyFields = checkEmptyFieldsEdit();

            // If there are empty fields, prevent form submission and highlight them
            if (emptyFields.length > 0) {
                event.preventDefault();
                highlightEmptyFieldsEdit(emptyFields);
            }
        });

        function checkEmptyFieldsEdit() {
            var emptyFields = [];

            // Add your form field IDs here
            var fieldIds = ['product_name', 'category_id', 'product_price', 'product_amount', 'size', 'color'];

            // Check if any of the fields are empty
            fieldIds.forEach(function (fieldId) {
                var fieldValue = $('#editProductModal #' + fieldId).val().trim();
                if (fieldValue === '') {
                    emptyFields.push(fieldId);
                }
            });

            return emptyFields;
        }

        function highlightEmptyFieldsEdit(emptyFields) {
            // Add a red border to the empty fields
            emptyFields.forEach(function (fieldId) {
                $('#editProductModal #' + fieldId).css('border-color', 'red');
            });
        }
    });

        $(window).resize(function () {
            if ($(window).width() > 768) {
                $('#sidebar').removeClass('show');
            }
        });
  // ใส่โค้ดที่ใช้ Ajax เพื่อดึงข้อมูลคำแนะนำ
  $('#searchInput').on('input', function () {
        var searchQuery = $(this).val();

        $.ajax({
            url: 'search.php',
            type: 'GET',
            data: { search: searchQuery },
            dataType: 'json',
            success: function (data) {
                // ใช้ Set เพื่อกรองชื่อสินค้าที่ไม่ซ้ำกัน
                var uniqueProductNames = [...new Set(data)];

                var dropdown = $('#suggestionsDropdown');
                dropdown.empty();

                uniqueProductNames.forEach(function (suggestion) {
                    dropdown.append('<option value="' + suggestion + '">' + suggestion + '</option>');
                });

                dropdown.show();
            }
        });
    });

    // สำหรับคลิกที่ suggestion เพื่อเลือก
    $('#suggestionsDropdown').on('click', 'option', function () {
        var selectedSuggestion = $(this).val();
        $('#searchInput').val(selectedSuggestion);
        $('#suggestionsDropdown').hide();
        // คุณสามารถทำการค้นหาหรือกระทำอื่น ๆ กับ suggestion ที่เลือกได้ที่นี่
    });

    $('#searchInput').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var selectedSuggestion = $('#suggestionsDropdown').val();
            $('#searchInput').val(selectedSuggestion);
            $('#suggestionsDropdown').hide();
            // คุณสามารถทำการค้นหาหรือกระทำอื่น ๆ กับ suggestion ที่เลือกได้ที่นี่

            // และ/หรือ, นำไปยังการ submit แบบฟอร์ม
            // $('#yourSearchFormId').submit();
        }
    });

    // ซ่อน dropdown ข้อเสนอเมื่อคลิกนอกเหนือจากนั้น
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#searchInput, #suggestionsDropdown').length) {
            $('#suggestionsDropdown').hide();
        }
    });

$(document).ready(function () {
        // Get the current URL
        var currentUrl = window.location.href;

        // Iterate through each sidebar link
        $('.sidebar a').each(function () {
            var linkUrl = $(this).attr('href');

            // Check if the current URL contains the link URL
            if (currentUrl.indexOf(linkUrl) !== -1) {
                // Add a class to highlight the link in the sidebar
                $(this).addClass('active');
            }
        });
    });
</script>
   </div>
  </div>
</body>
</html>