<?php
session_start(); 
include 'conn_db.php'; 
// ตรวจสอบ session และสิทธิ์การเข้าถึง
if (!isset($_SESSION['loggedin']) || 
    (!isset($_SESSION['Admin_account']) || $_SESSION['Admin_account'] !== $_SESSION['username']) &&
    (!isset($_SESSION['Store_account']) || $_SESSION['Store_account'] !== $_SESSION['username'])){
    echo '<script>alert("การเข้าถึงไม่ได้รับอนุญาต.");</script>';
    echo '<script>window.location.href = "index.html";</script>';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $store = isset($_POST['Store']) ? $_POST['Store'] : '';
    $emp_id = isset($_POST['Emp_id']) ? $_POST['Emp_id'] : '';
    $store_emp_name = isset($_POST['Store_emp_name']) ? $_POST['Store_emp_name'] : '';
    $req_date = isset($_POST['Req_date']) ? $_POST['Req_date'] : '';
    $req_topic = isset($_POST['Req_topic']) ? $_POST['Req_topic'] : '';
    $req_detail = isset($_POST['Req_detail']) ? $_POST['Req_detail'] : '';
    if ($req_topic === 'อื่นๆ') {
        $req_topic = isset($_POST['otherInput']) ? $_POST['otherInput'] : '';
    }

    // ใช้ Prepared Statements เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("INSERT INTO request (Store, Emp_id, Store_emp_name, Req_date, Req_topic, Req_detail, Status) VALUES (?, ?, ?, ?, ?, ?, 'รอยืนยัน')");
    $stmt->bind_param("ssssss", $store, $emp_id, $store_emp_name, $req_date, $req_topic, $req_detail);

    if ($stmt->execute()) {
        echo 'Successfully';
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo 'Error' . $conn->error;
    }

    $stmt->close();
}

// $request_id_to_select = 1;

$sqlemp = "SELECT request.*, employee.Emp_id, employee.Emp_name
           FROM request
           INNER JOIN employee ON request.Emp_id = employee.Emp_id";

$result = $conn->query($sqlemp);
if (!$result) {
    die("Error in SQL query: " . $conn->error);
}
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // ต้องระบุว่า $selected_request_id ถูกใช้ที่ไหน
        $selected_request_id = $row['Request_id'];
        $store = $row['Store'];
        $emp_id = $row['Emp_id'];
        $store_emp_name = $row['Store_emp_name'];
        $selected_req_date = $row['Req_date'];
        $selected_req_topic = $row['Req_topic'];
        $selected_req_detail = $row['Req_detail'];
    }
} else {
    echo "0 results";
}


session_write_close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Warehouse</title>
    <link rel="stylesheet" type="text/css" href="style 2.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <!-- Include Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
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
            margin-top:45px;
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
        height: 7%;
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        color: white;
        align-items: flex-end;
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

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%; /* Set form width to 100% */
            max-width: 600px; /* Optional: Set a maximum width for the form */
            margin: 3% auto; /* Center horizontally and adjust top margin for vertical centering */
        }

        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .result {
            margin-top: 20px;
        }
        .sidebar a.active {
            background-color: #9EB8D9; /* Change to the desired highlight color */
            color: white;
            width: 100%;
            padding: 5px ;
        }

        .notification-btn {
    position: fixed;
    top: 20px; /* ปรับตำแหน่งตามต้องการ */
    left: 97%; /* ปรับตำแหน่งตามต้องการ */
}

        .notification-container {
            position: fixed;
            top: 70px;
            right: 10px;
            width: 300px;
            max-height: 300px;
            overflow-y: auto;
            background-color: #ffffff;
            border: 1px solid #ccc;
            color: black;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: none; /* Initially hide the notification container */
            z-index: 1000;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .dropdown-frame {
    margin-bottom: 15px; /* Adjust as needed */
}

        .dropdown-container {
            display: inline-block;
            position: relative;
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Style for the frame */
        .dropdown-container::before {
            content: '\25BC'; /* Unicode character for down arrow */
            font-size: 14px; /* Adjust as needed */
            color: #333; /* Adjust color as needed */
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            pointer-events: none;
        }

        /* Style for the dropdown list */
        select {
            display: inline-block;
            position: relative;
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
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
                margin-top: 60px;
            }

            .content {
                margin-left: 0;
                margin-top: 60px;
            }

            /* Center-align the search form on smaller screens */
            .navbar {
                flex-direction: column-reverse;
                align-items: flex-end;
                height: 7.8%;
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
        <div class="notification-btn">
        <button class="btn btn-primary" onclick="toggleNotifications()">🔔</button>
        <div class="notification-container" id="notification-container">
            <!-- Add notification items here dynamically using JavaScript -->
        </div>
    </div>
</div>

    </div>
<div class="sidebar"id="sidebar">
    <header>
        <h1 style="color: white;">Request</h1>
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
                    <li><a href="hq.php"  style="color: white;">HQ</a></li>
                <?php } ?>
                <li><a id="storeLink" href="Stores.php" style="color: white;">Store</a></li>
            </ul>
        </div>
    <?php } else { ?>
        <a id="storeLink" href="Stores.php" style="color: white;font-weight: bold;">Store</a>
    <?php } ?>
</div>
            <a href="request.php"  class="link-body-emphasis d-inline-flex text-decoration-none rounded active"  style="color: white;font-weight: bold;">Request</a>
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
   function highlightActiveLink() {
        // Get the current URL
        var currentUrl = window.location.href;

        // Iterate through each sidebar link
        var sidebarLinks = document.querySelectorAll('.sidebar a');
        sidebarLinks.forEach(function (link) {
            var linkUrl = link.getAttribute('href');

            // Check if the current URL contains the link URL
            if (currentUrl.indexOf(linkUrl) !== -1) {
                // Add an "active" class to highlight the link in the sidebar
                link.classList.add('active');
            }
        });
    }

    // Call the function on page load
    window.onload = function () {
        highlightActiveLink();
    };

    function toggleNotifications() {
    var notificationContainer = document.getElementById('notification-container');
    var isCollapsed = notificationContainer.style.display === 'block';

    if (isCollapsed) {
        notificationContainer.style.display = 'none';
    } else {
        notificationContainer.style.display = 'block';
        // Call a function to load and display notifications (you'll need to implement this)
        loadNotifications();
    }
}

function loadNotifications() {
    // Make an AJAX request to your server to fetch notifications
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_notification_data.php', true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var notifications = JSON.parse(xhr.responseText);
            renderNotifications(notifications);
        }
    };

    xhr.send();
}

function renderNotifications(notifications) {
    var notificationContainer = document.getElementById('notification-container');
    notificationContainer.innerHTML = '';

    // Display notifications in the container
    notifications.forEach(function(notification) {
        var notificationItem = document.createElement('div');
        notificationItem.classList.add('notification-item');
        notificationItem.innerHTML = '<strong>Request ID:</strong> ' + notification.Request_id +
                                    '<br><strong>Topic:</strong> ' + notification.Req_topic +
                                    '<br><strong>Status:</strong> ' + notification.Status;
        notificationContainer.appendChild(notificationItem);
    });
}
</script>

    <div class="content">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="Store">Store:</label>
        <input type="text" name="Store" required><br>

        <label for="Emp_id">Employee ID:</label>
        <input type="number" name="Emp_id" required><br>

        <label for="Store_emp_name">Employee Name:</label>
        <input type="text" name="Store_emp_name" required><br>

        <label for="Req_date">Date:</label>
        <input type="date" name="Req_date" required><br>

   <!-- ตัวอย่าง HTML และ JavaScript -->
<div class="dropdown-frame">
    <label for="Req_topic">Topic:</label>
    <div class="dropdown-container">
        <select name="Req_topic" id="Req_topic" required onchange="checkOther(this)">
            <option value="โอนย้ายสินค้า">โอนย้ายสินค้า</option>
            <option value="สินค้าชำรุด">สินค้าชำรุด</option>
            <option value="สินค้าจำนวนไม่ครบ">สินค้าจำนวนไม่ครบ</option>
            <option value="ผิดสี">ผิดสี</option>
            <option value="ผิดไซส์">ผิดไซส์</option>
            <option value="อื่นๆ">อื่นๆ</option>
        </select>
        <input type="text" id="otherInput" name="otherInput" style="display:none;" placeholder="โปรดระบุ">
    </div>
</div>

<script>
    function checkOther(select) {
    var otherInput = document.getElementById('otherInput');
    otherInput.style.display = (select.value === 'อื่นๆ') ? 'block' : 'none';
    otherInput.required = (select.value === 'อื่นๆ');
    if (select.value === 'อื่นๆ') {
        otherInput.value = ''; // เคลียร์ค่าเมื่อเลือก "อื่นๆ"
    }
}

function submitForm() {
    var selectedTopic = document.getElementById('Req_topic').value;
    var otherInput = document.getElementById('otherInput');
    var otherInputValue = (selectedTopic === 'อื่นๆ') ? otherInput.value : '';

    // เก็บค่าในตัวแปรหรือทำอย่างอื่นตามที่ต้องการ
    console.log('Selected Topic:', selectedTopic);
    console.log('Other Input Value:', otherInputValue);
}

</script>


        <label for="Req_detail">Detail:</label>
        <input type="text" name="Req_detail" required><br>

        <input type="submit" value="Submit">
    </form>
        <div class="result">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "Store: " . $row['Store'] . "<br>";
                    echo "Employee ID: " . $row['Emp_id'] . "<br>";
                    echo "Employee Name: " . $row['Store_emp_name'] . "<br>";
                    echo "Date: " . $row['Req_date'] . "<br>";
                    echo "Topic: " . $row['Req_topic'] . "<br>";
                    echo "Detail: " . $row['Req_detail'] . "<br>";
                    echo "Status: " . $row['Status'] . "<br>";
                }
            } else {
                echo "0 results";
            }
            ?>
        </div>
    </div>

</body>
</html>