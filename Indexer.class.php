<?php

class Indexer {

  private $data      = array();
  public  $delta     = 0;
  public  $buffer    = 524288; // 512kb
  public  $positions = array(0,1,2,3,4,5,6,7);
  public  $chars     = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
  public  $status    = '';

  public function __construct( $delta = 0 )
  {
    $this->delta  = $delta;

    foreach ($this->positions as $position)
    {
      foreach ($this->chars as $char)
      {
        $this->data["$position/$char"] = new Index();
        $this->data["$position/$char"]->dim( $this->buffer-1 );
      }
    }
  }

  public function processHash( $hash , $code )
  {
    foreach ($this->positions as $position)
    {
      $index = $position . "/" . $hash{$position};

      if ($this->data[$index]->dimRequired($code - $this->delta) )
      {
        $this->save();
      }

      $this->data[$index]->set($code - $this->delta);
    }
  }

  public function save()
  {

    foreach ($this->data as $index=>$data)
    {
      $filename = dirname(__FILE__) . '/data/' . $index;
      // echo "Saving $filename". strlen($data). " bytes: ".$data->debug()."\n";
      if (!file_exists($dir = dirname($filename)))  mkdir($dir) && chmod($dir, 0777 );
      file_put_contents( $filename , $data, FILE_BINARY | FILE_APPEND );
      chmod($filename, 0666 );
      $data->clear();
      $data->dim($this->buffer-1);
    }
    $this->delta += $this->buffer;
    $this->status = 'saved';
  }



}