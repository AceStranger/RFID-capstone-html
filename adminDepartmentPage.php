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
                                <select name="department-content-filter-select-col" id="department-content-filter-select-col"  class="filter-select">
                                    <option value="" disabled selected>Filter By</option>
                                    <option value="program_name">Program Name</option>
                                    <option value="department_name">Department Name</option>
                                </select>
                                
                                <input type="text" name="department-content-search-input" id="department-content-search-input" placeholder="Search..." value="<?php echo htmlspecialchars(@$_GET['department-content-search-input']); ?>">
                                <input type="submit" name="filterSubmit" id="filterSubmit" class="filter-button" value="Search">
                            </div>
                        </form>
                    </div>
                    
                    <!-- Main Content Section -->
                    <div class="department-content-main-content-container">
                        <div class="department-content-table-content-container">
                            <table class="department-content-table-content">
                                <thead class="department-content-table-head">
                                    <tr class="department-content-table-row">
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
                                                    <form action="adminDepartmentDataHandler.php" method="post">
                                                        <input type="hidden" name="department-id" value="' . $row['department_id'] . '">
                                                        <td class="department-content-data-department-name">' . htmlspecialchars($row['department_name']) . '</td>
                                                        <td class="department-content-data-btn-action">
                                                            <button type="submit" name="eSubmit" class="user-edit">EDIT</button>
                                                            <button type="submit" name="dSubmit" class="user-delete">DELETE</button>
                                                        </td>
                                                    </form>
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
    // Show Edit Department Form with current data
document.querySelectorAll('.user-edit').forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault();
        
        const row = this.closest('tr');
        const departmentId = row.querySelector('input[name="department-id"]').value;
        const departmentName = row.querySelector('.department-content-data-department-name').textContent.trim();

        document.getElementById('edit-department-id').value = departmentId;
        document.getElementById('edit-department-name').value = departmentName;

        document.querySelector('.edit-department-container').style.display = 'block';
    });
});

// Close Edit Department Form
document.getElementById('close-edit-department-btn').addEventListener('click', function () {
    document.querySelector('.edit-department-container').style.display = 'none';
});

// Handle Edit Department Form Submission
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
                location.reload(); // Reload the page to reflect changes
            } else {
                alert('Failed to update department: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred: ' + error.message);
        });
});

</script>
                        </div>
                    </div>

                </div>
            </div>
        </div>  
    </div>
</body>
</html>
