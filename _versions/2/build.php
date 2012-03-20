<?php

set_time_limit(0);
ini_set('memory_limit', '200M');

include( dirname(__FILE__) . '/func.php');

$max = base2dec('ZZZZ');
$is = loadSerialized('index'); if (!is_array($is)) $is = array();
echo "Loading index: ". count($is) . " items\n";
$start = loadSerialized('current')*1; echo "Loaded current: $start\n";

echo "\n" . date('d/m/Y H:i:s') . " ";

$ds = array();
$usage = 0;

for ( $i = $start*1+1; $i<=$max; $i++)
{

  $md = md5( dec2base($i) );
  $mds = str_split( $md );

  foreach ($mds as $ext=>$hash)
  {

    $hash = $ext . '/' . $hash ;

    if (!isset ($is[$hash])) $is[$hash] = 0;
    if ($is[$hash] >= $i) continue;

    if (!isset($ds[$hash])) $ds[$hash] = '';

    $ds[$hash] .= chr( $i - $is[$hash] );
    $is[$hash]  = $i;

  }

  /* boosted index
   *
   *
  foreach (array_unique($mds) as $hash)
  {

    $hash = 'boost/' . $hash ;

    if (!isset ($is[$hash])) $is[$hash] = getFileLastI($hash);
    if ($is[$hash] >= $i) continue;

    if (!isset($ds[$hash])) $ds[$hash] = '';

    $ds[$hash] .= pack('n', $i - $is[$hash] );
    $is[$hash]  = $i;

  }
   * 
   */
  
  if ( time() - $pingtime > 60 || $i == $max)
  {
	$usage1 = round(memory_get_usage() / 1024 / 1024 *100) / 100;
	$usage2 = round(memory_get_usage(TRUE) / 1024 / 1024 *100) / 100;
    echo "$i of $max '" . dec2base($i) . "' " . round($i*100/$max*100)/100 . "% complete, used {$usage1}/{$usage2}Mb " ;
    $pingtime = time();
    echo "\n" . date('d/m/Y H:i:s') . " ";
  }
  
  if ($usage1 > 128 || $usage2 > 128 || $i == $max)
  {
    foreach ($ds as $hash=>$data)
    {
      saveFileData($hash, $data);
      unset($ds[$hash]);
    }
    $ds = array();
    $usage1 = $usage2 = 0;
    saveSerialized('index', $is);
    saveSerialized('current', $i);
    echo "Saved";
    echo "\n" . date('d/m/Y H:i:s') . " ";
  }

}


