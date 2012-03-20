<?php

/**
 * Class to create bitmaps, check bits
 * and mix with another bitmaps
 */
class Index
{

  private $data;
  public  $bits = "\x80\x40\x20\x10\x08\x04\x02\x01"; // bitmap masks for 1, 01, 001 etc.

  /**
   * Calculate in (bytes) and dex (bits)
   * @param array
   */
  public function calc($index)
  {
    $dex = $index % 8; // bytes
    $in = ($index - $dex)/8; // bits
    return array($in,$dex);
  }


  /**
   * Grow index, so it can fit new entry ($index)
   * @param int $index
   */
  public function dimRequired($index)
  {
    list($in,$dex) = $this->calc($index);
    $bytes = $in+1 - strlen($this->data);
    return ( $bytes > 0 ) ? $bytes : false;
  }

  public function hasValues()
  {
    return rtrim($this->data, "\x00") ? true : false;
  }

  /**
   * Grow index, so it can fit new entry ($index)
   * @param int $index
   */
  public function dim($index)
  {
    $bytes = $this->dimRequired($index);
    // echo "Added $bytes bytes\n";
    $this->data .= str_repeat( "\x00", $bytes );
  }


  /**
   * Set bit number ... to 1
   * @param int $index
   */
  public function set($index)
  {
    // echo "Set ". $index ."\n";
    list($in,$dex) = $this->calc($index);
    $this->data{$in} = $this->data{$in} | $this->bits{$dex} ; // commit ;)
  }

  /**
   * Check if bit set
   * @param int $index
   * @return bool
   */
  public function get($index)
  {
    //echo "Get ". $index ."\n";
    list($in,$dex) = $this->calc($index);
    return ord( $this->data{$in} & $this->bits{$dex} ) > 0 ? true : false ;
  }

  public function clear() { $this->data = ''; }


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
    $result = array();
    foreach (str_split($this->data) as $in=>$byte) {
      if (ord($byte) > 0) {
        foreach (str_split($this->bits) as $dex=>$bit) {
          if ( ord($byte & $bit) > 0) $result[] = $in*8+$dex;
    }}}
    return $result;
  }

  /*
  public function getall()
  {
    $result = array();
    foreach (str_split(rtrim($this->data,"\x00"), 1024) as $chunknum=>$chunk)
    {
      if (ltrim($chunk, "\x00") === "") continue;
      foreach (str_split($chunk) as $in=>$byte)
        if (ord($byte) > 0)
          foreach (str_split($this->bits) as $dex=>$bit)
            if ( ord($byte & $bit) > 0) $result[] = $chunknum*1024*8+$in*8+$dex;
    }
    return $result;
  }
*/

  public function getLast()
  {
    $in = strlen(rtrim($this->data,"\x00"))-1 ;
    foreach (str_split($this->bits) as $dex=>$bit)
      if ( ord($this->data{$in} & $bit) > 0) return $in*8+$dex;
  }


  public function getFirst()
  {
    $in = strlen($this->data) - strlen(ltrim($this->data,"\x00"))  ;
    foreach (str_split($this->bits) as $dex=>$bit)
      if ( ord($this->data{$in} & $bit) > 0) return $in*8+$dex;
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

$a = new Index();

$a->dim(42);
$a->set( 7 ); echo $a->debug() . "\n";
echo "\n";
$a->set( 8 ); echo $a->debug() . "\n";
echo "\n";
$a->set( 21 ); echo $a->debug() . "\n";
echo "\n";
$a->set( 42 ); echo $a->debug() . "\n";
print_r($a->getFirst()) . "\n";
echo "\n";
*/