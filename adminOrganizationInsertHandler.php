<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'dbh.php';

    $organizationName = filter_input(INPUT_POST, 'organization_name', FILTER_SANITIZE_STRING);
    $organizationResponsibility = filter_input(INPUT_POST, 'organization_responsibility', FILTER_SANITIZE_STRING);
    $organizationPosition = filter_input(INPUT_POST, 'organization_positions', FILTER_SANITIZE_STRING);

    if (empty($organizationName) || empty($organizationResponsibility) || empty($organizationPosition)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit;
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO organization (organization_name, organization_responsibility, organization_list_of_position) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL error: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "sss", $organizationName, $organizationResponsibility, $organizationPosition);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Organization added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Execution error: ' . mysqli_stmt_error($stmt)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

?>