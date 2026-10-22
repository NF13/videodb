<?php

$GLOBALS['steamIdPrefix'] = 'steam:';


function steamSearch($title, $aka = null)
{
    global $steamIdPrefix;
    global $CLIENTERROR;
    global $cache;

    // Steam Store Search API (keine API-Key nötig)
    $url = 'https://store.steampowered.com/api/storesearch/?term=' . urlencode($title) . '&l=german&cc=DE';

    $resp = httpClient($url, $cache);
    if (!$resp['success']) {
        $CLIENTERROR .= $resp['error'] . "\n";
        return array();
    }

    $json = json_decode($resp['data'], true);
    if (!$json || !isset($json['items'])) {
        $CLIENTERROR .= "Steam-Suche lieferte keine gültigen Daten\n";
        return array();
    }

    $data = array();
    $data['encoding'] = $resp['encoding'];

    foreach ($json['items'] as $item) {
        if (!isset($item['id']) || !isset($item['name'])) continue;

        $info = array();
        $info['id']    = $steamIdPrefix . $item['id'];
        $info['title'] = $item['name'];

        $data[] = $info;
    }

    // Sortieren nach Titel
    if (count($data) > 1) {
        $data = multidimsort($data, 'title');
    }

    return $data;
}

function multidimsort($array_in, $column)
{
    $multiarray = array_column($array_in, $column);
    $array_out  = array();
    asort($multiarray);
    // traverse new array of index values and add the corresponding element of the input array to the correct position in the output array
    foreach ($multiarray as $key => $val)
    {
        $array_out[] = $array_in[$key];
    }
    // return the output array which is all nicely sorted by the index you wanted!
    return $array_out;
}

function steamMeta()
{
    return array('name' => 'Steam', 'stable' => 1);
}

function steamData($steamId)
{
	global $steamIdPrefix;
	
	global $CLIENTERROR;
    global $cache;

    $steamId = preg_replace('/^'.$steamIdPrefix.'/', '', $steamId);
    $data= array();

	$resp = httpClient('https://store.steampowered.com/api/appdetails?appids='.$steamId.'&l=german', $cache);     // added trailing / to avoid redirect
    if (!$resp['success']) $CLIENTERROR .= $resp['error']."\n";
	
	$jsonObject = json_decode($resp['data']);
	
	if(!($jsonObject->{$steamId}->{'success'})) {$CLIENTERROR .= "Steam Id not found\n";return $data;}
	
	$data['encoding'] = 'UTF-8';
	$data['year'] = substr($jsonObject->{$steamId}->{'data'}->{'release_date'}->{'date'},-4);
	$data['title'] = $jsonObject->{$steamId}->{'data'}->{'name'};
	$data['coverurl'] = 'https://cdn.cloudflare.steamstatic.com/steam/apps/'.$steamId.'/library_600x900.jpg';
	$data['director'] =$jsonObject->{$steamId}->{'data'}->{'publishers'}[0];
	$data['rating']=$jsonObject->{$steamId}->{'data'}->{'metacritic'}->{'score'}/10;
	foreach($jsonObject->{$steamId}->{'data'}->{'genres'} as $genre) {
        $data['genres'][] = $genre->{'description'};
    }
	 $data['plot']=$jsonObject->{$steamId}->{'data'}->{'detailed_description'};
	 //print_r($data);
	 
	 return $data;
}
?>
