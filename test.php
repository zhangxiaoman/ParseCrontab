<?php

require "Crontab.php";

$cron_string = $argv[1];
$hide_past_sec = (boolean)$argv[2];
var_export(date("Y-m-d H:i:s"));
echo "\n";
$result = Crontab::parse($cron_string, $hide_past_sec);
var_export($result);