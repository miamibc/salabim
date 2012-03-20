<?php

set_time_limit(0);
ini_set('memory_limit', '-1');

include( dirname(__FILE__) . '/func.php');
include( dirname(__FILE__) . '/Index.class.php');
include( dirname(__FILE__) . '/Indexer.class.php');



$min = @filesize(dirname(__FILE__) . '/data/0/0')*8;
$max = 1024*1024*1024;
$a = new Indexer( $min );

for ($i = $min; $i <= $max; $i++) {
  $code = dec2base($i);
  $a->processHash(md5($code), $i);

  if ( !isset ($time) || time() >  $time+60)
  {
    $usage1 = round(memory_get_usage() / 1024 / 1024 *100) / 100;
    $usage2 = round(memory_get_usage(TRUE) / 1024 / 1024 *100) / 100;
    echo date('d/m/Y H:i:s') . " $i of $max '" . dec2base($i) . "' " . round($i*100/$max*100)/100 . "% complete, used {$usage1}/{$usage2}Mb {$a->status}\n" ;
    $a->status = '';
    $time = time();
  }

}


