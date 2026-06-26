<?php
class ModelExtensionModuleStable extends Model {
								
	public function addChat($data) {			
		$sql = "INSERT INTO `" . DB_PREFIX . "stable_chat` SET";

		$implode = array();
		
		if (!empty($data['chat_id'])) {
			$implode[] = "`chat_id` = '" . $this->db->escape($data['chat_id']) . "'";
		}
						
		if (!empty($data['user_id'])) {
			$implode[] = "`user_id` = '" . (int)$data['user_id'] . "'";
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
		
	public function getChatByUserId($user_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stable_chat` WHERE `user_id` = '" . $this->db->escape($user_id) . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return array();
		}
	}
	
	public function getToolUsesChat($side_code, $tool_code) {
		if ($side_code == 'frontend') {
			$query = $this->db->query("SELECT COUNT(DISTINCT sca.`chat_id`) AS `total` FROM `" . DB_PREFIX . "stable_chat_action` sca LEFT JOIN `" . DB_PREFIX . "stable_chat` sc ON (sca.`chat_id` = sc.`chat_id`) WHERE sc.`customer_id` > '0' AND sca.`tool_code` = '" . $this->db->escape($tool_code) . "'");
		} else {
			$query = $this->db->query("SELECT COUNT(DISTINCT sca.`chat_id`) AS `total` FROM `" . DB_PREFIX . "stable_chat_action` sca LEFT JOIN `" . DB_PREFIX . "stable_chat` sc ON (sca.`chat_id` = sc.`chat_id`) WHERE sc.`user_id` > '0' AND sca.`tool_code` = '" . $this->db->escape($tool_code) . "'");
		}

		return $query->row['total'];
	}
	
	public function getToolUsesTotal($side_code, $tool_code) {
		if ($side_code == 'frontend') {
			$query = $this->db->query("SELECT COUNT(DISTINCT sca.`chat_action_id`) AS total FROM `" . DB_PREFIX . "stable_chat_action` sca LEFT JOIN `" . DB_PREFIX . "stable_chat` sc ON (sca.`chat_id` = sc.`chat_id`) WHERE sc.`customer_id` > '0' AND sca.`tool_code` = '" . $this->db->escape($tool_code) . "'");
		} else {
			$query = $this->db->query("SELECT COUNT(DISTINCT sca.`chat_action_id`) AS `total` FROM `" . DB_PREFIX . "stable_chat_action` sca LEFT JOIN `" . DB_PREFIX . "stable_chat` sc ON (sca.`chat_id` = sc.`chat_id`) WHERE sc.`user_id` > '0' AND sca.`tool_code` = '" . $this->db->escape($tool_code) . "'");
		}

		return $query->row['total'];
	}
	
	public function getRecentActions($side_code) {
		if ($side_code == 'frontend') {
			$query = $this->db->query("SELECT sca.`chat_action_id`, sca.`chat_id`, sc.`customer_id`, sc.`user_id`, sca.`tool_code`, sca.`action_code`, sca.`action_message`, sca.`date_added` FROM `" . DB_PREFIX . "stable_chat_action` sca LEFT JOIN `" . DB_PREFIX . "stable_chat` sc ON (sca.`chat_id` = sc.`chat_id`) WHERE sc.`customer_id` > '0' ORDER BY sca.`chat_action_id` DESC LIMIT 0,10");
		} else {
			$query = $this->db->query("SELECT sca.`chat_action_id`, sca.`chat_id`, sc.`customer_id`, sc.`user_id`, sca.`tool_code`, sca.`action_code`, sca.`action_message`, sca.`date_added` FROM `" . DB_PREFIX . "stable_chat_action` sca LEFT JOIN `" . DB_PREFIX . "stable_chat` sc ON (sca.`chat_id` = sc.`chat_id`) WHERE sc.`user_id` > '0' ORDER BY sca.`chat_action_id` DESC LIMIT 0,10");
		}
		
		return $query->rows;
	}
	
	public function checkVersion($opencart_version, $stable_version) {
		$curl = curl_init();
			
		curl_setopt($curl, CURLOPT_URL, 'https://www.opencart.com/index.php?route=api/promotion/stable&opencart=' . $opencart_version . '&stable=' . $stable_version);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
							
		$response = curl_exec($curl);
			
		curl_close($curl);
			
		$result = json_decode($response, true);
		
		if ($result) {
			return $result;
		} else {
			return false;
		}
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
			
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stable_chat` (`chat_id` VARCHAR(32) NOT NULL, `customer_id` INT(11) NOT NULL, `user_id` INT(11) NOT NULL, `session_id` VARCHAR(32) NOT NULL, PRIMARY KEY (`chat_id`), KEY `session_id` (`session_id`), KEY `customer_id` (`customer_id`), KEY `user_id` (`user_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stable_chat_action` (`chat_action_id` int(11) NOT NULL AUTO_INCREMENT, `chat_id` VARCHAR(32) NOT NULL, `tool_code` VARCHAR(32) NOT NULL, `action_code` VARCHAR(32) NOT NULL, `action_message` TEXT NOT NULL, `date_added` DATETIME NOT NULL, PRIMARY KEY (`chat_action_id`), KEY `chat_id` (`chat_id`), KEY `tool_code` (`tool_code`), KEY `action_code` (`action_code`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stable_chat`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "stable_chat_action`");
	}
}
