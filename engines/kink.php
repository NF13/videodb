<?php

$GLOBALS['kinkPrefix'] = 'kink:';

function kinkSearch($title, $aka=null)
{
    global $kinkPrefix;

    $data = array();

    // add encoding
    $data['encoding'] = 'UTF-8';

	$found = preg_split ('/_/i', $title, 2);

	$info           = array();
	$info['id']     = $kinkPrefix.$found[0];
	$info['title']  = $found[1];
	$data[]         = $info;

    return $data;
}

function kinkActor($name, $actorid)
{
	global $CLIENTERROR;
    global $cache;

	$resp = httpClient('https://www.kink.com/model/'.$actorid.'/tst', $cache);     // added trailing / to avoid redirect
    if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
	
	preg_match('/https:\/\/cdnp\.kink\.com\/imagedb\/[^"]*/i', $resp['data'], $matches);
	
	$url = $matches[0];

    $ary = array();
    $ary[0][0] = $name;
    $ary[0][1] = $url;
	
    return $ary;
}


function kinkMeta()
{
    return array('name' => 'Kink', 'stable' => 1);
}

function kinkData($kinkId)
{
	global $kinkPrefix;
	
	global $CLIENTERROR;
    global $cache;

    $kinkId = preg_replace('/^'.$kinkPrefix.'/', '', $kinkId);
    $data= array();

	$resp = httpClient('https://www.kink.com/shoot/'.$kinkId, $cache);     // added trailing / to avoid redirect
    if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
	
	$data['encoding'] = 'UTF-8';
	
	//print_r($resp['data']);
	$response = $resp['data'];
	
	preg_match('/https:\/\/cdnp\.kink\.com\/imagedb\/[^"]*/i', $response, $matches);
	$data['coverurl'] = $matches[0];
	
	preg_match('/data-sitename="([^"]*)"/i', $response, $matches);
	$data['director'] = $matches[1];
	
	preg_match('/class="shoot-date".*>.*, (\d*)/i', $response, $matches);
	$data['year'] = $matches[1];
	
	preg_match('/class="shoot-title".*>([^<]*)/i', $response, $matches);
	$data['title'] = $matches[1];
	
	preg_match('/class="description-text".*><p>(.*)<\/p>/i', $response, $matches);
	$data['plot'] = $matches[1];
	
	
	preg_match('/<span.*class="names.*?".*?>[^<]*(.*)/i', $response, $matches);
	$casthtml = $matches[1];
	preg_match_all('/\/model\/(\d+)[^>]*>([^<^,]*)/i', $casthtml, $matches, PREG_SET_ORDER);
	foreach($matches as $match) {
		$cast .= trim($match[2]).'::::kink:'.$match[1]."\n";
	}
	$data['cast'] = $cast;
	
	$data['genres'][]='Adult';
	 //print_r($data);
	 
	 return $data;
}
?>