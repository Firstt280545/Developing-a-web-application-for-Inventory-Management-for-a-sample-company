<?php
include 'conn_db.php';
session_start();

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

$search = ''; // กำหนดค่าเริ่มต้นเป็นสตริงว่าง
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql = "SELECT * FROM Store WHERE NOT Store_id = 66000 AND Store_name LIKE '%$search%'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- รวม Bootstrap CSS -->
    <title>Warehouse</title>
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

        .btn-toggle-nav.list-unstyled {
            display: flex;
            justify-content: center;
            flex-direction: column; /* Change to column direction */
            margin-left: 20px
        }

        .btn-toggle-nav.list-unstyled li {
            margin-bottom: 10px; /* Add margin bottom to create space between items */
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

        /* Add the following style to make the table fill the available width */
        .table {
            width: 100%;
            overflow-x: auto; /* Add horizontal scroll on smaller screens */
        }
        #suggestionsDropdown {
        position: absolute;
        z-index: 1000;
        background-color: #343a40;
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        display: none; /* Initially hide the dropdown */
    }
    #suggestionsDropdown a {
        display: block;
        padding: 10px;
        color: white;
        text-decoration: none;
    }
    .sidebar a.active {
            background-color: #9EB8D9; /* Change to the desired highlight color */
            color: white;
            width: 100%;
            padding: 5px ;
        }

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
                align-items: flex-end;
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

        <form class="d-flex" role="search" method="get">
            <input id="searchInput" class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
            <div id="suggestionsDropdown"></div>
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
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
                            <li><a href="histrory_HQ.php" style="color: white;">History HQ</a></li>
                        <?php } ?>
                        <li><a href="Store_his.php"  style="color: white;">History Store</a></li>
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
</script>


<div class="content">
    <div class="btn-group" role="group" aria-label="View Options">
    <button type="button" class="btn btn-primary" onclick="showGalleryView()">Gallery View</button>
    <button type="button" class="btn btn-primary" onclick="showListView()">List View</button><br>
</div>

<div id="galleryView">
            <div class="card-group">
                <?php
                $count = 0;
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    if ($row["Store_id"] != 66000) {
                        echo '<div class="card col-md-4">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title"><a href="Store_wh.php?id=' . $row["Store_id"] . '">'. $row["Store_id"] . ' - ' . $row["Store_name"] . '</a></h5>';
                        echo '<p class="card-text">Address: ' . $row["Store_address"] . '</p>';
                        echo '</div>';
                        echo '</div>';
                        if (++$count % 3 === 0) {
                            echo '</div><br><div class="card-group">';
                        }
                    }
                }
                echo '</div>';
                ?>
            </div>

<!-- List View -->
<div id="listView">
    <?php
       $result->data_seek(0);
       if ($result->num_rows > 0) {
           echo "<table class='table'>";
           echo "<thead><th>Store</th><th>Name</th><th>Address</th></thead>";
           echo "<tbody>";
   
           while ($row = $result->fetch_assoc()) {
               echo "<tr>";
               echo '<td><a href="Store_wh.php?id=' . $row["Store_id"] . '">' . $row["Store_id"] . '</a></td>';
               echo "<td>" . $row["Store_name"] . "</td>";
               echo "<td>" . $row["Store_address"] . "</td>";
               echo "</tr>";
           }
           echo "</tbody></table>";
       } else {
           echo "ไม่พบข้อมูลสินค้า";
       }
       ?>
</div>

<script>
    // ฟังก์ชันแสดง Gallery View
    function showGalleryView() {
        document.getElementById('galleryView').style.display = 'block';
        document.getElementById('listView').style.display = 'none';
    }

    // ฟังก์ชันแสดง List View
    function showListView() {
        document.getElementById('galleryView').style.display = 'none';
        document.getElementById('listView').style.display = 'block';
    }

    function updateSearchResults(data) {
    if (data.length > 0) {
        var suggestionsHtml = '';

        for (var i = 0; i < data.length; i++) {
            var store = data[i];
            suggestionsHtml += '<a href="Store.php?search=' + store.Store_name + '">' + store.Store_name + '</a>';
        }

        $('#suggestionsDropdown').html(suggestionsHtml);
        $('#suggestionsDropdown').show(); // Show the suggestions dropdown
    } else {
        $('#suggestionsDropdown').hide(); // Hide the suggestions dropdown if no results
    }
}

$('#searchInput').on('input', function () {
    var searchQuery = $(this).val();

    $.ajax({
        url: 'search_store.php',
        type: 'GET',
        data: { search: searchQuery },
        dataType: 'json',
        success: function (data) {
            updateSearchResults(data);
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


</body>
</html>
