<?php
session_start();
include "dbh.php";
if (@!isset($_SESSION['user_ID']) || $_SESSION['user_ID'] === null) {
    header("Location: LogInPage.html");
    exit();
}
$user_ID = $_SESSION['user_ID'];
$query = "SELECT * FROM user WHERE user_id = $user_ID";
$result = mysqli_query($conn, $query);
$userInfo = $result->fetch_assoc();




// Fetch organization data
$organizationQuery = "SELECT organization_id, organization_name, organization_list_of_position FROM organization";
$organizationResult = $conn->query($organizationQuery);

$organizations = [];
if ($organizationResult->num_rows > 0) {
    while ($row = $organizationResult->fetch_assoc()) {
        $organizations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create User</title>
    <link rel="stylesheet" href="css/body.css">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/adminBody.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminCreateUserPage.css">
    <script src="js/adminSidebar.js"></script>
    <script src="js/adminUser.js"></script>
</head>
<body>
    <div class="main-content-container">
        <?php include_once "adminSidebar.php"; ?>
        <div class="main-content">
            <div class="user-content">
                <div class="user-content-top-part">
                    <h1 class="user-content-head-text">Add New User</h1>
                </div>
                
                <div class="create-users-account-content">
                    <div class="form-content-container">
                    <!-- Add New User Form -->
                        <form action="addUserHandler.php" method="post" class="form-content add-new-user" id="add-new-user" enctype="multipart/form-data">
                            <div class="form-content-input-field-col">
                                

                                <div class="form-content-input-field-row">
                                    <div class="form-content-input-field">
                                        <label for="role">Role:</label>
                                        <div class="role-selector-container">
                                            <button id="show-role-list-btn" type="button">Select Role</button>
                                            <div id="role-list-container" class="hidden">
                                                <ul class="role-list">
                                                    <li data-role="student">Student</li>
                                                    <li data-role="officer">Officer</li>
                                                    <li data-role="dean">Dean</li>
                                                    <li data-role="admin">Admin</li>
                                                </ul>
                                            </div>
                                            <input type="hidden" id="role" name="role" required>
                                        </div>
                                        <div id="selected-role-container"></div>
                                    </div>

                                </div>
                                <div class="form-content-input-field-row">
                                    <div class="form-content-input-field">
                                        <label for="username">Username:</label>
                                        <input type="text" id="username" name="username" required>
                                    </div>
                                    <div class="form-content-input-field">
                                        <label for="password">Password:</label>
                                        <input type="password" id="password" name="password" required>
                                        <div class="form-content-input-field-pw">
                                            <input type="checkbox" id="toggle-pw" onclick="togglePassword()" style="display: none;">
                                            <label for="toggle-pw">Show</label>
                                        </div>
                                    </div>


                                    <div class="form-content-input-field">
                                        <label for="pnumber">Phone Number:</label>
                                        <input type="tel" id="pnumber" name="pnumber" required>
                                    </div>
                                </div>
                                <div class="form-content-input-field">
                                    <label for="pfpPic" class="upload-label">Profile Picture</label>
                                    <input type="file" id="pfpPic" name="pfpPic" accept="image/*" onchange="previewProfilePic()">
                                    <span class="pfpPic-name">↑ Click here to upload</span>
                                    <img src="" alt="Profile Picture Preview" id="pfp-pic-img" class="pfp-pic-img">
                                </div>


                                <div class="form-content-input-field-row">
                                    <div class="form-content-input-field">
                                        <label for="schoolid">School ID:</label>
                                        <input type="text" id="schoolid" name="schoolid" required>
                                    </div>
                                    <div class="form-content-input-field">
                                        <label for="rfid-card-uid">RFID Card UID:</label>
                                        <input type="text" name="rfid-card-uid" id="rfid-card-uid" value="">
                                    </div>
                                    <div class="form-content-input-field scan-btn">
                                        <button type="button" id="rfid-scan-btn">Scan</button>
                                    </div>
                                </div>


                                <div class="form-content-input-field-row">
                                    <div class="form-content-input-field">
                                        <label for="fname">First Name:</label>
                                        <input type="text" id="fname" name="fname" required>
                                    </div>
                                    <div class="form-content-input-field">
                                        <label for="mname">Middle Name:</label>
                                        <input type="text" id="mname" name="mname" >
                                    </div>
                                    <div class="form-content-input-field">
                                        <label for="lname">Last Name:</label>
                                        <input type="text" id="lname" name="lname">
                                    </div>
                                    <div class="form-content-input-field">
                                        <label for="sname">Suffix:</label>
                                        <input type="text" id="sname" name="sname">
                                    </div>
                                </div>
                                <div class="form-content-input-field-row">
                                    <div class="form-content-input-field">
                                        <label for="gender">Gender:</label>
                                        <select id="gender" name="gender" required>
                                            <option value="" disabled selected>Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-content-input-field">
                                        <label for="address">Address:</label>
                                        <input type="text" id="address" name="address" required>
                                    </div>
                                    <div class="form-content-input-field">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" name="email" required>
                                    </div>
                                </div>


                                <!-- Placeholder for additional fields -->
                                <div id="additional-fields"></div>
                                <div class="form-content-btn">
                                    <button type="submit"  class="add-new-user-btn">Add New User</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>  
    </div>

    <script>

   // Preview profile picture
function previewProfilePic() {
    const fileInput = document.getElementById('pfpPic');
    const imgElement = document.getElementById('pfp-pic-img');
    const fileNameElement = document.querySelector('.pfpPic-name');

    if (fileInput.files.length) {
        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = (e) => {
            imgElement.src = e.target.result;
            imgElement.style.display = 'block';
        };
        reader.readAsDataURL(file);

        fileNameElement.textContent = file.name;
    } else {
        imgElement.src = '';
        imgElement.style.display = 'none';
        fileNameElement.textContent = '↑ Click here to upload';
    }
}

// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleCheckbox = document.getElementById('toggle-pw');
    const eyeIcon = document.querySelector('.form-content-input-field-pw > label');

    const isChecked = toggleCheckbox.checked;
    passwordInput.type = isChecked ? 'text' : 'password';
    eyeIcon.textContent = isChecked ? 'Hide' : 'Show';
}




const StartScan = document.getElementById("rfid-scan-btn");
if(StartScan) {
    StartScan.addEventListener("click", function () {
        scanRFID();
    });
}
async function scanRFID() {
    let port;
    try {
        // Request a port and open a connection
        port = await navigator.serial.requestPort();
        await port.open({ baudRate: 9600 });

        const textDecoder = new TextDecoderStream();
        const readableStreamClosed = port.readable.pipeTo(textDecoder.writable);
        const reader = textDecoder.readable.getReader();

        console.log("Connected to the serial port.");

        // Continuously read data from the serial port
        let buffer = "";
        while (true) {
            const { value, done } = await reader.read();
            if (done) {
                console.log("Stream closed.");
                reader.releaseLock();
                break;
            }
            if (value) {
                buffer += value; // Append the new data to the buffer

                // Split buffer by lines
                const lines = buffer.split("\n");

                for (let line of lines) {
                    line = line.trim(); // Trim any whitespace

                    // Process each line
                    if (line.includes("Card UID:")) {
                        const startIndex = line.indexOf("Card UID:") + "Card UID:".length;
                        const uid = line.substring(startIndex).trim(); // Extract UID

                        console.log("UID Detected:", uid); // Log UID for verification

                        // Update the RFID tag input field once UID is detected
                        document.getElementById("rfid-card-uid").value = uid;


                        // Clear the buffer after processing
                        buffer = "";
                    }
                }

                // If there is a partial line at the end, keep it in the buffer for the next iteration
                buffer = lines[lines.length - 1];
            }
        }
    } catch (error) {
        console.error("Error during RFID scan:", error);
    } finally {
        // Ensure the port is closed only when the scanning is completely done
        if (port && port.readable) {
            await port.close();
            console.log("Port closed safely.");
        }
    }
}





// Submit the form with AJAX
document.getElementById('add-new-user').addEventListener('submit', function (e) {
    console.log('Submit handler triggered');
    e.preventDefault();
    pfpPicInput = document.getElementById('pfpPic');

    // Check if the profile picture input is empty
    if (!pfpPicInput.files.length) {
        alert('Please select a profile picture to upload.');
        pfppicLabel = document.querySelector(".upload-label");
        // Scroll to the profile picture input field
        pfppicLabel.scrollIntoView({ behavior: 'smooth' });

        // Focus on the profile picture input field
        pfppicLabel.focus();

        return; // Prevent form submission if image is not selected
    }

    const formData = new FormData(this);

    fetch('addUserHandler.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert('User added successfully!');
                this.reset();
                document.getElementById('role').value = '';
                document.getElementById('pfp-pic-img').src = '';
                document.getElementById('additional-fields').innerHTML = '';
                document.querySelector('.pfpPic-name').textContent = '↑ Click here to upload';
                pfppicIMG = document.getElementById('pfp-pic-img');
                pfppicIMG.style.display = 'none';


                // Remove selected-role div elements and reset selectedRoles
                const selectedRoleContainer = document.getElementById('selected-role-container');
                selectedRoleContainer.innerHTML = ''; // Clear all role boxes
                selectedRoles = []; // Reset the selected roles array
            } else if (data.error === 'duplicate_schoolid') {
                alert('Error: The School ID is already in use.');
            } else if (data.error === 'duplicate_CardUID') {
                alert('Error: The Card UID is already in use.');
            } else {
                alert('Error: Failed to add user. Please try again.');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again.');
        });
});

// Fetch departments
async function fetchDepartments() {
    try {
        const response = await fetch('fetchDepartments.php');
        const departments = await response.json();
        populateSelect('department', departments, 'department_id', 'department_name');
    } catch (error) {
        console.error('Failed to fetch departments:', error);
    }
}

// Populate dropdown
function populateSelect(selectId, items, valueKey, textKey) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) return; // Ensure select element exists
    selectElement.innerHTML = `<option value="" disabled selected>Select</option>`;
    items.forEach((item) => {
        const option = document.createElement('option');
        option.value = item[valueKey];
        option.textContent = item[textKey];
        selectElement.appendChild(option);
    });
}



document.addEventListener("DOMContentLoaded", () => {
    const showRoleListBtn = document.getElementById("show-role-list-btn");
    const roleListContainer = document.getElementById("role-list-container");
    const roleList = document.querySelectorAll(".role-list li");
    const selectedRoleContainer = document.getElementById("selected-role-container");
    const roleInput = document.getElementById("role");
    const additionalFieldsContainer = document.getElementById("additional-fields");

    let selectedRoles = []; // Store selected roles

    // Toggle role list visibility
    showRoleListBtn.addEventListener("click", () => {
        roleListContainer.classList.toggle("hidden");
    });

    // Handle role selection
    roleList.forEach((roleItem) => {
        roleItem.addEventListener("click", () => {
            const selectedRole = roleItem.getAttribute("data-role");

            // Check "student and officer" combination logic
            if (
                selectedRoles.includes("student") &&
                selectedRole !== "officer"
            ) {
                alert("You can only select 'student' and 'officer' together. Remove one to add a different role.");
                return;
            }

            if (selectedRoles.includes(selectedRole)) {
                alert(`The role '${selectedRole}' is already selected.`);
                return;
            }

            // Add role to selected roles array
            selectedRoles.push(selectedRole);
            roleInput.value = selectedRoles.join(", "); // Update hidden input value

            // Add selected role display
            const roleBox = document.createElement("div");
            roleBox.classList.add("selected-role");
            roleBox.textContent = selectedRole;

            // Add remove button
            const removeBtn = document.createElement("button");
            removeBtn.classList.add("remove-role");
            removeBtn.textContent = "x";
            roleBox.appendChild(removeBtn);

            selectedRoleContainer.appendChild(roleBox);

            // Remove role functionality
            removeBtn.addEventListener("click", () => {
                selectedRoles = selectedRoles.filter((role) => role !== selectedRole);
                selectedRoleContainer.removeChild(roleBox);
                roleInput.value = selectedRoles.join(", ");
                handleAdditionalFields(); // Update fields on removal
            });

            roleListContainer.classList.add("hidden"); // Hide role list
            handleAdditionalFields(); // Update fields on selection
        });
    });

    // Handle additional fields based on selected roles
    function handleAdditionalFields() {
        additionalFieldsContainer.innerHTML = ''; // Clear the container before adding new fields

        selectedRoles.forEach((role) => {
            let roleFields = '';

            if (role === 'student') {
                roleFields = `
                    <div class="role-info" data-role="student">
                        <h3>Student Information</h3>
                        <div class="form-content-input-field">
                            <label for="department">Department:</label>
                            <select id="department" name="department" required>
                                <option value="" disabled selected>Select Department</option>
                            </select>
                        </div>
                        <div class="form-content-input-field">
                            <label for="program">Program:</label>
                            <select id="program" name="program" required>
                                <option value="" disabled selected>Select Program</option>
                            </select>
                        </div>
                        <div class="form-content-input-field">
                            <label for="year">Year/Grade Level:</label>
                            <select id="year" name="year" required>
                                <option value="" disabled selected>Select Year/Grade Level</option>
                            </select>
                        </div>
                        <div class="form-content-input-field">
                            <label for="section">Section:</label>
                            <select id="section" name="section" required>
                                <option value="" disabled selected>Select Section</option>
                            </select>
                        </div>
                    </div>`;
            } else if (role === 'officer') {
                roleFields = `
                    <div class="role-info" data-role="officer">
                        <h3>Officer Information</h3>
                        <div class="form-content-input-field">
                            <label for="organization">Organization:</label>
                            <select id="organization" name="organization" required>
                                <option value="" disabled selected>Select Organization</option>
                            </select>
                        </div>
                        <div class="form-content-input-field">
                            <label for="position">Position:</label>
                            <select id="position" name="position" required>
                                <option value="" disabled selected>Select Position</option>
                            </select>
                        </div>
                        <div class="form-content-input-field">
                            <label for="assign_duty">Assigned Duty:</label>
                            <select id="assign_duty" name="assign_duty" required>
                                <option value="" disabled selected>Select Duty</option>
                            </select>
                        </div>
                    </div>`;
            } else if (role === 'dean') {
                roleFields = `
                    <div class="role-info" data-role="dean">
                        <h3>Dean Information</h3>
                        <div class="form-content-input-field">
                            <label for="department">Department:</label>
                            <select id="department" name="department" required>
                                <option value="" disabled selected>Select Department</option>
                            </select>
                        </div>
                    </div>`;
            } else if (role === 'admin') {
                roleFields = `
                    <div class="role-info" data-role="admin">
                        <h3>Admin Information</h3>
                        <div class="form-content-input-field">
                            <label for="admin_level">Admin Level:</label>
                            <input type="text" id="admin_level" name="admin_level" required>
                        </div>
                    </div>`;
            }

            // Append role-specific fields
            additionalFieldsContainer.insertAdjacentHTML('beforeend', roleFields);

            // Populate dropdowns dynamically if necessary
            if (role === 'dean' || role === 'student') {
                fetchDepartments();  // Fetch and populate department dropdowns
            }
            if (role === 'student') {
                // Handle specific student functionality
                const departmentSelect = document.getElementById("department");
                const programSelect = document.getElementById("program");
                const yearSelect = document.getElementById("year");
                const sectionSelect = document.getElementById("section");

                // Fetch programs based on department selection
                departmentSelect.addEventListener("change", function () {
                    const departmentId = this.value;

                    // Reset program dropdown and fetch new programs
                    fetch(`fetchPrograms.php?department_id=${departmentId}`)
                        .then(response => response.json())
                        .then(programs => {
                            programSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
                            programs.forEach(program => {
                                const option = document.createElement("option");
                                option.value = program.program_id;
                                option.textContent = program.program_name;
                                option.dataset.yearLevels = program.program_year_grade_levels.join(", "); // Store year levels in data attribute
                                option.dataset.sections = program.program_sections.join(", "); // Store sections in data attribute
                                programSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error("Error fetching programs:", error));
                });

                // Populate year levels and sections when a program is selected
                programSelect.addEventListener("change", function () {
                    const selectedProgram = programSelect.options[programSelect.selectedIndex];
                    const yearLevels = selectedProgram.dataset.yearLevels.split(", ");
                    const sections = selectedProgram.dataset.sections.split(", ");

                    // Clear Year/Grade Level and Section dropdowns
                    yearSelect.innerHTML = '<option value="" disabled selected>Select Year/Grade Level</option>';
                    sectionSelect.innerHTML = '<option value="" disabled selected>Select Section</option>';

                    // Populate Year/Grade Level dropdown
                    yearLevels.forEach(year => {
                        const yearOption = document.createElement("option");
                        yearOption.value = year;
                        yearOption.textContent = year;
                        yearSelect.appendChild(yearOption);
                    });

                    // Populate Section dropdown
                    sections.forEach(section => {
                        const sectionOption = document.createElement("option");
                        sectionOption.value = section;
                        sectionOption.textContent = section;
                        sectionSelect.appendChild(sectionOption);
                    });
                });

            }
            // Populate Assigned Duty dropdown if the role is officer
            if (role === 'officer') {
                const assignDutySelect = document.getElementById('assign_duty');
                const assignDutyOptions = [
                    "Head of the Organization",
                    "Second-in-Command of the Organization",
                    "Organization Members"
                ];

                // Populate the Assigned Duty dropdown
                assignDutyOptions.forEach(duty => {
                    const option = document.createElement("option");
                    option.value = duty;
                    option.textContent = duty;
                    assignDutySelect.appendChild(option);
                });

                // Populate organizations and positions dynamically
                populateOfficerOrganizations();

                // Handle positions dynamically based on selected organization
                document.getElementById('organization').addEventListener('change', function () {
                    const positionSelect = document.getElementById('position');
                    const selectedOrganizationId = this.value;

                    // Find the selected organization
                    const selectedOrganization = window.organizations.find(org => org.organization_id == selectedOrganizationId);
                    if (selectedOrganization) {
                        const positions = selectedOrganization.organization_list_of_position.split(',');

                        // Clear existing positions
                        positionSelect.innerHTML = '<option value="" disabled selected>Select Position</option>';

                        // Populate the position dropdown
                        positions.forEach(position => {
                            const option = document.createElement('option');
                            option.value = position.trim();
                            option.textContent = position.trim();
                            positionSelect.appendChild(option);
                        });
                    }
                });
            }
        });
    }

    // Fetch departments and populate department dropdowns
    function fetchDepartments() {
        fetch('fetchDepartments.php')
            .then(response => response.json())
            .then(departments => {
                const departmentSelects = document.querySelectorAll('[id="department"]');
                departmentSelects.forEach(departmentSelect => {
                    departmentSelect.innerHTML = '<option value="" disabled selected>Select Department</option>';
                    departments.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.department_id;
                        option.textContent = department.department_name;
                        departmentSelect.appendChild(option);
                    });
                });
            })
            .catch(error => console.error('Error fetching departments:', error));
    }

    // Populate officer organizations dynamically
    function populateOfficerOrganizations() {
        const organizationSelect = document.getElementById('organization');
        fetch('fetchOrganizations.php')
            .then(response => response.json())
            .then(organizations => {
                organizationSelect.innerHTML = '<option value="" disabled selected>Select Organization</option>';
                organizations.forEach(org => {
                    const option = document.createElement('option');
                    option.value = org.organization_id;
                    option.textContent = org.organization_name;
                    organizationSelect.appendChild(option);
                });

                // Store organizations globally for later use
                window.organizations = organizations;
            })
            .catch(error => console.error('Error fetching organizations:', error));
    }


});

    </script>
</body>
</html>