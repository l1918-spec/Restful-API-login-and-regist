<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');
require __DIR__ . '/classes/db.php';
require __DIR__ . '/classes/jwtHandler.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

function msg($success, $status, $message, $extra = []) {
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message,
    ], $extra);
}

// Get data from request
$data = json_decode(file_get_contents('php://input'));
$returnData = [];

// Check request method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $returnData = msg(0, 404, 'Page not found');
} elseif (
    !isset($data->name) ||
    !isset($data->email) ||
    !isset($data->password) ||
    empty(trim($data->name)) ||
    empty(trim($data->email)) ||
    empty(trim($data->password))
) {
    $fields = ['fields' => ['name', 'email', 'password']];
    $returnData = msg(0, 422, 'Please fill in the required fields', $fields);
} else {
    $name = trim($data->name);
    $email = trim($data->email);
    $password = trim($data->password);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'Invalid email address');
    // Validate password strength
    } elseif (strlen($password) < 8) {
        $returnData = msg(0, 422, 'Your password must be at least 8 characters');
    // Validate name length
    } elseif (strlen($name) < 2) {
        $returnData = msg(0, 422, 'Your name must be at least 2 characters');
    } else {
        try {
            // Check if the email already exists
            $check_email = "SELECT email FROM users WHERE email = :email";
            $check_email_stm = $conn->prepare($check_email);
            $check_email_stm->bindValue(':email', $email, PDO::PARAM_STR);
            $check_email_stm->execute();

            if ($check_email_stm->rowCount()) {
                $returnData = msg(0, 422, 'This email has already been used');
            } else {
                // Insert new user
                $insert_query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
                $insert_stm = $conn->prepare($insert_query);
                
                // Data binding
                $insert_stm->bindValue(':name', htmlspecialchars(strip_tags($name)), PDO::PARAM_STR);
                $insert_stm->bindValue(':email', $email, PDO::PARAM_STR);
                $insert_stm->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                
                $insert_stm->execute();
                $returnData = msg(1, 201, 'You have successfully registered');
            }
        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}

echo json_encode($returnData);
?>