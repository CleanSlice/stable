<?php
namespace Opencart\Catalog\Controller\Extension\Stable\Stable;
class Frontend extends \Opencart\System\Engine\Controller {
	private $errors = [];
	
	public function index(): void {
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
		$_config->load('stable');
		
		$config_setting = $_config->get('stable_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
						
		$data = [
			'jsonrpc' => '2.0',
            'result' => [
				'tools' => [],
				'serverInfo' => [
                    'name' => 'php-mcp-server',
                    'version' => '1.0.0'
                ]
			]
		];
		
		$data['result']['tools']['getCategory'] = [
			'name' => 'getCategory',
			'description' => 'Get information about the product category',
			'endpoint' => $this->url->link('extension/stable/frontend/getCategory', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'category_id' => ['type' => 'number', 'description' => 'Category ID']
				],
				'required' => ['chat_id', 'category_id']
			]
		];
		
		$data['result']['tools']['getCategories'] = [
			'name' => 'getCategories',
			'description' => 'Get information about product categories',
			'endpoint' => $this->url->link('extension/stable/frontend/getCategories', '', true],
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'name' => ['type' => 'string', 'description' => 'Search categories by name'],
					'parent_category_id' => ['type' => 'number', 'description' => 'Search categories by parent Category ID'],
					'sort' => ['type' => 'string', 'description' => 'Sort in the category search results (name/sort_order)', 'default' => 'sort_order'],
					'order' => ['type' => 'string', 'description' => 'Order in the category search results (ASC/DESC)', 'default' => 'ASC'],
					'page' => ['type' => 'number', 'description' => 'Page number in the category search results', 'default' => 1]
				],
				'required' => ['chat_id']
			]
		];
		
		$data['result']['tools']['getManufacturer'] = [
			'name' => 'getManufacturer',
			'description' => 'Get information about the product manufacturer',
			'endpoint' => $this->url->link('extension/stable/frontend/getManufacturer', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'manufacturer_id' => ['type' => 'number', 'description' => 'Manufacturer ID']
				],
				'required' => ['chat_id', 'manufacturer_id']
			]
		];
		
		$data['result']['tools']['getManufacturers'] = [
			'name' => 'getManufacturers',
			'description' => 'Get information about product manufacturers',
			'endpoint' => $this->url->link('extension/stable/frontend/getManufacturers', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'name' => ['type' => 'string', 'description' => 'Search manufacturers by name'],
					'sort' => ['type' => 'string', 'description' => 'Sort in the manufacturer search results (name/sort_order)', 'default' => 'name'],
					'order' => ['type' => 'string', 'description' => 'Order in the manufacturer search results (ASC/DESC)', 'default' => 'ASC'],
					'page' => ['type' => 'number', 'description' => 'Page number in the manufacturer search results', 'default' => 1]
				],
				'required' => ['chat_id']
			]
		];
		
		$data['result']['tools']['getProduct'] = [
			'name' => 'getProduct',
			'description' => 'Get information about the product',
			'endpoint' => $this->url->link('extension/stable/frontend/getProduct', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'product_id' => ['type' => 'number', 'description' => 'Product ID']
				],
				'required' => ['chat_id', 'product_id']
			]
		];
		
		$data['result']['tools']['getProducts'] = [
			'name' => 'getProducts',
			'description' => 'Get information about products',
			'endpoint' =>$this->url->link('extension/stable/frontend/getProducts', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'name' => ['type' => 'string', 'description' => 'Search products by name'],
					'model' => ['type' => 'string', 'description' => 'Search products by model'],
					'price_min' => ['type' => 'number', 'description' => 'Search products priced at or above this value. Give it in the same terms the customer sees in the results: tax included, in the current currency. Numbers only, no currency symbol.'],
					'price_max' => ['type' => 'number', 'description' => 'Search products priced at or below this value. Give it in the same terms the customer sees in the results: tax included, in the current currency. Numbers only, no currency symbol.'],
					'quantity_min' => ['type' => 'number', 'description' => 'Search products with a quantity greater than this value'],
					'quantity_max' => ['type' => 'number', 'description' => 'Search products with a quantity less than this value'],
					'manufacturer_id' => ['type' => 'number', 'description' => 'Search products with this manufacturer ID'],
					'category_id' => ['type' => 'number', 'description' => 'Search products in the category with this ID'],
					'date_added_from' => ['type' => 'string', 'format' => 'date', 'description' => 'Search products by date added, starting from this date (Format: YYYY-MM-DD)'],
					'date_added_to' => ['type' => 'string', 'format' => 'date', 'description' => 'Search products by date added, ending with this date (Format: YYYY-MM-DD)'],
					'sort' => ['type' => 'string', 'description' => 'Sort in the product search results (name/model/price/quantity/sort_order/date_added/manufacturer/rating)', 'default' => 'sort_order'],
					'order' => ['type' => 'string', 'description' => 'Order in the product search results (ASC/DESC)', 'default' => 'ASC'],
					'page' => ['type' => 'number', 'description' => 'Page number in the product search results', 'default' => 1]
				],
				'required' => ['chat_id']
			]
		];
		
		$data['result']['tools']['getCurrentCustomer'] = [
			'name' => 'getCurrentCustomer',
			'description' => 'Get information about the current customer',
			'endpoint' => $this->url->link('extension/stable/frontend/getCurrentCustomer', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
				],
				'required' => ['chat_id']
			]
		];
		
		$data['result']['tools']['getCurrentCustomerOrder'] = [
			'name' => 'getCurrentCustomerOrder',
			'description' => 'Get information about current customer order',
			'endpoint' => $this->url->link('extension/stable/frontend/getCurrentCustomerOrder', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'order_id' => ['type' => 'number', 'description' => 'Order ID']
				],
				'required' => ['chat_id', 'order_id']
			]
		];
				
		$data['result']['tools']['getCurrentCustomerOrders'] = [
			'name' => 'getCurrentCustomerOrders',
			'description' => 'Get information about current customer orders',
			'endpoint' => $this->url->link('extension/stable/frontend/getCurrentCustomerOrders', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'page' => ['type' => 'number', 'description' => 'Page number in the customer order search results', 'default' => 1]
				],
				'required' => ['chat_id']
			]
		];
				
		$data['result']['tools']['addCartProduct'] = [
			'name' => 'addCartProduct',
			'description' => 'Add product to cart',
			'endpoint' => $this->url->link('extension/stable/frontend/addCartProduct', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'product_id' => ['type' => 'number', 'description' => 'Product ID'],
					'quantity' => ['type' => 'number', 'description' => 'Quantity', 'default' => 1],
					'option' => [
						'type' => 'object', 
						'description' => 'Product options where KEY is product_option_id (stringified number) and VALUE is product_option_value_id (number) or text value.',
						'additionalProperties' => [
							'type' => ['number', 'string', 'array'],
							'description' => 'The value of the product option (product_option_value_id, text, or array of IDs for checkboxes)'
						]
					],						
					'recurring_id' => ['type' => 'number', 'description' => 'Recurring ID', 'default' => 0],
				],
				'required' => ['chat_id', 'product_id']
			]
		];
		
		$data['result']['tools']['editCartProduct'] = [
			'name' => 'editCartProduct',
			'description' => 'Edit product in the cart',
			'endpoint' => $this->url->link('extension/stable/frontend/editCartProduct', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'cart_id' => ['type' => 'number', 'description' => 'Cart ID'],
					'quantity' => ['type' => 'number', 'description' => 'Quantity']
				],
				'required' => ['chat_id', 'cart_id', 'quantity']
			]
		];
		
		$data['result']['tools']['deleteCartProduct'] = [
			'name' => 'deleteCartProduct',
			'description' => 'Delete product from the cart',
			'endpoint' => $this->url->link('extension/stable/frontend/deleteCartProduct', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'cart_id' => ['type' => 'number', 'description' => 'Cart ID']
				],
				'required' => ['chat_id', 'cart_id']
			]
		];
		
		$data['result']['tools']['getCartProducts'] = [
			'name' => 'getCartProducts',
			'description' => 'Get information about products in the cart',
			'endpoint' => $this->url->link('extension/stable/frontend/getCartProducts', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID']
				],
				'required' => ['chat_id']
			]
		];
						
		$data['result']['tools']['createOrder'] = [
			'name' => 'createOrder',
			'description' => 'Create Order',
			'endpoint' => $this->url->link('extension/stable/frontend/createOrder', '', true],
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'firstname' => ['type' => 'string', 'description' => 'First Name'],
					'lastname' => ['type' => 'string', 'description' => 'Last Name'],
					'email' => ['type' => 'string', 'description' => 'E-Mail'],
					'telephone' => ['type' => 'string', 'description' => 'Telephone'],
					'company' => ['type' => 'string', 'description' => 'Company'],
					'address_1' => ['type' => 'string', 'description' => 'Address 1'],
					'address_2' => ['type' => 'string', 'description' => 'Address 2'],
					'city' => ['type' => 'string', 'description' => 'City'],
					'postcode' => ['type' => 'string', 'description' => 'Postcode'],
					'country_id' => ['type' => 'number', 'description' => 'Country ID'],
					'zone_id' => ['type' => 'number', 'description' => 'Zone ID'],
					'shipping_method_code' => ['type' => 'string', 'description' => 'Shipping Method Code, exactly as returned by getShippingMethods. Required whenever the cart contains a shippable product; omit only for digital-only carts.'],
					'payment_method_code' => ['type' => 'string', 'description' => 'Payment Method Code, exactly as returned by getPaymentMethods. Some methods need extra fields — getPaymentMethods lists them in required_fields, and they become required for this call.']
				],
				'required' => ['chat_id', 'payment_method_code'],
				'allOf' => []
			]
		];
		
		foreach ($setting['payment_method'] as $payment_method) {
			if (empty($payment_method['field'])) {
				continue;
			}
	
			$required_fields = [];
				
			foreach ($payment_method['field'] as $field) {
				$data['result']['tools']['createOrder']['inputSchema']['properties'][$field['code']] = [
					'type' => $field['type'], 
					'description' => $field['description']
				];
					
				if (!empty($field['required'])) {
					$required_fields[] = $field['code'];
				}
			}
				
			if ($required_fields) {
				$data['result']['tools']['createOrder']['inputSchema']['allOf'][] = [
					'if' => [
						'properties' => [
							'payment_method_code' => ['const' => $payment_method['code'])
						],
						'required' => ['payment_method_code']
					],
					'then' => [
						'required' => $required_fields
					]
				];
			}
		}
								
		$data['result']['tools']['getShippingMethods'] = [
			'name' => 'getShippingMethods',
			'description' => 'Get information about shipping methods',
			'endpoint' => $this->url->link('extension/stable/frontend/getShippingMethods', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'country_id' => ['type' => 'number', 'description' => 'Country ID'],
					'zone_id' => ['type' => 'number', 'description' => 'Zone ID']
				],
				'required' => ['chat_id', 'country_id', 'zone_id']
			]
		];
		
		$data['result']['tools']['getPaymentMethods'] = [
			'name' => 'getPaymentMethods',
			'description' => 'Get information about payment methods',
			'endpoint' => $this->url->link('extension/stable/frontend/getPaymentMethods', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'country_id' => ['type' => 'number', 'description' => 'Country ID'],
					'zone_id' => ['type' => 'number', 'description' => 'Zone ID']
				],
				'required' => ['chat_id', 'country_id', 'zone_id']
			]
		];
	
		$data['result']['tools']['getCountries'] = [
			'name' => 'getCountries',
			'description' => 'Get information about countries',
			'endpoint' => $this->url->link('extension/stable/frontend/getCountries', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID']
				],
				'required' => ['chat_id']
			]
		];
		
		$data['result']['tools']['getZonesByCountryId'] = [
			'name' => 'getZonesByCountryId',
			'description' => 'Get information about zones for this country ID',
			'endpoint' => $this->url->link('extension/stable/frontend/getZonesByCountryId', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => [
				'type' => 'object',
				'properties' => [
					'chat_id' => ['type' => 'string', 'description' => 'Chat ID'],
					'country_id' => ['type' => 'number', 'description' => 'Country ID']
				],
				'required' => ['chat_id', 'country_id']
			]
		];
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCategory(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
				
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
			
		if (empty($request['category_id'])) {
			$this->errors[] = 'Category ID required!';
		}
					
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
								
					$category = $this->model_extension_stable_stable_frontend->getCategory($request['category_id']);
				
					if ($category) {
						$data = [
							'jsonrpc' => "2.0",
							'result' => $category
						];
						
						$chat_action_data = [
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'product',
							'action_code' => 'getCategory',
							'action_message' => sprintf('Get information about a product category with ID %s.', $category['category_id'])
						];
						
						$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
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
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getCategory');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCategories(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
									
		if (!$this->errors) {				
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
				
					if (!empty($request['name'])) {
						$name = $request['name'];
					} else {
						$name = '';
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
								
					$filter_data = [
						'filter_name'         		=> $name,
						'filter_parent_category_id' => $parent_category_id,
						'sort'                		=> $sort,
						'order'               		=> $order,
						'start'               		=> ($page - 1) * $limit,
						'limit'               		=> $limit
					];
										
					$category_total = $this->model_extension_stable_stable_frontend->getTotalCategories($filter_data);
						
					$categories = $this->model_extension_stable_stable_frontend->getCategories($filter_data);
						
					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'categories' => $categories,
							'categoryCount' => $category_total,
							'page' => $page,
							'pageCount' => ceil($category_total / $limit)
						]
					];
					
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'product',
						'action_code' => 'getCategories',
						'action_message' => 'Get information about product categories.'
					];
					
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}

		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getCategories');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getManufacturer(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
			
		if (empty($request['manufacturer_id'])) {
			$this->errors[] = 'Manufacturer ID required!';
		}
					
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
								
					$manufacturer = $this->model_extension_stable_stable_frontend->getManufacturer($request['manufacturer_id']);
				
					if ($manufacturer) {
						$data = [
							'jsonrpc' => "2.0",
							'result' => $manufacturer
						];
						
						$chat_action_data = [
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'product',
							'action_code' => 'getManufacturer',
							'action_message' => sprintf('Get information about a product manufacturer with ID %s.', $manufacturer['manufacturer_id'])
						];
						
						$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
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
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getManufacturer');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getManufacturers(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
									
		if (!$this->errors)	{			
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
				
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
								
					$filter_data = [
						'filter_name'         		=> $name,
						'sort'                		=> $sort,
						'order'               		=> $order,
						'start'               		=> ($page - 1) * $limit,
						'limit'               		=> $limit
					];
										
					$manufacturer_total = $this->model_extension_stable_stable_frontend->getTotalManufacturers($filter_data);
						
					$manufacturers = $this->model_extension_stable_stable_frontend->getManufacturers($filter_data);
						
					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'manufacturers' => $manufacturers,
							'manufacturerCount' => $manufacturer_total,
							'page' => $page,
							'pageCount' => ceil($manufacturer_total / $limit)
						]
					];
					
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'product',
						'action_code' => 'getManufacturers',
						'action_message' => 'Get information about product manufacturers.'
					];
					
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getManufacturers');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getProduct(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (empty($request['product_id'])) {
			$this->errors[] = 'Product ID required!';
		}
									
		if (!$this->errors) {				
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
							
					$product = $this->model_extension_stable_stable_frontend->getProduct($request['product_id']);
				
					if ($product) {
						$data = [
							'jsonrpc' => "2.0",
							'result' => $product
						];
						
						$chat_action_data = [
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'product',
							'action_code' => 'getProduct',
							'action_message' => sprintf('Get information about the product with ID %s.', $product['product_id'])
						];
						
						$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
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
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getProduct');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getProducts(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
								
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (!$this->errors) {				
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
					
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
								
					$filter_data = [
						'filter_name'         		=> $name,
						'filter_model'        		=> $model,
						'filter_price_min'	  	  	=> $price_min,
						'filter_price_max'	  	  	=> $price_max,
						'filter_quantity_min'     	=> $quantity_min,
						'filter_quantity_max'     	=> $quantity_max,
						'filter_manufacturer_id'  	=> $manufacturer_id,
						'filter_category_id'  		=> $category_id,
						'filter_date_added_from'    => $date_added_from,
						'filter_date_added_to'    	=> $date_added_to,
						'sort'                		=> $sort,
						'order'               		=> $order,
						'start'               		=> ($page - 1) * $limit,
						'limit'               		=> $limit
					];
															
					$product_total = $this->model_extension_stable_stable_frontend->getTotalProducts($filter_data);
						
					$products = $this->model_extension_stable_stable_frontend->getProducts($filter_data);
						
					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'products' => $products,
							'productCount' => $product_total,
							'page' => $page,
							'pageCount' => ceil($product_total / $limit)
						]
					];
					
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'product',
						'action_code' => 'getProducts',
						'action_message' => 'Get information about products.'
					];
					
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
		
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getProducts');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCurrentCustomer(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
						
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
															
		if (!$this->errors) {				
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('customer')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
							
					$customer = $this->model_extension_stable_stable_frontend->getCustomer($this->customer->getId());
				
					if ($customer) {
						$data = [
							'jsonrpc' => "2.0",
							'result' => $customer
						];
						
						$chat_action_data = [
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'customer',
							'action_code' => 'getCurrentCustomer',
							'action_message' => 'Get information about the current customer.'
						];
						
						$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
					} else {
						$this->errors[] = 'Current customer not found!';
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}		
		
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getCurrentCustomer');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCurrentCustomerOrder(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
						
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
		
		if (empty($request['order_id'])) {
			$this->errors[] = 'Order ID required!';
		}
															
		if (!$this->errors) {				
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('customer')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
							
					$customer_order = $this->model_extension_stable_stable_frontend->getCustomerOrder($this->customer->getId(), $request['order_id']);
				
					if ($customer_order) {
						$data = [
							'jsonrpc' => "2.0",
							'result' => $customer_order
						];
						
						$chat_action_data = [
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'customer',
							'action_code' => 'getCurrentCustomerOrder',
							'action_message' => 'Get information about the current customer order.'
						];
						
						$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
					} else {
						$this->errors[] = 'Current customer order not found!';
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getCurrentCustomer');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCurrentCustomerOrders(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
															
		if (!$this->errors) {				
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('customer')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
					
					$customer_id = $this->customer->getId();
					
					if (!empty($request['page'])) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$limit = 20;		
					$start = ($page - 1) * $limit;
										
					$customer_order_total = $this->model_extension_stable_stable_frontend->getTotalCustomerOrders($customer_id);
						
					$customer_orders = $this->model_extension_stable_stable_frontend->getCustomerOrders($customer_id, $start, $limit);
						
					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'orders' => $customer_orders,
							'orderCount' => $customer_order_total,
							'page' => $page,
							'pageCount' => ceil($customer_order_total / $limit)
						]
					];
														
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'customer',
						'action_code' => 'getCurrentCustomerOrders',
						'action_message' => 'Get information about current customer orders.'
					];
						
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getCurrentCustomerOrders');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
				
	public function addCartProduct(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (empty($request['product_id'])) {
			$this->errors[] = 'Product ID required!';
		}
										
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('cart')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
				
					$product = $this->model_extension_stable_stable_frontend->getProduct($request['product_id']);
				
					if ($product) {
						if (!empty($request['quantity'])) {
							$quantity = (int)$request['quantity'];
						} else {
							$quantity = 1;
						}
						
						if (!empty($request['option'])) {
							$option = array_filter($request['option']);
						} else {
							$option = [];
						}
												
						foreach ($product['options'] as $product_option) {
							if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
								$this->errors[] = sprintf('%s required!', $product_option['name']);
							}
						}
						
						if (!empty($request['recurring_id'])) {
							$recurring_id = $request['recurring_id'];
						} else {
							$recurring_id = 0;
						}

						if ($product['recurrings']) {
							$recurring_ids = [];

							foreach ($product['recurrings'] as $recurring) {
								$recurring_ids[] = $recurring['recurring_id'];
							}

							if (!in_array($recurring_id, $recurring_ids)) {
								$this->errors[] = 'Please select a payment recurring!';
							}
						}
					
						if (!$this->errors) {
							$this->cart->add($request['product_id'], $quantity, $option, $recurring_id);
							
							$products = $this->cart->getProducts();
							
							$data = [
								'jsonrpc' => "2.0",
								'result' => [
									'products' => $products,
								]
							];
							
							$chat_action_data = [
								'chat_id' => $chat['chat_id'],
								'tool_code' => 'cart',
								'action_code' => 'addCartProduct',
								'action_message' => sprintf('Add product with ID %s to cart.', $product['product_id'])
							];
						
							$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
						}
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
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'addProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function editCartProduct(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (empty($request['cart_id'])) {
			$this->errors[] = 'Cart ID required!';
		}
		
		if (empty($request['quantity'])) {
			$this->errors[] = 'Quantity required!';
		}
										
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('cart')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
			
					$quantity = (int)$request['quantity'];
							
					$this->cart->update($request['cart_id'], $request['quantity']);
							
					$products = $this->cart->getProducts();
							
					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'products' => $products,
						]
					];
					
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'cart',
						'action_code' => 'editCartProduct',
						'action_message' => 'Edit product in the cart.'
					];
				
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'updateProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function deleteCartProduct(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
					
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (empty($request['cart_id'])) {
			$this->errors[] = 'Cart ID required!';
		}
										
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('cart')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
			
					$this->cart->remove($request['cart_id']);
						
					$products = $this->cart->getProducts();
							
					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'products' => $products,
						]
					];
					
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'cart',
						'action_code' => 'deleteCartProduct',
						'action_message' => 'Delete product from the cart.'
					];
				
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'deleteProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function getCartProducts(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
									
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('cart')) {						
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
	
					$products = $this->cart->getProducts();
						
					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'products' => $products,
						]
					];
					
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'cart',
						'action_code' => 'getCartProducts',
						'action_message' => 'Get information about products in the cart.'
					];
				
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getProductsInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function createOrder(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		$this->request->post = $request;
				
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
															
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('checkout')) {
					$_config = new \Opencart\System\Engine\Config();
					$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
					$_config->load('stable');
						
					$config_setting = $_config->get('stable_setting');
					
					$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
					
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
					
					if ($this->customer->isLogged()) {
						$customer = $this->model_extension_stable_stable_frontend->getCustomer($this->customer->getId());
					}
					
					if (empty($this->request->post['firstname'])) {
						if (!empty($customer['firstname'])) {
							$this->request->post['firstname'] = $customer['firstname'];
						} else {
							$this->request->post['firstname'] = '';
						}
					}
											
					if (empty($this->request->post['lastname'])) {
						if (!empty($customer['lastname'])) {
							$this->request->post['lastname'] = $customer['lastname'];
						} else {
							$this->request->post['lastname'] = '';
						}
					}
					
					if (empty($this->request->post['email'])) {
						if (!empty($customer['email'])) {
							$this->request->post['email'] = $customer['email'];
						} else {
							$this->request->post['email'] = '';
						}
					}
					
					if (empty($this->request->post['telephone'])) {
						if (!empty($customer['telephone'])) {
							$this->request->post['telephone'] = $customer['telephone'];
						} else {
							$this->request->post['telephone'] = '';
						}
					}
					
					if (empty($this->request->post['company'])) {
						if (!empty($customer['address']['company'])) {
							$this->request->post['company'] = $customer['address']['company'];
						} else {
							$this->request->post['company'] = '';
						}
					}
					
					if (empty($this->request->post['address_1'])) {
						if (!empty($customer['address']['address_1'])) {
							$this->request->post['address_1'] = $customer['address']['address_1'];
						} else {
							$this->request->post['address_1'] = '';
						}
					}
					
					if (empty($this->request->post['address_2'])) {
						if (!empty($customer['address']['address_2'])) {
							$this->request->post['address_2'] = $customer['address']['address_2'];
						} else {
							$this->request->post['address_2'] = '';
						}
					}
					
					if (empty($this->request->post['city'])) {
						if (!empty($customer['address']['city'])) {
							$this->request->post['city'] = $customer['address']['city'];
						} else {
							$this->request->post['city'] = '';
						}
					}
					
					if (empty($this->request->post['postcode'])) {
						if (!empty($customer['address']['postcode'])) {
							$this->request->post['postcode'] = $customer['address']['postcode'];
						} else {
							$this->request->post['postcode'] = '';
						}
					}
					
					if (empty($this->request->post['country_id'])) {
						if (!empty($customer['address']['country_id'])) {
							$this->request->post['country_id'] = $customer['address']['country_id'];
						} else {
							$this->request->post['country_id'] = '';
						}
					}
					
					if (empty($this->request->post['zone_id'])) {
						if (!empty($customer['address']['zone_id'])) {
							$this->request->post['zone_id'] = $customer['address']['zone_id'];
						} else {
							$this->request->post['zone_id'] = '';
						}
					}	
		
					if (empty($this->request->post['firstname'])) {
						$this->errors[] = 'First Name required!';
					} elseif ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
						$this->errors[] = 'First Name must be between 1 and 32 characters!';
					} 
						
					if (empty($this->request->post['lastname'])) {
						$this->errors[] = 'Last Name required!';
					} elseif ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
						$this->errors[] = 'Last Name must be between 1 and 32 characters!';
					}
						
					if (empty($this->request->post['email'])) {
						$this->errors[] = 'E-Mail required!';
					} elseif ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
						$this->errors[] = 'E-Mail address does not appear to be valid!';
					}
					
					if (!empty($this->request->post['telephone']) && (utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
						$this->errors[] = 'Telephone does not appear to be valid!';
					}
						
					if (empty($this->request->post['address_1'])) {
						$this->errors[] = 'Address 1 required!';
					} elseif ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
						$this->errors[] = 'Address 1 must be between 3 and 128 characters!';
					} 
					
					if (empty($this->request->post['city'])) {
						$this->errors[] = 'City required!';
					} elseif ((utf8_strlen($this->request->post['city']) < 3) || (utf8_strlen($this->request->post['city']) > 128)) {
						$this->errors[] = 'City must be between 2 and 128 characters!';
					}
						
					if (empty($this->request->post['postcode'])) {
						$this->errors[] = 'Postcode required!';
					} elseif ((utf8_strlen($this->request->post['postcode']) < 3) || (utf8_strlen($this->request->post['postcode']) > 10)) {
						$this->errors[] = 'Postcode must be between 2 and 10 characters!';
					}
									
					if (empty($this->request->post['country_id'])) {
						$this->errors[] = 'Country ID required!';
					}
									
					if (empty($this->request->post['zone_id'])) {
						$this->errors[] = 'Zone ID required!';
					}
								
					if (empty($this->request->post['payment_method_code'])) {
						$this->errors[] = 'Payment method required!';
					}
				
					if (!$this->cart->hasProducts()) {
						$this->errors[] = 'Your shopping cart is empty!';
					}
					
					if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
						$this->errors[] = 'Products in cart are not in stock!';
					}
					
					if ($this->cart->hasShipping() && empty($this->request->post['shipping_method_code'])) {
						$this->errors[] = 'Shipping method required!';
					}
					
					$products = $this->cart->getProducts();

					foreach ($products as $product) {
						$product_total = 0;

						foreach ($products as $product_2) {
							if ($product_2['product_id'] == $product['product_id']) {
								$product_total += $product_2['quantity'];
							}
						}

						if ($product['minimum'] > $product_total) {
							$this->errors[] = 'Products in cart are not available in the required quantity!';

							break;
						}
					}
					
					if (!empty($setting['payment_method'][$this->request->post['payment_method_code']]['field'])) {
						foreach ($setting['payment_method'][$this->request->post['payment_method_code']]['field'] as $field) {
							if (!empty($field['required']) && empty($this->request->post[$field['code']])) {
								$this->errors[] = sprintf('%s required for this payment method!', $field['name']);
							}
						}
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}	
					
			if (!$this->errors) {						
				$this->session->data['customer']['customer_id'] = $this->customer->getId();
				$this->session->data['customer']['customer_group_id'] = $this->customer->getGroupId();
				$this->session->data['customer']['firstname'] = trim($this->request->post['firstname']);
				$this->session->data['customer']['lastname'] = trim($this->request->post['lastname']);
				$this->session->data['customer']['email'] = trim($this->request->post['email']);
				$this->session->data['customer']['telephone'] = trim($this->request->post['telephone']);
				$this->session->data['customer']['custom_field'] = [];
									
				$this->session->data['payment_address']['address_id'] = $this->customer->getAddressId();
				$this->session->data['payment_address']['firstname'] = trim($this->request->post['firstname']);
				$this->session->data['payment_address']['lastname'] = trim($this->request->post['lastname']);
				$this->session->data['payment_address']['company'] = trim($this->request->post['company']);
				$this->session->data['payment_address']['address_1'] = trim($this->request->post['address_1']);
				$this->session->data['payment_address']['address_2'] = trim($this->request->post['address_2']);
				$this->session->data['payment_address']['city'] = trim($this->request->post['city']);
				$this->session->data['payment_address']['postcode'] = trim($this->request->post['postcode']);
				$this->session->data['payment_address']['country_id'] = $this->request->post['country_id'];
				$this->session->data['payment_address']['zone_id'] = $this->request->post['zone_id'];
				$this->session->data['payment_address']['custom_field'] = [];
				
				$this->session->data['shipping_address']['address_id'] = $this->customer->getAddressId();
				$this->session->data['shipping_address']['firstname'] = trim($this->request->post['firstname']);
				$this->session->data['shipping_address']['lastname'] = trim($this->request->post['lastname']);
				$this->session->data['shipping_address']['company'] = trim($this->request->post['company']);
				$this->session->data['shipping_address']['address_1'] = trim($this->request->post['address_1']);
				$this->session->data['shipping_address']['address_2'] = trim($this->request->post['address_2']);
				$this->session->data['shipping_address']['city'] = trim($this->request->post['city']);
				$this->session->data['shipping_address']['postcode'] = trim($this->request->post['postcode']);
				$this->session->data['shipping_address']['country_id'] = $this->request->post['country_id'];
				$this->session->data['shipping_address']['zone_id'] = $this->request->post['zone_id'];
				$this->session->data['shipping_address']['custom_field'] = [];
																
				$country_info = $this->model_extension_stable_stable_frontend->getCountry($this->request->post['country_id']);

				if ($country_info) {
					$this->session->data['payment_address']['country'] = $country_info['name'];
					$this->session->data['payment_address']['iso_code_2'] = $country_info['iso_code_2'];
					$this->session->data['payment_address']['iso_code_3'] = $country_info['iso_code_3'];
					$this->session->data['payment_address']['address_format'] = $country_info['address_format'];
					
					$this->session->data['shipping_address']['country'] = $country_info['name'];
					$this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
					$this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
					$this->session->data['shipping_address']['address_format'] = $country_info['address_format'];
				} else {
					$this->session->data['payment_address']['country'] = '';
					$this->session->data['payment_address']['iso_code_2'] = '';
					$this->session->data['payment_address']['iso_code_3'] = '';
					$this->session->data['payment_address']['address_format'] = '';
					
					$this->session->data['shipping_address']['country'] = '';
					$this->session->data['shipping_address']['iso_code_2'] = '';
					$this->session->data['shipping_address']['iso_code_3'] = '';
					$this->session->data['shipping_address']['address_format'] = '';
				}
						
				$zone_info = $this->model_extension_stable_stable_frontend->getZone($this->request->post['zone_id']);

				if ($zone_info) {
					$this->session->data['payment_address']['zone'] = $zone_info['name'];
					$this->session->data['payment_address']['zone_code'] = $zone_info['code'];
					
					$this->session->data['shipping_address']['zone'] = $zone_info['name'];
					$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
				} else {
					$this->session->data['payment_address']['zone'] = '';
					$this->session->data['payment_address']['zone_code'] = '';
					
					$this->session->data['shipping_address']['zone'] = '';
					$this->session->data['shipping_address']['zone_code'] = '';
				}
								
				$method_data = [];

				$results = $this->model_setting_extension->getExtensionsByType('shipping');

				foreach ($results as $result) {
					if ($this->config->get('shipping_' . $result['code'] . '_status')) {
						$this->load->model('extension/' . $result['extension'] . '/shipping/' . $result['code']);
						
						$quote = $this->{'model_extension_' . $result['extension'] . '_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

						if ($quote) {
							if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
								$method_data[$result['code']] = $quote;
							} else {
								$method_data[$result['code']] = [
									'title'      => $quote['title'],
									'quote'      => $quote['quote'],
									'sort_order' => $quote['sort_order'],
									'error'      => $quote['error']
								];
							}
						}
					}
				}

				$sort_order = [];

				foreach ($method_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $method_data);

				$this->session->data['shipping_methods'] = $method_data;
			
				if (!empty($this->request->post['shipping_method_code'])) {
					$shipping = explode('.', $this->request->post['shipping_method_code']);

					if (!empty($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
						$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
					}
				} 
	
				if (empty($this->session->data['shipping_method']) && $this->session->data['shipping_methods']) {
					$shipping_method = reset($this->session->data['shipping_methods']);
					$shipping_method = reset($shipping_method['quote']);
							
					$this->session->data['shipping_method'] = $shipping_method;
				}
				
				$method_data = [];

				$results = $this->model_setting_extension->getExtensionsByType('payment');
				
				foreach ($results as $result) {
					if ($this->config->get('payment_' . $result['code'] . '_status')) {
						$this->load->model('extension/' . $result['extension'] . '/payment/' . $result['code']);
						
						if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
							$methods = $this->{'model_extension_' . $result['extension'] . '_payment_' . $result['code']}->getMethods($this->session->data['payment_address']);

							if ($methods) {
								$method_data[$result['code']] = $methods;
							}
						} else {
							$method = $this->{'model_extension_' . $result['extension'] . '_payment_' . $result['code']}->getMethod($this->session->data['payment_address']);

							if ($method) {
								$method_data[$result['code']] = $method;
							}
						}							
					}
				}
				
				$sort_order = [];

				foreach ($method_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $method_data);
				
				$this->session->data['payment_methods'] = $method_data;
										
				if (!empty($this->request->post['payment_method_code'])) {
					if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
						$payment = explode('.', $this->request->post['payment_method_code']);
						
						if (!empty($this->session->data['payment_methods'][$payment[0]]['option'][$payment[1]])) {
							$this->session->data['payment_method'] = $this->session->data['payment_methods'][$payment[0]]['option'][$payment[1]];
						}						
					} else {
						if (!empty($this->session->data['payment_methods'][$this->request->post['payment_method_code']])) {
							$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method_code']];
						}
					}
				}
					
				if (empty($this->session->data['payment_method']) && $this->session->data['payment_methods']) {
					if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
						$payment_method = reset($this->session->data['payment_methods']);
						$payment_method = reset($payment_method['option']);
								
						$this->session->data['payment_method'] = $payment_method;
					} else {
						$this->session->data['payment_method'] = reset($this->session->data['payment_methods']);
					}
				}
				
				if (!empty($this->session->data['payment_method']['code'])) {
					$payment = explode('.', $this->session->data['payment_method']['code']);
					
					if (empty($setting['payment_method'][$payment[0]])) {
						$stable_payment_method = $setting['payment_method'][$this->session->data['payment_method']['code']];
						
						$order_data = [];

						$totals = [];
						$taxes = $this->cart->getTaxes();
						$total = 0;

						$this->load->model('setting/extension');

						$results = $this->model_setting_extension->getExtensionsByType('total');

						foreach ($results as $key => $value) {
							$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
						}

						array_multisort($sort_order, SORT_ASC, $results);
						
						foreach ($results as $result) {
							if ($this->config->get('total_' . $result['code'] . '_status')) {
								$this->load->model('extension/' . $result['extension'] . '/total/' . $result['code']);

								($this->{'model_extension_' . $result['extension'] . '_total_' . $result['code']}->getTotal)($totals, $taxes, $total);
							}
						}

						foreach ($results as $result) {
							if ($this->config->get('total_' . $result['code'] . '_status')) {
								$this->load->model('extension/' . $result['extension'] . '/total/' . $result['code']);

								($this->{'model_extension_' . $result['extension'] . '_total_' . $result['code']}->getTotal)($totals, $taxes, $total);
							}
						}
						
						$sort_order = [];

						foreach ($totals as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}

						array_multisort($sort_order, SORT_ASC, $totals);

						$order_data['totals'] = $totals;

						$this->load->language('checkout/checkout');

						$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
						$order_data['store_id'] = $this->config->get('config_store_id');
						$order_data['store_name'] = $this->config->get('config_name');
						$order_data['store_url'] = $this->config->get('config_url');
														
						$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
						$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
						$order_data['firstname'] = $this->session->data['customer']['firstname'];
						$order_data['lastname'] = $this->session->data['customer']['lastname'];
						$order_data['email'] = $this->session->data['customer']['email'];
						$order_data['telephone'] = $this->session->data['customer']['telephone'];
						$order_data['custom_field'] = $this->session->data['customer']['custom_field'];
						
						$order_data['payment_address_id'] = $this->session->data['payment_address']['address_id'];
						$order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
						$order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
						$order_data['payment_company'] = $this->session->data['payment_address']['company'];
						$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
						$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
						$order_data['payment_city'] = $this->session->data['payment_address']['city'];
						$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
						$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
						$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
						$order_data['payment_country'] = $this->session->data['payment_address']['country'];
						$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
						$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
						$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : []);
						
						if (isset($this->session->data['payment_method'])) {
							if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
								$payment = explode('.', $this->session->data['payment_method']['code']);
								
								if (isset($payment[0]) && isset($payment[1]) && isset($this->session->data['payment_methods'][$payment[0]]['option'][$payment[1]])) {
									$payment_method_info = $this->session->data['payment_methods'][$payment[0]]['option'][$payment[1]];
								}
							} else {
								if (isset($this->session->data['payment_methods'][$this->session->data['payment_method']])) {
									$payment_method_info = $this->session->data['payment_methods'][$this->session->data['payment_method']];
								}
							}
						}
						
						if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
							$order_data['payment_method'] = $payment_method_info;
						} else {				
							if (isset($payment_method_info['title'])) {
								$order_data['payment_method'] = $payment_method_info['title'];
							} else {
								$order_data['payment_method'] = '';
							}

							if (isset($payment_method_info['code'])) {
								$order_data['payment_code'] = $payment_method_info['code'];
							} else {
								$order_data['payment_code'] = '';
							}
						}
															
						if ($this->cart->hasShipping()) {
							$order_data['shipping_address_id'] = $this->session->data['shipping_address']['address_id'];
							$order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
							$order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
							$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
							$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
							$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
							$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
							$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
							$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
							$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
							$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
							$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
							$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
							$order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : []);
																													
							if (isset($this->session->data['shipping_method'])) {
								if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
									$shipping = explode('.', $this->session->data['shipping_method']['code']);
								} else {
									$shipping = explode('.', $this->session->data['shipping_method']);
								}

								if (isset($shipping[0]) && isset($shipping[1]) && isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
									$shipping_method_info = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
								}
							}
							
							if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
								$order_data['shipping_method'] = $shipping_method_info;
							} else {
								if (isset($shipping_method_info['title'])) {
									$order_data['shipping_method'] = $shipping_method_info['title'];
								} else {
									$order_data['shipping_method'] = '';
								}

								if (isset($shipping_method_info['code'])) {
									$order_data['shipping_code'] = $shipping_method_info['code'];
								} else {
									$order_data['shipping_code'] = '';
								}
							}
						} else {
							$order_data['shipping_address_id'] = '';
							$order_data['shipping_firstname'] = '';
							$order_data['shipping_lastname'] = '';
							$order_data['shipping_company'] = '';
							$order_data['shipping_address_1'] = '';
							$order_data['shipping_address_2'] = '';
							$order_data['shipping_city'] = '';
							$order_data['shipping_postcode'] = '';
							$order_data['shipping_zone'] = '';
							$order_data['shipping_zone_id'] = '';
							$order_data['shipping_country'] = '';
							$order_data['shipping_country_id'] = '';
							$order_data['shipping_address_format'] = '';
							$order_data['shipping_custom_field'] = [];
							$order_data['shipping_method'] = '';
							$order_data['shipping_code'] = '';
						}

						$order_data['products'] = [];

						foreach ($this->cart->getProducts() as $product) {
							$option_data = [];

							foreach ($product['option'] as $option) {
								$option_data[] = [
									'product_option_id'       => $option['product_option_id'],
									'product_option_value_id' => $option['product_option_value_id'],
									'option_id'               => $option['option_id'],
									'option_value_id'         => $option['option_value_id'],
									'name'                    => $option['name'],
									'value'                   => $option['value'],
									'type'                    => $option['type']
								];
							}
							
							$subscription_data = [];

							if (version_compare((string)VERSION, '4.0.2.0', '>=') && $product['subscription']) {
								$subscription_data = [
									'subscription_plan_id' => $product['subscription']['subscription_plan_id'],
									'name'                 => $product['subscription']['name'],
									'trial_price'          => $product['subscription']['trial_price'],
									'trial_tax'            => $this->tax->getTax($product['subscription']['trial_price'], $product['tax_class_id']),
									'trial_frequency'      => $product['subscription']['trial_frequency'],
									'trial_cycle'          => $product['subscription']['trial_cycle'],
									'trial_duration'       => $product['subscription']['trial_duration'],
									'trial_remaining'      => $product['subscription']['trial_remaining'],
									'trial_status'         => $product['subscription']['trial_status'],
									'price'                => $product['subscription']['price'],
									'tax'                  => $this->tax->getTax($product['subscription']['price'], $product['tax_class_id']),
									'frequency'            => $product['subscription']['frequency'],
									'cycle'                => $product['subscription']['cycle'],
									'duration'             => $product['subscription']['duration']
								];
							} else {
								$subscription_data = $product['subscription'];
							}

							$order_data['products'][] = [
								'product_id' 	=> $product['product_id'],
								'master_id'  	=> $product['master_id'],
								'name'       	=> $product['name'],
								'model'      	=> $product['model'],
								'option'     	=> $option_data,
								'subscription' 	=> $subscription_data,
								'download'   	=> $product['download'],
								'quantity'   	=> $product['quantity'],
								'subtract'   	=> $product['subtract'],
								'price'      	=> $product['price'],
								'total'      	=> $product['total'],
								'tax'       	=> $this->tax->getTax($product['price'], $product['tax_class_id']),
								'reward'     	=> $product['reward']
							];
						}

						$order_data['vouchers'] = [];

						if (!empty($this->session->data['vouchers'])) {
							$order_data['vouchers'] = $this->session->data['vouchers'];
						}
				
						$order_data['comment'] = '';
						
						$total_data = [
							'totals' => $totals,
							'taxes'  => $taxes,
							'total'  => $total
						];

						$order_data = array_merge($order_data, $total_data);
						
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
						$order_data['marketing_id'] = 0;
						$order_data['tracking'] = '';

						if ($this->config->get('config_affiliate_status') && isset($this->session->data['tracking'])) {
							$subtotal = $this->cart->getSubTotal();

							// Affiliate
							$this->load->model('account/affiliate');

							$affiliate_info = $this->model_account_affiliate->getAffiliateByTracking($this->session->data['tracking']);

							if ($affiliate_info) {
								$order_data['affiliate_id'] = $affiliate_info['customer_id'];
								$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
								$order_data['tracking'] = $this->session->data['tracking'];
							}
						}
						
						$order_data['language_id'] = $this->config->get('config_language_id');
						$order_data['language_code'] = $this->config->get('config_language');
						
						$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
						$order_data['currency_code'] = $this->session->data['currency'];
						$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);

						$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

						if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
							$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
						} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
							$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
						} else {
							$order_data['forwarded_ip'] = '';
						}

						if (isset($this->request->server['HTTP_USER_AGENT'])) {
							$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
						} else {
							$order_data['user_agent'] = '';
						}

						if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
							$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
						} else {
							$order_data['accept_language'] = '';
						}
						
						$this->load->model('checkout/order');

						$this->session->data['order_id'] = $this->model_checkout_order->addOrder($order_data);
										
						$this->load->controller('extension/payment/' . $stable_payment_method['code'] . '/' . $stable_payment_method['flow']);
										
						$output = $this->response->getOutput();
						
						$json = json_decode($output, true);

						if (!empty($json['error'])) {
							$this->errors[] = $json['error'];
						} else {
							$order = $this->model_extension_stable_stable_frontend->getOrder($this->session->data['order_id']);

							if ($order && $order['order_status_id'] > 0) {
								$data = [
									'jsonrpc' => "2.0",
									'result' => $order
								];

								$chat_action_data = [
									'chat_id' => $chat['chat_id'],
									'tool_code' => 'checkout',
									'action_code' => 'createOrder',
									'action_message' => sprintf('Create Order with ID %s.', $order['order_id'])
								];
							
								$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
								
								$this->cart->clear();

								unset($this->session->data['order_id']);
								unset($this->session->data['payment_address']);
								unset($this->session->data['payment_method']);
								unset($this->session->data['payment_methods']);
								unset($this->session->data['shipping_address']);
								unset($this->session->data['shipping_method']);
								unset($this->session->data['shipping_methods']);
								unset($this->session->data['comment']);
								unset($this->session->data['coupon']);
								unset($this->session->data['reward']);
								unset($this->session->data['voucher']);
								unset($this->session->data['vouchers']);
							} else {
								$this->errors[] = 'Payment could not be completed! The order was created but never confirmed.';
							}
						}
					} else {
						$this->errors[] = sprintf('Payment method %s cannot be completed in chat! It requires the customer to finish payment on the provider site.', $this->session->data['payment_method']['code']);
					}
				}
			}
		}
		
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'createOrder');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
		
	public function getShippingMethods(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
					
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
			
		if (empty($request['country_id'])) {
			$this->errors[] = 'Country ID required!';
		}
						
		if (empty($request['zone_id'])) {
			$this->errors[] = 'Zone ID required!';
		}
			
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('checkout')) {
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
				
					$this->load->model('setting/extension');
					
					$method_data = [];
				
					$results = $this->model_setting_extension->getExtensionsByType('shipping');
						
					foreach ($results as $result) {
						if ($this->config->get('shipping_' . $result['code'] . '_status')) {
							$this->load->model('extension/' . $result['extension'] . '/shipping/' . $result['code']);

							$quote = $this->{'model_extension_' . $result['extension'] . '_shipping_' . $result['code']}->getQuote(['country_id' => $request['country_id'], 'zone_id' => $request['zone_id']]);

							if ($quote) {
								foreach ($quote['quote'] as $quote_data) {
									if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
										$method_data[$quote_data['code']] = [
											'code' => $quote_data['code'],
											'title' => $quote_data['name'],
											'text' => $quote_data['text']
										];
									} else {
										$method_data[$quote_data['code']] = [
											'code' => $quote_data['code'],
											'title' => $quote_data['title'],
											'text' => $quote_data['text']
										];
									}
								}
							}
						}
					}
								
					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'shipping_methods' => $method_data
						]
					];
					
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'checkout',
						'action_code' => 'getShippingMethods',
						'action_message' => 'Get information about shipping methods.'
					];
				
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getShippingMethods');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}	
	
	public function getPaymentMethods(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
				
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
			
		if (empty($request['country_id'])) {
			$this->errors[] = 'Country ID required!';
		}
						
		if (empty($request['zone_id'])) {
			$this->errors[] = 'Zone ID required!';
		}
			
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('checkout')) {
					$_config = new \Opencart\System\Engine\Config();
					$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
					$_config->load('stable');
						
					$config_setting = $_config->get('stable_setting');
					
					$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
					
					$this->model_extension_stable_stable_frontend->refreshStartup($chat);
					
					$this->load->model('setting/extension');

					$method_data = [];

					$results = $this->model_setting_extension->getExtensionsByType('payment');
					
					foreach ($results as $result) {
						if ($this->config->get('payment_' . $result['code'] . '_status')) {
							$this->load->model('extension/' . $result['extension'] . '/payment/' . $result['code']);
				
							if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
								$methods = $this->{'model_extension_' . $result['extension'] . '_payment_' . $result['code']}->getMethods(['country_id' => $request['country_id'], 'zone_id' => $request['zone_id']]);

								if ($methods) {
									if (!empty($setting['payment_method'][$result['code']])) {
										$required_fields = [];
										$optional_fields = [];
										
										if ($setting['payment_method'][$result['code']]['field']) {
											foreach ($setting['payment_method'][$result['code']]['field'] as $field) {
												if (!empty($field['required'])) {
													$required_fields[] = $field['code'];
												} else {
													$optional_fields[] = $field['code'];
												}
											}
										}
										
										foreach ($methods['option'] as $option_data) {
											$method_data[$option_data['code']] = [
												'code' => $option_data['code'],
												'title' => $option_data['name'],
												'flow' => $setting['payment_method'][$result['code']]['flow'],
												'required_fields' => $required_fields,
												'optional_fields' => $optional_fields
											];
										}
									} else {
										foreach ($methods['option'] as $option_data) {
											$method_data[$option_data['code']] = [
												'code' => $option_data['code'],
												'title' => $option_data['name'],
												'flow' => 'unsupported',
												'reason' => 'This method needs the customer to complete payment on the provider\'s site!'
											];
										}
									}
								}
							} else {
								$method = $this->{'model_extension_' . $result['extension'] . '_payment_' . $result['code']}->getMethod(['country_id' => $request['country_id'], 'zone_id' => $request['zone_id']]);

								if ($method) {
									if (!empty($setting['payment_method'][$result['code']])) {
										$required_fields = [];
										$optional_fields = [];
										
										if ($setting['payment_method'][$result['code']]['field']) {
											foreach ($setting['payment_method'][$result['code']]['field'] as $field) {
												if (!empty($field['required'])) {
													$required_fields[] = $field['code'];
												} else {
													$optional_fields[] = $field['code'];
												}
											}
										}
										
										$method_data[$result['code']] = [
											'code' => $method['code'],
											'title' => $method['title'],
											'flow' => $setting['payment_method'][$result['code']]['flow'],
											'required_fields' => $required_fields,
											'optional_fields' => $optional_fields
										];
									} else {
										$method_data[$result['code']] = [
											'code' => $method['code'],
											'title' => $method['title'],
											'flow' => 'unsupported',
											'reason' => 'This method needs the customer to complete payment on the provider\'s site!'
										];
									}
								}
							}
						}
					}

					$data = [
						'jsonrpc' => "2.0",
						'result' => [
							'payment_methods' => $method_data
						]
					];
					
					$chat_action_data = [
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'checkout',
						'action_code' => 'getPaymentMethods',
						'action_message' => 'Get information about payment methods.'
					];
				
					$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getPaymentMethods');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCountries(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
				
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (!$this->errors) {				
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				$this->model_extension_stable_stable_frontend->refreshStartup($chat);
						
				$countries = $this->model_extension_stable_stable_frontend->getCountries();
			
				$data = [
					'jsonrpc' => "2.0",
					'result' => [
						'countries' => $countries
					]
				];
				
				$chat_action_data = [
					'chat_id' => $chat['chat_id'],
					'tool_code' => '',
					'action_code' => 'getCountries',
					'action_message' => 'Get information about countries.'
				];
			
				$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getCountries');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getZonesByCountryId(): void {
		$this->load->model('extension/stable/module/stable');
		$this->load->model('extension/stable/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
		
		if (empty($request['country_id'])) {
			$this->errors[] = 'Country ID required!';
		}
						
		if (!$this->errors) {
			$chat = $this->model_extension_stable_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				$this->model_extension_stable_stable_frontend->refreshStartup($chat);

				$zones = $this->model_extension_stable_stable_frontend->getZonesByCountryId($request['country_id']);
				
				$data = [
					'jsonrpc' => "2.0",
					'result' => [
						'zones' => $zones
					]
				];
				
				$chat_action_data = [
					'chat_id' => $chat['chat_id'],
					'tool_code' => '',
					'action_code' => 'getZonesByCountryId',
					'action_message' => sprintf('Get information about zones for country with ID %s.', $request['country_id'])
				];
			
				$this->model_extension_stable_module_stable->addChatAction($chat_action_data);
			} else {
				$this->errors[] = 'Chat not found!';
			}
		}
				
		if ($this->errors) {
			$data = [
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			];
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_stable_module_stable->log($request, $data, 'getZonesByCountryId');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	private function getRequestData(): array {
		$request = json_decode(file_get_contents('php://input'), true);
		
		if (!is_array($request)) {
			$request = [];
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
		
	private function validateToolPermission(string $tool_code): bool {
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'stable/system/config/');
		$_config->load('stable');
		
		$config_setting = $_config->get('stable_setting');
		
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		$permission = false;
				
		if (!empty($setting['side']['frontend']['tool'][$tool_code]['status'])) {
			$permission = true;
		}
							
		return $permission;
	}
}