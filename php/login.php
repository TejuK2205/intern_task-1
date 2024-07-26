<?php

require_once 'C:/xampp/htdocs/Login_registerpage/vendor/autoload.php';
use Predis\Client;

$response = ['success' => false];

$host = "localhost";
$username = "root";
$password = "";
$dbname = "profile_db";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $loginUsername = $_POST['username'];
    $loginPassword = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE user = :username AND pass = :password");
    $stmt->bindParam(':username', $loginUsername);
    $stmt->bindParam(':password', $loginPassword);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        
        $timestamp = time();
        $data = $loginUsername . '|' . $timestamp;
        $authToken = base64_encode($data);

        
        $redis = new Client();
        $redis->setex("auth:$authToken", 3600, $loginUsername);

        $response['success'] = true;
        $response['token'] = $authToken;
        $response['message'] = 'Login successful.';
    } else {
        $response['message'] = 'Invalid username or password.';
    }
} catch (PDOException $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
