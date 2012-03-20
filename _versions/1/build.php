<?php

include( dirname(__FILE__) . '/func.php');

$max = base2dec('ZZZZZ');
$is = loadSerialized('index'); if (!$is) $is = array();
echo "Loading index: ". count($is) . " items\n";
$start = loadSerialized('current'); echo "Loaded current: $start\n";
$ds = array();

for ( $i = $start*1+1; $i<=$max; $i++)
{

  $md = md5( dec2base($i) );
  $mds = str_split( $md , 2 );

  foreach ($mds as $ext=>$hash)
  {

    $hash = $ext . '/' . $hash ;

    if (!isset ($is[$hash])) $is[$hash] = getFileLastI($hash);
    if ($is[$hash] >= $i) continue;

    if (!isset($ds[$hash])) $ds[$hash] = '';

    $ds[$hash] .= pack('n', $i - $is[$hash] );
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
    echo date('d/m/Y H:i:s') . " $i of $max complete (" . round($i*100/$max*100)/100 . "%) " . dec2base($i);
    $pingtime = time();
    foreach ($ds as $hash=>$data)
    {
      saveFileData($hash, $data);
      unset ($ds[$hash]);
    }
    saveSerialized('index', $is);
    saveSerialized('current', $i);
    echo " Saved\n";
  }

}


