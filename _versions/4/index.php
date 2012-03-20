<?php
    if (!headers_sent())
    {
      header('Content-Type: text/html; charset=utf-8');
      header("Cache-Control: no-cache, must-revalidate");
      header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }
    
    include( dirname(__FILE__) . '/func.php');
    
    if (isset( $_POST['md'] )) exit( search($_POST['md'] ) );    
    
?><!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css" type="text/css" media="all" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js" type="text/javascript"></script>
<script language="javascript">

$(function() {
	
	$('.more').click(function(){
		$(this).hide();
		$('#more').slideDown();
		return false;		
	});
    
	$('form').submit(function(){	
		var md = $('.text').val();
		if (!md.match(/[0-9a-fA-F]{32}/)) { alert('Not valid md5 hash!'); return false; }
		if ($('#'+md).length > 0) { alert('This md5 hash is already opened! Press Ctrl+F to find.'); return false; }
		$('.text').val('');
		$('#todo').prepend('<div class="ui-corner-all item progress" id="'+md+'"><span class="del">s</span><span class="timer">0</span><span class="result">'+md+'</span></div>');
		
		$.post( location.href, { md: md } , function(msg){
			$('#'+md+" .result").html(msg);
			$('#'+md).removeClass('progress');
		});
		return false;
	});
  
	smallinterval = setInterval( function() {
		$('.progress').each(function(){
		  $('.timer', this).html( $('.timer', this).html()*1+1 );
		});
	}, 1000 );
	
	/*
  $('textarea').keyup(function(e){
      var code = e.keyCode || e.charCode || e.which;
      if ( code == 13 || code == 10 || (( code == 118 || code == 86 || code == 1084 || code == 1052 ) && e.ctrlKey) ) {
        $('form').submit();
  	}
  });
   */

});
</script>
<style type="text/css">
* { font-family: Verdana, sans-serif; }
body { padding: 30px;  }
A:link, A:visited { color: #0489B7; text-decoration: none; }
A:hover { color: #0489B7; text-decoration: underline; }
.logo { float: left;  width: 80px; font-size: 1.3em; color: #999; }
.logo A { font-size: 1.3em; color: #999; text-decoration: none; border-bottom: 2px dotted #999 }
.logo A:hover { border-bottom: 2px solid #999  }
.text { height: 1.3em;  font-size: 1.4em ; padding: 5px;
           margin-bottom: -10px; width: 585px; overflow: hidden;
           border: 1px solid #999; }
.submit { padding: 5px; margin: 0 20px; }

#todo { width: 600px; margin: 15px 0 50px 80px; }
.desc { width: 600px; margin-left: 80px; color: #999; font-size: 10pt; }
h1  { font-size: 40pt; color: #999; font-family:georgia,serif; 
        font-weight:normal; margin: 0; letter-spacing:-1px; }
.item { background: #f9f9f9; border: 1px solid #ddd; margin-bottom: 5px; padding: 5px; }
#todo .progress { opacity: .5; }
.del, .timer { float: right; margin-left: 5px;  }
.desc { }
</style>

</head>
<body>

<h1>Salabim</h1>

<p class="desc">Salabim - is open source database, built in PHP. 
It is optimized to handle large number of nearby-standing integers inside rapid search engine.
This demo allows you to search md5 hash in up to four indexes at the same time, and find
intersections in them. That'all, no magic ;) By pressing 'Enter' you agreed with term to 
use result for peaceful purposes only, not to brute passwords, hax0ring, etc.
<a class="more" href="more">Learn More</a></p>


<p class="desc" id="more" style="display: none">At the moment demonstration base is 
<?php $cur = unserialize(file_get_contents(getFileName('0/current'))); ?>
<?php echo round($cur * 100 / base2dec('ZZZZZ') * 100)/100?>% generated. It stores
<?php echo $cur ?> md5-encrypted strings 
[0-9a-zA-Z], up to <?php echo strlen(dec2base($cur))?> characters long. Last addition is '<?php echo dec2base($cur) ?>'.
There are <?php echo $count = count(unserialize(file_get_contents(getFileName('0/index')))) ?> index files, 
totally ~<?php $size = filesize(getFileName('0/0')); echo round($count * $size / 1024 / 1024 *100)/100?> Mbytes.
Great feature of Salabim is that process of indexing works simultaneously with searching 
or mixing data. It also can be interrupted and continued in any time.</p>


<form method="post">
<div class="logo"><a href="http://en.wikipedia.org/wiki/MD5" target="_blank">MD5</a></div>
<input type="text" name="md" value="5cec1a8d5a2cdd3b00c692255b73bfa1" tabindex="1" class="text ui-corner-all"/>
<input type="submit" value="← Enter" class="submit"/>
</form>

<div id="todo">



</div>
</body>
</html>

<?php

function search($md)
{
	set_time_limit(0); // actually, it's 120 seconds
	$timer = time();
	$md  = strtolower($md);
	$mds = str_split( $md );
	$fs = $is = array();
	$cur = $fi = 0;
	foreach ($mds as $ext=>$hash)
	{
	  //if ($ext % (32/2)) continue;  // this does searching faster x times
	  if (!file_exists(getFileName($ext . '/' . $hash))) continue;
	  $hash = $ext . '/' . $hash ;
	  $fs[] = fopen( getFileName($hash) , 'rb');
	  $is[] = 0;
	}	
	while (1)
	{
	  while (1)
	  {
		$bytes = fread( $fs[$fi] , 1);
		if (time() - $timer > 60*10)
		{
		  foreach ($fs as $f) fclose( $f );
		  return("$md time out");
		}    
		if (feof($fs[$fi]))
		{
		  fclose( $fs[$fi] );
		  unset ( $fs[$fi] );
		  if (!count($fs)) return("$md not found");
		  $fs = array_values( $fs );
		  break;
		}    
		$is[$fi] += ord( $bytes );
		if ($is[$fi] == $cur) { $got++; break; }
		if ($is[$fi] > $cur) {  $cur = $is[$fi]; $got = 1; break; }
	  }
	  if ( $got == count($fs) && md5(dec2base($cur)) == $md )
	  {
		foreach ($fs as $f) fclose($f);
		return( "$md decrypted '" . dec2base($cur). "'" );
	  }
	  $fi++;
	  if (!isset ($fs[$fi])) $fi = 0;
	}
}
