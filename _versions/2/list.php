<?php

include( dirname(__FILE__) . '/func.php');

$index = (string)$argv[1];

foreach (getFileIs($index) as $cur)
  echo "$cur '" . dec2base($cur). "' " . md5(dec2base($cur)) . "\n";
exit;