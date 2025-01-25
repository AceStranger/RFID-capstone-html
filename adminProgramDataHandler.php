<?php
session_start();
include "dbh.php";

// Verify if user is authenticated
if (!isset($_SESSION['user_ID'])) {
    header("Location: LogInPage.html");
    exit();
}

if (isset($_POST['dSubmit'])) {
    $program_id = filter_input(INPUT_POST, 'program-id', FILTER_SANITIZE_NUMBER_INT);

    if ($program_id) {
        $deleteQuery = "DELETE FROM program WHERE program_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $program_id);

        if ($stmt->execute()) {
            header("Location: adminPrograms.php?message=deleted");
        } else {
            header("Location: adminPrograms.php?message=error");
        }
    } else {
        header("Location: adminPrograms.php?message=invalid");
    }
}
?>
