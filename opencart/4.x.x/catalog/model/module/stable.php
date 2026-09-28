<?php
namespace Opencart\Catalog\Model\Extension\Stable\Module;
class Stable extends \Opencart\System\Engine\Model {
	
	public function addChat(array $data): void {		
		$sql = "INSERT INTO `" . DB_PREFIX . "stable_chat` SET";

		$implode = [];
		
		if (!empty($data['chat_id'])) {
			$implode[] = "`chat_id` = '" . $this->db->escape($data['chat_id']) . "'";
		}
						
		if (!empty($data['customer_id'])) {
			$implode[] = "`customer_id` = '" . (int)$data['customer_id'] . "'";
		}
		
		if (!empty($data['session_id'])) {
			$implode[] = "`session_id` = '" . $this->db->escape($data['session_id']) . "'";
		}
		
		if (!empty($data['date_reset'])) {
			$implode[] = "`date_reset` = '" . $this->db->escape($data['date_reset']) . "'";
		}
											
		if ($implode) {
			$sql .= implode(", ", $implode);
		}
		
		$this->db->query($sql);
	}
	
	public function editChat(array $data): void {
		$sql = "UPDATE `" . DB_PREFIX . "stable_chat` SET";

		$implode = [];
				
		if (!empty($data['session_id'])) {
			$implode[] = "`session_id` = '" . $this->db->escape($data['session_id']) . "'";
		}
		
		if (!empty($data['date_reset'])) {
			$implode[] = "`date_reset` = '" . $this->db->escape($data['date_reset']) . "'";
		}
					
		if ($implode) {
			$sql .= implode(", ", $implode);
		}

		$sql .= " WHERE `chat_id` = '" . $this->db->escape($data['chat_id']) . "'";
		
		$this->db->query($sql);
	}
		
	public function getChat(string $chat_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stable_chat` WHERE `chat_id` = '" . $this->db->escape($chat_id) . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}
		
	public function getChatByCustomerId(int $customer_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "stable_chat` WHERE `customer_id` = '" . $this->db->escape($customer_id) . "'");
		
		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}
	
	public function addChatAction(array $data): int {		
		$sql = "INSERT INTO `" . DB_PREFIX . "stable_chat_action` SET";

		$implode = [];
		
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
		
		$chat_action_id = (int)$this->db->getLastId();
		
		return $chat_action_id;
	}
			
	public function getRanchToken(string $api_key, array $data): string|bool {						
		$curl = curl_init();
			
		curl_setopt($curl, CURLOPT_URL, 'https://api.ranch.cleanslice.org/auth/embed/token');
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . $api_key]);
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
	
	public function resetRanchChat(string $api_key, string $agent_id, string $channel): bool {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, 'https://api.ranch.cleanslice.org/api/agent/' . $agent_id . '/transcript/archive?channel=' . urlencode($channel));
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . $api_key]);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '');

		$response = curl_exec($curl);
		
		curl_close($curl);

		$result = json_decode($response, true);
		
		if (!empty($result['success'])) {
			return true;
		} else {
			return false;
		}
	}
			
	public function log(array $input_data, array $output_data, string $action_code): void {
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
		$_config->load('stable');
		
		$config_setting = $_config->get('stable_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		if ($setting['debug_status']) {
			$log = new Log('stable.log');
			
			$log->write("Stable debug (" . $action_code . ")" . "\n" . "Input data: " . json_encode($input_data) . "\n" . "Output data: " . json_encode($output_data));
		}
	}
}