<?php
// Start the session
session_start();

// Include the Google API client library
require __DIR__ . '/vendor/autoload.php';
// Set up the Google API client
$client = new Google_Client();
$client->setApplicationName('Google Calendar API PHP Quickstart');
$client->setScopes(Google_Service_Calendar::CALENDAR_EVENTS);
$client->setAuthConfig('client_secret.json');
$client->setAccessType('offline');

// If the user is not authenticated, redirect to the login page
if (!isset($_SESSION['access_token'])) {
  header('Location: login.php');
  exit;
}

// Set the access token for the Google API client
$client->setAccessToken($_SESSION['access_token']);

// Create a Google Calendar API client
$service = new Google_Service_Calendar($client);

// Set the timezone to India
$timezone = new DateTimeZone('Asia/Kolkata');
$date = isset($_POST['date']) ? $_POST['date'] : '';
$time = isset($_POST['time']) ? $_POST['time'] : '';

if (!empty($date) && !empty($time)) {
  // Format the selected date and time as RFC3339 datetime
  $startDateTime = new DateTime($date . ' ' . $time, $timezone);
  $endDateTime = clone $startDateTime;
  $endDateTime->add(new DateInterval('PT1H'));

  // Set the event details
  $event = new Google_Service_Calendar_Event(array(
    'summary' => 'New Meeting',
    'description' => 'This is a test meeting created using the Google Calendar API',
    'start' => array(
      'dateTime' => $startDateTime->format(DateTime::RFC3339),
      'timeZone' => $timezone->getName(),
    ),
    'end' => array(
      'dateTime' => $endDateTime->format(DateTime::RFC3339),
      'timeZone' => $timezone->getName(),
    ),
    'conferenceData' => array(
      'createRequest' => array(
        'conferenceSolutionKey' => array(
          'type' => 'hangoutsMeet'
        ),
        'requestId' => uniqid(),
      ),
    ),
  ));

  // Insert the event into the user's calendar and get the generated meet link
  $event = $service->events->insert('primary', $event, array('conferenceDataVersion' => 1));
  $meetLink = $event->getHangoutLink();
}

// Render the page
?>
<!DOCTYPE html>
<html>
<head>
  <title>Google Calendar API Quickstart</title>
</head>
<body>
  <?php if (isset($meetLink)) { ?>
    <h1>Meeting created successfully!</h1>
    <p>Meet Link: <?php echo $meetLink; ?></p>
  <?php } else { ?>
    <h1>Create a new meeting</h1>
    <form method="post">
      <label for="date">Date:</label>
      <input type="date" id="date" name="date"><br><br>
      <label for="time">Time:</label>
      <input type="time" id="time" name="time"><br><br>
      <input type="submit" value="Create Meeting">
    </form>
  <?php } ?>
</body>
</html>
