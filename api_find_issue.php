<?php

require 'vendor/autoload.php';
require 'CONFIG.php';
require 'workers.php';
require 'util.php';

# Grab some of the values from the slash command
$command = $_POST['command'];
$text = $_POST['text'];
$token = $_POST['token'];
$response_url = $_POST['response_url'];

# Check the token and make sure the request is from our team
if(!array_key_exists($token, $bbi_slack_config)){
    die("Invalid token");
}

$issueNumber = -1;
if (0 === strrpos($text, "#")){
  $issueNumber = intval(substr($text, 1));
}

if ($issueNumber > 0){
  // Get config
  $config = $bbi_slack_config[$token];

  # Connect to Bitbucket
  $oauth_params = array(
        'client_id'         => $config['bb_client_id'],
        'client_secret'     => $config['bb_client_secret']
  );

  $issues = new Bitbucket\API\Repositories\Issues();
  $issues->getClient()->addListener(
      new \Bitbucket\API\Http\Listener\OAuth2Listener($oauth_params)
  );

  $results = $issues->get($config['bb_account'], $config['bb_repo'], $issueNumber);

  $data = json_decode($results->getContent());

  echo slackUrlForIssue($data->local_id, $data->title);
  return;
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
Workers::sendMessage('worker_find_issue', array(
  'response_url' => $response_url,
  'page' => $page,
  'title' => trim($text),
  'command' => $command,
  'token' => $token,
));

# Let user know we are working on it
echo 'searching...';

?>
