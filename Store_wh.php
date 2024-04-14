<?php
session_start();
include 'conn_db.php';

// ตรวจสอบ session และสิทธิ์การเข้าถึง
if (!isset($_SESSION['loggedin']) || 
    (!isset($_SESSION['HQ_account']) || $_SESSION['HQ_account'] !== $_SESSION['username']) &&
    (!isset($_SESSION['Admin_account']) || $_SESSION['Admin_account'] !== $_SESSION['username']) &&
    (!isset($_SESSION['Store_account']) || $_SESSION['Store_account'] !== $_SESSION['username']) &&
    (!isset($_SESSION['CEO_account']) || $_SESSION['CEO_account'] !== $_SESSION['username'])) {
    echo '<script>alert("การเข้าถึงไม่ได้รับอนุญาต.");</script>';
    echo '<script>window.location.href = "index.html";</script>';
    exit();
}

$showRequestButton = true; // กำหนดค่าเริ่มต้นเป็น true
if (isset($_SESSION['Admin_account']) || isset($_SESSION['HQ_account'])|| isset($_SESSION['CEO_account'])) {
    $showRequestButton = false;
}

$showHQButton = true; // กำหนดค่าเริ่มต้นเป็น true
if (isset($_SESSION['Store__account']) ) {
    $showHQButton = false;
}

if (isset($_GET['id'])) {
    // ดึงค่า 'id' จาก URL
    $store_id = $_GET['id'];

    // ใช้ prepared statement เพื่อป้องกัน SQL Injection
    $sql = "SELECT * FROM product WHERE Store_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $store_id);

    $stmt->execute();

    // ตรวจสอบ errors
    if ($stmt->error) {
        echo "Error: " . $stmt->error;
    }

    $result = $stmt->get_result();

    // เก็บค่า $store_id ใน session
    $_SESSION['current_store_id'] = $store_id;
}

// รับค่าจากฟอร์ม filter หากมีการส่งค่ามา
if (isset($_GET['productId']) || isset($_GET['size']) || isset($_GET['color'])) {
    $productId = $_GET['productId'];
    $size = $_GET['size'];
    $color = $_GET['color'];

    // สร้างเงื่อนไขของการค้นหาใน SQL
    $whereClause = "1"; // กำหนดเงื่อนไขเริ่มต้น

    // เพิ่มเงื่อนไขค้นหาตามข้อมูลที่ได้รับมา
    if (!empty($productId)) {
        $whereClause .= " AND Product_id = '$productId'";
    }
    if (!empty($size)) {
        $whereClause .= " AND Size = '$size'";
    }
    if (!empty($color)) {
        $whereClause .= " AND Color = '$color'";
    }

    // สร้างคำสั่ง SQL สำหรับค้นหา
    $sql = "SELECT * FROM product WHERE $whereClause";

    // ดำเนินการคิวรีข้อมูล
    $result = $conn->query($sql);

    // ตรวจสอบผลลัพธ์และแสดงผลลัพธ์ตามที่ต้องการ
    if ($result->num_rows > 0) {
        // แสดงผลลัพธ์
    } else {
        echo "ไม่พบข้อมูลสินค้าที่ตรงกับเงื่อนไข";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Warehouse</title>
    <link rel="stylesheet" type="text/css" href="style 2.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
          
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                display: flex;
                min-height: 100vh;
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

        .content {
            margin-left: 250px; /* Width of the sidebar */
            padding: 16px; /* Optional padding for content */
        }

        .sidebar {
            position: fixed;
            width: 250px;
            height: 100%;
            background: #343a40;
            color: white;
            transition: all 0.3s;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin-top: 50px;
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
            margin-top: 50px;
            padding: 16px;
            flex: 1; /* Fill remaining space */
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 0; /* Set z-index back to 0 for the content */
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

        #listView {
        margin-top: 20px;
        display: none; /* Hide listView by default */
        width: 100%;
    }

    #listView table {
        width: 100%;
        margin-left: 0%; /* Set margin to accommodate the sidebar */
    }

    #galleryView{
        width: 100%;
        margin-top: 20px;
    }
    #galleryView .card-container {
    display: flex;
    flex-wrap: wrap;
    }

    /* Adjust the style for individual cards in the Gallery View */
    #galleryView .card {
        width: calc(25% - 20px); /* Set width to 25% (four cards per row) with margin */
        margin: 10px;
    }


        /* Add the following style to make the table fill the available width */
        .table {
            width: 100%;
            overflow-x: auto; /* Add horizontal scroll on smaller screens */
        }

/* Add this style to remove the box-shadow from the modal */
.modal-dialog {
    box-shadow: none;
}
        /* Adjust the margin for smaller screens */
       
        @media (max-width: 768px) {
            .toggle-btn {
            display: block; /* Show the button on smaller screens */
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
                flex-direction: column-reverse;
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
            <button class="btn btn-primary" onclick="toggleCollapse('sidebar')">☰</button>
        </div>

        <!-- Your search form -->
        <div class="d-flex justify-content-center align-items-center">
            <form class="d-flex" role="search" method="get">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search" id="searchInput" list="suggestionsDropdown">
                <datalist id="suggestionsDropdown"></datalist>
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>

        </div>
    </div>

    <div class="sidebar" id="sidebar">
    <header>
        <h1 style="color: white;">Warehouse</h1>
        <?php
                if (isset($_SESSION['username'])) {
                    echo '<p style="color: white;">Logged in as: ' . $_SESSION['username'] . '</p>';
                }
            ?>
    </header>
    <nav>
    <!-- Inside the <ul> of the sidebar -->
    <ul>
    <div class="mb-1">
            <?php if(isset($_SESSION['HQ_account']) || isset($_SESSION['Admin_account']) || isset($_SESSION['CEO_account'])) { ?>
                <a href="#" onclick="toggleCollapse('home-collapse')" style="color: white; font-weight: bold;">
                    Home
                </a>
                <div class="collapse show" id="home-collapse" style="">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <?php if ($showHQButton) { ?>
                            <li><a href="hq.php" style="color: white;">HQ</a></li>
                        <?php } ?>
                        <li><a id="storeLink" href="Stores.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded active" style="color: white;">Store</a></li>
                    </ul>
                </div>
            <?php } else { ?>
                <a id="storeLink" href="Stores.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded active" style="color: white; font-weight: bold;">Store</a>
            <?php } ?>
        </div>
        <?php if ($showRequestButton) { ?>
                    <a href="request.php" style="color: white; font-weight: bold;">Request</a>
                <?php } ?>
        <div class="mb-1">
        <?php if(isset($_SESSION['HQ_account']) || isset($_SESSION['Admin_account']) || isset($_SESSION['CEO_account'])) { ?>
                <a href="#" onclick="toggleCollapse('home-collapse')" style="color: white; font-weight: bold;">
                History
                </a>
                <div class="collapse show" id="home-collapse" style="">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <?php if ($showHQButton) { ?>
                             <li><a href="histrory_HQ.php"  style="color: white;">History HQ</a></li>
                        <?php } ?>
                        <li><a href="Store_his.php" style="color: white;">History Store</a></li>
                    </ul>
                </div>
            <?php } else { ?>
                <a  href="Store_his.php" style="color: white; font-weight: bold;">History Store</a>
            <?php } ?>
        
        </div>
        <a href="signout.php" style="color: white;font-weight: bold;">Logout</a>
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
    $(document).ready(function () {
    // Get the current path
    var currentPath = window.location.pathname;

    // Iterate through each sidebar link
    $('.sidebar a').each(function () {
        var linkUrl = $(this).attr('href');

        // Check if the current path contains the link URL
        if (currentPath.indexOf(linkUrl) !== -1) {
            // Add a class to highlight the link in the sidebar
            $(this).addClass('active');

            // If the link is the "Store" link, also add the class to the parent
            if (linkUrl === 'Store.php') {
                $('#storeLink').addClass('active');
            }
        }
        console.log('Current Path:', currentPath);
        console.log('Link URL:', linkUrl);
    });
});

    
</script>

<div class="content">
    <div class="btn-group" role="group" aria-label="View Options">
        <button type="button" class="btn btn-primary" onclick="showGalleryView()">Gallery View</button>
        <button type="button" class="btn btn-primary" onclick="showListView()">List View</button><br>
    </div>
<?php
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        // ใช้ prepared statement เพื่อป้องกัน SQL Injection
        $sql = "SELECT * FROM product WHERE Product_name LIKE ? AND Store_id = ?";
        $stmt = $conn->prepare($sql);
        $searchParam = "%" . $search . "%";
        $stmt->bind_param("si", $searchParam, $_SESSION['current_store_id']);

        $stmt->execute();

        // ตรวจสอบ errors
        if ($stmt->error) {
            echo "Error: " . $stmt->error;
        }

        $result = $stmt->get_result();
    }
    ?>

    <!-- Gallery View -->
<div id="galleryView">
    <div class="card-container">
        <?php
        if (isset($result) && $result !== null && $result->num_rows > 0) {
        $count = 0;
        $result->data_seek(0);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card" style="width: 18rem; margin-right: 10px;">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $row["Product_name"] . '</h5>';
                echo '<p class="card-text">Product ID: ' . $row["Product_id"] . '</p>';
                echo '<p class="card-text">Category ID: ' . $row["Category_id"] . '</p>';
                echo '<p class="card-text">Price: ' . $row["Product_price"] . '</p>';
                echo '<p class="card-text">Amount: ' . $row["Product_amount"] . '</p>';
                echo '<p class="card-text">Size: ' . $row["Size"] . '</p>';
                echo '<p class="card-text">Color: ' . $row["Color"] . '</p>';
                echo '<p class="card-text">Store_Id: ' . $row["Store_id"] . '</p>';
                echo '</div>';
                echo '</div>';
                // Add a line break after every 4 cards
                if (++$count % 4 === 0) {
                    echo '<br>';
                }
            }
        } else {
            echo "ไม่พบข้อมูลสินค้า";
        }
    }
        ?>
    </div>
</div>
    <!-- List View -->
    <div id="listView" class="table-responsive">
    <?php
    if (isset($result) && $result !== null && $result->num_rows > 0) {
        // รีเซ็ตค่าของ $result
        $result->data_seek(0);
        if ($result->num_rows > 0) {
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Amount</th><th>Size</th><th>Color</th><th>Store</th></tr></thead>";
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
                echo "<td>" . $row["Store_id"] . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "ไม่พบข้อมูลสินค้า";
        }
    }

    ?>
</div>
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> <!-- ปุ่มปิด -->
            </div>
            <div class="modal-body">
                <form id="filterForm" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="mb-3">
                        <label for="productId" class="form-label">Product ID</label>
                        <input type="text" class="form-control" id="productId" name="productId">
                    </div>
                    <div class="mb-3">
                        <label for="size" class="form-label">Size</label>
                        <input type="text" class="form-control" id="size" name="size">
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="color" name="color">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>
    <script>
        // ฟังก์ชันแสดง Gallery View
        function showGalleryView() {
            document.getElementById('galleryView').style.display = 'flex';
            document.getElementById('listView').style.display = 'none';
        }

        // ฟังก์ชันแสดง List View
        function showListView() {
            document.getElementById('galleryView').style.display = 'none';
            document.getElementById('listView').style.display = 'block';
        }

        $(document).ready(function () {
    var searchInput = $('#searchInput');
    var suggestionsDropdown = $('#suggestionsDropdown');

    searchInput.on('input', function () {
        var searchQuery = $(this).val();

        $.ajax({
            url: 'search_product.php',
            type: 'GET',
            data: { search: searchQuery },
            dataType: 'json',
            success: function (data) {
                console.log('Response:', data, 'Type:', typeof data);

                suggestionsDropdown.empty();

                if (Array.isArray(data)) {
                    data.forEach(function (suggestion) {
                        suggestionsDropdown.append('<option value="' + suggestion + '">' + suggestion + '</option>');
                    });
                } else {
                    console.error('Invalid data format. Expected an array.');
                }

                suggestionsDropdown.show();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // Hide dropdown when clicking outside the search input
    $(document).on('click', function (e) {
        if (!searchInput.is(e.target) && !suggestionsDropdown.is(e.target) && suggestionsDropdown.has(e.target).length === 0) {
            suggestionsDropdown.hide();
        }
    });
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

            // If the link is the "Store" link, also add the class to the parent
            if (linkUrl === 'Store.php') {
                $('#storeLink').addClass('active');
            }
        }
    });
});

</script>

</div>
</body>
</html>