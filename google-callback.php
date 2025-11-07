<?php
session_start();
require_once __DIR__ . '/google-api-php-client/vendor/autoload.php';
require_once 'db.php';

$client = new Google_Client();
$client->setClientId('893942546879-32236p7a4defodh60gu72hhbuhf9dqtc.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/NZ-IMS/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($client);
        $data = $google_service->userinfo->get();

        $email = $data['email'];
        $name = $data['name'];
        $picture = $data['picture'];

        $query = $conn->prepare("SELECT * FROM zonal_head WHERE email=?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;
            $_SESSION['picture'] = $picture;
            header('Location: frame.php');
            exit();
        } else {
            echo "Access denied: This Gmail is not registered by admin.";
            exit();
        }
    } else {
        echo "Error fetching token: " . $token['error_description'];
    }
} else {
    header('Location: login.php');
    exit();
}
?>
