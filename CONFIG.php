<?php

// Configuration settings listed by slack token

$bb_account_a = [
  "bb_client_id" => "kjihgfedcba",
  "bb_client_secret" => "123456abcdef",
  "bb_account" => "foo",
  "bb_repo" => "bar"
];

$bbi_slack_config = [
    "abcdefghijklmnopqrs" => $bb_account_a,
    "srqponmlkjihgfedcba" => $bb_account_a,
];

include 'CONFIG_.php'; // dev enviroment overrides

?>
