<?php

class Finder {

  private $delta = 0;
  private $files = array();
  private $main;

  public function __construct( $files = array() )
  {
    foreach ($files as $file) $this->addFile($file);
  }

  public function addFile( $file )
  {
    $this->files[] = $this->main = fopen($file, 'rb');
  }


  public function doSearch( $size = 1024 ) // 1024*1024
  {

    while (1) {

      $index = null;
      foreach ($this->files as $file)
      {
        $data = fread($file, $size );
        if ($data === '' ) return $this->close();
        is_null($index) ? $index = $data : $index &= $data;
      }

      if ( ltrim($index,"\x00") !== '') {
        $index = new Index($index);
        $result = $index->getFirst();
        list($in,$dex) = $index->calc($result);
        $result += $this->delta*8;
        foreach ($this->files as $file) fseek($file, -$size + $in+1, SEEK_CUR);
        $this->delta += $in+1 ;
        return $result;
      }

      $this->delta += $size ;
    }
  }

  public function close()   {
    foreach ($this->files as $file) fclose($file);
  }




}

