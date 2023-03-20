<?php
// Start the session
session_start();

// Include the Google API client library
require __DIR__ . '/vendor/autoload.php';


// Set up the Google API client
$client = new Google_Client();
$client->setClientId('u r clientid');
$client->setClientSecret('u r client secret');
$client->setRedirectUri('http://localhost/oauth2callback.php');
$client->addScope('https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/calendar.events');

// If the user is already authenticated, redirect to the home page
if (isset($_SESSION['access_token'])) {
  header('Location: gmeetLink.php');
  exit;
}

// If the user clicks the "Sign in with Google" button
if (isset($_GET['code'])) {
  // Exchange the authorization code for an access token
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

  // Store the access token in the session
  $_SESSION['access_token'] = $token;

  // Redirect to the home page
  header('Location: gmeetLink.php');
  exit;
}

// Generate the Google OAuth URL
$auth_url = $client->createAuthUrl();

// Render the login page
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login with Google</title>
</head>
<body>
  <h1>Login with Google</h1>
  <p>Please click the button below to sign in with your Google account:</p>
  <a href="<?php echo $auth_url; ?>">Sign in with Google</a>
</body>
</html>
