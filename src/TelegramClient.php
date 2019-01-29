<?php
	class TelegramClient {
		var $token = '';
		var $api = 'https://api.telegram.org/bot';
		var $db = null;
		
		public $chatID = null; //tid в бд (telegram id)
		public $name = null;
		public $message = null;
		public $debug = null;
		public $type = null;
		public $telegram_user_id = null;
		
		public function __construct ($token, $db=null) {
			$this->db = $db;
			$this->token = $token;
		}
		
		public function getMessage() {
			//получаем сообщение
			$output = json_decode(file_get_contents('php://input'), TRUE);
			$chatID = DataFilter($output['message']['chat']['id'])+0;
			if($this->chatID) {
				exit("invalid chat id");
				//TODO: выдавать exception
			} else {
				$this->chatID = $chatID;
			}
			
			$user_id = DataFilter($output['message']['from']['id'])+0;
			$this->telegram_user_id = $user_id;
			
			$this->name = DataFilter($output['message']['chat']['first_name']);
			$message = DataFilter($output['message']['text']);
			$this->message = $message;
			$this->type = DataFilter($output['message']['chat']['type']);
			//$this->debug = DataFilter($output['message']['entities']);
			return $message;
		}
		
		public function getUserPhoto() {
			//получает UserProfilePhotos объект
			$queryURL = $this->api . $this->token . '/getUserProfilePhotos?user_id=' . $this->telegram_user_id . '&limit=1';
			$json = file_get_contents($queryURL);
			if(!isJSON($json)) {
				throw new Exception("Ошибка запроса аватара пользователя, полученный ответ - не json.");
			} else {
				$arr = json_decode($json, true);
				if($arr['ok'] !== true) {
					throw new Exception("Получена ошибка при запросе аватара пользователя. status != ok");
				} else {
					//ассоциативный массив с индексами file_id, file_size, width, height
					return $arr['result']['photos'][0][0];
				}
			}
		}
		
		public function getTelegramFile($file_id) {
			//получает объект File по его id в telegram
			$queryURL = $this->api . $this->token . '/getFile?file_id=' . $file_id;
			$json = file_get_contents($queryURL);
			if(!isJSON($json)) {
				throw new Exception("Ошибка запроса аватара пользователя, полученный ответ - не json.");
			} else {
				$arr = json_decode($json, true);
				if($arr['ok'] !== true) {
					throw new Exception("Получена ошибка при запросе аватара пользователя. status != ok");
				} else {
					//ассоциативный массив с индексами file_id, file_size, file_path (например, "photos/file_0.jpg");
					//доступ к файлу по шаблону https://api.telegram.org/file/bot<token>/<file_path>
					//ссылка будет действительна в течении часа
					$arr['result']['url'] = "https://api.telegram.org/file/bot" . $this->token . "/" . $arr['result']['file_path'];
					return $arr['result'];
				}
			}
		}
		
		public function postImage($info, $path, $external=false) {
			$chatID = $this->chatID;
			$url  = $this->api . $this->token . "/sendPhoto?chat_id=".$chatID;
			if($external) {
				$curl_file = new CURLFile($path);
			} else {
				$curl_file = new CURLFile(realpath($path));
			}
			
			$post_fields = array('chat_id' => $chatID,
				'caption' => $info,
				'photo' => $curl_file
			);
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
			$output = curl_exec($ch);
		}
		
		public function postMessage($message) {
			$queryURL = $this->api . $this->token . '/sendMessage?chat_id=' . $this->chatID . '&text=' . urlencode($message);
			file_get_contents($queryURL);
		}
		
		public function postMessageByTID($tid, $message) {
			$queryURL = $this->api . $this->token . '/sendMessage?chat_id=' . $tid . '&text=' . urlencode($message);
			file_get_contents($queryURL);
		}
	}