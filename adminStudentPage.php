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
    <title>Student</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminStudent.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="student-content">
                <div class="student-content-top-part">
                    <h1 class="student-content-head-text">STUDENT</h1>
                    <div class="student-content-btn">
                    </div>
                </div>
                <div class="student-content-student-account-content">
                    <form id="student-account-content-form" class="student-account-content-form" enctype="multipart/form-data">
                        <div class="student-account-content-top-part">
                            <div class="student-account-content-search-input-container">
                                <label for="student-account-content-filter-select-program">Filter By Program:</label>
                                <select name="student-account-content-filter-select-program" id="student-account-content-filter-select-program">
                                    <option value="all" selected>All</option>
                                    <option value="None">None</option>
                                    <?php 
                                        $programQuery = "SELECT * FROM program";
                                        $programQueryResult = mysqli_query($conn, $programQuery);
                                        if (mysqli_num_rows($programQueryResult) > 0) {
                                            while ($row = mysqli_fetch_assoc($programQueryResult)) {
                                                echo '<option value="'. $row['program_id'] .'">'. $row['program_name'] .'</option>';
                                            }
                                        }
                                    ?>
                                </select>

                                <label for="student-account-content-filter-select-col">Filter By:</label>
                                <select name="student-account-content-filter-select-col" id="student-account-content-filter-select-col">
                                    <option value="none">None</option>
                                    <option value="School ID">School ID</option>
                                    <option value="Full Name">Name</option>
                                    <option value="year/gradeLevel">Year/Grade Level</option>
                                    <option value="section">Section</option>
                                </select>

                                <input type="text" id="student-account-content-search-input" placeholder="Search...">
                                <button type="button" id="filterSubmit">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="student-account-content-main-content">
                        <table class="student-account-content-table">
                            <thead class="student-account-content-table-head">
                                <tr class="student-account-content-table-row">
                                    <th class="student-account-content-data-school-id">School ID</th>
                                    <th class="student-account-content-data-fullname">Name</th>
                                    <th class="student-account-content-data-program">Program</th>
                                    <th class="student-account-content-data-year">Year</th>
                                    <th class="student-account-content-data-section">Section</th>
                                    <th class="student-account-content-data-btn-action">Action</th>
                                </tr>
                            </thead>
                            <tbody id="student-account-content-table-body" class="student-account-content-table-body">
                                <!-- Table rows will be populated here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    // DOM Elements
                    const selectProgram = document.getElementById('student-account-content-filter-select-program');
                    const selectColumn = document.getElementById('student-account-content-filter-select-col');
                    const searchInput = document.getElementById('student-account-content-search-input');
                    const filterSubmit = document.getElementById('filterSubmit');
                    const tableBody = document.getElementById('student-account-content-table-body');

                    // Store fetched data in memory
                    let allStudentData = [];

                    // Function to populate the table with filtered data
                    function populateTable(filteredData) {
                        // Clear previous table data
                        tableBody.innerHTML = '';

                        if (filteredData.length > 0) {
                            filteredData.forEach(row => {
                                const programName = row.program_name || 'No program';
                                const year = row['year/grade_level'] || 'N/A';
                                const section = row.section || 'N/A';
                                const fullName = `${row.user_firstname} ${row.user_middlename} ${row.user_lastname} ${row.user_suffixname}`;

                                const tableRow = `
                                    <tr class="student-account-content-table-row">
                                        <td class="student-account-content-data-school-id">${row.user_school_id}</td>
                                        <td class="student-account-content-data-fullname">${fullName}</td>
                                        <td class="student-account-content-data-program">${programName}</td>
                                        <td class="student-account-content-data-year">${year}</td>
                                        <td class="student-account-content-data-section">${section}</td>
                                        <td class="student-account-content-data-btn-action">
                                            <form action="adminUserAccount.php" method="post" class="student-account-content-data-form">
                                                <input type="hidden" name="user-id" value="${row.user_id}">
                                                <input type="hidden" name="student-id" value="${row.student_id}">
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
                            tableBody.innerHTML = '<tr><td colspan="6">No records found.</td></tr>';
                        }
                    }

                    // Function to apply filters and search to in-memory data
                    function filterData() {
                        const program = selectProgram.value.toLowerCase();
                        const column = selectColumn.value.toLowerCase();
                        const search = searchInput.value.toLowerCase();

                        let filteredData = allStudentData;

                        // Filter by program
                        if (program && program !== 'all') {
                            filteredData = filteredData.filter(row => row.program_id.toLowerCase() === program);
                        }

                        // Filter by column
                        if (column && column !== 'none') {
                            filteredData = filteredData.filter(row => {
                                if (column === 'school id') {
                                    return row.user_school_id.toLowerCase().includes(search);
                                }
                                if (column === 'full name') {
                                    const fullName = [  
                                        row.user_firstname || '',
                                        row.user_middlename || '',
                                        row.user_lastname || '',
                                        row.user_suffixname || ''
                                    ]
                                    .filter(Boolean)
                                    .join(' ')
                                    .toLowerCase();
                                    
                                    return fullName.includes(search);
                                }
                                if (column === 'year/gradelevel') {
                                    return row['year/grade_level'].toLowerCase().includes(search);
                                }
                                if (column === 'section') {
                                    return row.section.toLowerCase().includes(search);
                                }
                                return true;
                            });
                        } else {
                            // General search across all columns
                            filteredData = filteredData.filter(row => {
                                return (
                                    row.user_school_id.toLowerCase().includes(search) ||
                                    row.user_firstname.toLowerCase().includes(search) ||
                                    row.user_middlename.toLowerCase().includes(search) ||
                                    row.user_lastname.toLowerCase().includes(search) ||
                                    row.user_suffixname.toLowerCase().includes(search) ||
                                    row.program_name.toLowerCase().includes(search) ||
                                    row['year/grade_level'].toLowerCase().includes(search) ||
                                    row.section.toLowerCase().includes(search)
                                );
                            });
                        }

                        // Populate the table with filtered data
                        populateTable(filteredData);
                    }

                    // Function to fetch all data from the backend once
                    async function fetchAllData() {
                        try {
                            const response = await fetch('studentAccountFetch.php');
                            const data = await response.json();

                            // Store the fetched data in memory
                            allStudentData = data;

                            // Populate the table with all data initially
                            populateTable(allStudentData);
                        } catch (error) {
                            console.error('Error fetching data:', error);
                            tableBody.innerHTML = '<tr><td colspan="6">Error fetching data.</td></tr>';
                        }
                    }

                    // Debounce function to limit search input calls
                    function debounce(func, delay) {
                        let timeout;
                        return function (...args) {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => func.apply(this, args), delay);
                        };
                    }

                    // Debounced version of filterData
                    const debouncedFilterData = debounce(filterData, 300);

                    // Event listeners for filtering
                    searchInput.addEventListener('input', debouncedFilterData);
                    selectProgram.addEventListener('change', filterData);
                    selectColumn.addEventListener('change', filterData);
                    filterSubmit.addEventListener('click', filterData);

                    // Initial fetch on page load
                    window.onload = fetchAllData;

                </script>


            </div>
            
        </div>  
    </div>

    <script>
    // Function to confirm deletion
    function confirmDelete(e) {
        if (confirm("Are you sure you want to Delete?") === false) {
            e.preventDefault(); // Prevent form submission if cancelled
        }
    }


</script>

</body>
</html>