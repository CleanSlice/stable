<?php
namespace Opencart\Catalog\Controller\Extension\Stable\Module;
class Stable extends \Opencart\System\Engine\Controller {
	private $separator = '';
	
	public function __construct($registry) {
        parent::__construct($registry);

		if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
			$this->separator = '.';
		} else {
			$this->separator = '|';
		}
    }
									
	public function content_top_before(string $route, array &$data): void {					
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
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
						
			$this->load->model('extension/stable/module/stable');
						
			$chat_info = $this->model_extension_stable_module_stable->getChatByCustomerId($customer_id);
						
			if (empty($chat_info)) {
				$chat_id = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
				
				$chat_data = [
					'chat_id' => $chat_id,
					'session_id' => $session_id,
					'customer_id' => $customer_id,
					'date_reset' => date('Y-m-d H:i:s')
				];
				
				$this->model_extension_stable_module_stable->addChat($chat_data);
			} else {
				$chat_id = $chat_info['chat_id'];
				$date_reset	= $chat_info['date_reset'];			
				
				if (!empty($date_reset) && ((time() - strtotime($chat_info['date_reset'])) >= ($setting['side']['frontend']['chat_session_duration'] * 86400))) {
					$result = $this->model_extension_stable_module_stable->resetRanchChat($setting['side']['frontend']['api_key'], $setting['side']['frontend']['agent_id'], 'customer-' . $customer_id);

					if ($result) {
						$date_reset = date('Y-m-d H:i:s');
					}
				}				
				
				$chat_data = [
					'chat_id' => $chat_id,
					'session_id' => $session_id,
					'date_reset' => $date_reset
				];
				
				$this->model_extension_stable_module_stable->editChat($chat_data);
			}
			
			$ranch_data = [
				'sub' => 'customer-' . $customer_id,
				'email' => $customer_email,
				'expiresIn' => '1d'
			];
		
			$ranch_token = $this->model_extension_stable_module_stable->getRanchToken($setting['side']['frontend']['api_key'], $ranch_data);
			
			if (!empty($ranch_token)) {												
				$stable_api_url = $this->url->link('extension/stable/stable/frontend', '');
		
				setcookie('stable_chat_id', $chat_id, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_agent_id', $setting['side']['frontend']['agent_id'], time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_token', $ranch_token, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_api_url', $stable_api_url, time() + 60, '/', $this->request->server['HTTP_HOST']);
						
				$this->document->addScript('extension/stable/catalog/view/javascript/stable.js');
				
				$this->session->data['language_catalog'] = $this->config->get('config_language');
			}
		}
	}
}