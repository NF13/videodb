<?php

$GLOBALS['dcPrefix'] = 'dc:';

function dcData($videoId)
{
	global $dcPrefix;
	
	global $CLIENTERROR;
    global $cache;

    $videoId = preg_replace('/^'.$dcPrefix.'/', '', $videoId);
	list ($site, $update, $id) = preg_split('/-/', $videoId);
    $data= array();
    $url = '';
	if($site == 'cum')
	{
		preg_match('/(\d+)\w*/i', $update, $matches);
		$url = $url.'http://www.cumbots.com/'.$matches[1].'updates/';
		if (preg_match("/[a-z]+/i", $update))
		{
			$url = $url.$update.'/';
		}
		$data['director'] = "cumbots";
	}
	else if($site == 'DRE')
	{
		preg_match('/(\d+)\w*/i', $update, $matches);
		$url = $url.'http://www.dungeoncorp.com/updates'.$matches[1].'/DRE/';
		if (preg_match("/[a-z]+/i", $update))
		{
			$url = $url.$update.'/';
		}
		$data['director'] = "Dungeon of Cum: Redux";
	}
	else if($site == 'MIT')
	{
		preg_match('/(\d+)\w*/i', $update, $matches);
		$url = $url.'http://www.dungeoncorp.com/updates'.$matches[1].'/MIT/';
		if (preg_match("/[a-z]+/i", $update))
		{
			$url = $url.$update.'/';
		}
		$data['director'] = "MightyGirlz";
	}
	else if($site == 'ssm')
	{
		preg_match('/(\d+)\w*/i', $update, $matches);
		$url = $url.'http://www.societysm.com/updates'.$matches[1].'/';
		if (preg_match("/[a-z]+/i", $update))
		{
			$url = $url.$update.'/';
		}
		$data['director'] = "SocietySM";
	}
	$url = $url.$id.'/';
	//print_r($url);
	$resp = httpClient($url, $cache);     // added trailing / to avoid redirect
    if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
	
	$data['encoding'] = 'UTF-8';
	
	//print_r(htmlspecialchars($resp['data']));
	$response = $resp['data'];
	
	$data['coverurl'] = $url.'3.jpg';
	
	preg_match('/&quot;([^&]+)/i', $response, $matches);
	$data['title'] = $matches[1];
	
	preg_match('/(\d+) Streaming/i', $response, $matches);
	$data['runtime'] = $matches[1];
	
	preg_match('/class="descriptext">([^<]+)/i', $response, $matches);
	$data['plot'] = $matches[1];
	
	preg_match_all('/([\w ]+) - \d+ Photo Galler/i', $response, $matches, PREG_SET_ORDER);
	foreach($matches as $match) {
		$actor = trim($match[1]);
		$resp = httpClient($url, $cache);     // added trailing / to avoid redirect
		if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
		
		$data['encoding'] = 'UTF-8';
		
		//print_r(htmlspecialchars($resp['data']));
		$response = $resp['data'];
		$cast .= $actor.'::::url:http://www.dungeoncorp.com/models/'.str_replace (' ', '_', strtolower ($actor)).".jpg\n";
	}
	preg_match_all('/([\w ]+)<\/span><span class="heading"> - \d+ Photo Galler/i', $response, $matches, PREG_SET_ORDER);
	foreach($matches as $match) {
		$actor = trim($match[1]);
		$resp = httpClient($url, $cache);     // added trailing / to avoid redirect
		if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
		
		$data['encoding'] = 'UTF-8';
		
		//print_r(htmlspecialchars($resp['data']));
		$response = $resp['data'];
		$cast .= $actor.'::::url:http://www.dungeoncorp.com/models/'.str_replace (' ', '_', strtolower ($actor)).".jpg\n";
	}
	preg_match_all('/([\w ]+)<\/span><span class="heading"> - \d+ Image Galler/i', $response, $matches, PREG_SET_ORDER);
	foreach($matches as $match) {
		$actor = trim($match[1]);
		$resp = httpClient($url, $cache);     // added trailing / to avoid redirect
		if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
		
		$data['encoding'] = 'UTF-8';
		
		//print_r(htmlspecialchars($resp['data']));
		$response = $resp['data'];
		$cast .= $actor.'::::url:http://www.dungeoncorp.com/models/'.str_replace (' ', '_', strtolower ($actor)).".jpg\n";
	}
	$data['cast'] = $cast;
	
	$data['genres'][]='Adult';
	 //print_r($data);
	 
	 return $data;
}
?>