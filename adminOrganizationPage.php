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
    <title>Organization</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminOrganization.css">
    <script src="js\adminSidebar.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once"adminSidebar.php";?>
        <div class="main-content">
            <div class="organization-content">
                <div class="organization-content-top-part">
                    <h1 class="organization-content-head-text">ORGANIZATION</h1>
                    <div class="organization-content-btn">
                        <button type="button" id="add-new-organization-btn-show" class="add-new-organization-btn-show">Add New</button>
                    </div>
                </div>
                <div class="add-organization-container">
                    <h2>Add New Organization</h2>
                    <form action="adminOrganizationInsertHandler.php" method="post" id="add-organization-form" class="add-organization-form">
                        <div class="form-group">
                            <label for="organization-name">Organization Name</label>
                            <input type="text" name="organization_name" id="organization-name" required placeholder="Enter organization name">
                        </div>

                        <div class="form-group">
                            <label for="organization-responsibility">Responsibility</label>
                            <input type="hidden" name="organization_responsibility" id="organization-responsibility" required>
                            <div class="custom-dropdown">
                                <button type="button" id="dropdown-toggle" class="dropdown-toggle">Select Responsibility</button>
                                <div id="dropdown-options" class="dropdown-options">
                                    <!-- PHP options here -->
                                    <?php
                                        // Fetch programs grouped by level, program, year, and section from the database
                                        $allProgramsQuery = "SELECT program_name, program_level, program_year_grade_level, section FROM program ORDER BY program_level, program_name";
                                        $allProgramsQueryResult = mysqli_query($conn, $allProgramsQuery);

                                        $programData = [];
                                        if (mysqli_num_rows($allProgramsQueryResult) > 0) {
                                            while ($programRow = mysqli_fetch_assoc($allProgramsQueryResult)) {
                                                $programLevel = $programRow['program_level'];
                                                $programName = $programRow['program_name'];
                                                $programYears = $programRow['program_year_grade_level']
                                                                ? explode(", ", $programRow['program_year_grade_level'])
                                                                : ['All Levels'];
                                                $programSections = $programRow['section']
                                                                    ? explode(", ", $programRow['section'])
                                                                    : ['All Sections'];

                                                // Group programs by level, then by program name, then by year and section
                                                $programData[$programLevel][$programName] = [
                                                    'years' => $programYears,
                                                    'sections' => $programSections
                                                ];
                                            }
                                        }
                                        foreach ($programData as $level => $programs) {
                                            echo "<div class='dropdown-group'>";
                                            echo "<div class='dropdown-group-label'>" . htmlspecialchars($level) . "</div>";
                                            echo "<div class='dropdown-option' data-value='All - " . htmlspecialchars($level) . "'>All - " . htmlspecialchars($level) . " Students</div>";
                                            foreach ($programs as $programName => $details) {
                                                echo "<div class='dropdown-group'>";
                                                echo "<div class='dropdown-group-label'>" . htmlspecialchars($programName) . "</div>";
                                                echo "<div class='dropdown-option' data-value='All - " . htmlspecialchars($programName) . "'>All - " . htmlspecialchars($programName) . " Students</div>";
                                                foreach ($details['years'] as $year) {
                                                    echo "<div class='dropdown-group'>";
                                                    echo "<div class='dropdown-group-label'>" . htmlspecialchars($programName . ' - ' . $year) . "</div>";
                                                    echo "<div class='dropdown-option' data-value='All - " . htmlspecialchars($programName . ' - ' . $year) . "'>All - " . htmlspecialchars($programName . ' - ' . $year) . " Students</div>";
                                                    foreach ($details['sections'] as $section) {
                                                        echo "<div class='dropdown-option' data-value='" . htmlspecialchars($programName . ' - ' . $year . ' - ' . $section) . "'>" . htmlspecialchars($programName . ' - ' . $year . ' - ' . $section) . " Students</div>";
                                                    }
                                                    echo "</div>";
                                                }
                                                echo "</div>";
                                            }
                                            echo "</div>";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div id="selected-options" class="selected-options"></div>
                        </div>





                        <div class="form-group">
                            <label for="organization-positions">Positions</label>
                            <textarea name="organization_positions" id="organization-positions" required placeholder="Enter positions separated by commas (e.g., President, Vice-President, Secretary)"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="addOrganizationSubmit" class="add-organization-btn">Add Organization</button>
                            <button type="button" id="close-add-organization-btn" class="close-add-organization-btn">X</button>
                        </div>
                    </form>
                    <div id="add-organization-message"></div>
                </div>

                <script>

                    const dropdownToggle = document.getElementById('dropdown-toggle');
                    const dropdownOptions = document.getElementById('dropdown-options');
                    const selectedOptionsContainer = document.getElementById('selected-options');
                    const hiddenInput = document.getElementById('organization-responsibility');

                    // Toggle dropdown visibility
                    dropdownToggle.addEventListener('click', () => {
                        dropdownOptions.style.display = dropdownOptions.style.display === 'block' ? 'none' : 'block';
                    });

                    // Add option to selected list
                    dropdownOptions.addEventListener('click', (event) => {
                        if (event.target.classList.contains('dropdown-option')) {
                            const value = event.target.getAttribute('data-value');

                            // Avoid duplicates
                            if (hiddenInput.value.split(',').includes(value)) return;

                            // Add to hidden input
                            hiddenInput.value = hiddenInput.value ? `${hiddenInput.value},${value}` : value;
                            console.log("hiddenInput.value", hiddenInput.value);
                            
                            // Add to UI
                            const selectedOption = document.createElement('div');
                            selectedOption.className = 'selected-option';
                            selectedOption.textContent = value;
                            const removeButton = document.createElement('button');
                            removeButton.textContent = '×';
                            removeButton.addEventListener('click', () => {
                                // Remove from hidden input
                                hiddenInput.value = hiddenInput.value
                                    .split(',')
                                    .filter(item => item !== value)
                                    .join(',');

                                // Remove from UI
                                selectedOption.remove();
                            });
                            selectedOption.appendChild(removeButton);
                            selectedOptionsContainer.appendChild(selectedOption);
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', (event) => {
                        if (!event.target.closest('.custom-dropdown')) {
                            dropdownOptions.style.display = 'none';
                        }
                    });
                    document.getElementById("close-add-organization-btn").addEventListener("click", function() {
                        const addOrganizationContainer = document.querySelector(".add-organization-container");
                        addOrganizationContainer.style.display = "none";
                    });

                    document.getElementById("add-new-organization-btn-show").addEventListener("click", function() {
                        const addOrganizationContainer = document.querySelector(".add-organization-container");
                        addOrganizationContainer.style.display = "block"; 
                    });
                            
                    document.getElementById('add-organization-form').addEventListener('submit', async function(event) {
                        event.preventDefault();

                        const formData = new FormData(this);

                        try {
                            const response = await fetch('adminOrganizationInsertHandler.php', {
                                
                                method: 'POST',
                                body: formData,
                            });

                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.success) {
                                document.getElementById('add-organization-message').textContent = data.message;
                            } else {
                                document.getElementById('add-organization-message').textContent = data.message;
                            }
                        } catch (error) {
                            console.error('Fetch error:', error);
                            document.getElementById('add-organization-message').textContent = 'An error occurred. Please try again.';
                        }
                    });

                </script>


                <div class="organization-content-organization-content">
                    <div class="organization-content-top-part-filter">
                        <form action="" method="get" enctype="multipart/form-data" class="organization-content-form" id="organization-content-form">
                            <div class="organization-content-search-input-container">
                                <select name="organization-content-filter-select-col" id="organization-content-filter-select-col" class="filter-select">
                                    <option value="" disabled selected>Filter By</option>
                                    <option value="organization_name">Organization Name</option>
                                    <option value="organization_responsibility">Responsibility</option>
                                </select>
                                <input type="text" 
                                name="organization-content-search-input" 
                                id="organization-content-search-input" 
                                placeholder="Search..." 
                                value="">
                            </div>
                        </form>
                    </div>

                    <div class="organization-content-main-content-container">
                        <div class="organization-content-table-content-container">
                            <table class="organization-content-table-content">
                                <thead class="organization-content-table-head">
                                    <tr class="organization-content-table-row">
                                        <th class="organization-content-data-organization-id">ID</th>
                                        <th class="organization-content-data-organization-name">Organization Name</th>
                                        <th class="organization-content-data-responsibility">Responsibility</th>
                                        <th class="organization-content-data-btn-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="organization-content-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const selectCol = document.getElementById('organization-content-filter-select-col');
                            const searchInput = document.getElementById('organization-content-search-input');
                            const tableBody = document.querySelector('.organization-content-table-body');

                            // Mapping between dropdown values and organization table keys
                            const columnKeyMap = {
                                "Organization Name": "organization_name",
                                "Responsibility": "organization_responsibility",
                            };

                            let allOrganizationData = [];

                            // Function to fetch all organization data
                            async function fetchOrganizationData() {
                                try {
                                    const response = await fetch('organizationDataFetch.php'); // Ensure this points to your PHP endpoint
                                    const data = await response.json();

                                    allOrganizationData = data;
                                    renderTableData(allOrganizationData);
                                } catch (error) {
                                    console.error('Error fetching organization data:', error);
                                    tableBody.innerHTML = '<tr><td colspan="3">Failed to fetch data. Please try again later.</td></tr>';
                                }
                            }

                            // Function to render table data
                            function renderTableData(data) {
                                tableBody.innerHTML = '';

                                if (data.length > 0) {
                                    data.forEach(row => {
                                        const tableRow = `
                                            <tr class="organization-content-table-row">
                                                <td class="organization-content-data-organization-id">${row.organization_id}</td>
                                                <td class="organization-content-data-organization-name">${row.organization_name}</td>
                                                <td class="organization-content-data-responsibility">${row.organization_responsibility}</td>
                                                <td class="organization-content-data-btn-actions">
                                                    <form action="adminOrganizationDataHandler.php" method="post" class="organization-content-data-form">
                                                        <input type="hidden" name="organization-id" value="${row.organization_id}">
                                                        <button type="submit" name="vSubmit" class="organization-view">VIEW</button>
                                                        <button type="submit" name="eSubmit" class="organization-edit">EDIT</button>
                                                        <button type="submit" name="dSubmit" class="organization-delete" onclick="return confirm('Are you sure you want to delete this organization?');">DELETE</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        `;
                                        tableBody.innerHTML += tableRow;
                                    });
                                } else {
                                    tableBody.innerHTML = '<tr><td colspan="3">No records found.</td></tr>';
                                }
                            }

                            // Function to apply filters
                            function applyFilters() {
                                const col = selectCol.value; // Selected column
                                const search = searchInput.value.trim().toLowerCase(); // Search input value

                                // Filter data
                                const filteredData = allOrganizationData.filter(row => {
                                    let matchesSearch = true;

                                    if (col && search) {
                                        // Directly use the col value to fetch data
                                        const colValue = row[col] || '';
                                        matchesSearch = colValue.toLowerCase().includes(search);
                                    } else if (search) {
                                        // Search across all fields if no column is selected
                                        matchesSearch = Object.values(row).some(value =>
                                            value && value.toString().toLowerCase().includes(search)
                                        );
                                    }


                                    return matchesSearch;
                                });

                                renderTableData(filteredData);
                            }

                            // Event listeners for filtering
                            selectCol.addEventListener('change', applyFilters);
                            searchInput.addEventListener('input', applyFilters);

                            // Fetch and display data on page load
                            fetchOrganizationData();
                        });
                    </script>
                </div>
            </div>
        </div>  
    </div>
</body>
</html>