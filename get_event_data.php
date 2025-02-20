<?php

header('Content-Type: application/json');

require_once 'dbh.php'; // Adjust the path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = isset($_POST['report-content-filter-select-event']) ? (int)$_POST['report-content-filter-select-event'] : null;

    if (!$eventId) {
        echo json_encode(['error' => 'Event ID is required']);
        exit;
    }

    try {

        // Get event details
        $eventQuery = "SELECT * FROM event WHERE event_id = ? LIMIT 1";
        $stmt = $conn->prepare($eventQuery);
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
        $eventDetails = $stmt->get_result()->fetch_assoc();
        
        if (!$eventDetails) {
            echo json_encode(['error' => 'Event not found']);
            exit;
        }

        // Get attendance data
        $attendanceQuery = "SELECT * FROM attendance WHERE event_id = ?";
        $stmt = $conn->prepare($attendanceQuery);
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
        $attendanceData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Process event participants
        $participants = [];
        $eventParticipants = explode(',', $eventDetails['event_participant']);

        foreach ($eventParticipants as $participant) {
            $parts = array_map('trim', explode('-', $participant));

            if (strcasecmp($parts[0], 'all') === 0) {
                if (isset($parts[1]) && isProgramLevel($parts[1])) {
                    $participants = array_merge($participants, getParticipantsByProgramLevel($parts[1], $conn));
                } else if (isset($parts[1])) {
                    $participants = array_merge($participants, getParticipantsByProgramName($parts[1], $conn));
                }
            } else {
                $programName = $parts[0];
                $yearLevel = $parts[1] ?? null;
                $section = $parts[2] ?? null;
                $participants = array_merge($participants, getParticipantsByYearAndSection($programName, $yearLevel, $section, $conn));
            }
        }
        // Process event participants and fetch program details
        $programQuery = "SELECT * FROM program"; // Get all program data
        $programResult = $conn->query($programQuery);
        $programs = $programResult->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            'event' => $eventDetails,
            'attendance' => $attendanceData,
            'participants' => $participants,
            'programs' => $programs // Include program data
        ], JSON_PRETTY_PRINT);

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function isProgramLevel($level) {
    $validLevels = ['College', 'college', 'Secondary', 'secondary', 'Primary', 'primary'];
    return in_array($level, $validLevels, true);
}

function getParticipantsByProgramLevel($level, $conn) {
    $query = "SELECT * FROM program WHERE program_level = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $level);
    $stmt->execute();
    $programs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $participants = [];
    foreach ($programs as $program) {
        $participants = array_merge($participants, getParticipantsByProgramId($program['program_id'], $conn));
    }

    return $participants;
}

function getParticipantsByProgramName($programName, $conn) {
    $query = "SELECT * FROM program WHERE program_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $programName);
    $stmt->execute();
    $program = $stmt->get_result()->fetch_assoc();

    return $program ? getParticipantsByProgramId($program['program_id'], $conn) : [];
}

function getParticipantsByYearAndSection($programName, $yearLevel, $section, $conn) {
    if ($section === null || $section === '') {
        $query = "
            SELECT s.* FROM student s
            JOIN program p ON s.program_id = p.program_id
            WHERE p.program_name = ? AND s.`year/grade_level` = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $programName, $yearLevel);
    } else {
        $query = "
            SELECT s.* FROM student s
            JOIN program p ON s.program_id = p.program_id
            WHERE p.program_name = ? AND s.`year/grade_level` = ? AND s.section = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $programName, $yearLevel, $section);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


function getParticipantsByProgramId($programId, $conn) {
    $query = "SELECT * FROM student WHERE program_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $programId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

?>
