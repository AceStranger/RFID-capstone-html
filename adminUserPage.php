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
    <title>Document</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminUser.css">
    <script src="js\adminSidebar.js"></script>
    <script src="js\adminUser.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="user-content">
                <div class="user-content-top-part">
                    <h1 class="user-content-head-text">USER</h1>
                        <div class="user-content-btn"><button type="button" class="btn-add-new-user" onclick="redirectToCreateUserPage()">Add New</button>

                    </div>
                </div>
                
                <div class="user-content-users-account-content">
                    <form id="users-account-content-form" class="users-account-content-form" enctype="multipart/form-data">
                        <div class="users-account-content-top-part">
                            <div class="users-account-content-search-input-container">
                                <div class="filter-label">
                                    <label for="users-account-content-filter-select-role">Filter By Role:</label>
                                </div>
                                <select name="users-account-content-filter-select-role" id="users-account-content-filter-select-role">
                                    <option value="all">All</option>
                                    <option value="none">None</option>
                                    <option value="student">Student</option>
                                    <option value="officer">Officer</option>
                                    <option value="dean">Dean</option>
                                </select>

                                <div class="filter-label">
                                    <label for="users-account-content-filter-select-col">Filter By:</label>
                                </div>
                                <select name="users-account-content-filter-select-col" id="users-account-content-filter-select-col">
                                    <option value="none">None</option>
                                    <option value="School ID">School ID</option>
                                    <option value="First Name">First Name</option>
                                    <option value="Middle Name">Middle Name</option>
                                    <option value="Last Name">Last Name</option>
                                    <option value="Suffix Name">Suffix Name</option>
                                    <option value="Phone Number">Phone Number</option>
                                </select>

                                <div class="search-bar">
                                    <input type="text" name="users-account-content-search-input" id="users-account-content-search-input" autocomplete="off" placeholder="Search...">
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="users-account-content-main-content">
                        <div class="users-account-content-table-content">
                            <table class="users-account-content-table">
                                <thead class="users-account-content-table-head">
                                    <tr class="users-account-content-table-row">
                                        <th class="users-account-content-data-user-id">User ID</th>
                                        <th class="users-account-content-data-school-id">School ID</th>
                                        <th class="users-account-content-data-fname">First Name</th>
                                        <th class="users-account-content-data-mname">Middle Name</th>
                                        <th class="users-account-content-data-lname">Last Name</th>
                                        <th class="users-account-content-data-suffix">Suffix Name</th>
                                        <th class="users-account-content-data-pnumber">Phone Number</th>
                                        <th class="users-account-content-data-role">Role</th>
                                        <th class="users-account-content-data-btn-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="users-account-content-table-body" class="users-account-content-table-body">
                                    <!-- Table rows will be dynamically populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <script>
                    // Fetch data based on filter
                    const selectRole = document.getElementById('users-account-content-filter-select-role');
                    const selectCol = document.getElementById('users-account-content-filter-select-col');
                    const searchInput = document.getElementById('users-account-content-search-input');
                    const tableBody = document.getElementById('users-account-content-table-body');

                    // Variables to store fetched data
                    let allUserData = [];

                    // Fetch all data once on page load
                    async function fetchAllData() {
                        try {
                            const response = await fetch('userAccountFetch.php'); // Assuming this endpoint returns all user data
                            allUserData = await response.json();
                            applyFilters(); // Apply filters to the loaded data initially
                        } catch (error) {
                            console.error('Error fetching all data:', error);
                        }
                    }
                    // Mapping between dropdown values and allUserData keys
                    const columnKeyMap = {
                        "School ID": "user_school_id",
                        "First Name": "user_firstname",
                        "Middle Name": "user_middlename",
                        "Last Name": "user_lastname",
                        "Suffix Name": "user_suffixname",
                        "Phone Number": "user_phone_number",
                    };

                    function applyFilters() {
                        const role = selectRole.value.toLowerCase();
                        const col = selectCol.value;
                        const search = searchInput.value.trim().toLowerCase();

                        // Filter data
                        const filteredData = allUserData.filter(row => {
                            const matchesRole = role === 'all' || row.user_role.toLowerCase() === role;

                            let matchesSearch = true;
                            if (col !== 'none' && search) {
                                // Map dropdown value to actual key
                                const key = columnKeyMap[col];
                                const colValue = row[key] || '';
                                matchesSearch = colValue.toLowerCase().includes(search);
                            } else if (search) {
                                // Search across all fields if "None" is selected in the dropdown
                                matchesSearch = Object.values(row).some(value =>
                                    value && value.toString().toLowerCase().includes(search)
                                );
                            }

                            return matchesRole && matchesSearch;
                        });

                        // Render the filtered data
                        renderTableData(filteredData);
                    }


                    // Function to render table data
                    function renderTableData(data) {
                        tableBody.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach(row => {
                                const tableRow = `
                                    <tr class="users-account-content-table-row">
                                        <td class="users-account-content-data-user-id">${row.user_id}</td>
                                        <td class="users-account-content-data-school-id">${row.user_school_id}</td>
                                        <td class="users-account-content-data-fname">${row.user_firstname}</td>
                                        <td class="users-account-content-data-mname">${row.user_middlename}</td>
                                        <td class="users-account-content-data-lname">${row.user_lastname}</td>
                                        <td class="users-account-content-data-suffix">${row.user_suffixname}</td>
                                        <td class="users-account-content-data-pnumber">${row.user_phone_number}</td>
                                        <td class="users-account-content-data-role">${row.user_role}</td>
                                        <td class="users-account-content-data-btn-actions">
                                            <form action="adminUserAccount.php" method="post" class="users-account-content-data-form">
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
                            tableBody.innerHTML = '<tr><td colspan="8">No records found.</td></tr>';
                        }
                    }

                    // Debounce function to limit rapid calls
                    function debounce(func, delay) {
                        let timer;
                        return function (...args) {
                            clearTimeout(timer);
                            timer = setTimeout(() => func.apply(this, args), delay);
                        };
                    }

                    // Debounced applyFilters for search input
                    const debouncedApplyFilters = debounce(applyFilters, 300);

                    // Event listeners for filters
                    searchInput.addEventListener('input', debouncedApplyFilters);
                    selectRole.addEventListener('change', applyFilters);
                    selectCol.addEventListener('change', applyFilters);

                    // Initial data load on page load
                    window.onload = fetchAllData;

                </script>
            </div>
        </div>
    </div>
    <script>
        function redirectToCreateUserPage() {
            window.location.href = 'adminCreateUserPage.php';
        }

    </script>
</body>
</html>