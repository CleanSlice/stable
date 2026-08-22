<?php
class ControllerExtensionModuleStable extends Controller {
								
	public function content_top_before($route, &$data) {					
		$_config = new Config();
		$_config->load('stable');
				
		$config_setting = $_config->get('stable_setting');
				
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		$status = $this->config->get('module_stable_status');
		
		$store_id = $this->config->get('config_store_id');
								
		if ($status && $setting['side']['frontend']['status'] && in_array($store_id, $setting['side']['frontend']['store_id']) && $this->customer->isLogged()) {		
			$customer_id = $this->customer->getId();
			$customer_email = $this->customer->getEmail();
			$session_id = $this->session->getId();
			$chat_id = 'customer-' . $customer_id;
						
			$this->load->model('extension/module/stable');
						
			$chat_info = $this->model_extension_module_stable->getChatByCustomerId($customer_id);
						
			if (empty($chat_info)) {
				$chat_id = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
				
				$chat_data = array(
					'chat_id' => $chat_id,
					'session_id' => $session_id,
					'customer_id' => $customer_id,
					'date_reset' => date('Y-m-d H:i:s')
				);
				
				$this->model_extension_module_stable->addChat($chat_data);
			} else {
				$chat_id = $chat_info['chat_id'];
				$date_reset	= $chat_info['date_reset'];			
				
				if (!empty($date_reset) && ((time() - strtotime($chat_info['date_reset'])) >= ($setting['side']['frontend']['chat_session_duration'] * 86400))) {
					$result = $this->model_extension_module_stable->resetRanchChat($setting['side']['frontend']['api_key'], $setting['side']['frontend']['agent_id'], 'customer-' . $customer_id);

					if ($result) {
						$date_reset = date('Y-m-d H:i:s');
					}
				}				
				
				$chat_data = array(
					'chat_id' => $chat_id,
					'session_id' => $session_id,
					'date_reset' => $date_reset
				);
				
				$this->model_extension_module_stable->editChat($chat_data);
			}
			
			$ranch_data = array(
				'sub' => 'customer-' . $customer_id,
				'email' => $customer_email,
				'expiresIn' => '1d'
			);
		
			$ranch_token = $this->model_extension_module_stable->getRanchToken($setting['side']['frontend']['api_key'], $ranch_data);
			
			if (!empty($ranch_token)) {												
				$stable_api_url = $this->url->link('extension/stable/frontend', '', true);
		
				setcookie('stable_chat_id', $chat_id, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_agent_id', $setting['side']['frontend']['agent_id'], time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_token', $ranch_token, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_api_url', $stable_api_url, time() + 60, '/', $this->request->server['HTTP_HOST']);
						
				$this->document->addScript('catalog/view/javascript/stable/stable.js');
			}
		}
	}
}