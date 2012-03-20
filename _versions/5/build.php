<?php

set_time_limit(0);
ini_set('memory_limit', '-1');

include( dirname(__FILE__) . '/func.php');
include( dirname(__FILE__) . '/bitmap.class.php');

$num = isset($argv[1]) ? $argv[1] : 0;
$min = loadPlain("$num/min")*1;
$max = isset($argv[2]) ? base2dec($argv[2]) : base2dec('ZZZZZZ');
$savetime = $pingtime = time();

echo "\n" . date('d/m/Y H:i:s') . " ";

for ($i = $min; $i <= $max; $i++) {
  $md = md5( dec2base($i) );
  $c  = $md{$num};
  if (!isset ($b["$num/$c"])) $b["$num/$c"] = new Bitmap( );
  $b["$num/$c"]->set($i - $min);

  if ( time() - $pingtime > 10 || $i == $max) {
    $usage1 = round(memory_get_usage() / 1024 / 1024 *100) / 100;
    $usage2 = round(memory_get_usage(TRUE) / 1024 / 1024 *100) / 100;
    echo "$i of $max '" . dec2base($i) . "' " . round($i*100/$max*100)/100 . "% complete, used {$usage1}/{$usage2}Mb " ;
    $pingtime = time();
    echo "\n" . date('d/m/Y H:i:s') . " ";
  }

  if ($i - $min +1 > 8*8*8 || $i >= $max) {
    foreach ($b as $hash=>$data) {
      saveFileData($hash, $data->save());
      unset($b[$hash]);
    }
    $min = $i;
    savePlain( "$num/min", $min );
    echo "Saved";
    echo "\n" . date('d/m/Y H:i:s') . " ";
  }
}
