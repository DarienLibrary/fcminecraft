<?php
header('Access-Control-Allow-Origin: *');

require_once('MDB2.php');

switch ($_GET['callback']) {
	case 'player_count':
		print player_count();
		break;
	case 'player_list':
		print player_list();
		break;
}

/* Mentod functions */

function player_count() {
	$player_list = get_players();

	$count_arr['max_players'] = get_max_players();
	$count_arr['num_players'] = count($player_list);
	$count_arr['num_registered'] = trim(`ls -1 /home/minecraft/minecraft/minecraft_server/plugins/Essentials/userdata | wc -l`);
	return json_encode($count_arr);
}

function player_list() {

	$player_list_raw = get_players();

	foreach ($player_list_raw as $player_arr) {
		$player_list[] = $player_arr['player'];
	}
	sort($player_list);
	
	$players['list'] = $player_list;
	$players['txtlist'] = count($player_list) ? implode($player_list, ', ') : 'No players online, currently.';
	foreach ($player_list as $player) {
		$player_html[] = '<span class="player_name">' . $player . '</span>';
	}
	$players['htmllist'] = count($player_list) ? implode($player_html, '<span class="player_comma">,</span> ') : '<span class="player_name">No players online, currently.</span>';
	$players['num_players'] = count($player_list);
	$players['max_players'] = get_max_players();
	$players['registered_players'] = trim(`ls -1 /home/minecraft/minecraft/minecraft_server/plugins/Essentials/userdata | wc -l`);
	return json_encode($players);
}

/* Support functions */

function get_players() {

	require_once('dsn.php');

	$mdb2 = MDB2::connect($player_db_dsn);
	if (PEAR::isError($mdb2))
	{
		die($mdb2->getMessage());
	}
	$mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);

	$sql = 'SELECT * FROM online_players WHERE online = 1';
	$result = $mdb2->query($sql);

	return $result->fetchAll();
}

function get_max_players() {
	$server_ini = trim(`grep max-players /home/minecraft/minecraft/minecraft_server/server.properties`);
	$max_pl_arr = explode('=', $server_ini);
	return trim($max_pl_arr[1]);
}






