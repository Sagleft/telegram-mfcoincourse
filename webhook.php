<?php
	require_once __DIR__ . "/config.php";
	require_once __DIR__ . "/src/filters.php";
	require_once __DIR__ . "/src/TelegramClient.php";
	
	$client = new TelegramClient($config['token']);
	$message = $client->getMessage();
	
	//проверяем, адресовано ли сообщение боту, если он добавлен в группу
	$type = $client->type;
	if($type == 'group' || $type == 'supergroup') {
		if((strpos($message, $config['tag']) !== false) && ($message[0] == '/')) {
			//обнаружено обращение к боту
			$message = str_replace($config['tag'], '', $message);
		} else {
			exit; //сообщение адресовано не боту
		}
	}
	
	switch($message) {
		default:
			//не смог разобрать команду
			exit;
		case '/rate':
			//команда на запрос курса
			//$client->postMessage("debug");
			//запрос курса
			$json = cURL("https://coinlib.io/api/v1/coin?key=".$config['api_key']."&pref=USD&symbol=MFC", '', '');
			if(!isJSON($json)) {
				$client->postMessage("Ошибка, повторите запрос позже");
			} else {
				$arr = json_decode($json, true);
				$usd_mfc = number_format($arr['price'], 6, '.', ' ');
				$btc_mfc = number_format(round($arr['markets'][0]['price'] * 100000000), 0, '.', ' ');
				$client->postMessage('1 MFC = $' . $usd_mfc . "\n1 MFC = ".$btc_mfc." сатоши");
			}
			break;
	}
	