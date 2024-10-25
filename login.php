<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Methods: access');
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

// Get JSON data from the request
$data = json_decode(file_get_contents('php://input'));
$returnData = [];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $returnData = msg(0, 404, 'Page not found');
} elseif (
    !isset($data->email) || !isset($data->password) ||
    empty(trim($data->email)) || empty(trim($data->password))
) {
    $fields = ['fields' => ['email', 'password']];
    $returnData = msg(0, 422, 'Please fill in the required fields', $fields);
} else {
    $email = trim($data->email);
    $password = trim($data->password);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'Invalid email address');
    } elseif (strlen($password) < 8) { // Password strength check
        $returnData = msg(0, 422, 'Your password must be at least 8 characters');
    } else {
        try {
            $fetch_user_by_email = "SELECT * FROM users WHERE email = :email";
            $query_stm = $conn->prepare($fetch_user_by_email);
            $query_stm->bindValue(':email', $email, PDO::PARAM_STR);
            $query_stm->execute();

            // Check if the user exists
            if ($query_stm->rowCount()) {
                $row = $query_stm->fetch(PDO::FETCH_ASSOC);

                // Log password details for debugging
                error_log("User password (hashed): " . $row['password']);
                error_log("Submitted password: " . $password);

                // Verify password
                if (password_verify($password, $row['password'])) {
                    $jwt = new JwtHandler();
                    $token = $jwt->jwtEncodeData(
                        'http://localhost/php_auth_api',
                        ['user_id' => $row['id']]
                    );

                    $returnData = [
                        'success' => 1,
                        'message' => 'You have successfully logged in',
                        'token' => $token
                    ];
                } else {
                    $returnData = msg(0, 422, 'Invalid password. Please try again.');
                }
            } else {
                $returnData = msg(0, 422, 'Invalid email address.');
            }
        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}

echo json_encode($returnData);
?>
