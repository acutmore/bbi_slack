<?php

require 'vendor/autoload.php';
require 'CONFIG.php';
require 'util.php';

ignore_user_abort(true); // GET'ing this script triggers it. Client can then close
set_time_limit(30); // Run for up to 30 seconds

$response_url = $_GET['response_url'];
$page = (int) $_GET['page'];
$title = $_GET['title'];
$command = $_GET['command'];
$token = $_GET['token'];
$user = $_GET['user'];

if ($page < 1){
  $page = 1;
}

if (empty($title)){
  $title = "";
}

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
$just_open_issues = TRUE;

if (0 === strrpos($title, "*")){
  $just_open_issues = FALSE;
  $title = substr($title, 1);
}

$pageSize = 10;

if (empty($title)){
  $pageSize = 50;
}

$limit = $pageSize;
$start = $pageSize * ($page - 1);

$search_params = array(
    'limit' => $limit,
    'start' => $start,
);

if (!empty($title)){
    $search_params += ['title' => ('~' . $title)]; // containing $title
}

if (!empty($user)){
    $search_params += ['responsible' =>  $user];
}

if ($just_open_issues){
    $search_params += ['status' =>  ['new', 'open']];
}

$results = $issues->all($config['bb_account'], $config['bb_repo'], $search_params);


# Parse the JSON
$data = json_decode($results->getContent());
$count = $data->count;

$formatted_results = "* $user : $count issues containing \" $title \":*\r\n";

foreach ($data->issues as $issue){
    $formatted_results .=  slackUrlForIssue($issue->local_id, $issue->title) . "\r\n";
}

if ((count($data->issues) + $start) < (int) $data->count){
  // There are more results that could be shown. Append nessesary command to show more
   $formatted_results .= "type  `$command $title page " . ($page + 1) . "`  for more results" ;
}

# Send results to Slack
$jsonPayload = json_encode([ 'text' => $formatted_results ]);

echo $jsonPayload;

if (!empty($response_url)){
  $headers = ['Content-Type', 'application/json'];
  $browser = new Buzz\Browser();
  $browser->post($response_url, $headers, $jsonPayload);
}

?>
