<?php
session_start();
include 'conn_db.php';

// ตรวจสอบ session และสิทธิ์การเข้าถึง
if (!isset($_SESSION['loggedin']) || 
    (!isset($_SESSION['HQ_account']) || $_SESSION['HQ_account'] !== $_SESSION['username']) &&
    (!isset($_SESSION['Admin_account']) || $_SESSION['Admin_account'] !== $_SESSION['username']) &&
    (!isset($_SESSION['CEO_account']) || $_SESSION['CEO_account'] !== $_SESSION['username'])) {
    echo '<script>alert("การเข้าถึงไม่ได้รับอนุญาต.");</script>';
    echo '<script>window.location.href = "index.html";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept</title>
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

    <!-- Your search form -->
    <div class="d-flex justify-content-center align-items-center">
        <form class="d-flex" role="search" method="get">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </div>
</div>

<div class="sidebar"id="sidebar">
    <header>
        <h1 style="color: white;">Accept Request</h1>
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
                        <li><a href="Stores.php"  style="color: white;">Store</a></li>
                    </ul>
                </div>
            </div>
            <a href="accept.php"class="link-body-emphasis d-inline-flex text-decoration-none rounded active"  style="color: white;font-weight: bold;">Accept</a>
            <div class="mb-1">
                <a href="#" onclick="toggleCollapse('history-collapse')" style="color: white;font-weight: bold;">
                    History
                </a>
                <div class="collapse show" id="history-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="histrory_HQ.php" style="color: white;">History HQ</a></li>
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

    // Define nl2br function in JavaScript
    function nl2br(str){
        return str.replace(/(.:\r\n|\r|\n)/g, '<br>');
    }
    $(document).ready(function () {
        // ดึง URL ปัจจุบัน
        var currentUrl = window.location.pathname;

        // วนลูปผ่านทุกลิงก์ในแถบข้าง
        $('.sidebar a').each(function () {
            var linkUrl = $(this).attr('href');

            // ตรวจสอบว่า URL ปัจจุบันมี URL ของลิงก์หรือไม่
            if (currentUrl.includes(linkUrl)) {
                // เพิ่มคลาสเพื่อเน้นลิงก์ในแถบข้าง
                $(this).addClass('active');
            }
        });

        // ตรวจสอบโดยเฉพาะสำหรับหน้า "Accept" และเพิ่มคลาส active
        if (currentUrl.includes('accept.php')) {
            $('.sidebar a:contains("Accept")').addClass('active');
        }
    });
</script>


<div class="content">
<!-- เพิ่มลิงก์หรือปุ่มสำหรับการเรียงลำดับตามสถานะ -->
<div class="form-group d-inline-flex">
    <label class="me-2 col-form-label" for="statusOrder">Sort by:</label>
    <select class="form-control form-control-sm me-3 w-auto" id="statusOrder">
        <option value="รอยืนยัน,ยืนยันแล้ว,ไม่ยืนยัน">รอยืนยัน</option>
        <option value="ยืนยันแล้ว,รอยืนยัน,ไม่ยืนยัน">ยืนยันแล้ว</option>
        <option value="ไม่ยืนยัน,รอยืนยัน,ยืนยันแล้ว">ไม่ยืนยัน</option>
    </select>
    <button class="btn btn-primary btn-sm" onclick="sortByStatus()">Sort</button>
</div>


    <table class="table">
        <thead>

            <tr>
                <th>Request ID</th>
                <th>Store</th>
                <th>Topic</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
          $AccReq = "SELECT * FROM request";
          $count = 0;
          $result = $conn->query($AccReq);
          if (isset($_GET['search'])) {
              $search = $_GET['search'];
              $sql = "SELECT * FROM request WHERE Store like '%$search%' ";
              $result = $conn->query($sql);
          }
          
            if ($result->num_rows > 0) {
                // แสดงข้อมูลทั้งหมด
                while ($row = $result->fetch_assoc()) {
                    // Truncate Topic to 100 characters
                    $truncatedTopic = strlen($row['Req_topic']) > 100 ? substr($row['Req_topic'], 0, 100) . '...' : $row['Req_topic'];
                
                    // กำหนดสีของช่อง status ตามเงื่อนไข
                    $statusColor = '';
                    switch ($row['Status']) {
                        case 'ยืนยันแล้ว':
                            $statusColor = 'green'; // สีเขียวสำหรับ "ยืนยันแล้ว"
                            break;
                        case 'ไม่ยืนยัน':
                            $statusColor = 'red'; // สีแดงสำหรับ "ไม่ยืนยัน"
                            break;
                        case 'รอยืนยัน':
                            $statusColor = 'orange'; // สีเหลืองสำหรับ "รอยืนยัน"
                            break;
                        default:
                            $statusColor = ''; // สีเป็นค่าว่างสำหรับสถานะอื่น ๆ
                    }
                
                    // แสดงข้อมูลในแต่ละแถวของตาราง
                    echo '<tr>';
                    echo '<td>' . $row['Request_id'] . '</td>';
                    echo '<td>' . $row['Store'] . '</td>';
                    echo '<td style="white-space: pre-line;">' . nl2br($truncatedTopic) . '</td>';
                    echo '<td style="color: ' . $statusColor . ';">' . $row['Status'] . '</td>'; // กำหนดสีให้กับช่อง status
                    echo '<td><button onclick="showDetails(' . $row['Request_id'] . ')" class="btn btn-primary">Detail</button></td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal for displaying details -->
<div class="modal fade" id="detailsModal1" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel1">Request Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailsModalBody1">
                <!-- Content will be dynamically added here -->
                <textarea id="additionalInfo" class="form-control" placeholder="Additional Information"></textarea>
                <input type="text" id="hqCaption" class="form-control mt-2" placeholder="HQ Caption">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="approveButton" onclick="approveData()" disabled>Approve</button>
                <button type="button" class="btn btn-danger" id="notApproveButton" onclick="NotAPP()" disabled>Not Approved</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    function showDetails(id) {
    document.getElementById('approveButton').disabled = !userCanApprove();
    document.getElementById('notApproveButton').disabled = !userCanNotApprove();

    fetch('getDetails.php?id=' + id, {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the modal content with details
            var statusElement = document.getElementById('status_' + id);
            document.getElementById('detailsModalBody1').innerHTML =
                'Request_id: ' + data.details.Request_id + '<br>' +
                'Store: ' + data.details.Store + '<br>' +
                'Employee ID: ' + data.details.Emp_id + '<br>' +
                'Employee Name: ' + data.details.Store_emp_name + '<br>' +
                'Date: ' + data.details.Req_date + '<br>' +
                'Topic: ' + nl2br(data.details.Req_topic) + '<br>' +  // Use nl2br for multiline display
                'Status:' + data.details.Status + '<br>' +
                'Detail: ' + data.details.Req_detail;

            var currentStatus = data.details.Status;
            console.log(currentStatus);

            // Show the modal
            $('#detailsModal1').modal('show');

            // Hide the buttons if the status is "ยืนยัน" or "ไม่ยืนยัน"
            if (currentStatus === 'ยืนยันแล้ว' || currentStatus === 'ไม่ยืนยัน') {
                document.getElementById('approveButton').style.display = 'none';
                document.getElementById('notApproveButton').style.display = 'none';
            } else {
                document.getElementById('approveButton').style.display = 'block';
                document.getElementById('notApproveButton').style.display = 'block';
            }
        } else {
            alert('Failed to fetch details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


    function closeModal() {
        $('#detailsModal1').modal('hide');
    }

    function approveData() {
    // Fetch the details of the currently displayed request
    const details = document.getElementById('detailsModalBody1').innerHTML;
    const requestId = details.split('Request_id: ')[1].split('<br>')[0].trim();

    // Perform Ajax request to update the status
    fetch('updateStatus.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'requestId=' + encodeURIComponent(requestId),
    })
    .then(response => response.json())
    .then(dataResponse => {
        if (dataResponse.success) {
            // Log the details to the console (you can replace this with your actual logic)
            console.log('Details to be approved:', details);

            // Access the updated status from the response and assign it to currentStatus
            currentStatus = dataResponse.updatedStatus;
            console.log(currentStatus);

            // Close the modal
            $('#detailsModal1').modal('hide');

            // Reload the page
            location.reload();
        } else {
            alert('Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
function updateButtonsVisibility() {
    const approveButton = document.getElementById('approveButton');
    const notApproveButton = document.getElementById('notApproveButton');

    // Check if the status is either "ยืนยัน" or "ไม่ยืนยัน"
    if (currentStatus === 'ยืนยัน' || currentStatus === 'ไม่ยืนยัน') {
        // Hide both buttons
        approveButton.style.display = 'none';
        notApproveButton.style.display = 'none';
    } else {
        // Show both buttons
        approveButton.style.display = 'block';
        notApproveButton.style.display = 'block';
    }
}

// Function to check if the user can approve
function userCanApprove() {
    // Add conditions based on the user's role
    return <?php echo isset($_SESSION['CEO_account']) && $_SESSION['CEO_account'] === $_SESSION['username'] ? 'false' : 'true'; ?>;
}

// Function to check if the user can not approve
function userCanNotApprove() {
    // Add conditions based on the user's role
    return <?php echo isset($_SESSION['CEO_account']) && $_SESSION['CEO_account'] === $_SESSION['username'] ? 'false' : 'true'; ?>;
}

function NotAPP() {
    // Fetch the details of the currently displayed request
    const details = document.getElementById('detailsModalBody1').innerHTML;
    const requestId = details.split('Request_id: ')[1].split('<br>')[0].trim();

    // Perform Ajax request to update the status
    fetch('UpStatus_fail.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'requestId=' + encodeURIComponent(requestId),
    })
    .then(response => response.json())
    .then(dataResponse => {
        if (dataResponse.success) {
            // Log the details to the console (you can replace this with your actual logic)
            console.log('Details to be not approved:', details);

            // Access the updated status from the response and assign it to currentStatus
            currentStatus = dataResponse.updatedStatus;
            console.log(currentStatus);

            // Close the modal
            $('#detailsModal1').modal('hide');

            // Reload the page
            location.reload();
        } else {
            alert('Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
function sortByStatus() {
    // Get the selected order of statuses
    var selectedStatuses = document.getElementById('statusOrder').value.split(',');

    // Get the table element
    var table = document.querySelector('table');

    // Get the table body
    var tbody = table.querySelector('tbody');

    // Get all rows in the table body
    var rows = Array.from(tbody.querySelectorAll('tr'));

    // Sort the rows based on status
    rows.sort(function(rowA, rowB) {
        // Get the status cell for each row
        var statusCellA = rowA.querySelector('td:nth-child(4)');
        var statusCellB = rowB.querySelector('td:nth-child(4)');

        // Get the status text for each row
        var statusTextA = statusCellA.textContent.trim();
        var statusTextB = statusCellB.textContent.trim();

        // Find the index of each status in the selected order
        var indexA = selectedStatuses.indexOf(statusTextA);
        var indexB = selectedStatuses.indexOf(statusTextB);

        // Compare the indices to determine the order
        return indexA - indexB;
    });

    // Remove existing rows from the table body
    rows.forEach(function(row) {
        tbody.removeChild(row);
    });

    // Append sorted rows back to the table body
    rows.forEach(function(row) {
        tbody.appendChild(row);
    });
}




</script>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
