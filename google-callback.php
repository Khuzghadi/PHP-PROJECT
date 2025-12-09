<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/google-api-php-client/vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('893942546879-32236p7a4defodh60gu72hhbuhf9dqtc.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/NZ-IMS/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {

        $client->setAccessToken($token['access_token']);

        $oauth = new Google_Service_Oauth2($client);
        $google_user = $oauth->userinfo->get();

        // Google user fields
        $email = $google_user->email;
        $name  = $google_user->name;  // FIXED

        // Check if user exists
        $query = $conn->prepare("SELECT * FROM users WHERE email=?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows == 0) {
            // Create a new user
            $default_role = "user";   // CHANGE IF YOU WANT ADMIN
            $default_zone = "";

            $insert = $conn->prepare(
                "INSERT INTO users (username, email, role, zone_name, password) VALUES (?, ?, ?, ?, '')"
            );
            $insert->bind_param("ssss", $name, $email, $default_role, $default_zone);
            $insert->execute();
        }

        // Fetch user
        $query = $conn->prepare("SELECT * FROM users WHERE email=?");
        $query->bind_param("s", $email);
        $query->execute();
        $user = $query->get_result()->fetch_assoc();

        // Set session like login.php does
        $_SESSION['user']      = $user['username'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['zone_name'] = $user['zone_name'];

        // Update force logout
        $conn->query("UPDATE users SET force_logout = 0 WHERE user_id = " . $user['user_id']);

        // Redirect based on role
        if ($_SESSION['role'] == 'admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();

    } else {
        echo "Error fetching token: " . $token['error'];
    }

} else {
    echo "No authorization code returned.";
}
?>
