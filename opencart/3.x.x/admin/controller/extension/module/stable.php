<?php
class ControllerExtensionModuleStable extends Controller {
	private $error = array();
		
	public function index() {
		$this->load->language('extension/module/stable');
		
		$this->document->addStyle('view/stylesheet/stable/stable.css');
		$this->document->addStyle('view/stylesheet/stable/bootstrap-switch.css');
		
		$this->document->addScript('view/javascript/stable/bootstrap-switch.js');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/stable');
		$this->load->model('setting/setting');
							
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/stable', 'user_token=' . $this->session->data['user_token'], true)
		);
				
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['server'] = HTTPS_SERVER;
			$data['catalog'] = HTTPS_CATALOG;
        } else {
            $data['server'] = HTTP_SERVER;
			$data['catalog'] = HTTP_CATALOG;
        }
				
		$_config = new Config();
		$_config->load('stable');
		
		$data['setting'] = $_config->get('stable_setting');
		
		$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('module_stable_setting'));
		
		$data['action'] = $this->url->link('extension/module/stable/save', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['status'] = $this->config->get('module_stable_status');			
		
		foreach ($data['setting']['side'] as $side) {
			foreach ($side['tool'] as $tool) {
				$data['setting']['side'][$side['code']]['tool'][$tool['code']]['uses_chat'] = $this->model_extension_module_stable->getToolUsesChat($side['code'], $tool['code']);
				$data['setting']['side'][$side['code']]['tool'][$tool['code']]['uses_total'] = $this->model_extension_module_stable->getToolUsesTotal($side['code'], $tool['code']);
			}
			
			$data['setting']['side'][$side['code']]['recent_action'] = array();
			
			$recent_actions = $this->model_extension_module_stable->getRecentActions($side['code']);
		
			foreach ($recent_actions as $recent_action) {
				if (!empty($recent_action['customer_id'])) {
					$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['customer_id'] = $recent_action['customer_id'];
					$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['customer_href'] = $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $recent_action['customer_id'], true);
				} else {
					$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['user_id'] = $recent_action['user_id'];
					$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['user_href'] = $this->url->link('user/user/edit', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $recent_action['user_id'], true);
				}
				
				$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['tool_code'] = $recent_action['tool_code'];
				$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['action_code'] = $recent_action['action_code'];
				$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['action_message'] = $recent_action['action_message'];
				$data['setting']['side'][$side['code']]['recent_action'][$recent_action['chat_action_id']]['date_added'] = date($this->language->get('datetime_format'), strtotime($recent_action['date_added']));
			}
		}
		
		$result = $this->model_extension_module_stable->checkVersion(VERSION, $data['setting']['extension']['version']);
		
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

		$this->response->setOutput($this->load->view('extension/module/stable', $data));
	}
				
	public function save() {
		$this->load->language('extension/module/stable');
		
		$this->load->model('extension/module/stable');
		$this->load->model('setting/setting');
						
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSave()) {
			$setting = $this->model_setting_setting->getSetting('module_stable');
			
			$setting = array_replace_recursive($setting, $this->request->post);
						
			$this->model_setting_setting->editSetting('module_stable', $setting);
														
			$data['success'] = $this->language->get('success_save');
		}
		
		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
			
	public function install() {		
		$this->load->model('extension/module/stable');
		
		$this->model_extension_module_stable->install();
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('stable_header');
		$this->model_setting_event->deleteEventByCode('stable_content_top');
		
		$this->model_setting_event->addEvent('stable_header', 'admin/controller/common/header/before', 'extension/module/stable/header_before');
		$this->model_setting_event->addEvent('stable_content_top', 'catalog/controller/common/content_top/before', 'extension/module/stable/content_top_before');
						
		$_config = new Config();
		$_config->load('stable');
			
		$config_setting = $_config->get('stable_setting');
				
		$setting['stable_version'] = $config_setting['extension']['version'];
		
		$this->load->model('setting/setting');
		
		$this->model_setting_setting->editSetting('stable_version', $setting);
	}
		
	public function uninstall() {
		$this->load->model('extension/module/stable');
		
		$this->model_extension_module_stable->uninstall();
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('stable_header');
		$this->model_setting_event->deleteEventByCode('stable_content_top');
		
		$this->load->model('setting/setting');
		
		$this->model_setting_setting->deleteSetting('stable_version');
	}
		
	public function header_before($route, &$data) {	
		$_config = new Config();
		$_config->load('stable');
				
		$config_setting = $_config->get('stable_setting');
				
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		$status = $this->config->get('module_stable_status');
		
		if ($status && $setting['side']['backend']['status'] && $this->user->isLogged()) {
			$user_id = $this->user->getId();
			$session_id = $this->session->getId();
			$chat_id = 'user-' . $user_id;
			
			$this->load->model('user/user');
			
			$user_info = $this->model_user_user->getUser($user_id);
			
			$this->load->model('extension/module/stable');
						
			$chat_info = $this->model_extension_module_stable->getChatByUserId($user_id);
						
			if (empty($chat_info)) {
				$chat_data = array(
					'chat_id' => $chat_id,
					'session_id' => $session_id,
					'user_id' => $user_id
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
				'email' => $user_info['email'],
				'expiresIn' => '7d'
			);
		
			$ranch_token = $this->model_extension_module_stable->getRanchToken($setting['side']['backend']['api_key'], $ranch_data);
			
			if (!empty($ranch_token)) {										
				if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
					$catalog = HTTPS_CATALOG;
				} else {
					$catalog = HTTP_CATALOG;
				}
				
				$mcp_url = $catalog . 'index.php?route=extension/stable/backend';
		
				setcookie('stable_chat_id', $chat_id, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_agent_id', $setting['side']['backend']['agent_id'], time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_token', $ranch_token, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_mcp_url', $mcp_url, time() + 60, '/', $this->request->server['HTTP_HOST']);
				
				$this->document->addScript('view/javascript/stable/stable.js');
			}
		}		
	}
	
	private function validateSave() {
		if (!$this->user->hasPermission('modify', 'extension/module/stable')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
						
		return !$this->error;
	}
}