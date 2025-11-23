<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/google-api-php-client/vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('893942546879-32236p7a4defodh60gu72hhbuhf9dqtc.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/NZ-IMS/google-callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        $oauth = new Google_Service_Oauth2($client);
        $google_user = $oauth->userinfo->get();

        $email = $google_user->email;
        $name = $google_user->name;
        $picture = $google_user->picture;

        $query = $conn->prepare("SELECT * FROM zonal_head WHERE email=?");
        if (!$query) {
            die("Prepare failed: " . $conn->error);
        }
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO zonal_head (name, email, picture) VALUES (?, ?, ?)");
            if (!$insert) {
                die("Prepare failed: " . $conn->error);
            }
            $insert->bind_param("sss", $name, $email, $picture);
            $insert->execute();
        }
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
        $_SESSION['picture'] = $picture;
        

        header('Location: frame.php');
        exit();
    } else {
        echo "Error fetching token: " . $token['error'];
    }
} else {
    echo "No authorization code returned.";
}
?>
