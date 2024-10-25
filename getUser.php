<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access, Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=UTF-8');

require __DIR__ . '/classes/db.php';
require __DIR__ . '/AuthMiddleware.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$auth = new Auth($conn, $allHeaders);

// Check if the authentication is valid
$isValid = $auth->isValid();

if ($isValid) {
    // If valid, return a success message or user data
    echo json_encode(['success' => true, 'message' => 'Authenticated successfully']);
} else {
    // If not valid, return an error message
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
}
?>