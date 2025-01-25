<?php
session_start();
include "dbh.php";
if(@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
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
    <title>Programs</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminProgram.css">
    <script src="js/adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once "adminSidebar.php"; ?>
        <div class="main-content">
            <div class="program-content">
                <div class="program-content-top-part">
                    <h1 class="program-content-head-text">Programs</h1>
                    <div class="program-content-btn">
                        <button type="button" id="add-new-program-btn-show" class="add-new-program-btn-show">+ Add New Program</button>
                    </div>
                </div>

                <!-- Add New Program Form -->
                <div class="add-program-container" style="display: none;">
                    <h2>Add New Program</h2>
                    <form id="add-program-form" class="add-program-form">
                        <div class="form-group">
                            <label for="program-name">Program Name</label>
                            <input type="text" name="program_name" id="program-name" required placeholder="Enter program name">
                        </div>
                        <div class="form-group">
                            <label for="department-name">Department Name</label>
                            <input type="text" name="department_name" id="department-name" required placeholder="Enter department name">
                        </div>
                        <div class="form-group">
                            <label for="program-level">Program Level</label>
                            <input type="text" name="program_level" id="program-level" required placeholder="Enter program level">
                        </div>
                        <div class="form-group">
                            <label for="year-grade-level">Year's/Grade's Level</label>
                            <input type="text" name="year_grade_level" id="year-grade-level" required placeholder="Enter year/grade level">
                        </div>
                        <div class="form-group">
                            <label for="section">Section</label>
                            <input type="text" name="section" id="section" required placeholder="Enter sections">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="add-program-btn">Add Program</button>
                            <button type="button" id="close-add-program-btn" class="close-add-program-btn">X</button>
                        </div>
                    </form>
                    <div id="add-program-message"></div>
                </div>

                <script>
                    // Show Add Program Form
                    document.getElementById('add-new-program-btn-show').addEventListener('click', function () {
                        const addProgramContainer = document.querySelector('.add-program-container');
                        addProgramContainer.style.display = 'block';
                    });

                    // Close Add Program Form
                    document.getElementById('close-add-program-btn').addEventListener('click', function () {
                        const addProgramContainer = document.querySelector('.add-program-container');
                        addProgramContainer.style.display = 'none';
                    });

                    // Handle Add Program Form Submission
                    document.getElementById('add-program-form').addEventListener('submit', async function (event) {
                        event.preventDefault(); // Prevent page reload

                        const formData = new FormData(this);

                        try {
                            const response = await fetch('adminProgramInsertHandler.php', {
                                method: 'POST',
                                body: formData
                            });

                            const data = await response.json();

                            if (data.success) {
                                document.getElementById('add-program-message').textContent = 'Program added successfully!';
                                setTimeout(() => location.reload(), 2000); // Reload the page after 2 seconds
                            } else {
                                document.getElementById('add-program-message').textContent = 'Failed to add program: ' + data.message;
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            document.getElementById('add-program-message').textContent = 'An error occurred. Please try again.';
                        }
                    });

                </script>
                <div class="program-content-program-content">
                    <div class="program-content-top-part-filter">
                        <form action="" method="get" enctype="multipart/form-data" class="program-content-form" id="program-content-form">
                            <div class="program-content-search-input-container">
                                <select name="program-content-filter-select-col" id="program-content-filter-select-col" class="filter-select">
                                    <option value="" disabled selected>Filter By</option>
                                    <option value="program_name">Program Name</option>
                                    <option value="department_name">Department Name</option>
                                </select>
                                <input type="text" name="program-content-search-input" id="program-content-search-input" placeholder="Search...">
                                <button type="submit" id="filterSubmit" class="filter-button">Search</button>
                            </div>
                        </form>
                    </div>

                    <div class="program-content-main-content-container">
                        <div class="program-content-table-content-container">
                            <table class="program-content-table-content">
                                <thead class="program-content-table-head">
                                    <tr class="program-content-table-row">
                                        <th>Department</th>
                                        <th>Program / Course</th>
                                        <th>Program Level</th>
                                        <th>Year's/Grade's Level</th>
                                        <th>Section's</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="program-content-table-body">
                                    <?php
                                        // Query to fetch program data along with department name
                                        $programQuery = "SELECT 
                                                        program.program_id, 
                                                        program.program_name, 
                                                        program.program_level, 
                                                        program.program_year_grade_level, 
                                                        program.section, 
                                                        CASE 
                                                            WHEN program.department_id = 0 THEN NULL
                                                            ELSE department.department_name
                                                        END AS department_name
                                                        FROM program 
                                                        LEFT JOIN department 
                                                        ON program.department_id = department.department_id
                                                        ORDER BY program.department_id ASC";
                                            

                                        $result = mysqli_query($conn, $programQuery);

                                        // Check if there are results
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo '
                                                    <tr class="program-content-table-row">
                                                        <form action="adminProgramDataHandler.php" method="post">
                                                            <input type="hidden" name="program-id" value="' . $row['program_id'] . '">
                                                            <td class="program-content-data-department-name">' . htmlspecialchars($row['department_name']) . '</td>
                                                            <td class="program-content-data-program-name">' . htmlspecialchars($row['program_name']) . '</td>
                                                            <td class="program-content-data-program-level">' . htmlspecialchars($row['program_level']) . '</td>
                                                            <td class="program-content-data-program-year-grade-level">' . htmlspecialchars($row['program_year_grade_level']) . '</td>
                                                            <td class="program-content-data-section">' . htmlspecialchars($row['section']) . '</td>
                                                            <td class="program-content-data-btn">
                                                                <button type="submit" name="eSubmit" class="user-edit">EDIT</button>
                                                                <button type="submit" name="dSubmit" class="user-delete">DELETE</button>
                                                            </td>
                                                        </form>
                                                    </tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="7">No records found.</td></tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <!-- Edit Program Modal -->
<div class="edit-program-container" style="display: none;">
    <h2>Edit Program</h2>
    <form id="edit-program-form" class="edit-program-form">
        <input type="hidden" name="program_id" id="edit-program-id">
        <div class="form-group">
            <label for="edit-program-name">Program Name</label>
            <input type="text" name="program_name" id="edit-program-name" required>
        </div>
        <div class="form-group">
            <label for="edit-department-name">Department Name</label>
            <input type="text" name="department_name" id="edit-department-name" required>
        </div>
        <div class="form-group">
            <label for="edit-program-level">Program Level</label>
            <input type="text" name="program_level" id="edit-program-level" required>
        </div>
        <div class="form-group">
            <label for="edit-year-grade-level">Year's/Grade's Level</label>
            <textarea name="year_grade_level" id="edit-year-grade-level" required></textarea>
        </div>
        <div class="form-group">
            <label for="edit-section">Section</label>
            <textarea name="section" id="edit-section" required></textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="edit-program-btn">Save Changes</button>
            <button type="button" id="close-edit-program-btn" class="close-edit-program-btn">Cancel</button>
        </div>
    </form>
    <div id="edit-program-message"></div>
</div>

<script>
    // Show Edit Program Form
    document.querySelectorAll('.user-edit').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const row = this.closest('tr');
            document.querySelector('.edit-program-container').style.display = 'flex';
            document.getElementById('edit-program-id').value = row.querySelector('[name="program-id"]').value;
            document.getElementById('edit-program-name').value = row.querySelector('.program-content-data-program-name').textContent.trim();
            document.getElementById('edit-department-name').value = row.querySelector('.program-content-data-department-name').textContent.trim();
            document.getElementById('edit-program-level').value = row.querySelector('.program-content-data-program-level').textContent.trim();
            document.getElementById('edit-year-grade-level').value = row.querySelector('.program-content-data-program-year-grade-level').textContent.trim();
            document.getElementById('edit-section').value = row.querySelector('.program-content-data-section').textContent.trim();
        });
    });

    // Close Edit Program Form
    document.getElementById('close-edit-program-btn').addEventListener('click', function () {
        document.querySelector('.edit-program-container').style.display = 'none';
    });

    // Handle Edit Program Form Submission
    document.getElementById('edit-program-form').addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(this);

        try {
            const response = await fetch('adminProgramUpdateHandler.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('edit-program-message').textContent = 'Program updated successfully!';
                setTimeout(() => location.reload(), 2000);
            } else {
                document.getElementById('edit-program-message').textContent = 'Failed to update program: ' + data.message;
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('edit-program-message').textContent = 'An error occurred. Please try again.';
        }
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
