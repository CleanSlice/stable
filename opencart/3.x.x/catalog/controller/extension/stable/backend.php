<?php
class ControllerExtensionStableBackend extends Controller {
								
	public function index() {
		$_config = new Config();
		$_config->load('stable');
				
		$config_setting = $_config->get('stable_setting');
				
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
						
		$data = array(
			'jsonrpc' => '2.0',
            'result' => array(
				'tools' => array(),
				'serverInfo' => array(
                    'name' => 'php-mcp-server',
                    'version' => '1.0.0'
                )
			)
		);
		
		$data['result']['tools']['getCategory'] = array(
			'name' => 'getCategory',
			'description' => 'Get information about the product category',
			'endpoint' => $this->url->link('extension/stable/backend/getCategory', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'category_id' => array('type' => 'number', 'description' => 'Category ID')
			),
			'required' => array('chat_id', 'category_id')
		);
		
		$data['result']['tools']['getCategories'] = array(
			'name' => 'getCategories',
			'description' => 'Get information about product categories',
			'endpoint' => $this->url->link('extension/stable/backend/getCategories', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'parent_id' => array('type' => 'number', 'description' => 'Parent Category ID', 'default' => '0')
			),
			'required' => array('chat_id')
		);
		
		$data['result']['tools']['getProduct'] = array(
			'name' => 'getProduct',
			'description' => 'Get information about the product',
			'endpoint' => $this->url->link('extension/stable/backend/getProduct', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'product_id' => array('type' => 'number', 'description' => 'Product ID')
			),
			'required' => array('chat_id', 'product_id')
		);
		
		$data['result']['tools']['getProducts'] = array(
			'name' => 'getProducts',
			'description' => 'Get information about products',
			'endpoint' =>$this->url->link('extension/stable/backend/getProducts', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'name' => array('type' => 'string', 'description' => 'Search products by name', 'default' => ''),
				'model' => array('type' => 'string', 'description' => 'Search products by model', 'default' => ''),
				'tag' => array('type' => 'string', 'description' => 'Search products by tag', 'default' => ''),
				'description' => array('type' => 'string', 'description' => 'Search products by description', 'default' => ''),
				'category_id' => array('type' => 'number', 'description' => 'Search products in the category with this ID', 'default' => '0'),
				'page' => array('type' => 'number', 'description' => 'Page number in the product search results', 'default' => '1')
			),
			'required' => array('chat_id')
		);
		
		$data['result']['tools']['getCustomer'] = array(
			'name' => 'getCustomer',
			'description' => 'Get information about the customer',
			'endpoint' => $this->url->link('extension/stable/backend/getCustomer', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'customer_id' => array('type' => 'number', 'description' => 'Customer ID')
			),
			'required' => array('chat_id', 'customer_id')
		);
		
		$data['result']['tools']['getCustomers'] = array(
			'name' => 'getCustomers',
			'description' => 'Get information about customers',
			'endpoint' => $this->url->link('extension/stable/backend/getCustomers', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'name' => array('type' => 'string', 'description' => 'Search customers by name', 'default' => ''),
				'email' => array('type' => 'string', 'description' => 'Search customers by e-mail', 'default' => ''),
				'customer_group_id' => array('type' => 'number', 'description' => 'Search customers by customer group ID', 'default' => '0'),
				'page' => array('type' => 'number', 'description' => 'Page number in the customer search results', 'default' => '1')
			),
			'required' => array('chat_id')
		);
		
		$data['result']['tools']['getCustomerGroups'] = array(
			'name' => 'getCustomerGroups',
			'description' => 'Get information about customer groups',
			'endpoint' => $this->url->link('extension/stable/backend/getCustomerGroups', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
			),
			'required' => array('chat_id')
		);			
	
		$data['result']['tools']['getOrder'] = array(
			'name' => 'getOrder',
			'description' => 'Get information about the order',
			'endpoint' => $this->url->link('extension/stable/backend/getOrder', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'customer_id' => array('type' => 'number', 'description' => 'Order ID')
			),
			'required' => array('chat_id', 'order_id')
		);
		
		$data['result']['tools']['getOrders'] = array(
			'name' => 'getOrders',
			'description' => 'Get information about orders',
			'endpoint' => $this->url->link('extension/stable/backend/getOrders', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'customer_name' => array('type' => 'string', 'description' => 'Search orders by customer name', 'default' => ''),
				'order_status_id' => array('type' => 'number', 'description' => 'Search orders by order status ID', 'default' => ''),
				'date_added_from' => array('type' => 'string', 'format' => 'date', 'description' => 'Search orders by date added, starting from this date (Format: YYYY-MM-DD)', 'default' => ''),
				'date_added_to' => array('type' => 'string', 'format' => 'date', 'description' => 'Search orders by date added, ending with this date (Format: YYYY-MM-DD)', 'default' => ''),
				'page' => array('type' => 'number', 'description' => 'Page number in the order search results', 'default' => '1')
			),
			'required' => array('chat_id')
		);
		
		$data['result']['tools']['getOrderStatuses'] = array(
			'name' => 'getOrderStatuses',
			'description' => 'Get information about order statuses',
			'endpoint' => $this->url->link('extension/stable/backend/getOrderStatuses', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
			),
			'required' => array('chat_id')
		);
	
		$data['result']['tools']['getCountries'] = array(
			'name' => 'getCountries',
			'description' => 'Get information about countries',
			'endpoint' => $this->url->link('extension/stable/backend/getCountries', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
			),
			'required' => array('chat_id')
		);
		
		$data['result']['tools']['getZonesByCountryId'] = array(
			'name' => 'getZonesByCountryId',
			'description' => 'Get information about zones for this country ID',
			'endpoint' => $this->url->link('extension/stable/backend/getZonesByCountryId', '', true),
			'requestMethod' => 'GET',
			'properties' => array(
				'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				'country_id' => array('type' => 'number', 'description' => 'Country ID')
			),
			'required' => array('chat_id', 'country_id')
		);
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCategory() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
				
			if (empty($this->request->get['category_id'])) {
				$error = 'Category ID required!';
			}
						
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('product')) {
						$this->model_extension_stable_backend->refreshStartup($chat);
									
						$category = $this->model_extension_stable_backend->getCategory($this->request->get['category_id']);
					
						if ($category) {
							$data = array(
								'jsonrpc' => "2.0",
								'result' => $category
							);
							
							$chat_action_data = array(
								'chat_id' => $chat['chat_id'],
								'tool_code' => 'product',
								'action_code' => 'getCategory',
								'action_message' => sprintf('Get information about a product category with ID %s.', $category['category_id'])
							);
							
							$this->model_extension_module_stable->addChatAction($chat_action_data);
						} else {
							$error = 'Category not found!';
						}
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getCategory');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCategories() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
										
			if (!$error) {				
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('product')) {
						$this->model_extension_stable_backend->refreshStartup($chat);
					
						if (!empty($this->request->get['parent_id'])) {
							$parent_id = $this->request->get['parent_id'];
						} else {
							$parent_id = 0;
						}
						
						$categories = $this->model_extension_stable_backend->getCategories($parent_id);
							
						$data = array(
							'jsonrpc' => "2.0",
							'result' => array(
								'categories' => $categories
							)
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'product',
							'action_code' => 'getCategories',
							'action_message' => 'Get information about product categories.'
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getCategories');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getProduct() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (empty($this->request->get['product_id'])) {
				$error = 'Product ID required!';
			}
										
			if (!$error) {				
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('product')) {
						$this->model_extension_stable_backend->refreshStartup($chat);
								
						$product = $this->model_extension_stable_backend->getProduct($this->request->get['product_id']);
					
						if ($product) {
							$data = array(
								'jsonrpc' => "2.0",
								'result' => $product
							);
							
							$chat_action_data = array(
								'chat_id' => $chat['chat_id'],
								'tool_code' => 'product',
								'action_code' => 'getProduct',
								'action_message' => sprintf('Get information about the product with ID %s.', $product['product_id'])
							);
							
							$this->model_extension_module_stable->addChatAction($chat_action_data);
						} else {
							$error = 'Product not found!';
						}
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getProduct');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getProducts() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {						
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (!$error) {				
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('product')) {
						$this->model_extension_stable_backend->refreshStartup($chat);

						if (!empty($this->request->get['name'])) {
							$name = $this->request->get['name'];
						} else {
							$name = '';
						}
						
						if (!empty($this->request->get['model'])) {
							$model = $this->request->get['model'];
						} else {
							$model = '';
						}
						
						if (!empty($this->request->get['tag'])) {
							$tag = $this->request->get['tag'];
						} else {
							$tag = '';
						}
						
						if (!empty($this->request->get['description'])) {
							$description = $this->request->get['description'];
						} else {
							$description = '';
						}
						
						if (!empty($this->request->get['category_id'])) {
							$category_id = $this->request->get['category_id'];
						} else {
							$category_id = 0;
						}
											
						if (!empty($this->request->get['page'])) {
							$page = $this->request->get['page'];
						} else {
							$page = 1;
						}

						$limit = 100;
									
						$filter_data = array(
							'filter_name'         => $name,
							'filter_model'        => $model,
							'filter_tag'          => $tag,
							'filter_description'  => $description,
							'filter_category_id'  => $category_id,
							'start'               => ($page - 1) * $limit,
							'limit'               => $limit
						);
																
						$product_total = $this->model_extension_stable_backend->getTotalProducts($filter_data);
							
						$products = $this->model_extension_stable_backend->getProducts($filter_data);
							
						$data = array(
							'jsonrpc' => "2.0",
							'result' => array(
								'products' => $products,
								'productCount' => $product_total,
								'page' => $page,
								'pageCount' => ceil($product_total / $limit)
							)
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'product',
							'action_code' => 'getProducts',
							'action_message' => 'Get information about products.'
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getProducts');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
		
	public function getCustomer() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {			
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (empty($this->request->get['customer_id'])) {
				$error = 'Customer ID required!';
			}
										
			if (!$error) {				
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('customer')) {
						$this->model_extension_stable_backend->refreshStartup($chat);
								
						$customer = $this->model_extension_stable_backend->getCustomer($this->request->get['customer_id']);
					
						if ($customer) {
							$data = array(
								'jsonrpc' => "2.0",
								'result' => $customer
							);
							
							$chat_action_data = array(
								'chat_id' => $chat['chat_id'],
								'tool_code' => 'customer',
								'action_code' => 'getCustomer',
								'action_message' => sprintf('Get information about the customer with ID %s.', $customer['customer_id'])
							);
							
							$this->model_extension_module_stable->addChatAction($chat_action_data);
						} else {
							$error = 'Customer not found!';
						}
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getCustomer');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCustomers() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {			
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('customer')) {
						$this->model_extension_stable_backend->refreshStartup($chat);

						if (!empty($this->request->get['name'])) {
							$name = $this->request->get['name'];
						} else {
							$name = '';
						}
						
						if (!empty($this->request->get['email'])) {
							$email = $this->request->get['email'];
						} else {
							$email = '';
						}
											
						if (!empty($this->request->get['customer_group_id'])) {
							$customer_group_id = $this->request->get['customer_group_id'];
						} else {
							$customer_group_id = 0;
						}
											
						if (!empty($this->request->get['page'])) {
							$page = $this->request->get['page'];
						} else {
							$page = 1;
						}

						$limit = 100;
									
						$filter_data = array(
							'filter_name'         => $name,
							'filter_email'  	  => $email,
							'filter_customer_id'  => $customer_group_id,
							'start'               => ($page - 1) * $limit,
							'limit'               => $limit
						);
																
						$customer_total = $this->model_extension_stable_backend->getTotalCustomers($filter_data);
							
						$customers = $this->model_extension_stable_backend->getCustomers($filter_data);
							
						$data = array(
							'jsonrpc' => "2.0",
							'result' => array(
								'customers' => $customers,
								'customerCount' => $customer_total,
								'page' => $page,
								'pageCount' => ceil($customer_total / $limit)
							)
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'customer',
							'action_code' => 'getCustomers',
							'action_message' => 'Get information about customers.'
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
			
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getCustomers');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCustomerGroups() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('customer')) {
						$this->model_extension_stable_backend->refreshStartup($chat);
								
						$customer_groups = $this->model_extension_stable_backend->getCustomerGroups();
					
						$data = array(
							'jsonrpc' => "2.0",
							'result' => array(
								'customer_groups' => $customer_groups
							)
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'customer',
							'action_code' => 'getCustomerGroups',
							'action_message' => 'Get information about customer groups.'
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getCustomerGroups');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getOrder() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (empty($this->request->get['order_id'])) {
				$error = 'Order ID required!';
			}
										
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('order')) {
						$this->model_extension_stable_backend->refreshStartup($chat);
								
						$order = $this->model_extension_stable_backend->getOrder($this->request->get['order_id']);
					
						if ($order) {
							$data = array(
								'jsonrpc' => "2.0",
								'result' => $order
							);
							
							$chat_action_data = array(
								'chat_id' => $chat['chat_id'],
								'tool_code' => 'order',
								'action_code' => 'getOrder',
								'action_message' => sprintf('Get information about the order with ID %s.', $order['order_id'])
							);
							
							$this->model_extension_module_stable->addChatAction($chat_action_data);
						} else {
							$error = 'Order not found!';
						}
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getOrder');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getOrders() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {						
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('order')) {
						$this->model_extension_stable_backend->refreshStartup($chat);

						if (!empty($this->request->get['customer_name'])) {
							$customer_name = $this->request->get['customer_name'];
						} else {
							$customer_name = '';
						}
																
						if (!empty($this->request->get['order_status_id'])) {
							$order_status_id = $this->request->get['order_status_id'];
						} else {
							$order_status_id = '';
						}
						
						if (!empty($this->request->get['date_added_from'])) {
							$date_added_from = $this->request->get['date_added_from'];
						} else {
							$date_added_from = '';
						}
						
						if (!empty($this->request->get['date_added_to'])) {
							$date_added_to = $this->request->get['date_added_to'];
						} else {
							$date_added_to = '';
						}
											
						if (!empty($this->request->get['page'])) {
							$page = $this->request->get['page'];
						} else {
							$page = 1;
						}

						$limit = 100;
									
						$filter_data = array(
							'filter_customer_name'  	=> $customer_name,
							'filter_order_status_id'  	=> $order_status_id,
							'filter_date_added_from'    => $date_added_from,
							'filter_date_added_to'    	=> $date_added_to,
							'start'               		=> ($page - 1) * $limit,
							'limit'               		=> $limit
						);
																
						$order_total = $this->model_extension_stable_backend->getTotalOrders($filter_data);
							
						$orders = $this->model_extension_stable_backend->getOrders($filter_data);
							
						$data = array(
							'jsonrpc' => "2.0",
							'result' => array(
								'orders' => $orders,
								'orderCount' => $order_total,
								'page' => $page,
								'pageCount' => ceil($order_total / $limit)
							)
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'order',
							'action_code' => 'getOrders',
							'action_message' => 'Get information about orders.'
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getOrders');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getOrderStatuses() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission('order')) {
						$this->model_extension_stable_backende->refreshStartup($chat);
								
						$order_statuses = $this->model_extension_stable_backend->getOrderStatuses();
					
						$data = array(
							'jsonrpc' => "2.0",
							'result' => array(
								'order_statuses' => $order_statuses
							)
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'order',
							'action_code' => 'getOrderStatuses',
							'action_message' => 'Get information about order statuses.'
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getOrderStatuses');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
				
	public function getCountries() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {			
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (!$error) {				
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					$this->model_extension_stable_backend->refreshStartup($chat);
							
					$countries = $this->model_extension_stable_backend->getCountries();
				
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'countries' => $countries
						)
					);
					
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => '',
						'action_code' => 'getCountries',
						'action_message' => 'Get information about countries.'
					);
				
					$this->model_extension_module_stable->addChatAction($chat_action_data);
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getCountries');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getZonesByCountryId() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
			
			if (empty($this->request->get['country_id'])) {
				$error = 'Country ID required!';
			}
							
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					$this->model_extension_stable_backend->refreshStartup($chat);

					$zones = $this->model_extension_stable_backend->getZonesByCountryId($this->request->get['country_id']);
					
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'zones' => $zones
						)
					);
					
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => '',
						'action_code' => 'getZonesByCountryId',
						'action_message' => sprintf('Get information about zones for country with ID %s.', $this->request->get['country_id'])
					);
				
					$this->model_extension_module_stable->addChatAction($chat_action_data);
				} else {
					$error = 'Chat not found!';
				}
			}
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getZonesByCountryId');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
		
	private function validateToolPermission($tool_code) {
		$_config = new Config();
		$_config->load('stable');
				
		$config_setting = $_config->get('stable_setting');
				
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		$permission = false;
							
		if (!empty($setting['side']['backend']['tool'][$tool_code]['status'])) {
			$permission = true;
		}
						
		return $permission;
	}
}