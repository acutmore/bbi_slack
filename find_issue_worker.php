<?php

require 'vendor/autoload.php';
require 'CONFIG.php';

ignore_user_abort(true); // GET'ing this script triggers it. Client can then close
set_time_limit(30); // Run for up to 30 seconds

$response_url = $_GET['response_url'];
$page = (int) $_GET['page'];
$title = $_GET['title'];
$command = $_GET['command'];
$token = $_GET['token'];

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

# Perform the search
$pageSize = 10;
$limit = $pageSize;
$start = $pageSize * ($page - 1);

$results = $issues->all($config['bb_account'], $config['bb_repo'], array(
    'limit' => $limit,
    'start' => $start,
    'title' => '~' . $title // containing $title
));

# Parse the JSON
$data = json_decode($results->getContent());
$formatted_results = '*Issues containing "'. $title . "\":*\r\n";

function urlForIssue($issue){
    global $config;

    return 'https://bitbucket.org/'
      . $config['bb_account']
      . '/'
      . $config['bb_repo']
      . '/issues/'
      . $issue;
}

foreach ($data->issues as $issue){
    $formatted_results .= ('<'.urlForIssue($issue->local_id).'|#'.$issue->local_id.' '.$issue->title.">\r\n");
}

if ((count($data->issues) + $start) < (int) $data->count){
  // There are more results that could be shown. Append nessesary command to show more
   $formatted_results .= "type  `" . $command . ' ' . $title . ' page ' . ($page + 1) . "`  for more results" ;
}

# Send results to Slack
$jsonPayload = json_encode([ text => $formatted_results ]);

echo $jsonPayload;

if (!empty($response_url)){
  $headers = ['Content-Type', 'application/json'];
  $browser = new Buzz\Browser();
  $browser->post($response_url, $headers, $jsonPayload);
}

?>
