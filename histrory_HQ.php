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
// ตรวจสอบว่ามีการส่งค่า Time_Stamp มาหรือไม่
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search_timestamp = $_GET['search'];

    $sql = "SELECT * FROM product WHERE Store_id = 66000 AND Time_Stamp = '$search_timestamp'";
} else {
    $sql = "SELECT * FROM product WHERE Store_id = 66000";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>History HQ</title>
    <link rel="stylesheet" type="text/css" href="style 2.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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


        /* Adjust the button display for smaller screens */
        @media (max-width: 768px) {
            .toggle-btn {
                display: block; /* Show the button on smaller screens */
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
                width: 100%;
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
        <button class="btn btn-primary" onclick="toggleCollapse('sidebar')">☰</button>
    </div>

    <!-- Your search form -->
    <div class="d-flex justify-content-center align-items-center">
    <nav>
        <form class="d-flex" role="search" method="get">
            <div class="well">
                <div id="datepicker-container" class="input-append">
                    <input data-format="yyyy-MM-dd" type="text" id="datepicker" class="form-control me-2" placeholder="Search" aria-label="Search" name="search">
                    <span class="add-on">
              <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
            </span>
                </div>
            </div>
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </nav>
    </div>
</div>
<div class="sidebar" id="sidebar">
    <header>
        <h1 style="color: white;">History HQ</h1>
        <?php
            if (isset($_SESSION['username'])) {
                echo '<p style="color: white;">Logged in as: ' . $_SESSION['username'] . '</p>';
            }
        ?>
    </header>
    <nav>
        <ul>
            <div class="mb-1">
                <a href="#" onclick="toggleCollapse('home-collapse')" style="color: white;font-weight: bold;">
                    Home
                </a>
                <div class="collapse show" id="home-collapse" style="">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="hq.php" style="color: white;">HQ</a></li>
                        <li><a href="Stores.php" style="color: white;">Store</a></li>
                    </ul>
                </div>
            </div>
            <a href="accept.php" style="color: white;font-weight: bold;">Accept</a>
            <div class="mb-1">
                <a href="#" onclick="toggleCollapse('history-collapse')" style="color: white;font-weight: bold;">
                    History
                </a>
                <div class="collapse show" id="history-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="histrory_HQ.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded" style="color: white;">History HQ</a></li>
                        <li><a href="Store_his.php" style="color: white;">History Store</a></li>
                    </ul>
                </div>
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
    <?php
    // ... Your PHP code ...

    // Debug the part that displays data
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
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "ไม่พบข้อมูลสินค้า";
    }
    ?>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                showButtonPanel: true
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
            }
        });
    });
    </script>
</div>
</body>
</html>
