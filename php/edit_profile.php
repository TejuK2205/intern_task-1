<?php

require_once 'C:/xampp/htdocs/Login_registerpage/vendor/autoload.php';
use MongoDB\Client as MongoClient;
use Predis\Client as RedisClient;

$mongoClient = new MongoClient("mongodb://localhost:27017");
$redis = new RedisClient();

$mongoDbName = "profile_db";
$mongoCollectionName = "profiles";


$response = ['success' => false];

if (isset($_POST['token'])) {
    $authToken = $_POST['token'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $contact = $_POST['contact'];

    try {
        
        $username = $redis->get("auth:$authToken");

        if ($username) {
            $db = $mongoClient->$mongoDbName;
            $collection = $db->$mongoCollectionName;

            
            $updateResult = $collection->updateOne(
                ['username' => $username],
                ['$set' => ['name' => $name, 'age' => $age, 'contact' => $contact]]
            );

            if ($updateResult->getMatchedCount() > 0) {
                
                $profile = [
                    'name' => $name,
                    'age' => $age,
                    'contact' => $contact,
                    'username' => $username
                ];

                $redis->hmset("profile:$username", $profile);
                $redis->expire("profile:$username", 3600);

                $response['success'] = true;
                $response['message'] = 'Profile updated successfully';
            } else {
                $response['message'] = 'Profile update failed. Username not found in MongoDB.';
            }
        } else {
            $response['message'] = 'Invalid token.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error updating profile: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Token parameter not provided.';
}


header('Content-Type: application/json');
echo json_encode($response);
?>
