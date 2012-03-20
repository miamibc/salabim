<?php

include( dirname(__FILE__) . '/func.php');
include( dirname(__FILE__) . '/bitmap.class.php');

$md = str_split( $argv[1] );

foreach ($md as $i=>$char)
{
  if (!$i) $bitmap = new Bitmap( loadPlain( "$i/$char" ) );
  else $bitmap->intersect( loadPlain("$i/$char") );
}

foreach ($bitmap->getall() as $cur)
{
  echo "$cur '" . dec2base($cur). "' " . md5(dec2base($cur)) . "\n";
}
exit;
