<?php

$GLOBALS['captivechrissymariePrefix'] = 'captivechrissymarie:';

function captivechrissymarieSearch($title, $aka=null)
{
    global $captivechrissymariePrefix;
    global $CLIENTERROR;
    global $cache;

    $url = 'https://xsiteability.com/x-new/new-preview-list.php?user=captivechrissymarie&type=all&submit=Search&search='.str_replace ('_', ' ',$title);

    $resp = httpClient($url, $cache);
    if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";

    $data = array();

    // add encoding
    $data['encoding'] = $resp['encoding'];

//print_r(htmlspecialchars($resp['data']));
	$response = $resp['data'];

	preg_match_all('/href="\/x-new\/new-set\.php\?setid=(\d+)&user=.*"[^%]*?<h3>([^<]*)</i', $response, $matches, PREG_SET_ORDER);
	foreach($matches as $match) {
			$info           = array();
			$info['id']     = $captivechrissymariePrefix.$match[1];
			$info['title']  = $match[2];
			$data[]         = $info;
#           dump($info);
	}

    return $data;
}

function captivechrissymarieMeta()
{
    return array('name' => 'captivechrissymarie', 'stable' => 1);
}

function captivechrissymarieData($videoId)
{
	global $captivechrissymariePrefix;
	
	global $CLIENTERROR;
    global $cache;

    $videoId = preg_replace('/^'.$captivechrissymariePrefix.'/', '', $videoId);
    $data= array();

	$resp = httpClient('https://xsiteability.com/x-new/new-set.php?user=captivechrissymarie&setid='.$videoId, $cache);     // added trailing / to avoid redirect
    if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
	
	$data['encoding'] = 'UTF-8';
	
	//print_r(htmlspecialchars($resp['data']));
	$response = $resp['data'];
	
	preg_match('/"([^"]*xsiteability.com\/x\/users\/captivechrissymarie\/previews\/thumbnails[^"]*)"/i', $response, $matches);
	$data['coverurl'] = $matches[1];
	
	$data['director'] = "Captive Chrissy Marie";
	
	preg_match('/<h3.*>(.*)</i', $response, $matches);
	$data['title'] = $matches[1];
	
	preg_match('/(\d+):\d+ video/i', $response, $matches);
	$data['runtime'] = $matches[1];
	
	preg_match('/<p.*red.*\n.*<p>([^<]*)/i', $response, $matches);
	$data['plot'] = $matches[1];
	
	$data['genres'][]='Adult';
	 //print_r($data);
	 
	 return $data;
}
?>