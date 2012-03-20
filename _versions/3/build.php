<?php

set_time_limit(0);
ini_set('memory_limit', '200M');

include( dirname(__FILE__) . '/func.php');

$num = isset($argv[1]) ? $argv[1] : 0;
$max = isset($argv[2]) ? base2dec($argv[2]) : base2dec('ZZZZZ');
$min = loadSerialized("$num/current")*1;
$is  = loadSerialized("$num/index"); if (!is_array($is)) $is = array();
$savetime = $pingtime = time();

echo "\n" . date('d/m/Y H:i:s') . " ";

for ($i = $min; $i <= $max; $i++)
{
	$md = md5( dec2base($i) );
	$c  = $md{$num};
	if ($is[$c] >= $i) continue;
    if (!isset($ds[$c])) $ds[$c] = '';
    $ds[$c] .= chr( $i - $is[$c] );
    $is[$c]  = $i;
    
	if ( time() - $pingtime > 60 || $i == $max)
	{
		$usage1 = round(memory_get_usage() / 1024 / 1024 *100) / 100;
		$usage2 = round(memory_get_usage(TRUE) / 1024 / 1024 *100) / 100;
		echo "$i of $max '" . dec2base($i) . "' " . round($i*100/$max*100)/100 . "% complete, used {$usage1}/{$usage2}Mb " ;
		$pingtime = time();
		echo "\n" . date('d/m/Y H:i:s') . " ";
	}
	if ( time() - $savetime > 60*5 || $i == $max)
  	{
		foreach ($ds as $c=>$data)
		{
			saveFileData("$num/$c", $data);
			unset($ds[$c]);
		}
		saveSerialized("$num/index", $is);
		saveSerialized("$num/current", $i);
		echo "Saved";
		$savetime = time();
		echo "\n" . date('d/m/Y H:i:s') . " ";
	}
}
