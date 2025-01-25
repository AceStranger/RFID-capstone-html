<?php
include "dbh.php";

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Query to select first name, middle name, last name, and suffix
    $query = "SELECT user_firstname, user_middlename, user_lastname, user_suffixname FROM user WHERE user_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Bind result variables for all selected columns
        $stmt->bind_result($firstname, $middlename, $lastname, $suffixname);

        if ($stmt->fetch()) {
            // If record is found, return all relevant details
            echo json_encode([
                'user_firstname' => $firstname,
                'user_middlename' => $middlename,
                'user_lastname' => $lastname,
                'user_suffixname' => $suffixname
            ]);
        } else {
            // If no record found, return default "Unknown Student"
            echo json_encode([
                'user_firstname' => 'Unknown',
                'user_middlename' => '',
                'user_lastname' => 'Student',
                'user_suffixname' => ''
            ]);
        }

        $stmt->close();
    }
}
?>
