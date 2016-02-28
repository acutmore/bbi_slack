<?php

require 'vendor/autoload.php';
require 'CONFIG.php';

ignore_user_abort(true);
set_time_limit(30);

$response_url = $_GET['response_url'];
$limit = $_GET['limit'];
$start = $_GET['start'];
$title = $_GET['title'];

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
    'limit' => $limit,
    'start' => $start,
    'title' => $title
));

# Parse the JSON
$data = json_decode($results->getContent());
$formatted_results = $data->count . " results\r\n\r\n";

function urlForIssue($issue){
    global $bbi_slack_config; 

    return 'https://bitbucket.org/'
      . $bbi_slack_config['bb_account']
      . '/'
      . $bbi_slack_config['bb_repo']
      . '/issues/'
      . $issue;
}

foreach ($data->issues as $issue){
    $formatted_results .= ('<'.urlForIssue($issue->local_id).'|#'.$issue->local_id.' '.$issue->title.">\r\n");
}

# Send results to Slack
$jsonPayload = json_encode([
    text => $formatted_results,
]);

echo $jsonPayload;

if (!empty($response_url)){
  $headers = ['Content-Type', 'application/json'];
  $browser = new Buzz\Browser();
  $browser->post($response_url, $headers, $jsonPayload);
}

?>
