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
                            <select name="organization_responsibility" id="organization-responsibility" required>
                                <option value="" disabled selected>Select Responsibility</option>
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

                                    // Generate options grouped by program level
                                    foreach ($programData as $level => $programs) {
                                        // Add "All - [Level]" option
                                        echo "<optgroup label='" . htmlspecialchars($level) . "'>";
                                        echo "<option value='All - " . htmlspecialchars($level) . "'>All - " . htmlspecialchars($level) . " Students</option>";

                                        // Add options for each program
                                        foreach ($programs as $programName => $details) {
                                            echo "<optgroup label='" . htmlspecialchars($programName) . "'>";
                                            // Add "All - [Program]" option
                                            echo "<option value='All - " . htmlspecialchars($programName) . "'>All - " . htmlspecialchars($programName) . " Students</option>";

                                            // Add options for each year/grade
                                            foreach ($details['years'] as $year) {
                                                echo "<optgroup label='" . htmlspecialchars($programName . " - " . $year) . "'>";
                                                echo "<option value='All - " . htmlspecialchars($programName . " - " . $year) . "'> All - " . htmlspecialchars($programName . " - " . $year) . " Students</option>";

                                                // Add options for each section
                                                foreach ($details['sections'] as $section) {
                                                    echo "<option value='" . htmlspecialchars($programName . " - " . $year . " - " . $section) . "'>" . htmlspecialchars($programName . " - " . $year . " - " . $section) . " Students</option>";
                                                }
                                                echo "</optgroup>";
                                            }
                                            echo "</optgroup>";
                                        }
                                        echo "</optgroup>";
                                    }

                                ?>
                            </select>
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
                                <input type="text" name="organization-content-search-input" id="organization-content-search-input" placeholder="Search..." value="<?php echo htmlspecialchars(@$_GET['organization-content-search-input']); ?>">
                                <input type="submit" name="filterSubmit" id="filterSubmit" class="filter-button" value="Search">
                            </div>
                        </form>
                    </div>

                    <div class="organization-content-main-content-container">
                        <div class="organization-content-table-content-container">
                            <table class="organization-content-table-content">
                                <thead class="organization-content-table-head">
                                    <tr class="organization-content-table-row">
                                        <th class="organization-content-data-organization-name">Organization Name</th>
                                        <th class="organization-content-data-responsibility">Responsibility</th>
                                        <th class="organization-content-data-btn-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="organization-content-table-body">
                                    <?php 

                                        // Query to fetch event data
                                        $organizationQuery = "SELECT * FROM organization";
                                        
                                        $result = mysqli_query($conn, $organizationQuery);

                                        // Check if there are results
                                        // if (mysqli_num_rows($result) > 0) {
                                        //     while ($row = mysqli_fetch_assoc($result)) {

                                        //         echo '
                                        //             <tr class="organization-content-table-row">
                                        //                 <form action="adminOrganizationDataHandler.php" method="post">
                                        //                     <input type="hidden" name="organization-id" value="'. $row['organization_id'] .'">
                                        //                     <td class="organization-content-data-organization-name">'. htmlspecialchars($row['organization_name']) .'</td>
                                        //                     <td class="organization-content-data-responsibility">'. htmlspecialchars($row['organization_responsibility']) .'</td>
                                        //                     <td class="organization-content-data-btn-actions">
                                        //                         <button type="submit" name="vSubmit" class="user-view">VIEW</button>
                                        //                         <button type="submit" name="dSubmit" class="user-delete">DELETE</button>
                                        //                     </td>
                                        //                 </form>
                                        //             </tr>';
                                        //     }
                                        // } else {
                                        //     echo '<tr><td colspan="8">No records found.</td></tr>';
                                        // }
                                    ?>
                                    <?php if (mysqli_num_rows($result) > 0):?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr class="organization-content-table-row">
                                                <form action="adminOrganizationDataHandler.php" method="post">
                                                    <input type="hidden" name="organization-id" value="<?php echo $row['organization_id'];?>">
                                                    <td class="organization-content-data-organization-name"><?php echo htmlspecialchars($row['organization_name']); ?></td>
                                                    <td class="organization-content-data-responsibility"><?php echo htmlspecialchars($row['organization_responsibility']); ?></td>
                                                    <td class="organization-content-data-btn-actions">
                                                        <button type="submit" name="vSubmit" class="organization-view">VIEW</button>
                                                        <button type="submit" name="eSubmit" class="organization-edit">EDIT</button>
                                                        <button type="submit" name="dSubmit" class="organization-delete">DELETE</button>
                                                    </td>
                                                </form>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8">No records found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
            
        </div>  
    </div>
    

    <script>
    </script>
</body>
</html>