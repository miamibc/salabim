<?php

$debug = false;

$time  = time();

include( dirname(__FILE__) . '/func.php');

$md = $argv[1]; // md5( (string)$argv[1] );


$mds = str_split( $md , 2 );
$fs = $is = array();

foreach ($mds as $ext=>$hash)
{

  //if ($ext % 4) continue;  // this does searching faster x times

  $hash = $ext . '/' . $hash ;
  if ($debug) echo getFileName($hash) . " opened for binary reading\n";
  $fs[] = fopen( getFileName($hash) , 'rb');
  $is[] = 0;
}

echo "Searching for $md in ". count($fs)." indexes ...\n";

$cur = $fi = 0;

while (1)
{

  if ($debug) echo "Searching file $fi for $cur or more\n";

  while (1)
  {
   
    $bytes = fread( $fs[$fi] , 2);

    if (feof($fs[$fi]))
    {
      echo "Nothing found in " . (time() - $time) . " sec.\n";
      exit;
    }
    
    $is[$fi] += array_shift( unpack('nint', $bytes ) );
    
    if ($debug) echo "Read 2 bytes from file $fi. I = $is[$fi]\n";

    if ($is[$fi] == $cur) {
      $got++;
      if ($debug) echo "Got is $got\n";
      break;
    }
    if ($is[$fi] > $cur)
    {
      $cur = $is[$fi];
      $got = 1;
      if ($debug) echo "Cur is $cur\n";
      break;
    }
    
  }


  if ( $got == count($fs) && md5(dec2base($cur)) == $md )
  {
    foreach ($fs as $f) fclose($f);
    echo "Found '" . dec2base($cur). "' in " . (time() - $time) . " sec.\n";
    exit;
  }

  $fi++;
  if (!isset ($fs[$fi])) $fi = 0;

}
