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
                    // Get the necessary DOM elements
                    const selectProgram = document.getElementById('student-account-content-filter-select-program');
                    const selectColumn = document.getElementById('student-account-content-filter-select-col');
                    const searchInput = document.getElementById('student-account-content-search-input');
                    const filterSubmit = document.getElementById('filterSubmit');
                    const tableBody = document.getElementById('student-account-content-table-body');

                    // Function to fetch filtered data from the server
                    async function fetchFilteredData() {
                        const program = selectProgram.value;
                        const column = selectColumn.value;
                        const search = searchInput.value;

                        const params = new URLSearchParams({
                            'program': program,
                            'column': column,
                            'search': search
                        });

                        try {
                            const response = await fetch('studentAccountFetch.php?' + params.toString());
                            const data = await response.json();  // Assuming the server returns JSON

                            // Clear previous table data
                            tableBody.innerHTML = '';

                            if (data.length > 0) {
                                data.forEach(row => {
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
                                tableBody.innerHTML = '<tr><td colspan="6">No records found.</td></tr>';
                            }
                        } catch (error) {
                            console.error('Error fetching data:', error);
                        }
                    }

                    // Event listeners for filtering
                    filterSubmit.addEventListener('click', fetchFilteredData);
                    searchInput.addEventListener('input', fetchFilteredData);
                    selectProgram.addEventListener('change', fetchFilteredData);
                    selectColumn.addEventListener('change', fetchFilteredData);

                    // Initial fetch on page load
                    window.onload = fetchFilteredData;
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