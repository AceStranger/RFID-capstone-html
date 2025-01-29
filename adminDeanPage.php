<?php
session_start();
include "dbh.php";

if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}

$user_ID = $_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminDean.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="dean-content">
                <div class="dean-content-top-part">
                    <h1 class="dean-content-head-text">DEAN</h1>
                    <div class="dean-content-btn">
                    </div>
                 
                </div>
                <div class="dean-content-dean-account-content">
                    <!-- Dean Account Content Form -->
                    <form id="dean-account-content-form" class="dean-account-content-form" enctype="multipart/form-data">
                        <div class="dean-account-content-top-part">
                            <div class="dean-account-content-filter-container">
                                <!-- Department Filter -->
                                <label for="dean-account-content-filter-select">Filter By Department:</label>
                                <select name="dean-account-content-filter-select" id="dean-account-content-filter-select">
                                    <option value="" selected>All</option>
                                    <option value="none">None</option>
                                    <?php 
                                        $departmentQuery = "SELECT * FROM department";
                                        $departmentQueryResult = mysqli_query($conn, $departmentQuery);

                                        // Check if there are any results
                                        if (mysqli_num_rows($departmentQueryResult) > 0) {
                                            while ($row = mysqli_fetch_assoc($departmentQueryResult)) {
                                                echo '<option value="'. $row['department_id'] .'">'. $row['department_name'] .'</option>';
                                            }
                                        }
                                    ?>
                                </select>

                                <!-- Name Filter -->
                                <label for="dean-account-content-search-input">Filter By Name:</label>
                                <input type="text" name="dean-account-content-search-input" id="dean-account-content-search-input" placeholder="Search...">
                            </div>
                        </div>
                    </form>

                    <!-- Dean Account Content Table -->
                    <div class="dean-account-content-main-content">
                        <div class="dean-account-content-table-content">
                            <table class="dean-account-content-table">
                                <thead class="dean-account-content-table-head">
                                    <tr class="dean-account-content-table-row">
                                        <th class="dean-account-content-data-fullname">Name</th>
                                        <th class="dean-account-content-data-department">Department</th>
                                        <th class="dean-account-content-data-btn-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dean-account-content-table-body" class="dean-account-content-table-body">
                                    <!-- Table rows will be dynamically populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                        // Fetch data based on filter
                        const selectDepartment = document.getElementById('dean-account-content-filter-select');
                        const searchInput = document.getElementById('dean-account-content-search-input');
                        const tableBody = document.getElementById('dean-account-content-table-body');

                        // Function to fetch data with filters
                        async function fetchFilteredData() {
                            const department = selectDepartment.value;
                            const search = searchInput.value;
                            const params = new URLSearchParams({
                                'department': department,
                                'search': search
                            });

                            try {
                                const response = await fetch('deanAccountFetch.php?' + params.toString());
                                const data = await response.json();  // Assuming the server returns JSON

                                // Clear previous table data
                                tableBody.innerHTML = '';

                                if (data.length > 0) {
                                    data.forEach(row => {
                                        const departmentName = row.department_name || 'No Department';
                                        const tableRow = `
                                            <tr class="dean-account-content-table-row">
                                                <td class="dean-account-content-data-fullname">${row.user_firstname} ${row.user_lastname}</td>
                                                <td class="dean-account-content-data-department">${departmentName}</td>
                                                <td class="dean-account-content-data-btn-action">
                                                    <form action="adminUserAccount.php" method="post" class="users-account-content-data-form">
                                                        <input type="hidden" name="user-id" value="${row.user_id}">
                                                        <button type="submit" name="pSubmit" class="user-account-action user-account-profile">PROFILE</button>
                                                        <button type="submit" name="eSubmit" class="user-account-action user-account-edit">EDIT</button>
                                                        <button type="submit" name="dSubmit" class="user-account-action user-account-delete" onclick="confirmDelete(event);">DELETE</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        `;
                                        tableBody.innerHTML += tableRow;
                                    });
                                } else {
                                    tableBody.innerHTML = '<tr><td colspan="5">No records found.</td></tr>';
                                }
                            } catch (error) {
                                console.error('Error fetching data:', error);
                            }
                        }
                        // Trigger search on input change
                        searchInput.addEventListener('input', fetchFilteredData);
                        selectDepartment.addEventListener('change', fetchFilteredData);

                        // Optional: Initial load on page load
                        window.onload = fetchFilteredData;
                    </script>
                </div>


            </div>
            
        </div>  
    </div>
    
    </div>
    <script>
    


    </script>

</body>
</html>