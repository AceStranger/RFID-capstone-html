<?php
session_start();
include "dbh.php";
if (@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
$user_ID = @$_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments</title>
    <?php include "titleIcon.php" ;?>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminDepartment.css">
    <script src="js/adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once "adminSidebar.php"; ?>
        <div class="main-content">
            <div class="department-content">
                <div class="department-content-top-part">
                    <h1 class="department-content-head-text">DEPARTMENTS</h1>
                    <div class="department-content-btn">
                        <button type="button" id="add-new-department-btn-show" class="add-new-department-btn-show">+ Add New Department</button>
                    </div>
                </div>
                <!-- Add New Department Form -->
                <div class="add-department-container" style="display: none;">
                    <h2>Add New Department</h2>
                    <form id="add-department-form" class="add-department-form">
                        <div class="form-group">
                            <label for="department-name">Department Name</label>
                            <input type="text" name="department_name" id="department-name" required placeholder="Enter department name">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="add-department-btn">Add Department</button>
                            <button type="button" id="close-add-department-btn" class="close-add-department-btn">X</button>
                        </div>
                    </form>
                </div>
                <script>
                    // Toggle display of the add department form
                    document.getElementById('add-new-department-btn-show').addEventListener('click', function () {
                        const addDepartmentContainer = document.querySelector('.add-department-container');
                        addDepartmentContainer.style.display ='block';
                    });
                    // Close Add Department Form
                    document.getElementById('close-add-department-btn').addEventListener('click', function () {
                        const addDepartmentContainer = document.querySelector('.add-department-container');
                        addDepartmentContainer.style.display = 'none';
                    });

                // Handle form submission via AJAX
                document.getElementById('add-department-form').addEventListener('submit', function (event) {
                    event.preventDefault(); // Prevent page reload

                    const formData = new FormData(event.target);

                    fetch('adminDepartmentInsertHandler.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Department added successfully!');
                                document.querySelector('.add-department-container').style.display = 'none';
                                location.reload(); // Reload the page to fetch updated data
                            } else {
                                alert('Failed to add department: ' + data.message);
                            }
                        })
                        .catch(error => {
                            alert('An error occurred: ' + error.message);
                        });
                });

                </script>
                <div class="department-content-department-content">
                    <!-- Filter Section -->
                    <div class="department-content-top-part-filter">
                        <form action="" method="get" enctype="multipart/form-data" class="department-content-form" id="department-content-form">
                            <div class="department-content-search-input-container">
                                <label for="department-content-search-input">Filter By Department Name:</label>
                                <input type="text" name="department-content-search-input" id="department-content-search-input" placeholder="Search..." value="<?php echo htmlspecialchars(@$_GET['department-content-search-input']); ?>">

                            </div>
                        </form>
                    </div>
                    
                    <!-- Main Content Section -->
                    <div class="department-content-main-content-container">
                        <div class="department-content-table-content-container">
                            <table class="department-content-table-content">
                                <thead class="department-content-table-head">
                                    <tr class="department-content-table-row">
                                        <th class="department-content-data-department-id">ID</th>
                                        <th class="department-content-data-department-name">Department Name</th>
                                        <th class="department-content-data-btn-action">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="department-content-table-body">
                                    <?php
                                    // Query to fetch department data
                                    $departmentQuery = "SELECT 
                                                        department_id, 
                                                        department_name 
                                                        FROM department 
                                                        ORDER BY department_id ASC";

                                    $result = mysqli_query($conn, $departmentQuery);

                                    // Check if there are results
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '
                                                <tr class="department-content-table-row">
                                                    <td class="department-content-data-department-name">' . htmlspecialchars($row['department_name']) . '</td>
                                                    <td class="department-content-data-btn-action">
                                                        <form action="adminDepartmentDataHandler.php" method="post" class="department-content-data-form">
                                                            <input type="hidden" name="department-id" value="' . $row['department_id'] . '">
                                                            <button type="submit" name="eSubmit" class="user-edit">EDIT</button>
                                                            <button type="submit" name="dSubmit" class="user-delete">DELETE</button>
                                                        </form>
                                                    </td>
                                                </tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="3">No records found.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- Edit Department Modal -->
                            <div class="edit-department-container" style="display: none;">
                                <h2>Edit Department</h2>
                                <form id="edit-department-form" class="edit-department-form">
                                    <input type="hidden" name="department_id" id="edit-department-id">
                                    <div class="form-group">
                                        <label for="edit-department-name">Department Name</label>
                                        <input type="text" name="department_name" id="edit-department-name" required placeholder="Enter department name">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="edit-department-btn">Save Changes</button>
                                        <button type="button" id="close-edit-department-btn" class="close-edit-department-btn">Cancel</button>
                                    </div>
                                </form>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    // Fetch all departments on page load
                                    fetchDepartments();

                                    // Handle search/filter
                                    const filterInput = document.getElementById('department-content-search-input'); // Ensure there's an input element for filtering
                                    filterInput.addEventListener('input', function () {
                                        const searchValue = filterInput.value.trim().toLowerCase();
                                        filterDepartments(searchValue);
                                    });
                                });

                                // Store all department data
                                let allDepartments = [];

                                // Fetch departments from the server
                                function fetchDepartments() {
                                    fetch('fetchDepartments.php')
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                allDepartments = data.departments; // Save all department data
                                                displayDepartments(allDepartments); // Display all departments
                                            } else {
                                                alert('Failed to load departments.');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error fetching departments:', error);
                                        });
                                }

                                // Display departments in the table
                                function displayDepartments(departments) {
                                    const tbody = document.querySelector('.department-content-table-body');
                                    tbody.innerHTML = ''; // Clear the current rows

                                    if (departments.length > 0) {
                                        departments.forEach(department => {
                                            const tr = document.createElement('tr');
                                            tr.className = 'department-content-table-row';
                                            tr.innerHTML = `
                                                <td class="department-content-data-department-id">${department.department_id}</td>
                                                <td class="department-content-data-department-name">${department.department_name}</td>
                                                <td class="department-content-data-btn-action">
                                                    <button type="button" class="user-edit" data-id="${department.department_id}">EDIT</button>
                                                    <button type="button" class="user-delete" data-id="${department.department_id}">DELETE</button>
                                                </td>
                                            `;
                                            tbody.appendChild(tr);
                                        });

                                        // Add event listeners for edit and delete buttons
                                        document.querySelectorAll('.user-edit').forEach(button => {
                                            button.addEventListener('click', handleEdit);
                                        });
                                        document.querySelectorAll('.user-delete').forEach(button => {
                                            button.addEventListener('click', handleDelete);
                                        });
                                    } else {
                                        tbody.innerHTML = '<tr><td colspan="2">No records found.</td></tr>';
                                    }
                                }

                                // Filter departments based on the search input
                                function filterDepartments(searchValue) {
                                    const filteredDepartments = allDepartments.filter(department =>
                                        department.department_name.toLowerCase().includes(searchValue)
                                    );
                                    displayDepartments(filteredDepartments);
                                }

                                // Handle editing a department
                                function handleEdit(event) {
                                    const departmentId = event.target.getAttribute('data-id');
                                    const departmentName = event.target.closest('tr').querySelector('.department-content-data-department-name').textContent.trim();

                                    // Show edit form with current data
                                    document.getElementById('edit-department-id').value = departmentId;
                                    document.getElementById('edit-department-name').value = departmentName;
                                    document.querySelector('.edit-department-container').style.display = 'block';
                                }

                                // Handle closing the edit form
                                document.getElementById('close-edit-department-btn').addEventListener('click', function () {
                                    document.querySelector('.edit-department-container').style.display = 'none';
                                });

                                // Handle edit department form submission
                                document.getElementById('edit-department-form').addEventListener('submit', function (event) {
                                    event.preventDefault();

                                    const formData = new FormData(this);

                                    fetch('adminDepartmentUpdateHandler.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert('Department updated successfully!');
                                            fetchDepartments(); // Re-fetch and display updated list
                                            document.querySelector('.edit-department-container').style.display = 'none';
                                        } else {
                                            alert('Failed to update department: ' + data.message);
                                        }
                                    })
                                    .catch(error => {
                                        alert('An error occurred: ' + error.message);
                                    });
                                });

                                // Handle deleting a department
                                function handleDelete(event) {
                                    const departmentId = event.target.getAttribute('data-id');
                                    
                                    if (confirm('Are you sure you want to delete this department?')) {
                                        fetch('adminDepartmentDeleteHandler.php', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            body: JSON.stringify({ department_id: departmentId })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                alert('Department deleted successfully!');
                                                fetchDepartments(); // Re-fetch and display updated list
                                            } else {
                                                alert('Failed to delete department: ' + data.message);
                                            }
                                        })
                                        .catch(error => {
                                            alert('An error occurred while deleting the department.');
                                        });
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</body>
</html>
