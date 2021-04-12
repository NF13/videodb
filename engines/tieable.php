<?php

$GLOBALS['tieablePrefix'] = 'tieable:';

function tieableData($tieableId)
{
	global $tieablePrefix;
	
	global $CLIENTERROR;
    global $cache;

    $tieableId = preg_replace('/^'.$tieablePrefix.'/', '', $tieableId);
    $data= array();

	$resp = httpClient('https://www.tieable.com/?p='.$tieableId, $cache);     // added trailing / to avoid redirect
    if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
	
	$data['encoding'] = 'UTF-8';
	
	//print_r($resp['data']);
	$response = $resp['data'];
	
	preg_match('/https:\/\/www\.tieable\.com\/updates\/ta.*?\/.*?\.[Jj][Pp][Gg]/i', $response, $matches);
	$data['coverurl'] = $matches[0];
	
	$data['director'] = "tieable";
	
	preg_match('/<a class="selected">(.*?)<\/a>/i', $response, $matches);
	$data['title'] = $matches[1];
	
	preg_match('/<p>([^>]{20,})<\/p>/i', $response, $matches);
	$data['plot'] = $matches[1];
	
	preg_match('/https:\/\/www\.tieable\.com\/updates\/(ta.*?)\/.*?\.[Jj][Pp][Gg]/i', $response, $matches);
	$data['filename'] = $matches[1];
	
	$found = false;
	preg_match_all('/<a class="selected">(.*?) -/i', $response, $matches, PREG_SET_ORDER);
	foreach($matches as $match) {
		$found = true;
		$actors = preg_split("/[\s,]{2}| and | & /", $match[1]);
		foreach($actors as $actor) {
			$cast .= $actor.'::::url:https://www.tieable.com/wp-content/uploads/'.str_replace (' ', '-', strtolower ($actor)).".jpg\n";
		}
	}
	if(!$found)
	{
		preg_match_all('/<a class="selected">(.*?\s.*?)\s/i', $response, $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			$actors = preg_split("/[\s,]{2}| and | & /", $match[1]);
			foreach($actors as $actor) {
				$cast .= $actor.'::::url:https://www.tieable.com/wp-content/uploads/'.str_replace (' ', '-', strtolower ($actor)).".jpg\n";
			}
		}
	}
	$data['cast'] = $cast;
	
	$data['genres'][]='Adult';
	 //print_r($data);
	 
	 return $data;
}
?>