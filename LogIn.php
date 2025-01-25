<?php
session_start(); // Start the session first
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'dbh.php';

    $username = filter_input(INPUT_POST, 'log-in-content-input-username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'log-in-content-input-password', FILTER_SANITIZE_STRING);

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Fill in All Fields']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE user_name = ? OR user_school_id = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_ID'] = $user['user_id'];
            $_SESSION['user_ROLE'] = $user['user_role'];

            $currentDate = date("Y-m-d H:i:s");
            $updateStmt = $conn->prepare("UPDATE user SET user_last_login_date = ? WHERE user_id = ?");
            $updateStmt->bind_param("si", $currentDate, $user['user_id']);

            if ($updateStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Log-in successful']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error updating last login date']);
            }
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Incorrect password']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Username or School ID not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    http_response_code(405);
}
?>
