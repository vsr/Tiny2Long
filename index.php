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
<?php
if(isset($_POST['tinyurl'])){
echo <<<ADTEXT
<div id='ad'>
<script type="text/javascript"><!--
google_ad_client = "pub-7576293061984551";
/* 234x60, created 7/9/09 */
google_ad_slot = "4364229858";
google_ad_width = 234;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
ADTEXT;
}
?>

<p id='credits'>app by <a href='http://vinayraikar.com'>vsr</a> | More apps at <a href='http://wg.vinayraikar.com/apps/'>apps</a</p>
</body>
</html>
