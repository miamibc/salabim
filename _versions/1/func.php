<?php

function dec2base($n,  $t = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")
{
    for($r = ""; $n >= 0; $n = intval($n / strlen($t)) - 1)
        $r = $t{$n%strlen($t)} . $r;
    return $r;
} 

/*
 * Convert a string of uppercase letters to an integer.
 */
function base2dec($a, $t = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")
{
    $r = 0;
    $l = strlen($a);
    for ($i = 0; $i < $l; $i++) {
        $r += pow(strlen($t), $i) * (strpos($t,$a[$l - $i - 1]) +1);
    }
    return $r-1;
}



function getFileName($hash) {
  return dirname(__FILE__) . "/0/$hash";
}


function getFileLastI($hash) {
  if (!file_exists(getFileName($hash))) return 0;

  $i = 0;
  $fp = fopen( getFileName($hash) , 'rb');
  while (!feof($fp) && $bytes = fread($fp, 2) ) {
    $i += array_shift( unpack('nint', $bytes ) );
  }
  echo "Last I of $hash found $i\n";
  return $i;
}


function getFileIs($hash) {
  if (!file_exists(getFileName($hash))) return array();

  $fp = fopen( getFileName($hash) , 'rb');
  $result = array();
  while (!feof($fp) && $bytes = fread($fp, 2) ) {
    $i += array_shift( unpack('nint', $bytes ) );
    $result[] = $i;
  }
  return $result;
}


function saveFileData($hash, $data) {
  $dir = dirname( getFileName($hash) );
  if (!file_exists($dir)) mkdir($dir, 0777 , true);
  $fil = getFileName($hash);
  $fp = fopen( $fil , 'ab');
  fwrite($fp, $data);
  fclose($fp);
  chmod( $fil , 0755);
}


function saveFileIs($hash, $data) {
  $dir = dirname( getFileName($hash) );
  if (!file_exists($dir)) mkdir($dir, 0777 , true);
  $fil = getFileName($hash);
  $fp = fopen( $fil , 'wb');

  $i_ = 0;
  $out = '';
  sort($data);

  foreach ($data as $i) {
    $out .= pack('n', $i - $i_ );
    $i_ = $i;
  }

  fwrite($fp, $out);
  fclose($fp);
  chmod( $fil , 0755);
}




function saveSerialized($hash, $data) {
  file_put_contents(getFileName($hash), serialize($data));
  chmod(getFileName($hash), 0755);
}

function loadSerialized($hash) {
  $data = file_get_contents(getFileName($hash));
  if (!$data) return 0;
  return unserialize($data);
}
