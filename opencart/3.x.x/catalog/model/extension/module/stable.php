<?php
class ModelExtensionModuleStable extends Model {
	
	public function addChat($data) {		
		$sql = "INSERT INTO `" . DB_PREFIX . "stable_chat` SET";

		$implode = array();
		
		if (!empty($data['chat_id'])) {
			$implode[] = "`chat_id` = '" . $this->db->escape($data['chat_id']) . "'";
		}
						
		if (!empty($data['customer_id'])) {
			$implode[] = "`customer_id` = '" . (int)$data['customer_id'] . "'";
		}
		
		if (!empty($data['session_id'])) {
			$implode[] = "`session_id` = '" . $this->db->escape($data['session_id']) . "'";
		}
											
		if ($implode) {
			$sql .= implode(", ", $implode);
		}
		
		$this->db->query($sql);
	}
	
	public function editChat($data) {
		$sql = "UPDATE `" . DB_PREFIX . "stable_chat` SET";

		$implode = array();
				
		if (!empty($data['session_id'])) {
			$implode[] = "`session_id` = '" . $this->db->escape($data['session_id']) . "'";
		}
					
		if ($implode) {
			$sql .= implode(", ", $implode);
		}

		$sql .= " WHERE `chat_id` = '" . $this->db->escape($data['chat_id']) . "'";
		
		$this->db->query($sql);
	}
		
	public function getChat($chat_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stable_chat` WHERE `chat_id` = '" . $this->db->escape($chat_id) . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return array();
		}
	}
		
	public function getChatByCustomerId($customer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stable_chat` WHERE `customer_id` = '" . $this->db->escape($customer_id) . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return array();
		}
	}
	
	public function addChatAction($data) {		
		$sql = "INSERT INTO `" . DB_PREFIX . "stable_chat_action` SET";

		$implode = array();
		
		if (!empty($data['chat_id'])) {
			$implode[] = "`chat_id` = '" . $this->db->escape($data['chat_id']) . "'";
		}
						
		if (!empty($data['tool_code'])) {
			$implode[] = "`tool_code` = '" . $this->db->escape($data['tool_code']) . "'";
		}
		
		if (!empty($data['action_code'])) {
			$implode[] = "`action_code` = '" . $this->db->escape($data['action_code']) . "'";
		}
		
		if (!empty($data['action_message'])) {
			$implode[] = "`action_message` = '" . $this->db->escape($data['action_message']) . "'";
		}
		
		$implode[] = "`date_added` = NOW()";
													
		if ($implode) {
			$sql .= implode(", ", $implode);
		}
		
		$this->db->query($sql);
		
		$chat_action_id = $this->db->getLastId();
		
		return $chat_action_id;
	}
			
	public function getRanchToken($api_key, $data) {						
		$curl = curl_init();
			
		curl_setopt($curl, CURLOPT_URL, 'https://api.ranch.cleanslice.org/auth/embed/token');
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . $api_key));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($curl);
		
		curl_close($curl);
		
		$result = json_decode($response, true);
		
		if (!empty($result['data']['token'])) {
			return $result['data']['token'];
		} else {
			return false;
		}
	}
		
	public function log($input_data, $output_data, $action_code) {
		$_config = new Config();
		$_config->load('stable');
		
		$config_setting = $_config->get('stable_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		if ($setting['debug_status']) {
			$log = new Log('stable.log');
			
			$log->write("Stable debug (" . $action_code . ")" . "\n" . "Input data: " . json_encode($input_data) . "\n" . "Output data: " . json_encode($output_data));
		}
	}
}