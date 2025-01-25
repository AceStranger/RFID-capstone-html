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
                                <button type="button" id="filterSubmit">Search</button>
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
                    const filterSubmit = document.getElementById('filterSubmit');
                    const tableBody = document.getElementById('officer-account-content-table-body');

                    // Function to fetch data based on filter
                    async function fetchFilteredData() {
                        const organization = selectOrganization.value;
                        const column = selectColumn.value;
                        const search = searchInput.value;

                        const params = new URLSearchParams({
                            'organization': organization,
                            'column': column,
                            'search': search
                        });

                        try {
                            const response = await fetch('officerAccountFetch.php?' + params.toString());
                            const data = await response.json(); // Assuming the server returns JSON

                            // Clear previous table data
                            tableBody.innerHTML = '';

                            if (data.length > 0) {
                                data.forEach(row => {
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
                        } catch (error) {
                            console.error('Error fetching data:', error);
                        }
                    }

                    // Event listeners for filtering
                    filterSubmit.addEventListener('click', fetchFilteredData);

                    // Trigger search on input change
                    searchInput.addEventListener('input', fetchFilteredData);
                    selectOrganization.addEventListener('change', fetchFilteredData);
                    selectColumn.addEventListener('change', fetchFilteredData);

                    // Initial load on page load
                    window.onload = fetchFilteredData;
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
        // Buttons for opening and closing the form
        const btnOpen = document.querySelector('.btn-add-new-officer');
        
        
        // Form container and select user container
        const formContainer = document.querySelector('.add-new-officer-form-content-container');

        const fName = document.getElementById('fname');
        const mName = document.getElementById('mname');
        const lName = document.getElementById('lname');
        const organization = document.getElementById('organization');

        function clearInput(FN, MN, LN, Org) {
            FN.value = '';
            MN.value = '';
            LN.value = '';
            Org.value = ''; 
        }

        btnOpen.addEventListener('click', function () {
            formContainer.style.display = 'flex';
            clearInput(fName, mName, lName, organization);
        });


    </script>
</body>
</html>