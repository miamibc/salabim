<?php

include( dirname(__FILE__) . '/func.php');
include( dirname(__FILE__) . '/Index.class.php');
include( dirname(__FILE__) . '/Finder.class.php');

$md = $argv[1];
// $md = md5($md);
$md = str_split( $md );
$time = microtime(true);

// new method, using Index_Finder
$finder = new Finder( );

foreach ($md as $i=>$char)
{
  // if ($i > 3) continue;
  $filename = dirname(__FILE__) . "/data/$i/$char";
  if (!file_exists($filename)) continue;
  $finder->addFile($filename);
  echo "Using index: $filename\n";
}

echo "\n";
while($cur = $finder->doSearch())
{
  echo md5(dec2base($cur)) . " => " . dec2base($cur) . "\n";
}

echo round( (microtime(true)- $time) * 100) / 100 . " seconds wasted\n" ;

/*
// this is last working method, but it is slow about 120 sec
// most time spent in intersecting indexes

foreach ($md as $i=>$char)
{
  // if ($i > 3) continue;
  $filename = dirname(__FILE__) . "/data/$i/$char";
  if (!file_exists($filename)) continue;
  !isset ($bitmap)
    ? $bitmap = new Index( file_get_contents( $filename ) )
    : $bitmap->intersect( file_get_contents( $filename ) );
  echo "+";
}
echo "\n";

foreach ($bitmap->getall() as $cur)
  echo md5(dec2base($cur)) . " => " . dec2base($cur) . "\n";
echo round( (microtime(true)- $time) * 100) / 100 . " seconds wasted\n" ;

*/

/*
// creating temporary file and search in it
// is too slow

$filename = dirname(__FILE__) . '/data/tmp';
file_put_contents( $filename , $bitmap); chmod($filename, 0666);

$handle = fopen($filename, "rb");
while (!feof($handle))
{
  $fp = ftell($handle);
  $contents = fread($handle, 1024);
  if (trim($contents, "\x00") === "") continue;
  $b = new Index($contents);
  foreach ($b->getall() as $cur)
  {
    $cur += $fp*8;
    echo md5(dec2base($cur)) . " => " . dec2base($cur) . "\n";
  }
}
fclose($handle);
*/
