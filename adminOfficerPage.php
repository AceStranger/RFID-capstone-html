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
    <title>Officer</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminOfficer.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="officer-content">
                <div class="officer-content-top-part">
                    <h1 class="officer-content-head-text">OFFICER</h1>
                    <div class="officer-content-btn">
                    </div>
                </div>
                <div class="officer-content-officer-account-content">
                    <form id="officer-account-content-form" class="officer-account-content-form" enctype="multipart/form-data">
                        <div class="officer-account-content-top-part">
                            <div class="officer-account-content-filter-container">
                                <label for="officer-account-content-filter-select">Filter By Organization:</label>
                                <select name="officer-account-content-filter-select" id="officer-account-content-filter-select">
                                    <option value="" selected>All</option>
                                    <option value="none">None</option>
                                    <?php 
                                        $organizationQuery = "SELECT * FROM organization";
                                        $organizationQueryResult = mysqli_query($conn, $organizationQuery);
                                        if (mysqli_num_rows($organizationQueryResult) > 0) {
                                            while ($row = mysqli_fetch_assoc($organizationQueryResult)) {
                                                echo '<option value="'. $row['organization_id'] .'">'. $row['organization_name'] .'</option>';
                                            }
                                        }
                                    ?>
                                </select>

                                <label for="officer-account-content-filter-select-col">Filter By:</label>
                                <select name="officer-account-content-filter-select-col" id="officer-account-content-filter-select-col">
                                    <option value="none" selected>None</option>
                                    <option value="name">Name</option>
                                    <option value="position">Position</option>
                                </select>

                                <input type="text" name="officer-account-content-search-input" id="officer-account-content-search-input" placeholder="Search...">
                            </div>
                        </div>
                    </form>

                    <div class="officer-account-content-main-content">
                        <table class="officer-account-content-table">
                            <thead class="officer-account-content-table-head">
                                <tr class="officer-account-content-table-row">
                                    <th class="officer-account-content-data-fullname">Name</th>
                                    <th class="officer-account-content-data-organization">Organization</th>
                                    <th class="officer-account-content-data-position">Position</th>
                                    <th class="officer-account-content-data-btn-action">Action</th>
                                </tr>
                            </thead>
                            <tbody id="officer-account-content-table-body" class="officer-account-content-table-body">
                                <!-- Table rows will be dynamically populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    const selectOrganization = document.getElementById('officer-account-content-filter-select');
                    const selectColumn = document.getElementById('officer-account-content-filter-select-col');
                    const searchInput = document.getElementById('officer-account-content-search-input');
                    const tableBody = document.getElementById('officer-account-content-table-body');

                    // Store fetched data in memory
                    let allOfficerData = [];

                    // Function to populate the table with filtered data
                    function populateTable(filteredData) {
                        // Clear previous table data
                        tableBody.innerHTML = '';

                        if (filteredData.length > 0) {
                            filteredData.forEach(row => {
                                const organizationName = row.organization_name || 'No Organization';
                                const tableRow = `
                                    <tr class="officer-account-content-table-row">
                                        <td class="officer-account-content-data-fullname">${row.user_firstname} ${row.user_lastname}</td>
                                        <td class="officer-account-content-data-organization">${organizationName}</td>
                                        <td class="officer-account-content-data-position">${row.officer_position}</td>
                                        <td class="officer-account-content-data-btn-action">
                                            <form action="adminUserAccount.php" method="post" class="officer-account-content-data-form">
                                                <input type="hidden" name="user-id" value="${row.user_id}">
                                                <button type="submit" name="pSubmit" class="user-account-profile">PROFILE</button>
                                                <button type="submit" name="eSubmit" class="user-account-edit">EDIT</button>
                                                <button type="submit" name="dSubmit" class="user-account-delete" onclick="confirmDelete(event);">DELETE</button>
                                            </form>
                                        </td>
                                    </tr>
                                `;
                                tableBody.innerHTML += tableRow;
                            });
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="4">No records found.</td></tr>';
                        }
                    }

                    // Function to apply filters and search to the in-memory data
                    function filterData() {
                        const organization = selectOrganization.value.toLowerCase();
                        const column = selectColumn.value.toLowerCase();
                        const search = searchInput.value.toLowerCase();

                        let filteredData = allOfficerData;

                        // Apply organization filter
                        if (organization && organization !== 'all') {
                            filteredData = filteredData.filter(row => row.organization_id.toLowerCase() === organization);
                        }

                        // Apply column filter
                        if (column && column !== 'none') {
                            filteredData = filteredData.filter(row => {
                                if (column === 'name') {
                                    return (
                                        row.user_firstname.toLowerCase().includes(search) ||
                                        row.user_lastname.toLowerCase().includes(search)
                                    );
                                }
                                if (column === 'position') {
                                    return row.officer_position.toLowerCase().includes(search);
                                }
                                return true;
                            });
                        } else {
                            // General search across all columns
                            filteredData = filteredData.filter(row => {
                                return (
                                    row.user_firstname.toLowerCase().includes(search) ||
                                    row.user_lastname.toLowerCase().includes(search) ||
                                    row.organization_name.toLowerCase().includes(search) ||
                                    row.officer_position.toLowerCase().includes(search)
                                );
                            });
                        }

                        // Populate the table with filtered data
                        populateTable(filteredData);
                    }

                    // Function to fetch all data from the backend once
                    async function fetchAllData() {
                        try {
                            const response = await fetch('officerAccountFetch.php');
                            const data = await response.json(); // Assuming the server returns JSON

                            // Store the fetched data in memory
                            allOfficerData = data;

                            // Populate the table with all data initially
                            populateTable(allOfficerData);
                        } catch (error) {
                            console.error('Error fetching data:', error);
                            tableBody.innerHTML = '<tr><td colspan="4">Error fetching data.</td></tr>';
                        }
                    }

                    // Add event listeners for filtering
                    searchInput.addEventListener('input', filterData);
                    selectOrganization.addEventListener('change', filterData);
                    selectColumn.addEventListener('change', filterData);

                    // Fetch data initially on page load
                    window.onload = fetchAllData;

                </script>



            </div>
            
        </div>  
    </div>

    <script>
        function confirmDelete(e) {
            if (confirm("Are you sure you want to Delete?") === false) {
                e.preventDefault(); // Prevent form submission if cancelled
            }
        }
        

    </script>
</body>
</html>