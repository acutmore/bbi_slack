<?php

require 'vendor/autoload.php';

# Grab some of the values from the slash command, create vars for post back to Slack
$command = $_POST['command'];
$text = $_POST['text'];
$token = $_POST['token'];

# Check the token and make sure the request is from our team
if($token != 'vnLfaOlI7natbpU5tKQBm5dQ'){
  $msg = "Invalid token";
//  die($msg);
//  echo $msg;
}

// TODO - search for issue

# Send the reply back to the user.
$reply = "No issues found";
echo $reply;

?>
