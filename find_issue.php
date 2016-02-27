<?php

require 'vendor/autoload.php';
require 'CONFIG.php';

# Grab some of the values from the slash command, create vars for post back to Slack
$command = $_POST['command'];
$text = $_POST['text'];
$token = $_POST['token'];
$response_url = $_POST['response_url'];

# Check the token and make sure the request is from our team
if($token != $bbi_slack_config['slack_token']){
    die("Invalid token");
}

# Connect to Bitbucket
$oauth_params = array(
      'client_id'         => $bbi_slack_config['bb_client_id'],
      'client_secret'     => $bbi_slack_config['bb_client_secret']
);

$issues = new Bitbucket\API\Repositories\Issues();
$issues->getClient()->addListener(
    new \Bitbucket\API\Http\Listener\OAuth2Listener($oauth_params)
);

# Perform the search
$results = $issues->all($bbi_slack_config['bb_account'], $bbi_slack_config['bb_repo'], array(
    'limit' => 5,
    'start' => 0,
    'title' => '~' . $text
));

# Parse the JSON
$data = json_decode($results->getContent());
$formatted_results = $data->count . " results\r\n\r\n";

foreach ($data->issues as $issue){
    $formatted_results .= ('#' . $issue->local_id . ' ' . $issue->title . "\r\n");
}

# Send results to slack asynchronously
$jsonPayload = json_encode([
    text => $formatted_results,
]);

$headers = ['Content-Type', 'application/json'];
$browser = new Buzz\Browser();
$response = $browser->post($response_url, $headers, $jsonPayload);

?>
