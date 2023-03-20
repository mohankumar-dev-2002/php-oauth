<?php
require_once 'vendor/autoload.php'; // Include Google API PHP client library
require_once 'login.php'; // Include client configuration and setup

// Start session (if not started already)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['code'])) {
    $authCode = $_GET['code'];

    // Exchange authorization code for access token and refresh token
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Save access token and refresh token in session or database for later use
    $_SESSION['access_token'] = $accessToken['access_token'];
    $_SESSION['refresh_token'] = $accessToken['refresh_token'];

    // Include login logic here (use $_SESSION['access_token'] and $_SESSION['refresh_token'])

    // Redirect user to home page or any other page
    header('Location: /gmeetLink.php');
    exit();
} else {
    // Handle error if authorization code is not present
    echo 'Error: Authorization code not found';
    exit();
}
?>

