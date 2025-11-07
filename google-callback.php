<?php
session_start();
require_once __DIR__ . '/google-api-php-client/vendor/autoload.php';
require_once 'db.php';

$client = new Google_Client();
$client->setClientId('893942546879-32236p7a4defodh60gu72hhbuhf9dqtc.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/NZ-IMS/google-callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($client);
        $data = $google_service->userinfo->get();

        $email = $data['email'];
        $name = $data['name'];

        $query = $conn->prepare("SELECT * FROM zonal_head WHERE email=?");
       
        if (!$query) {
            die("SQL prepare failed: " . $conn->error);
        }
 
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['user'] = $result->fetch_assoc();
            header('Location: welcome.php');
            exit();
        } else {
            echo "Access denied: This Gmail is not registered by admin.";
            exit();
        }
    } else {
        echo "Invalid token response.";
    }
} else {
    echo "No code parameter received.";
}
?>
