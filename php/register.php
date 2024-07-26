<?php

require_once 'C:/xampp/htdocs/Login_registerpage/vendor/autoload.php';
use MongoDB\Client;

$response = ['success' => false];

$host = "localhost";
$username = "root";
$password = "";
$dbname = "profile_db";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $name = $_POST['name'];
    $age = $_POST['age'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password)) {
        $response['message'] = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
    } else {
        
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $response['message'] = 'Username already exists in MySQL.';
        } else {
            
            $stmt = $conn->prepare("INSERT INTO users (user, pass) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);

            if ($stmt->execute()) {
                
                $mongoClient = new Client("mongodb://localhost:27017");
                $mongoDbName = "profile_db";
                $mongoCollectionName = "profiles";

                $db = $mongoClient->$mongoDbName;
                $collection = $db->$mongoCollectionName;

                $insertResult = $collection->insertOne([
                    'name' => $name,
                    'age' => $age,
                    'contact' => $contact,
                    'email' => $email,
                    'username' => $username,
                    'password' => $password
                ]);

                if ($insertResult->getInsertedCount() > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Registration successful.';
                } else {
                    $response['message'] = "Error inserting data into MongoDB.";
                }
            } else {
                $response['message'] = 'Error inserting data into MySQL.';
            }
        }
    }
} catch (PDOException $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
