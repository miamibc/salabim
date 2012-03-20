<?php

/**
 * Class to create bitmaps, check bits
 * and mix with another bitmaps
 */
class Bitmap
{

  private $data = "\x00";

  // here comes bitmap masks for 1, 01, 001 etc.
  private $bits = "\x80\x40\x20\x10\x08\x04\x02\x01";



  /**
   * Set bit number ... to 1
   * @param int $index
   */
  public function  set($index)
  {
    if (!is_numeric($index)) throw new Exception("Index is not a number ($index)");
    $in = floor($index/8); // get this byte from index
    $dex = $index - $in*8; // turn thois bit 'on' or 'off'
    if ($in > strlen($this->data)-1 ) $this->data .= str_repeat( "\x00", $in - strlen($this->data)-1 );
    $this->data{$in} = $this->data{$in} | $this->bits{$dex} ; // commit ;)
  }

  /**
   * Check if bit set
   * @param int $index
   * @return bool
   */
  public function  get($index)
  {
    if (!is_numeric($index)) throw new Exception("Index is not a number ($index)");
    $in = floor($index/8); // get this byte from index
    $dex = $index - $in*8; // check thois bit 'on' or 'off'
    return ord( $this->data{$in} & $this->bits{$dex} ) > 0 ? true : false ;
  }

  /**
   * Merge bitmap with another bitmap
   * @param string $data
   */
  public function merge($data) { $this->data |= $data; }

  /**
   * Intersect bitmap with another bitmap
   * @param string $data
   */
  public function intersect($data) { $this->data &= $data; }

  /**
   * Load data
   * @param string $data
   */
  public function load($data = "\x00") { $this->data = $data; }
  public function  __construct($data = "\x00") { $this->load($data); }

  /**
   * Save data
   * @return string
   */
  public function save() { return $this->data; }
  public function  __toString() { return $this->save(); }

  public function getall()
  {    
    $i = 0;
    $result = array();
    do
    {
      if (ord($this->data{$i}) > 0)
        foreach (str_split($this->bits) as $dex=>$bit)
          if (ord($this->data{$i} & $bit) > 0) $result[] = $i*8+$dex;          
    }
    while (++$i < strlen($this->data) );
    return $result;
  }

  public function debug()
  {
    $result = array();
    foreach (str_split($this->data) as $char)
    {
      $result[] = str_pad( decbin( ord($char) ) , 8 , '0' , STR_PAD_LEFT);
    }
    return implode("|",$result);
  }

 
}

/*

$a = new Bitmap();
$a->set(0);
$a->set(1);
$a->set(20);
$a->set(23);
$a->set(24);
echo $a->debug() . "\n";
print_r($a->getall()) . "\n";
echo "\n";
*/