<?php
include "dbh.php";

header('Content-Type: application/json');

// Get the posted JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Ensure the event_id is set
if (isset($data['event_id'])) {
    $event_id = $data['event_id'];
    $event_participant = trim($data['event_participant']);

    $participants = [];
    $attendance = [];
    $students = [];      // Placeholder for actual logic
    // Check if $event_participant contains commas and process accordingly
    $event_participants = [];
    if (str_contains($event_participant, ",")) {
        // Split the string into an array if it contains commas
        $event_participants = explode(",", $event_participant);
    } else {
        // Treat $event_participant as a single item array
        $event_participants[] = $event_participant;
    }
    
    foreach ($event_participants as $event_participant) {
        $part = array_map('trim', explode("-", $event_participant));
        $programLevels = ["College", "college", "Secondary", "secondary", "Primary", "primary"];

        if (str_contains($part[0], "all") || str_contains($part[0], "All")) {

            if (in_array($part[1], $programLevels)) {
                error_log("Processing event participant: " . print_r( "programLevels true", true));
                // Query program_id for the given program_level
                $level = ucfirst(strtolower($part[1])); // Normalize case
                $sql = "SELECT program_id, program_name FROM program WHERE program_level = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $level);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $program_id = $row['program_id'];
                    $program_name = $row['program_name'];

                    // Query students by program_id
                    $sqlStudents = "SELECT * FROM student WHERE program_id = ?  ORDER BY `student`.`year/grade_level` ASC";
                    $stmtStudents = $conn->prepare($sqlStudents);
                    $stmtStudents->bind_param("i", $program_id);
                    $stmtStudents->execute();
                    $resultStudents = $stmtStudents->get_result();

                    while ($student = $resultStudents->fetch_assoc()) {
                        // Fetch student details using user_id
                        $studentDetails = fetchStudentName($student['user_id'], $conn);
                        // Add the student details to the $students array
                        $students[] = $studentDetails;
                        $participants[$level][$program_name][$student['year/grade_level']][$student['section']][] = $student;
                    }
                }
            } else {
                error_log("Processing event participant: " . print_r( $part[1], true));
                // Handle when the second part is a program_name
                $programName = trim($part[1]);
                $sql = "SELECT program_id FROM program WHERE program_name = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $programName);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $program_id = $row['program_id'];

                    // Query students by program_id
                    $sqlStudents = "SELECT * FROM student WHERE program_id = ?  ORDER BY `student`.`year/grade_level` ASC";
                    $stmtStudents = $conn->prepare($sqlStudents);
                    $stmtStudents->bind_param("i", $program_id);
                    $stmtStudents->execute();
                    $resultStudents = $stmtStudents->get_result();

                    while ($student = $resultStudents->fetch_assoc()) {
                        // Fetch student details using user_id
                        $studentDetails = fetchStudentName($student['user_id'], $conn);
                        // Add the student details to the $students array
                        $students[] = $studentDetails;
                        $participants[$programName][$student['year/grade_level']][$student['section']][] = $student;
                    }
                }
            }
        } else {
            error_log("Processing event participant: " . print_r( "all false", true));
            // Handle when the first part is program_name and second is year/grade_level
            $programName = trim($part[0]);
            $yearGradeLevel = trim($part[1]);
            $section = trim($part[2]) ?? null;

            $sql = "SELECT program_id FROM program WHERE program_name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $programName);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $program_id = $row['program_id'];

                // Query students by program_id, year/grade_level, and optionally section
                $sqlStudents = "SELECT * FROM student WHERE program_id = ? AND `year/grade_level` = ?  ORDER BY `student`.`year/grade_level` ASC";
                if ($section) {
                    $sqlStudents .= " AND section = ?";
                }
                $stmtStudents = $conn->prepare($sqlStudents);
                if ($section) {
                    $stmtStudents->bind_param("iss", $program_id, $yearGradeLevel, $section);
                } else {
                    $stmtStudents->bind_param("is", $program_id, $yearGradeLevel);
                }
                $stmtStudents->execute();
                $resultStudents = $stmtStudents->get_result();

                while ($student = $resultStudents->fetch_assoc()) {
                    // Fetch student details using user_id
                    $studentDetails = fetchStudentName($student['user_id'], $conn);
                    // Add the student details to the $students array
                    $students[] = $studentDetails;
                
                    // Add the student to the participants array
                    $participants[$programName][$yearGradeLevel][$section][] = $student;
                
                }
            }
        }
    }

    // Fetch attendance data
    $sqlAttendance = "SELECT * FROM attendance WHERE event_id = ?";
    $stmt = $conn->prepare($sqlAttendance);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attendance[] = $row;
    }

    // Return response
    echo json_encode([
        'success' => true,
        'event_participant' => [
            'participant' => $participants,
            'attendance' => $attendance,
            'students' => $students 
        ]
    ]);
    // Close the database connection
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No event_id provided']);
}
// Function to fetch student name from the user table (using user_id)
function fetchStudentName($user_id, $conn) {
    $firstname = "";
    $middlename = "";
    $lastname = "";
    $suffixname = "";
    // Query to select first name, middle name, last name, and suffix
    $query = "SELECT  user_firstname, user_middlename, user_lastname, user_suffixname FROM user WHERE user_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Bind result variables for all selected columns
        $stmt->bind_result($firstname, $middlename, $lastname, $suffixname);

        if ($stmt->fetch()) {
            // Return an object with all student details if record is found
            return (object) [
                'user_id' => $user_id,
                'full_name' => $firstname . ' ' . $middlename . ' ' . $lastname . ' ' . $suffixname,
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'suffixname' => $suffixname
            ];
        } else {
            // Return an object with default values if no record found
            return (object) [
                'user_id' => $user_id,
                'full_name' => 'Unknown Student',
                'firstname' => 'Unknown',
                'middlename' => '',
                'lastname' => 'Student',
                'suffixname' => ''
            ];
        }

        $stmt->close();
    }
}
?>
