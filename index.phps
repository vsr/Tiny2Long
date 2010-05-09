<!--
http://wg.vinayraikar.com/apps/t2l/  - tiny to long url converter.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of the organisation nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
-->
<?php
error_reporting(0);

$base_url = 'http://'.$_SERVER['HTTP_HOST'];
if ($directory = trim(dirname($_SERVER['SCRIPT_NAME']), '/\,')) {
  $base_url .= '/'.$directory;
}
define('BASE_URL', $base_url.'/');


function getlong($url){

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);


	$result = curl_exec($ch);
	curl_close($ch);
	if($result !== false){
	  $inp = 1;
	  $resarray = explode("\r\n",$result);
	  foreach($resarray as $line){
		if(stripos($line, "HTTP/1") !== FALSE){
		   $temp = explode(" ",$line);
		   $redirectcode = $temp[1];
		}
		else{ 
              $temp = explode(":",$line,2);
			  if(strcasecmp($temp[0],"location") == 0){
				  return trim($temp[1]);
			  }
	       }
	  }
	}
	return '';



}


if(isset($_GET['api'])){
	//called via api
	$result = array();
	if(is_array($_GET['url'])){
		foreach($_GET['url'] as $url){
			//array_push($result,array($url=>getlong($url)));
			$result[$url] = getlong($url);
		}
	}
	else{
		//array_push($result,array($_GET['url']=>getlong($_GET['url'])));
		$result[$_GET['url']] = getlong($_GET['url']);
	}

	if($_GET['callback']){
		header('Content-type: application/json');
		echo $_GET['callback'].'('.json_encode($result).')';
		exit;
	}
	else{
		header('Content-type: application/json');
		echo json_encode($result);
		exit;
	}

}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="utf-8" />
<title>Tiny2Long</title>
<link type="text/css" rel="stylesheet" href="style.css"/>
<body onload='document.forms[0].tinyurl.focus();'>
<h1><a href="<?php echo BASE_URL; ?>">Tiny2Long</a></h1>
<div id='result'>
<?php

if(isset($_POST['tinyurl'])){
	$longurl=getlong($_POST['tinyurl']);
	if($longurl == ''){
		echo "<p class='error' ><a target='_blank' href='".$_POST['tinyurl']."'>".$_POST['tinyurl']."</a> this url doesn't seem to be a short url!</p>";
	}
	else{
		echo "<p><a target='_blank' href='".$_POST['tinyurl']."' >".htmlentities($_POST['tinyurl'])."</a><br/> redirects to <br/> <a target='_blank' class='longurl' href='".$longurl."'>".htmlentities($longurl)."</a></p>";
	}
}

?>
</div>
<form class="urlinput" id="tiny2long" name="geturl" action="" method="POST">
Small Url :<input name="tinyurl" type="text" value="<?php echo $_POST['tinyurl']; ?>" size="32" /> <input name="submit" type="submit" value="Get long url" />
</form>

<p class='c'>tiny2long also has an <a href='api.html'>API</a>. The source code is available <a href='index.phps'>here</a>.</p>

<p id='credits'>app by <a href='http://vinayraikar.com'>vsr</a> | More apps at <a href='http://wg.vinayraikar.com/apps/'>apps</a</p>
</body>
</html>
