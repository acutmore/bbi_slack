<?php

require 'vendor/autoload.php';
require 'CONFIG.php';
require 'workers.php';

# Grab some of the values from the slash command
$command = $_POST['command'];
$text = $_POST['text'];
$token = $_POST['token'];
$response_url = $_POST['response_url'];

# Check the token and make sure the request is from our team
if($token != $bbi_slack_config['slack_token']){
    die("Invalid token");
}

# Parse the slack command text
# e.g /findissue foo crashes page 2
$pagePos = strrpos($text, "page");
$page = 1;

if ($pagePos){
 $pg = intval(substr($text, $pagePos + 4));
 if ($pg > 0){
   $page = $pg;
   $text = substr($text, 0, $pagePos);
 }
}

# Pass search to worker
Workers::sendMessage('find_issue_worker', array(
  'response_url' => $response_url,
  'page' => $page,
  'title' => trim($text),
  'command' => $command
));

# Let user know we are working on it
echo 'searching...';

?>
