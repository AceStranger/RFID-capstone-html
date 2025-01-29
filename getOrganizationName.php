<?php
// Include database connection
include('dbh.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if organizerId is provided
    if (isset($data['organizerId'])) {
        $organizerId = $data['organizerId'];

        // Validate the input (sanitize and type-check)
        if (is_numeric($organizerId)) {
            try {
                // Prepare the SQL query to fetch the organization name securely
                $stmt = $conn->prepare("SELECT organization_name FROM organization WHERE organization_id = ?");
                $stmt->bind_param('i', $organizerId); // 'i' is the type for integer
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if the organization exists
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo json_encode(['organization_name' => $row['organization_name']]);
                } else {
                    // Organization not found
                    echo json_encode(['message' => 'Organization not found']);
                }

                $stmt->close();
            } catch (Exception $e) {
                // Error occurred
                echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['message' => 'Invalid organizer ID']);
        }
    } else {
        echo json_encode(['message' => 'Missing organizer ID']);
    }
} else {
    // Handle invalid request method
    echo json_encode(['message' => 'Invalid request method']);
}
?>
