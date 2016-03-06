<?php

require 'vendor/autoload.php';
require 'CONFIG.php';
require 'workers.php';
require 'storage.php';

# Grab some of the values from the slash command
$command = $_POST['command'];
$text = $_POST['text'];
$token = $_POST['token'];
$response_url = $_POST['response_url'];
$user_id = $_POST['user_id'];

# Check the token and make sure the request is from our team
if(!array_key_exists($token, $bbi_slack_config)){
    die("Invalid token");
}

# Check if the user is telling us their bitbucket username
$bb_user;
$storage = new Storage('bb_usernames');

if (0 === strpos($text, 'i am ')) {
  $bb_user = substr($text, 5);
  $storage.put($user_id, $bb_user);
  exit ("Thank you. I'll do my best to remember that you are '" . $bb_user . "'");
}

# Otherwise look up coresponding bitbucket username for slack user_id
if ($bb_user == NULL){
  $bb_user = $storage->get($user_id);

  if ($bb_user == NULL){
      # Ask user for their bitbucket username
     exit("I need to know your Bitbucket username. type  `" . $command . " i am [username]`");
  }
}

# Parse the slack command text
# e.g /findmyissues foo crashes page 2
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
Workers::sendMessage('worker_find_issue', array(
  'response_url' => $response_url,
  'page' => $page,
  'title' => trim($text),
  'command' => $command,
  'token' => $token,
  'user' => $bb_user,
));

# Let user know we are working on it
echo 'searching...';

?>
