<?php
class ControllerExtensionStableBackend extends Controller {
	private $errors = array();
	
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
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'category_id' => array('type' => 'number', 'description' => 'Category ID')
				),
				'required' => array('chat_id', 'category_id')
			)
		);
		
		$data['result']['tools']['getCategories'] = array(
			'name' => 'getCategories',
			'description' => 'Get information about product categories',
			'endpoint' => $this->url->link('extension/stable/backend/getCategories', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'name' => array('type' => 'string', 'description' => 'Search categories by name'),
					'parent_category_id' => array('type' => 'number', 'description' => 'Search categories by parent Category ID'),
					'status' => array('type' => 'number', 'description' => 'Search categories by status (1/0)'),
					'sort' => array('type' => 'string', 'description' => 'Sort in the category search results (name/status/sort_order)', 'default' => 'sort_order'),
					'order' => array('type' => 'string', 'description' => 'Order in the category search results (ASC/DESC)', 'default' => 'ASC'),
					'page' => array('type' => 'number', 'description' => 'Page number in the category search results', 'default' => 1)
				),
				'required' => array('chat_id')
			)
		);
		
		$data['result']['tools']['getManufacturer'] = array(
			'name' => 'getManufacturer',
			'description' => 'Get information about the product manufacturer',
			'endpoint' => $this->url->link('extension/stable/backend/getManufacturer', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'manufacturer_id' => array('type' => 'number', 'description' => 'Manufacturer ID')
				),
				'required' => array('chat_id', 'manufacturer_id')
			)
		);
		
		$data['result']['tools']['getManufacturers'] = array(
			'name' => 'getManufacturers',
			'description' => 'Get information about product manufacturers',
			'endpoint' => $this->url->link('extension/stable/backend/getManufacturers', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'name' => array('type' => 'string', 'description' => 'Search manufacturers by name'),
					'sort' => array('type' => 'string', 'description' => 'Sort in the manufacturer search results (name/sort_order)', 'default' => 'name'),
					'order' => array('type' => 'string', 'description' => 'Order in the manufacturer search results (ASC/DESC)', 'default' => 'ASC'),
					'page' => array('type' => 'number', 'description' => 'Page number in the manufacturer search results', 'default' => 1)
				),
				'required' => array('chat_id')
			)
		);
		
		$data['result']['tools']['getProduct'] = array(
			'name' => 'getProduct',
			'description' => 'Get information about the product',
			'endpoint' => $this->url->link('extension/stable/backend/getProduct', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'product_id' => array('type' => 'number', 'description' => 'Product ID')
				),
				'required' => array('chat_id', 'product_id')
			)
		);
		
		$data['result']['tools']['getProducts'] = array(
			'name' => 'getProducts',
			'description' => 'Get information about products',
			'endpoint' =>$this->url->link('extension/stable/backend/getProducts', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'name' => array('type' => 'string', 'description' => 'Search products by name'),
					'model' => array('type' => 'string', 'description' => 'Search products by model'),
					'price_min' => array('type' => 'number', 'description' => 'Search products with a price greater than this value'),
					'price_max' => array('type' => 'number', 'description' => 'Search products with a price less than this value'),
					'quantity_min' => array('type' => 'number', 'description' => 'Search products with a quantity greater than this value'),
					'quantity_max' => array('type' => 'number', 'description' => 'Search products with a quantity less than this value'),
					'status' => array('type' => 'number', 'description' => 'Search products by status (1/0)'),
					'manufacturer_id' => array('type' => 'number', 'description' => 'Search products with this manufacturer ID'),
					'category_id' => array('type' => 'number', 'description' => 'Search products in the category with this ID'),
					'date_added_from' => array('type' => 'string', 'format' => 'date', 'description' => 'Search products by date added, starting from this date (Format: YYYY-MM-DD)'),
					'date_added_to' => array('type' => 'string', 'format' => 'date', 'description' => 'Search products by date added, ending with this date (Format: YYYY-MM-DD)'),
					'sort' => array('type' => 'string', 'description' => 'Sort in the product search results (name/model/price/quantity/status/sort_order/date_added/manufacturer/rating)', 'default' => 'sort_order'),
					'order' => array('type' => 'string', 'description' => 'Order in the product search results (ASC/DESC)', 'default' => 'ASC'),
					'page' => array('type' => 'number', 'description' => 'Page number in the product search results', 'default' => 1)
				),
				'required' => array('chat_id')
			)
		);
		
		$data['result']['tools']['getCustomer'] = array(
			'name' => 'getCustomer',
			'description' => 'Get information about the customer',
			'endpoint' => $this->url->link('extension/stable/backend/getCustomer', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'customer_id' => array('type' => 'number', 'description' => 'Customer ID')
				),
				'required' => array('chat_id', 'customer_id')
			)
		);
		
		$data['result']['tools']['getCustomers'] = array(
			'name' => 'getCustomers',
			'description' => 'Get information about customers',
			'endpoint' => $this->url->link('extension/stable/backend/getCustomers', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'name' => array('type' => 'string', 'description' => 'Search customers by name'),
					'email' => array('type' => 'string', 'description' => 'Search customers by e-mail'),
					'customer_group_id' => array('type' => 'number', 'description' => 'Search customers by customer group ID'),
					'status' => array('type' => 'number', 'description' => 'Search customers by status (1/0)'),
					'date_added_from' => array('type' => 'string', 'format' => 'date', 'description' => 'Search customers by date added, starting from this date (Format: YYYY-MM-DD)'),
					'date_added_to' => array('type' => 'string', 'format' => 'date', 'description' => 'Search customers by date added, ending with this date (Format: YYYY-MM-DD)'),
					'sort' => array('type' => 'string', 'description' => 'Sort in the customer search results (name/email/customer_group/status/date_added)', 'default' => 'name'),
					'order' => array('type' => 'string', 'description' => 'Order in the customer search results (ASC/DESC)', 'default' => 'ASC'),
					'page' => array('type' => 'number', 'description' => 'Page number in the customer search results', 'default' => 1)
				),
				'required' => array('chat_id')
			)
		);
		
		$data['result']['tools']['getCustomerGroups'] = array(
			'name' => 'getCustomerGroups',
			'description' => 'Get information about customer groups',
			'endpoint' => $this->url->link('extension/stable/backend/getCustomerGroups', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
				),
				'required' => array('chat_id')
			)
		);			
	
		$data['result']['tools']['getOrder'] = array(
			'name' => 'getOrder',
			'description' => 'Get information about the order',
			'endpoint' => $this->url->link('extension/stable/backend/getOrder', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'order_id' => array('type' => 'number', 'description' => 'Order ID')
				),
				'required' => array('chat_id', 'order_id')
			)
		);
		
		$data['result']['tools']['getOrders'] = array(
			'name' => 'getOrders',
			'description' => 'Get information about orders',
			'endpoint' => $this->url->link('extension/stable/backend/getOrders', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'customer_name' => array('type' => 'string', 'description' => 'Search orders by customer name'),
					'order_status_id' => array('type' => 'number', 'description' => 'Search orders by order status ID'),
					'total_min' => array('type' => 'number', 'description' => 'Search orders with a total greater than this value'),
					'total_max' => array('type' => 'number', 'description' => 'Search orders with a total less than this value'),
					'date_added_from' => array('type' => 'string', 'format' => 'date', 'description' => 'Search orders by date added, starting from this date (Format: YYYY-MM-DD)'),
					'date_added_to' => array('type' => 'string', 'format' => 'date', 'description' => 'Search orders by date added, ending with this date (Format: YYYY-MM-DD)'),
					'sort' => array('type' => 'string', 'description' => 'Sort in the order search results (order_id/customer_name/order_status/total/date_added)', 'default' => 'order_id'),
					'order' => array('type' => 'string', 'description' => 'Order in the order search results (ASC/DESC)', 'default' => 'DESC'),
					'page' => array('type' => 'number', 'description' => 'Page number in the order search results', 'default' => 1)
				),
				'required' => array('chat_id')
			)
		);

		$data['result']['tools']['getOrderStatuses'] = array(
			'name' => 'getOrderStatuses',
			'description' => 'Get information about order statuses',
			'endpoint' => $this->url->link('extension/stable/backend/getOrderStatuses', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
				),
				'required' => array('chat_id')
			)
		);
	
		$data['result']['tools']['getCountries'] = array(
			'name' => 'getCountries',
			'description' => 'Get information about countries',
			'endpoint' => $this->url->link('extension/stable/backend/getCountries', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
				),
				'required' => array('chat_id')
			)
		);
		
		$data['result']['tools']['getZonesByCountryId'] = array(
			'name' => 'getZonesByCountryId',
			'description' => 'Get information about zones for this country ID',
			'endpoint' => $this->url->link('extension/stable/backend/getZonesByCountryId', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'country_id' => array('type' => 'number', 'description' => 'Country ID')
				),
				'required' => array('chat_id', 'country_id')
			)
		);
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCategory() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
			
		if (empty($request['category_id'])) {
			$this->errors[] = 'Category ID required!';
		}
					
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_backend->refreshStartup($chat);
								
					$category = $this->model_extension_stable_backend->getCategory($request['category_id']);
				
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
						$this->errors[] = 'Category not found!';
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getCategory');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCategories() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
									
		if (!$this->errors)	{			
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_backend->refreshStartup($chat);
				
					if (!empty($request['name'])) {
						$name = $request['name'];
					} else {
						$name = '';
					}
					
					if (isset($request['status']) && $request['status'] !== '') {
						$status = $request['status'];
					} else {
						$status = '';
					}
					
					if (!empty($request['parent_category_id'])) {
						$parent_category_id = $request['parent_category_id'];
					} else {
						$parent_category_id = 0;
					}
					
					if (!empty($request['sort'])) {
						$sort = $request['sort'];
					} else {
						$sort = 'sort_order';
					}

					if (!empty($request['order'])) {
						$order = $request['order'];
					} else {
						$order = 'ASC';
					}
										
					if (!empty($request['page'])) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$limit = 20;
								
					$filter_data = array(
						'filter_name'         		=> $name,
						'filter_parent_category_id' => $parent_category_id,
						'sort'                		=> $sort,
						'order'               		=> $order,
						'start'               		=> ($page - 1) * $limit,
						'limit'               		=> $limit
					);
										
					$category_total = $this->model_extension_stable_backend->getTotalCategories($filter_data);
						
					$categories = $this->model_extension_stable_backend->getCategories($filter_data);
						
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'categories' => $categories,
							'categoryCount' => $category_total,
							'page' => $page,
							'pageCount' => ceil($category_total / $limit)
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
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getCategories');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getManufacturer() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
			
		if (empty($request['manufacturer_id'])) {
			$this->errors[] = 'Manufacturer ID required!';
		}
					
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_backend->refreshStartup($chat);
								
					$manufacturer = $this->model_extension_stable_backend->getManufacturer($request['manufacturer_id']);
				
					if ($manufacturer) {
						$data = array(
							'jsonrpc' => "2.0",
							'result' => $manufacturer
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'product',
							'action_code' => 'getManufacturer',
							'action_message' => sprintf('Get information about a product manufacturer with ID %s.', $manufacturer['manufacturer_id'])
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
					} else {
						$this->errors[] = 'Manufacturer not found!';
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getManufacturer');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getManufacturers() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
									
		if (!$this->errors)	{			
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_backend->refreshStartup($chat);
				
					if (!empty($request['name'])) {
						$name = $request['name'];
					} else {
						$name = '';
					}
										
					if (!empty($request['sort'])) {
						$sort = $request['sort'];
					} else {
						$sort = 'sort_order';
					}

					if (!empty($request['order'])) {
						$order = $request['order'];
					} else {
						$order = 'ASC';
					}
										
					if (!empty($request['page'])) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$limit = 20;
								
					$filter_data = array(
						'filter_name'         		=> $name,
						'sort'                		=> $sort,
						'order'               		=> $order,
						'start'               		=> ($page - 1) * $limit,
						'limit'               		=> $limit
					);
										
					$manufacturer_total = $this->model_extension_stable_backend->getTotalManufacturers($filter_data);
						
					$manufacturers = $this->model_extension_stable_backend->getManufacturers($filter_data);
						
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'manufacturers' => $manufacturers,
							'manufacturerCount' => $manufacturer_total,
							'page' => $page,
							'pageCount' => ceil($manufacturer_total / $limit)
						)
					);
					
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'product',
						'action_code' => 'getManufacturers',
						'action_message' => 'Get information about product manufacturers.'
					);
					
					$this->model_extension_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getManufacturers');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getProduct() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
				
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (empty($request['product_id'])) {
			$this->errors[] = 'Product ID required!';
		}
									
		if (!$this->errors)	{			
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_backend->refreshStartup($chat);
							
					$product = $this->model_extension_stable_backend->getProduct($request['product_id']);
				
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
						$this->errors[] = 'Product not found!';
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getProduct');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getProducts() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();

		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (!$this->errors) {				
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_backend->refreshStartup($chat);

					if (!empty($request['name'])) {
						$name = $request['name'];
					} else {
						$name = '';
					}
					
					if (!empty($request['model'])) {
						$model = $request['model'];
					} else {
						$model = '';
					}
															
					if (isset($request['price_min']) && $request['price_min'] !== '') {
						$price_min = $request['price_min'];
					} else {
						$price_min = '';
					}
					
					if (isset($request['price_max']) && $request['price_max'] !== '') {
						$price_max = $request['price_max'];
					} else {
						$price_max = '';
					}
					
					if (isset($request['quantity_min']) && $request['quantity_min'] !== '') {
						$quantity_min = $request['quantity_min'];
					} else {
						$quantity_min = '';
					}
					
					if (isset($request['quantity_max']) && $request['quantity_max'] !== '') {
						$quantity_max = $request['quantity_max'];
					} else {
						$quantity_max = '';
					}
					
					if (isset($request['status']) && $request['status'] !== '') {
						$status = $request['status'];
					} else {
						$status = '';
					}
					
					if (!empty($request['manufacturer_id'])) {
						$manufacturer_id = $request['manufacturer_id'];
					} else {
						$manufacturer_id = 0;
					}
					
					if (!empty($request['category_id'])) {
						$category_id = $request['category_id'];
					} else {
						$category_id = 0;
					}
					
					if (!empty($request['date_added_from'])) {
						$date_added_from = $request['date_added_from'];
					} else {
						$date_added_from = '';
					}
					
					if (!empty($request['date_added_to'])) {
						$date_added_to = $request['date_added_to'];
					} else {
						$date_added_to = '';
					}
					
					if (!empty($request['sort'])) {
						$sort = $request['sort'];
					} else {
						$sort = 'sort_order';
					}

					if (!empty($request['order'])) {
						$order = $request['order'];
					} else {
						$order = 'ASC';
					}
										
					if (!empty($request['page'])) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$limit = 20;
								
					$filter_data = array(
						'filter_name'         		=> $name,
						'filter_model'        		=> $model,
						'filter_price_min'	  	  	=> $price_min,
						'filter_price_max'	  	  	=> $price_max,
						'filter_quantity_min'     	=> $quantity_min,
						'filter_quantity_max'     	=> $quantity_max,
						'filter_status'       		=> $status,
						'filter_manufacturer_id'  	=> $manufacturer_id,
						'filter_category_id'  		=> $category_id,
						'filter_date_added_from'    => $date_added_from,
						'filter_date_added_to'    	=> $date_added_to,
						'sort'                		=> $sort,
						'order'               		=> $order,
						'start'               		=> ($page - 1) * $limit,
						'limit'               		=> $limit
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
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getProducts');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
		
	public function getCustomer() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
				
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (empty($request['customer_id'])) {
			$this->errors[] = 'Customer ID required!';
		}
									
		if (!$this->errors)	{			
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('customer')) {
					$this->model_extension_stable_backend->refreshStartup($chat);
							
					$customer = $this->model_extension_stable_backend->getCustomer($request['customer_id']);
				
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
						$this->errors[] = 'Customer not found!';
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getCustomer');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCustomers() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
							
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('customer')) {
					$this->model_extension_stable_backend->refreshStartup($chat);

					if (!empty($request['name'])) {
						$name = $request['name'];
					} else {
						$name = '';
					}
					
					if (!empty($request['email'])) {
						$email = $request['email'];
					} else {
						$email = '';
					}
										
					if (!empty($request['customer_group_id'])) {
						$customer_group_id = $request['customer_group_id'];
					} else {
						$customer_group_id = 0;
					}
					
					if (isset($request['status']) && $request['status'] !== '') {
						$status = $request['status'];
					} else {
						$status = '';
					}
					
					if (!empty($request['date_added_from'])) {
						$date_added_from = $request['date_added_from'];
					} else {
						$date_added_from = '';
					}
					
					if (!empty($request['date_added_to'])) {
						$date_added_to = $request['date_added_to'];
					} else {
						$date_added_to = '';
					}
					
					if (!empty($request['sort'])) {
						$sort = $request['sort'];
					} else {
						$sort = 'name';
					}

					if (!empty($request['order'])) {
						$order = $request['order'];
					} else {
						$order = 'ASC';
					}
										
					if (!empty($request['page'])) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$limit = 20;
								
					$filter_data = array(
						'filter_name'         		=> $name,
						'filter_email'  	  		=> $email,
						'filter_customer_group_id'  => $customer_group_id,
						'filter_status'  			=> $status,
						'filter_date_added_from'    => $date_added_from,
						'filter_date_added_to'    	=> $date_added_to,
						'sort'                      => $sort,
						'order'                     => $order,
						'start'               		=> ($page - 1) * $limit,
						'limit'               		=> $limit
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
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
					
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getCustomers');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCustomerGroups() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
				
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
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
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getCustomerGroups');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getOrder() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (empty($request['order_id'])) {
			$this->errors[] = 'Order ID required!';
		}
									
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('order')) {
					$this->model_extension_stable_backend->refreshStartup($chat);
							
					$order = $this->model_extension_stable_backend->getOrder($request['order_id']);
				
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
						$this->errors[] = 'Order not found!';
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getOrder');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getOrders() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
							
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('order')) {
					$this->model_extension_stable_backend->refreshStartup($chat);

					if (!empty($request['customer_name'])) {
						$customer_name = $request['customer_name'];
					} else {
						$customer_name = '';
					}
															
					if (isset($request['order_status_id']) && $request['order_status_id'] !== '') {
						$order_status_id = $request['order_status_id'];
					} else {
						$order_status_id = '';
					}
					
					if (isset($request['total_min']) && $request['total_min'] !== '') {
						$total_min = $request['total_min'];
					} else {
						$total_min = '';
					}
					
					if (isset($request['total_max']) && $request['total_max'] !== '') {
						$total_max = $request['total_max'];
					} else {
						$total_max = '';
					}
					
					if (!empty($request['date_added_from'])) {
						$date_added_from = $request['date_added_from'];
					} else {
						$date_added_from = '';
					}
					
					if (!empty($request['date_added_to'])) {
						$date_added_to = $request['date_added_to'];
					} else {
						$date_added_to = '';
					}

					if (!empty($request['sort'])) {
						$sort = $request['sort'];
					} else {
						$sort = 'order_id';
					}

					if (!empty($request['order'])) {
						$order = $request['order'];
					} else {
						$order = 'DESC';
					}
										
					if (!empty($request['page'])) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$limit = 20;
								
					$filter_data = array(
						'filter_customer_name'  	=> $customer_name,
						'filter_order_status_id'  	=> $order_status_id,
						'filter_total_min'			=> $total_min,
						'filter_total_max'			=> $total_max,
						'filter_date_added_from'    => $date_added_from,
						'filter_date_added_to'    	=> $date_added_to,
						'sort'                      => $sort,
						'order'                     => $order,
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
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getOrders');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getOrderStatuses() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
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
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getOrderStatuses');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
				
	public function getCountries() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
							
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (!$this->errors)	{			
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
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
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getCountries');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getZonesByCountryId() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/backend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
		
		if (empty($request['country_id'])) {
			$this->errors[] = 'Country ID required!';
		}
						
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				$this->model_extension_stable_backend->refreshStartup($chat);

				$zones = $this->model_extension_stable_backend->getZonesByCountryId($request['country_id']);
				
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
					'action_message' => sprintf('Get information about zones for country with ID %s.', $request['country_id'])
				);
			
				$this->model_extension_module_stable->addChatAction($chat_action_data);
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getZonesByCountryId');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	private function getRequestData() {
		$request = json_decode(file_get_contents('php://input'), true);
		
		if (!is_array($request)) {
			$request = array();
		}

		if (!empty($this->request->post) && is_array($this->request->post)) {
			$request = array_merge($this->request->post, $request);
		}

		if (!empty($this->request->get) && is_array($this->request->get)) {
			$request = array_merge($this->request->get, $request);
		}

		unset($request['route']);

		return $request;
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