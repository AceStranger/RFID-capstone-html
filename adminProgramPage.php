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


// Retrieve department data
$departmentQuery = "SELECT department_id, department_name FROM department";
$departmentResult = mysqli_query($conn, $departmentQuery);

if (mysqli_num_rows($departmentResult) > 0) {
    $departmentRow = mysqli_fetch_assoc($departmentResult);
} else {
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs</title>
    <?php include "titleIcon.php" ;?>
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
                            <select name="department" id="department-name" required>
                                <option value="" disabled selected>Select Department</option>
                                <?php
                                // Retrieve department data
                                $departmentQuery = "SELECT department_id, department_name FROM department";
                                $departmentResult = mysqli_query($conn, $departmentQuery);

                                if (mysqli_num_rows($departmentResult) > 0) {
                                    while ($departmentRow = mysqli_fetch_assoc($departmentResult)) {
                                        echo "<option value='" . htmlspecialchars($departmentRow['department_id']) . "'>" . htmlspecialchars($departmentRow['department_name']) . "</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>" . "The Department is empty." . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="program_level">Program Level</label>
                            <select name="program_level" id="program_level" placeholder="Enter program level" required>
                                <option value="" disabled>Select Program Level</option>
                                <option value="Primary">Primary</option>
                                <option value="Secondary">Secondary</option>
                                <option value="College">College</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="year_grade_level">Year's/Grade's Level</label>
                            <textarea name="year_grade_level" id="year_grade_level" required placeholder="Enter year/grade level, Separate them by comma `,`. (E.g. `1st YEAR, 2nd YEAR`)"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="section">Section</label>
                            <textarea name="section" id="section" required placeholder="Enter sections, Separate them by comma `,`. (E.g. `1st YEAR, 2nd YEAR`)"></textarea>
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
                        <div class="program-content-search-input-container">
                            <select name="program-content-filter-select-col" id="program-content-filter-select-col" class="filter-select">
                                <option value="" disabled >Filter By</option>
                                <option value="program_name" selected>Program Name</option>
                                <option value="department_name">Department Name</option>
                            </select>
                            <input type="text" 
                            name="program-content-search-input" 
                            id="program-content-search-input" 
                            placeholder="Search..."
                            value="">
                        </div>
                    </div>

                    <div class="program-content-main-content-container">
                        <div class="program-content-table-content-container">
                            <table class="program-content-table-content">
                                <thead class="program-content-table-head">
                                    <tr class="program-content-table-row">
                                        <th>ID</th>
                                        <th>Department</th>
                                        <th>Program / Course</th>
                                        <th>Program Level</th>
                                        <th>Year's/Grade's Level</th>
                                        <th>Section's</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="program-content-table-body">
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
                                        <select name="department" id="edit-department-name" required>
                                            <option value="" disabled selected>Select Department</option>
                                            <?php
                                            // Retrieve department data
                                            $departmentQuery = "SELECT department_id, department_name FROM department";
                                            $departmentResult = mysqli_query($conn, $departmentQuery);

                                            if (mysqli_num_rows($departmentResult) > 0) {
                                                while ($departmentRow = mysqli_fetch_assoc($departmentResult)) {
                                                    echo "<option value='" . htmlspecialchars($departmentRow['department_id']) . "'>" . htmlspecialchars($departmentRow['department_name']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>" . "The Department is empty." . "</option>";
                                            }
                                            ?>
                                        </select>
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
                                // Function to fetch and display program data
                                async function fetchProgramData(filterColumn = document.getElementById('program-content-filter-select-col').value, searchInput = '') {
                                    try {
                                        const response = await fetch('filterPrograms.php', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            body: JSON.stringify({ filterColumn, searchInput })
                                        });
                                        const data = await response.json();

                                        const tbody = document.querySelector('.program-content-table-body');
                                        tbody.innerHTML = ''; // Clear existing rows
                                        if (data.length > 0) {
                                            data.forEach(row => {
                                                const tr = document.createElement('tr');
                                                tr.className = 'program-content-table-row';
                                                tr.setAttribute('data-program-id', row.program_id);

                                                tr.innerHTML = `
                                                    <td class="program-content-data-program-id">${row.program_id || ''}</td>
                                                    <td class="program-content-data-department-name">${row.department_name || ''}</td>
                                                    <td class="program-content-data-program-name">${row.program_name}</td>
                                                    <td class="program-content-data-program-level">${row.program_level}</td>
                                                    <td class="program-content-data-program-year-grade-level">${row.program_year_grade_level}</td>
                                                    <td class="program-content-data-section">${row.section}</td>
                                                    <td class="program-content-data-btn">
                                                        <form method="post" class="program-content-data-form">
                                                            <button type="button" class="program-edit">EDIT</button>
                                                            <button type="button" class="program-delete">DELETE</button>
                                                        </form>
                                                    </td>
                                                `;
                                                tbody.appendChild(tr);
                                            });
                                        } else {
                                            tbody.innerHTML = '<tr><td colspan="7">No records found.</td></tr>';
                                        }
                                    } catch (error) {
                                        console.error('Error fetching data:', error);
                                    }
                                }

                                // Trigger fetchProgramData on page load
                                document.addEventListener('DOMContentLoaded', function () {
                                    fetchProgramData(); // Fetch all data without filters

                                    // Add event listeners for input changes
                                    const filterSelect = document.getElementById('program-content-filter-select-col');
                                    const searchInput = document.getElementById('program-content-search-input');

                                    filterSelect.addEventListener('change', function () {
                                        const filterColumn = filterSelect.value;
                                        const searchValue = searchInput.value.trim();
                                        fetchProgramData(filterColumn, searchValue);
                                    });

                                    searchInput.addEventListener('input', function () {
                                        const filterColumn = filterSelect.value;
                                        const searchValue = searchInput.value.trim();
                                        fetchProgramData(filterColumn, searchValue);
                                    });
                                });

                                // Show Edit Program Form
                                document.addEventListener('click', function(event) {
                                    if (event.target.classList.contains('program-edit')) {
                                        event.preventDefault(); // Prevent form submission
                                        const row = event.target.closest('tr');
                                        document.querySelector('.edit-program-container').style.display = 'flex';
                                        document.getElementById('edit-program-id').value = row.getAttribute('data-program-id');
                                        document.getElementById('edit-program-name').value = row.querySelector('.program-content-data-program-name').textContent.trim();
                                        document.getElementById('edit-program-level').value = row.querySelector('.program-content-data-program-level').textContent.trim();
                                        document.getElementById('edit-year-grade-level').value = row.querySelector('.program-content-data-program-year-grade-level').textContent.trim();
                                        document.getElementById('edit-section').value = row.querySelector('.program-content-data-section').textContent.trim();
                                    }

                                    if (event.target.classList.contains('program-delete')) {
                                        event.preventDefault(); // Prevent form submission
                                        const row = event.target.closest('tr');
                                        const programId = row.getAttribute('data-program-id');

                                        if (confirm('Are you sure you want to delete this program?')) {
                                            deleteProgram(programId, row);
                                        }
                                    }
                                });

                                // Handle Delete Program
                                async function deleteProgram(programId, row) {
                                    try {
                                        const response = await fetch('deleteProgramHandler.php', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            body: JSON.stringify({ program_id: programId })
                                        });

                                        const result = await response.json();
                                        if (result.success) {
                                            alert('Program deleted successfully!');
                                            row.remove(); // Remove row from the table
                                        } else {
                                            alert('Failed to delete program: ' + result.message);
                                        }
                                    } catch (error) {
                                        console.error('Error:', error);
                                        alert('An error occurred while deleting the program.');
                                    }
                                }

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

                                // Close Edit Program Form
                                document.getElementById('close-edit-program-btn').addEventListener('click', function () {
                                    document.querySelector('.edit-program-container').style.display = 'none';
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
