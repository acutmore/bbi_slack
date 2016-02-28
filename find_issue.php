<?php

require 'vendor/autoload.php';
require 'CONFIG.php';
require 'workers.php';

# Grab some of the values from the slash command
$text = $_POST['text'];
$token = $_POST['token'];
$response_url = $_POST['response_url'];

# Check the token and make sure the request is from our team
if($token != $bbi_slack_config['slack_token']){
    die("Invalid token");
}

# Pass search to worker
Workers::sendMessage('find_issue_worker', array(
  'response_url' => $response_url,
  'limit' => 10,
  'start' => 0,
  'title' => '~' . $text
));

# Let user know we are working on it
echo 'searching...';

?>
