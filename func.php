<?php

function dec2base($n,  $t = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")
{
    for($r = ""; $n >= 0; $n = intval($n / strlen($t)) - 1)
        $r = $t{$n%strlen($t)} . $r;
    return $r;
} 

function base2dec($a, $t = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")
{
    $r = 0;
    $l = strlen($a);
    for ($i = 0; $i < $l; $i++) {
        $r += pow(strlen($t), $i) * (strpos($t,$a[$l - $i - 1]) +1);
    }
    return $r-1;
}

