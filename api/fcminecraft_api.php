<?php
header('Access-Control-Allow-Origin: *');

require_once('dsn.php');
require_once('JSONAPI.php');

$mc_api = new JSONAPI($host, $port, $uname, $pword, $salt);

switch ($_GET['callback']) {
	case 'player_count':
		print player_count($mc_api);
		break;
	case 'player_list':
		print player_list($mc_api);
		break;
}

/* Mentod functions */

function player_count($mc_api) {
	$count_arr_raw = $mc_api->callMultiple(array("getPlayerLimit", "getPlayerCount"), array(array(), array(), ));
	$count_arr['max_players'] = $count_arr_raw[0]['success'];
	$count_arr['num_players'] = $count_arr_raw[1]['success'];
	return json_encode($count_arr);
}

function player_list($mc_api) {
	$player_list_raw = $mc_api->call("players.online.names");
	$player_list = $player_list_raw[0]['success'];
	sort($player_list);
	$players['list'] = $player_list;
	$players['txtlist'] = count($players['list']) ? implode($player_list, ', ') : 'No players online, currently.';
	return json_encode($players);
}