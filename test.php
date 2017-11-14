<?php

require "Crontab.php";

$cron_string = $argv[1];
var_export(date("Y-m-d H:i:s"));
echo "\n";
$result = Crontab::parse($cron_string, false);
var_export($result);