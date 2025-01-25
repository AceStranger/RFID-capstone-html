<?php
include 'dbh.php';

$program = isset($_GET['program']) ? $_GET['program'] : 'all';
$column = isset($_GET['column']) ? $_GET['column'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build your SQL query dynamically based on filters
$where = "WHERE user.user_role LIKE '%student%'";

if ($program !== 'all') {
    $where .= $program === 'None' ? " AND (student.program_id IS NULL OR student.program_id = '')" : " AND student.program_id = '$program'";
}

if ($column !== 'none' && !empty($search)) {
    switch ($column) {
        case 'School ID':
            $where .= " AND user.user_school_id = '$search'";
            break;
        case 'Full Name':
            $where .= " AND (LOWER(user.user_firstname) LIKE LOWER('%$search%') OR LOWER(user.user_lastname) LIKE LOWER('%$search%'))";
            break;
        case 'year/gradeLevel':
            $where .= " AND student.year LIKE '%$search%'";
            break;
        case 'section':
            $where .= " AND student.section LIKE '%$search%'";
            break;
    }
}

// Query the database for users based on the filters
$sql = "SELECT user.*, student.*, program.program_name FROM user 
        LEFT JOIN student ON user.user_id = student.user_id
        LEFT JOIN program ON student.program_id = program.program_id
        $where";

$result = mysqli_query($conn, $sql);
$data = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
