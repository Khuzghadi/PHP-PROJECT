<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/google-api-php-client/vendor/autoload.php';  // adjust path if needed
require_once 'db.php';

$client = new Google_Client();
$client->setClientId('893942546879-32236p7a4defodh60gu72hhbuhf9dqtc.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/NZ-IMS/google-callback.php');  // ensure this matches your server setup
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        die('Token error: ' . htmlspecialchars($token['error_description']));
    }

    $client->setAccessToken($token['access_token']);

    $google_service = new Google_Service_Oauth2($client);
    $data = $google_service->userinfo->get();

    $email = $data->email;
    $name  = $data->name;

    if (!$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?")) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
        header('Location: welcome.php');
        exit();
    } else {
        echo "Access denied: This Gmail is not registered by admin.";
        exit();
    }
} else {
    echo "No code parameter returned from Google.";
    exit();
}
?>
