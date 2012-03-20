<?php

include( dirname(__FILE__) . '/func.php');
include( dirname(__FILE__) . '/bitmap.class.php');

array_shift($argv);

$bitmap = new Bitmap( loadPlain( array_shift( $argv ) ) );

foreach ($argv as $file)
  $bitmap->intersect( loadPlain($file) );

foreach ($bitmap->getall() as $cur)
  echo "$cur '" . dec2base($cur). "' " . md5(dec2base($cur)) . "\n";
exit;
