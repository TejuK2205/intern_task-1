<?php

require_once 'C:/xampp/htdocs/Login_registerpage/vendor/autoload.php';
use Predis\Client;
use MongoDB\Client as MongoClient;

$response = ['success' => false];

$authToken = $_GET['token'] ?? '';

if ($authToken) {
    try {
        $redis = new Client();

        $decodedData = base64_decode($authToken);

        
        list($loginUsername, $timestamp) = explode('|', $decodedData);

        
        if (time() - $timestamp > 3600) {
            $response['message'] = 'Token expired.';
        } else {
            
            $storedUsername = $redis->get("auth:$authToken");

            if ($storedUsername && $storedUsername === $loginUsername) {
                $profile = $redis->hgetall("profile:$loginUsername");

                if ($profile) {
                    $response['success'] = true;
                    $response['source'] = 'redis';
                    $response['name'] = $profile['name'];
                    $response['age'] = $profile['age'];
                    $response['contact'] = $profile['contact'];
                    $response['username'] = $profile['username'];
                } else {
                    
                    $mongoClient = new MongoClient("mongodb://localhost:27017");
                    $mongoDbName = "profile_db";
                    $mongoCollectionName = "profiles";

                    $db = $mongoClient->$mongoDbName;
                    $collection = $db->$mongoCollectionName;

                    $user = $collection->findOne(['username' => $loginUsername]);

                    if ($user) {
                        $response['success'] = true;
                        $response['source'] = 'mongodb';
                        $response['name'] = $user['name'];
                        $response['age'] = $user['age'];
                        $response['contact'] = $user['contact'];
                        $response['username'] = $user['username'];

                        
                        $profile = [
                            'name' => $user['name'],
                            'age' => $user['age'],
                            'contact' => $user['contact'],
                            'username' => $user['username']
                        ];

                        $redis->hmset("profile:$loginUsername", $profile);
                        $redis->expire("profile:$loginUsername", 3600);
                    } else {
                        $response['message'] = 'User not found in MongoDB.';
                    }
                }
            } else {
                $response['message'] = 'Invalid token or username mismatch.';
            }
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Token required.';
}


header('Content-Type: application/json');
echo json_encode($response);
?>
