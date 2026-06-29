<?php
class ControllerExtensionModuleStable extends Controller {
								
	public function content_top_before($route, &$data) {					
		$_config = new Config();
		$_config->load('stable');
				
		$config_setting = $_config->get('stable_setting');
				
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		$status = $this->config->get('module_stable_status');
								
		if ($status && $setting['side']['frontend']['status'] && $this->customer->isLogged()) {		
			$customer_id = $this->customer->getId();
			$customer_email = $this->customer->getEmail();
			$session_id = $this->session->getId();
			$chat_id = 'customer-' . $customer_id;
						
			$this->load->model('extension/module/stable');
						
			$chat_info = $this->model_extension_module_stable->getChatByCustomerId($customer_id);
						
			if (empty($chat_info)) {
				$chat_data = array(
					'chat_id' => $chat_id,
					'session_id' => $session_id,
					'customer_id' => $customer_id
				);
				
				$this->model_extension_module_stable->addChat($chat_data);
			} else {
				$chat_data = array(
					'chat_id' => $chat_id,
					'session_id' => $session_id
				);
				
				$this->model_extension_module_stable->editChat($chat_data);
			}
			
			$ranch_data = array(
				'sub' => $chat_id,
				'email' => $customer_email,
				'expiresIn' => '1d'
			);
		
			$ranch_token = $this->model_extension_module_stable->getRanchToken($setting['side']['frontend']['api_key'], $ranch_data);
			
			if (!empty($ranch_token)) {												
				$mcp_url = $this->url->link('extension/stable/frontend', '', true);
		
				setcookie('stable_chat_id', $chat_id, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_agent_id', $setting['side']['frontend']['agent_id'], time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_token', $ranch_token, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_mcp_url', $mcp_url, time() + 60, '/', $this->request->server['HTTP_HOST']);
						
				$this->document->addScript('catalog/view/javascript/stable/stable.js');
			}
		}
	}
}