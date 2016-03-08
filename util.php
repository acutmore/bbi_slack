<?php

function urlForIssue($issue){
    global $config;

    return 'https://bitbucket.org/'
      . $config['bb_account']
      . '/'
      . $config['bb_repo']
      . '/issues/'
      . $issue;
}

function slackUrlForIssue($issueNumber, $title){
  return "<".urlForIssue($issueNumber)."|#$issueNumber $title >";
}

 ?>
