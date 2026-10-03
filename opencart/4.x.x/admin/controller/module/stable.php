<?php
namespace Opencart\Admin\Controller\Extension\Stable\Module;
class Stable extends \Opencart\System\Engine\Controller {
	private $error = [];
	private $separator = '';
	
	public function __construct($registry) {
        parent::__construct($registry);

		if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
			$this->separator = '.';
		} else {
			$this->separator = '|';
		}
    }
		
	public function index(): void {
		$this->load->language('extension/stable/module/stable');
		
		$this->document->addStyle('../extension/stable/admin/view/stylesheet/stable.css');
		$this->document->addStyle('view/stylesheet/stable/stable.css');
		$this->document->addStyle('../extension/stable/admin/view/stylesheet/bootstrap-switch.css');
		
		$this->document->addScript('../extension/stable/admin/view/javascript/bootstrap-switch.js');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/stable/module/stable');
		$this->load->model('setting/setting');
							
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/stable/module/stable', 'user_token=' . $this->session->data['user_token'])
		];
		
		$data['server'] = HTTP_SERVER;
		$data['catalog'] = HTTP_CATALOG;
		
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
		$_config->load('stable');
		
		$data['setting'] = $_config->get('stable_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('module_stable_setting'));
				
		$data['save'] = $this->url->link('extension/stable/module/stable' . $this->separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
		
		$data['status'] = $this->config->get('module_stable_status');			
		
		foreach ($data['setting']['side'] as $side) {
			foreach ($side['tool'] as $tool) {
				$data['setting']['side'][$side['code']]['tool'][$tool['code']]['uses_chat'] = $this->model_extension_stable_module_stable->getToolUsesChat($side['code'], $tool['code']);
				$data['setting']['side'][$side['code']]['tool'][$tool['code']]['uses_total'] = $this->model_extension_stable_module_stable->getToolUsesTotal($side['code'], $tool['code']);
			}
			
			$data['setting']['side'][$side['code']]['recent_action'] = [];
			
			$recent_actions = $this->model_extension_stable_module_stable->getRecentActions($side['code']);
		
			foreach ($recent_actions as $recent_action) {
				if (!empty($recent_action['customer_id'])) {
					$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['customer_id'] = $recent_action['customer_id'];
					$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['customer_href'] = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $recent_action['customer_id']);
				} else {
					$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['user_id'] = $recent_action['user_id'];
					$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['user_href'] = $this->url->link('user/user/edit', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $recent_action['user_id']);
				}
				
				$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['tool_code'] = $recent_action['tool_code'];
				$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['action_code'] = $recent_action['action_code'];
				$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['action_message'] = $recent_action['action_message'];
				$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['date_added'] = date($this->language->get('datetime_format'), strtotime($recent_action['date_added']));
			}
		}
		
		$data['stores'] = $this->model_extension_stable_module_stable->getStores();
		
		$result = $this->model_extension_stable_module_stable->checkVersion(VERSION, $data['setting']['extension']['version']);
		
		if (!empty($result['href'])) {
			$data['text_version'] = sprintf($this->language->get('text_version'), $result['href']);
		} else {
			$data['text_version'] = '';
		}
										
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
									
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/stable/module/stable', $data));
	}
				
	public function save(): void {
		$this->load->language('extension/stable/module/stable');
		
		$this->load->model('extension/stable/module/stable');
		$this->load->model('setting/setting');
						
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSave()) {
			$setting = $this->model_setting_setting->getSetting('module_stable');
			
			if (!empty($this->request->post['module_stable_setting']['side']['frontend']['store_id'])) {
				$setting['module_stable_setting']['side']['frontend']['store_id'] = [];
			}
			
			$setting = array_replace_recursive($setting, $this->request->post);
						
			$this->model_setting_setting->editSetting('module_stable', $setting);
														
			$data['success'] = $this->language->get('success_save');
		}
		
		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
			
	public function install() {		
		$this->load->model('extension/stable/module/stable');
		
		$this->model_extension_stable_module_stable->install();
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('stable_header');
		$this->model_setting_event->deleteEventByCode('stable_content_top');
		
		if (version_compare(VERSION, '4.0.2.0', '>=')) {
			$this->model_setting_event->addEvent(['code' => 'stable_header', 'description' => '', 'trigger' => 'admin/controller/common/header/before', 'action' => 'extension/stable/module/stable.header_before', 'status' => true, 'sort_order' => 1]);
			$this->model_setting_event->addEvent(['code' => 'stable_content_top', 'description' => '', 'trigger' => 'catalog/controller/common/content_top/before', 'action' => 'extension/stable/module/stable.content_top_before', 'status' => true, 'sort_order' => 2]);
		} elseif (version_compare(VERSION, '4.0.1.0', '>=')) {
			$this->model_setting_event->addEvent(['code' => 'stable_header', 'description' => '', 'trigger' => 'admin/controller/common/header/before', 'action' => 'extension/stable/module/stable|header_before', 'status' => true, 'sort_order' => 1]);
			$this->model_setting_event->addEvent(['code' => 'stable_content_top', 'description' => '', 'trigger' => 'catalog/controller/common/content_top/before', 'action' => 'extension/stable/module/stable|content_top_before', 'status' => true, 'sort_order' => 2]);
		} else {
			$this->model_setting_event->addEvent('stable_header', '', 'admin/controller/common/header/before', 'extension/stable/module/stable|header_before', true, 1);
			$this->model_setting_event->addEvent('stable_content_top', '', 'catalog/controller/common/content_top/before', 'extension/stable/module/stable|content_top_before', true, 2);
		}
						
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
		$_config->load('stable');
			
		$config_setting = $_config->get('stable_setting');
				
		$setting['stable_version'] = $config_setting['extension']['version'];
		
		$this->load->model('setting/setting');
		
		$this->model_setting_setting->editSetting('stable_version', $setting);
	}
		
	public function uninstall(): void {
		$this->load->model('extension/stable/module/stable');
		
		$this->model_extension_stable_module_stable->uninstall();
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('stable_header');
		$this->model_setting_event->deleteEventByCode('stable_content_top');
		
		$this->load->model('setting/setting');
		
		$this->model_setting_setting->deleteSetting('stable_version');
	}
		
	public function header_before(string $route, array &$data): void {
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
		$_config->load('stable');
		
		$config_setting = $_config->get('stable_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		$status = $this->config->get('module_stable_status');
		
		if ($status && $setting['side']['backend']['status'] && $this->user->isLogged()) {
			$user_id = $this->user->getId();
			$session_id = $this->session->getId();
						
			$this->load->model('user/user');
			
			$user_info = $this->model_user_user->getUser($user_id);
			
			$this->load->model('extension/stable/module/stable');
						
			$chat_info = $this->model_extension_stable_module_stable->getChatByUserId($user_id);
						
			if (empty($chat_info)) {
				$chat_id = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26); 
				
				$chat_data = [
					'chat_id' => $chat_id,
					'session_id' => $session_id,
					'user_id' => $user_id,
					'date_reset' => date('Y-m-d H:i:s')
				];
				
				$this->model_extension_stable_module_stable->addChat($chat_data);
			} else {
				$chat_id = $chat_info['chat_id'];
				$date_reset	= $chat_info['date_reset'];			
				
				if (!empty($date_reset) && ((time() - strtotime($chat_info['date_reset'])) >= ($setting['side']['backend']['chat_session_duration'] * 86400))) {
					$result = $this->model_extension_stable_module_stable->resetRanchChat($setting['side']['backend']['api_key'], $setting['side']['backend']['agent_id'], 'user-' . $user_id);

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
				'sub' => 'user-' . $user_id,
				'email' => $user_info['email'],
				'expiresIn' => '1d'
			];
		
			$ranch_token = $this->model_extension_stable_module_stable->getRanchToken($setting['side']['backend']['api_key'], $ranch_data);
			
			if (!empty($ranch_token)) {										
				$catalog = HTTP_CATALOG;
				
				$stable_api_url = $catalog . 'index.php?route=extension/stable/stable/backend';
		
				setcookie('stable_chat_id', $chat_id, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_agent_id', $setting['side']['backend']['agent_id'], time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_token', $ranch_token, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_api_url', $stable_api_url, time() + 60, '/', $this->request->server['HTTP_HOST']);
				
				$this->document->addScript('../extension/stable/admin/view/javascript/stable.js');
				
				$this->session->data['language_admin'] = $this->config->get('config_language');
			}
		}		
	}
	
	private function validateSave(): array|bool {
		if (!$this->user->hasPermission('modify', 'extension/stable/module/stable')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
						
		return !$this->error;
	}
}