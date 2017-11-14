<?php

require "Crontab.php";

$cron_string = $argv[1];
$result = Crontab::parse($cron_string);
var_export(date("Y-m-d H:i:s"));
var_export($result);