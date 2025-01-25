<?php
session_start();
include "dbh.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dSubmit'])) {
    $department_id = filter_input(INPUT_POST, 'department-id', FILTER_SANITIZE_NUMBER_INT);

    if (!$department_id) {
        echo "Invalid input.";
        exit;
    }

    // Use prepared statement to delete securely
    $stmt = $conn->prepare("DELETE FROM department WHERE department_id = ?");
    $stmt->bind_param("i", $department_id);

    if ($stmt->execute()) {
        header("Location: adminDepartments.php?message=Department+deleted+successfully");
    } else {
        echo "Failed to delete department.";
    }

    $stmt->close();
    $conn->close();
}
?>
