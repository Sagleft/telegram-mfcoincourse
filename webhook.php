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
	
	$command_list = "📈 /rate - запрос курса MFCoin;";
	
	switch($message) {
		default:
			//не смог разобрать команду
			exit;
		case '/start':
			$client->postImage("Приветсвую, " . $client->name . "\n\nДобро пожаловать во Freeland. Я буду твоим ассистентом", "logo.png");
			$client->postMessage("Команды: \n" . $command_list);
			break;
		case '/rate':
			//команда на запрос курса
			$json = cURL("http://api.mfc-market.ru/ticker_local", '', '');
			if(!isJSON($json)) {
				$client->postMessage("Ошибка, повторите запрос позже");
			} else {
				$arr = json_decode($json, true);
				$usd_mfc = number_format($arr['USD'], 6, '.', ' ');
				$btc_mfc = number_format(round($arr['BTC'] * 100000000), 0, '.', ' ');
				$client->postMessage('1 MFC = $' . $usd_mfc . "\n1 MFC = ".$btc_mfc." сатоши");
			}
			break;
	}
	