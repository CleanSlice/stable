<?php
class ControllerExtensionStableFrontend extends Controller {
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
			'endpoint' => $this->url->link('extension/stable/frontend/getCategory', '', true),
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
			'endpoint' => $this->url->link('extension/stable/frontend/getCategories', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'name' => array('type' => 'string', 'description' => 'Search categories by name'),
					'parent_category_id' => array('type' => 'number', 'description' => 'Search categories by parent Category ID'),
					'sort' => array('type' => 'string', 'description' => 'Sort in the category search results (name/sort_order)', 'default' => 'sort_order'),
					'order' => array('type' => 'string', 'description' => 'Order in the category search results (ASC/DESC)', 'default' => 'ASC'),
					'page' => array('type' => 'number', 'description' => 'Page number in the category search results', 'default' => 1)
				),
				'required' => array('chat_id')
			)
		);
		
		$data['result']['tools']['getProduct'] = array(
			'name' => 'getProduct',
			'description' => 'Get information about the product',
			'endpoint' => $this->url->link('extension/stable/frontend/getProduct', '', true),
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
			'endpoint' =>$this->url->link('extension/stable/frontend/getProducts', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'name' => array('type' => 'string', 'description' => 'Search products by name'),
					'model' => array('type' => 'string', 'description' => 'Search products by model'),
					'price_min' => array('type' => 'number', 'description' => 'Search products priced at or above this value. Give it in the same terms the customer sees in the results: tax included, in the current currency. Numbers only, no currency symbol.'),
					'price_max' => array('type' => 'number', 'description' => 'Search products priced at or below this value. Give it in the same terms the customer sees in the results: tax included, in the current currency. Numbers only, no currency symbol.'),
					'quantity_min' => array('type' => 'number', 'description' => 'Search products with a quantity greater than this value'),
					'quantity_max' => array('type' => 'number', 'description' => 'Search products with a quantity less than this value'),
					'category_id' => array('type' => 'number', 'description' => 'Search products in the category with this ID'),
					'date_added_from' => array('type' => 'string', 'format' => 'date', 'description' => 'Search products by date added, starting from this date (Format: YYYY-MM-DD)'),
					'date_added_to' => array('type' => 'string', 'format' => 'date', 'description' => 'Search products by date added, ending with this date (Format: YYYY-MM-DD)'),
					'sort' => array('type' => 'string', 'description' => 'Sort in the product search results (name/model/price/quantity/sort_order/date_added/rating)', 'default' => 'sort_order'),
					'order' => array('type' => 'string', 'description' => 'Order in the product search results (ASC/DESC)', 'default' => 'ASC'),
					'page' => array('type' => 'number', 'description' => 'Page number in the product search results', 'default' => 1)
				),
				'required' => array('chat_id')
			)
		);
		
		$data['result']['tools']['getCurrentCustomer'] = array(
			'name' => 'getCurrentCustomer',
			'description' => 'Get information about the current customer',
			'endpoint' => $this->url->link('extension/stable/frontend/getCurrentCustomer', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
				),
				'required' => array('chat_id')
			)
		);
		
		$data['result']['tools']['getCurrentCustomerOrder'] = array(
			'name' => 'getCurrentCustomerOrder',
			'description' => 'Get information about current customer order',
			'endpoint' => $this->url->link('extension/stable/frontend/getCurrentCustomerOrder', '', true),
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
				
		$data['result']['tools']['getCurrentCustomerOrders'] = array(
			'name' => 'getCurrentCustomerOrders',
			'description' => 'Get information about current customer orders',
			'endpoint' => $this->url->link('extension/stable/frontend/getCurrentCustomerOrders', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'page' => array('type' => 'number', 'description' => 'Page number in the customer order search results', 'default' => 1)
				),
				'required' => array('chat_id')
			)
		);
				
		$data['result']['tools']['addCartProduct'] = array(
			'name' => 'addCartProduct',
			'description' => 'Add product to cart',
			'endpoint' => $this->url->link('extension/stable/frontend/addCartProduct', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'product_id' => array('type' => 'number', 'description' => 'Product ID'),
					'quantity' => array('type' => 'number', 'description' => 'Quantity', 'default' => 1),
					'option' => array(
						'type' => 'object', 
						'description' => 'Product options where KEY is product_option_id (stringified number) and VALUE is product_option_value_id (number) or text value.',
						'additionalProperties' => array(
							'type' => array('number', 'string', 'array'),
							'description' => 'The value of the product option (product_option_value_id, text, or array of IDs for checkboxes)'
						)
					),						
					'recurring_id' => array('type' => 'number', 'description' => 'Recurring ID', 'default' => 0),
				),
				'required' => array('chat_id', 'product_id')
			)
		);
		
		$data['result']['tools']['editCartProduct'] = array(
			'name' => 'editCartProduct',
			'description' => 'Edit product in the cart',
			'endpoint' => $this->url->link('extension/stable/frontend/editCartProduct', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'cart_id' => array('type' => 'number', 'description' => 'Cart ID'),
					'quantity' => array('type' => 'number', 'description' => 'Quantity')
				),
				'required' => array('chat_id', 'cart_id', 'quantity')
			)
		);
		
		$data['result']['tools']['deleteCartProduct'] = array(
			'name' => 'deleteCartProduct',
			'description' => 'Delete product from the cart',
			'endpoint' => $this->url->link('extension/stable/frontend/deleteCartProduct', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'cart_id' => array('type' => 'number', 'description' => 'Cart ID')
				),
				'required' => array('chat_id', 'cart_id')
			)
		);
		
		$data['result']['tools']['getCartProducts'] = array(
			'name' => 'getCartProducts',
			'description' => 'Get information about products in the cart',
			'endpoint' => $this->url->link('extension/stable/frontend/getCartProducts', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
				),
				'required' => array('chat_id')
			)
		);
				
		$data['result']['tools']['createOrder'] = array(
			'name' => 'createOrder',
			'description' => 'Create Order',
			'endpoint' => $this->url->link('extension/stable/frontend/createOrder', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'firstname' => array('type' => 'string', 'description' => 'First Name'),
					'lastname' => array('type' => 'string', 'description' => 'Last Name'),
					'email' => array('type' => 'string', 'description' => 'E-Mail'),
					'telephone' => array('type' => 'string', 'description' => 'Telephone'),
					'company' => array('type' => 'string', 'description' => 'Company'),
					'address_1' => array('type' => 'string', 'description' => 'Address 1'),
					'address_2' => array('type' => 'string', 'description' => 'Address 2'),
					'city' => array('type' => 'string', 'description' => 'City'),
					'postcode' => array('type' => 'string', 'description' => 'Postcode'),
					'country_id' => array('type' => 'number', 'description' => 'Country ID'),
					'zone_id' => array('type' => 'number', 'description' => 'Zone ID'),
					'shipping_method_code' => array('type' => 'string', 'description' => 'Shipping Method Code, exactly as returned by getShippingMethods. Required whenever the cart contains a shippable product; omit only for digital-only carts.'),
					'payment_method_code' => array('type' => 'string', 'description' => 'Payment Method Code, exactly as returned by getPaymentMethods. If this is "authorizenet_aim", the cc_* fields below are all required.'),
					'cc_owner' => array('type' => 'string', 'description' => 'Card Owner. Required only when payment_method_code is "authorizenet_aim".'),
					'cc_number' => array('type' => 'string', 'description' => 'Card Number, digits only. Required only when payment_method_code is "authorizenet_aim".'),
					'cc_expire_date_month' => array('type' => 'string', 'description' => 'Card Expiry Month, 2 digits ("01".."12"). Required only when payment_method_code is "authorizenet_aim".'),
					'cc_expire_date_year' => array('type' => 'string', 'description' => 'Card Expiry Year, 4 digits ("2029"). Required only when payment_method_code is "authorizenet_aim".'),
					'cc_cvv2' => array('type' => 'string', 'description' => 'Card Security Code (CVV2), 3-4 digits. Required only when payment_method_code is "authorizenet_aim".')
				),
				'required' => array('chat_id', 'payment_method_code'),
				'allOf' => array(
					array(
						'if' => array(
							'properties' => array(
								'payment_method_code' => array('const' => 'authorizenet_aim')
							),
							'required' => array('payment_method_code')
						),
						'then' => array(
							'required' => array('cc_owner', 'cc_number', 'cc_expire_date_month', 'cc_expire_date_year', 'cc_cvv2')
						)
					)
				)
			)
		);
								
		$data['result']['tools']['getShippingMethods'] = array(
			'name' => 'getShippingMethods',
			'description' => 'Get information about shipping methods',
			'endpoint' => $this->url->link('extension/stable/frontend/getShippingMethods', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'country_id' => array('type' => 'number', 'description' => 'Country ID'),
					'zone_id' => array('type' => 'number', 'description' => 'Zone ID')
				),
				'required' => array('chat_id', 'country_id', 'zone_id')
			)
		);
		
		$data['result']['tools']['getPaymentMethods'] = array(
			'name' => 'getPaymentMethods',
			'description' => 'Get information about payment methods',
			'endpoint' => $this->url->link('extension/stable/frontend/getPaymentMethods', '', true),
			'requestMethod' => 'POST',
			'inputSchema' => array(
				'type' => 'object',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'country_id' => array('type' => 'number', 'description' => 'Country ID'),
					'zone_id' => array('type' => 'number', 'description' => 'Zone ID')
				),
				'required' => array('chat_id', 'country_id', 'zone_id')
			)
		);
	
		$data['result']['tools']['getCountries'] = array(
			'name' => 'getCountries',
			'description' => 'Get information about countries',
			'endpoint' => $this->url->link('extension/stable/frontend/getCountries', '', true),
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
			'endpoint' => $this->url->link('extension/stable/frontend/getZonesByCountryId', '', true),
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
		$this->load->model('extension/stable/frontend');
				
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
					$this->model_extension_stable_frontend->refreshStartup($chat);
								
					$category = $this->model_extension_stable_frontend->getCategory($request['category_id']);
				
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
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
									
		if (!$this->errors) {				
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
				
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
	
	public function getProduct() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (empty($request['product_id'])) {
			$this->errors[] = 'Product ID required!';
		}
									
		if (!$this->errors) {				
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
							
					$product = $this->model_extension_stable_frontend->getProduct($request['product_id']);
				
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
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
								
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
					
		if (!$this->errors) {				
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('product')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
					
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
						'filter_category_id'  		=> $category_id,
						'filter_date_added_from'    => $date_added_from,
						'filter_date_added_to'    	=> $date_added_to,
						'sort'                		=> $sort,
						'order'               		=> $order,
						'start'               		=> ($page - 1) * $limit,
						'limit'               		=> $limit
					);
															
					$product_total = $this->model_extension_stable_frontend->getTotalProducts($filter_data);
						
					$products = $this->model_extension_stable_frontend->getProducts($filter_data);
						
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
	
	public function getCurrentCustomer() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
						
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
															
		if (!$this->errors) {				
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('customer')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
							
					$customer = $this->model_extension_stable_frontend->getCustomer($this->customer->getId());
				
					if ($customer) {
						$data = array(
							'jsonrpc' => "2.0",
							'result' => $customer
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'customer',
							'action_code' => 'getCurrentCustomer',
							'action_message' => 'Get information about the current customer.'
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
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
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getCurrentCustomer');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCurrentCustomerOrder() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
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
				if ($this->validateToolPermission('customer')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
							
					$customer_order = $this->model_extension_stable_frontend->getCustomerOrder($this->customer->getId(), $request['order_id']);
				
					if ($customer_order) {
						$data = array(
							'jsonrpc' => "2.0",
							'result' => $customer_order
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'customer',
							'action_code' => 'getCurrentCustomerOrder',
							'action_message' => 'Get information about the current customer order.'
						);
						
						$this->model_extension_module_stable->addChatAction($chat_action_data);
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
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'getCurrentCustomer');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCurrentCustomerOrders() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
															
		if (!$this->errors) {				
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('customer')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
					
					$customer_id = $this->customer->getId();
					
					if (!empty($request['page'])) {
						$page = $request['page'];
					} else {
						$page = 1;
					}

					$limit = 20;		
					$start = ($page - 1) * $limit;
										
					$customer_order_total = $this->model_extension_stable_frontend->getTotalCustomerOrders($customer_id);
						
					$customer_orders = $this->model_extension_stable_frontend->getCustomerOrders($customer_id, $start, $limit);
						
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'orders' => $customer_orders,
							'orderCount' => $customer_order_total,
							'page' => $page,
							'pageCount' => ceil($customer_order_total / $limit)
						)
					);
														
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'customer',
						'action_code' => 'getCurrentCustomerOrders',
						'action_message' => 'Get information about current customer orders.'
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
		
		$this->model_extension_module_stable->log($request, $data, 'getCurrentCustomerOrders');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
				
	public function addCartProduct() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (empty($request['product_id'])) {
			$this->errors[] = 'Product ID required!';
		}
										
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('cart')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
				
					$product = $this->model_extension_stable_frontend->getProduct($request['product_id']);
				
					if ($product) {
						if (!empty($request['quantity'])) {
							$quantity = (int)$request['quantity'];
						} else {
							$quantity = 1;
						}
						
						if (!empty($request['option'])) {
							$option = array_filter($request['option']);
						} else {
							$option = array();
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
							$recurring_ids = array();

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
							
							$data = array(
								'jsonrpc' => "2.0",
								'result' => array(
									'products' => $products,
								)
							);
							
							$chat_action_data = array(
								'chat_id' => $chat['chat_id'],
								'tool_code' => 'cart',
								'action_code' => 'addCartProduct',
								'action_message' => sprintf('Add product with ID %s to cart.', $product['product_id'])
							);
						
							$this->model_extension_module_stable->addChatAction($chat_action_data);
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
			$data = array(
				'jsonrpc' => "2.0",
				'error' => implode(' ', $this->errors),
				'errors' => $this->errors
			);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 400 Bad Request');
		}
		
		$this->model_extension_module_stable->log($request, $data, 'addProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function editCartProduct() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
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
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('cart')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
			
					$quantity = (int)$request['quantity'];
							
					$this->cart->update($request['cart_id'], $request['quantity']);
							
					$products = $this->cart->getProducts();
							
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'products' => $products,
						)
					);
					
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'cart',
						'action_code' => 'editCartProduct',
						'action_message' => 'Edit product in the cart.'
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
		
		$this->model_extension_module_stable->log($request, $data, 'updateProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function deleteCartProduct() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
					
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (empty($request['cart_id'])) {
			$this->errors[] = 'Cart ID required!';
		}
										
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('cart')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
			
					$this->cart->remove($request['cart_id']);
						
					$products = $this->cart->getProducts();
							
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'products' => $products,
						)
					);
					
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'cart',
						'action_code' => 'deleteCartProduct',
						'action_message' => 'Delete product from the cart.'
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
		
		$this->model_extension_module_stable->log($request, $data, 'deleteProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function getCartProducts() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
		
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
									
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('cart')) {						
					$this->model_extension_stable_frontend->refreshStartup($chat);
	
					$products = $this->cart->getProducts();
						
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'products' => $products,
						)
					);
					
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'cart',
						'action_code' => 'getCartProducts',
						'action_message' => 'Get information about products in the cart.'
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
		
		$this->model_extension_module_stable->log($request, $data, 'getProductsInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function createOrder() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
		
		$this->request->post = $request;
				
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
															
		if (!$this->errors) {
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('checkout')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
					
					if ($this->customer->isLogged()) {
						$customer = $this->model_extension_stable_frontend->getCustomer($this->customer->getId());
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
					
					if ($this->request->post['payment_method_code'] == 'authorizenet_aim') {
						if (empty($this->request->post['cc_owner'])) {
							$this->errors[] = 'Card Owner required for this payment method!';
						}
						
						if (empty($this->request->post['cc_number'])) {
							$this->errors[] = 'Card Number required for this payment method!';
						}
						
						if (empty($this->request->post['cc_expire_date_month'])) {
							$this->errors[] = 'Card Expiry Date Month required for this payment method!';
						}
						
						if (empty($this->request->post['cc_expire_date_year'])) {
							$this->errors[] = 'Card Expiry Date Year required for this payment method!';
						}
						
						if (empty($this->request->post['cc_cvv2'])) {
							$this->errors[] = 'Card Security Code (CVV2) required for this payment method!';
						}
					}
				} else {
					$this->errors[] = 'You do not have permission to use this tool!';
				}
			} else {
				$this->errors[] = 'Chat not found!';
			}	
					
			if (!$this->errors) {						
				$this->session->data['account'] = 'guest';
				$this->session->data['guest']['customer_id'] = $this->customer->getId();
				$this->session->data['guest']['customer_group_id'] = $this->customer->getGroupId();
				$this->session->data['guest']['firstname'] = trim($this->request->post['firstname']);
				$this->session->data['guest']['lastname'] = trim($this->request->post['lastname']);
				$this->session->data['guest']['email'] = trim($this->request->post['email']);
				$this->session->data['guest']['telephone'] = trim($this->request->post['telephone']);
				$this->session->data['guest']['custom_field'] = array();
									
				$this->session->data['payment_address']['firstname'] = trim($this->request->post['firstname']);
				$this->session->data['payment_address']['lastname'] = trim($this->request->post['lastname']);
				$this->session->data['payment_address']['company'] = trim($this->request->post['company']);
				$this->session->data['payment_address']['address_1'] = trim($this->request->post['address_1']);
				$this->session->data['payment_address']['address_2'] = trim($this->request->post['address_2']);
				$this->session->data['payment_address']['city'] = trim($this->request->post['city']);
				$this->session->data['payment_address']['postcode'] = trim($this->request->post['postcode']);
				$this->session->data['payment_address']['country_id'] = $this->request->post['country_id'];
				$this->session->data['payment_address']['zone_id'] = $this->request->post['zone_id'];
				$this->session->data['payment_address']['custom_field'] = array();
				
				$this->session->data['shipping_address']['firstname'] = trim($this->request->post['firstname']);
				$this->session->data['shipping_address']['lastname'] = trim($this->request->post['lastname']);
				$this->session->data['shipping_address']['company'] = trim($this->request->post['company']);
				$this->session->data['shipping_address']['address_1'] = trim($this->request->post['address_1']);
				$this->session->data['shipping_address']['address_2'] = trim($this->request->post['address_2']);
				$this->session->data['shipping_address']['city'] = trim($this->request->post['city']);
				$this->session->data['shipping_address']['postcode'] = trim($this->request->post['postcode']);
				$this->session->data['shipping_address']['country_id'] = $this->request->post['country_id'];
				$this->session->data['shipping_address']['zone_id'] = $this->request->post['zone_id'];
				$this->session->data['shipping_address']['custom_field'] = array();
																
				$country_info = $this->model_extension_stable_frontend->getCountry($this->request->post['country_id']);

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
						
				$zone_info = $this->model_extension_stable_frontend->getZone($this->request->post['zone_id']);

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
				
				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;

				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);

				$this->load->model('setting/extension');

				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);

						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}
				
				$method_data = array();

				$results = $this->model_setting_extension->getExtensions('shipping');

				foreach ($results as $result) {
					if ($this->config->get('shipping_' . $result['code'] . '_status')) {
						$this->load->model('extension/shipping/' . $result['code']);

						$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

						if ($quote) {
							$method_data[$result['code']] = array(
								'title'      => $quote['title'],
								'quote'      => $quote['quote'],
								'sort_order' => $quote['sort_order'],
								'error'      => $quote['error']
							);
						}
					}
				}

				$sort_order = array();

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
				
				$method_data = array();

				$results = $this->model_setting_extension->getExtensions('payment');

				$recurring = $this->cart->hasRecurringProducts();

				foreach ($results as $result) {
					if ($this->config->get('payment_' . $result['code'] . '_status')) {
						$this->load->model('extension/payment/' . $result['code']);

						$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

						if ($method) {
							if ($recurring) {
								if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
									$method_data[$result['code']] = $method;
								}
							} else {
								$method_data[$result['code']] = $method;
							}
						}
					}
				}

				$sort_order = array();

				foreach ($method_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $method_data);

				$this->session->data['payment_methods'] = $method_data;
										
				if (!empty($this->request->post['payment_method_code'])) {
					if (!empty($this->session->data['payment_methods'][$this->request->post['payment_method_code']])) {
						$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method_code']];
					}
				}
	
				if (empty($this->session->data['payment_method']) && $this->session->data['payment_methods']) {
					$this->session->data['payment_method'] = reset($this->session->data['payment_methods']);
				}
			
				$order_data = array();

				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;

				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);

				$this->load->model('setting/extension');

				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);

						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totals);

				$order_data['totals'] = $totals;

				$this->load->language('checkout/checkout');

				$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
				$order_data['store_id'] = $this->config->get('config_store_id');
				$order_data['store_name'] = $this->config->get('config_name');

				if ($order_data['store_id']) {
					$order_data['store_url'] = $this->config->get('config_url');
				} else {
					if ($this->request->server['HTTPS']) {
						$order_data['store_url'] = HTTPS_SERVER;
					} else {
						$order_data['store_url'] = HTTP_SERVER;
					}
				}
												
				$order_data['customer_id'] = $this->session->data['guest']['customer_id'];
				$order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
				$order_data['firstname'] = $this->session->data['guest']['firstname'];
				$order_data['lastname'] = $this->session->data['guest']['lastname'];
				$order_data['email'] = $this->session->data['guest']['email'];
				$order_data['telephone'] = $this->session->data['guest']['telephone'];
				$order_data['custom_field'] = $this->session->data['guest']['custom_field'];
				
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
				$order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());
							
				if (!empty($this->session->data['payment_method']['title'])) {
					$order_data['payment_method'] = $this->session->data['payment_method']['title'];
				} else {
					$order_data['payment_method'] = '';
				}

				if (!empty($this->session->data['payment_method']['code'])) {
					$order_data['payment_code'] = $this->session->data['payment_method']['code'];
				} else {
					$order_data['payment_code'] = '';
				}
				
				if ($this->cart->hasShipping()) {
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
					$order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());
																											
					if (!empty($this->session->data['shipping_method']['title'])) {
						$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
					} else {
						$order_data['shipping_method'] = '';
					}

					if (!empty($this->session->data['shipping_method']['code'])) {
						$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
					} else {
						$order_data['shipping_code'] = '';
					}
				} else {
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
					$order_data['shipping_custom_field'] = array();
					$order_data['shipping_method'] = '';
					$order_data['shipping_code'] = '';
				}

				$order_data['products'] = array();

				foreach ($this->cart->getProducts() as $product) {
					$option_data = array();

					foreach ($product['option'] as $option) {
						$option_data[] = array(
							'product_option_id'       => $option['product_option_id'],
							'product_option_value_id' => $option['product_option_value_id'],
							'option_id'               => $option['option_id'],
							'option_value_id'         => $option['option_value_id'],
							'name'                    => $option['name'],
							'value'                   => $option['value'],
							'type'                    => $option['type']
						);
					}

					$order_data['products'][] = array(
						'product_id' 		=> $product['product_id'],
						'name'       		=> $product['name'],
						'model'      		=> $product['model'],
						'option'     		=> $option_data,
						'download'   		=> $product['download'],
						'quantity'   		=> $product['quantity'],
						'subtract'   		=> $product['subtract'],
						'price'      		=> $product['price'],
						'total'      		=> $product['total'],
						'tax'        		=> $this->tax->getTax($product['price'], $product['tax_class_id']),
						'reward'     		=> $product['reward']
					);
				}

				$order_data['vouchers'] = array();

				if (!empty($this->session->data['vouchers'])) {
					foreach ($this->session->data['vouchers'] as $voucher) {
						$order_data['vouchers'][] = array(
							'description'      => $voucher['description'],
							'code'             => token(10),
							'to_name'          => $voucher['to_name'],
							'to_email'         => $voucher['to_email'],
							'from_name'        => $voucher['from_name'],
							'from_email'       => $voucher['from_email'],
							'voucher_theme_id' => $voucher['voucher_theme_id'],
							'message'          => $voucher['message'],
							'amount'           => $voucher['amount']
						);
					}
				}
		
				$order_data['comment'] = '';
				$order_data['total'] = $total_data['total'];

				if (isset($this->request->cookie['tracking'])) {
					$order_data['tracking'] = $this->request->cookie['tracking'];

					$subtotal = $this->cart->getSubTotal();
					
					$this->load->model('account/customer');

					$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

					if ($affiliate_info) {
						$order_data['affiliate_id'] = $affiliate_info['customer_id'];
						$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					$this->load->model('checkout/marketing');

					$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

					if ($marketing_info) {
						$order_data['marketing_id'] = $marketing_info['marketing_id'];
					} else {
						$order_data['marketing_id'] = 0;
					}
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
					$order_data['marketing_id'] = 0;
					$order_data['tracking'] = '';
				}
				
				$order_data['language_id'] = $this->config->get('config_language_id');
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
								
				if ($this->request->post['payment_method_code'] == 'authorizenet_aim') {
					$this->load->controller('extension/payment/' . $this->session->data['payment_method']['code'] . '/send');
				} else {
					$this->load->controller('extension/payment/' . $this->session->data['payment_method']['code'] . '/confirm');
				}
			
				$output = $this->response->getOutput();
				
				$json = json_decode($output, true);
				
				if (!empty($json['redirect'])) {
					$order = $this->model_extension_stable_frontend->getOrder($this->session->data['order_id']);
					
					if ($order) {							
						$data = array(
							'jsonrpc' => "2.0",
							'result' => $order
						);
						
						$chat_action_data = array(
							'chat_id' => $chat['chat_id'],
							'tool_code' => 'checkout',
							'action_code' => 'createOrder',
							'action_message' => sprintf('Create Order with ID %s.', $order['order_id'])
						);
					
						$this->model_extension_module_stable->addChatAction($chat_action_data);
					}
					
					$this->cart->clear();

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
					unset($this->session->data['guest']);
					unset($this->session->data['comment']);
					unset($this->session->data['order_id']);
					unset($this->session->data['coupon']);
					unset($this->session->data['reward']);
					unset($this->session->data['voucher']);
					unset($this->session->data['vouchers']);
					unset($this->session->data['totals']);
				} else {
					$data = array(
						'jsonrpc' => "2.0"
					);
					
					$data = array_merge($data, $json);
				}
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
		
		$this->model_extension_module_stable->log($request, $data, 'createOrder');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
		
	public function getShippingMethods() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
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
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('checkout')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
				
					$this->load->model('setting/extension');
						
					$method_data = array();

					$results = $this->model_setting_extension->getExtensions('shipping');

					foreach ($results as $result) {
						if ($this->config->get('shipping_' . $result['code'] . '_status')) {
							$this->load->model('extension/shipping/' . $result['code']);

							$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote(array('country_id' => $request['country_id'], 'zone_id' => $request['zone_id']));

							if ($quote) {
								foreach ($quote['quote'] as $quote_data) {
									$method_data[$quote_data['code']] = array(
										'code' => $quote_data['code'],
										'title' => $quote_data['title'],
										'text' => $quote_data['text']
									);
								}
							}
						}
					}
								
					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'shipping_methods' => $method_data
						)
					);
					
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'checkout',
						'action_code' => 'getShippingMethods',
						'action_message' => 'Get information about shipping methods.'
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
		
		$this->model_extension_module_stable->log($request, $data, 'getShippingMethods');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}	
	
	public function getPaymentMethods() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
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
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				if ($this->validateToolPermission('checkout')) {
					$this->model_extension_stable_frontend->refreshStartup($chat);
					
					$this->load->model('setting/extension');

					$totals = array();
					$taxes = $this->cart->getTaxes();
					$total = 0;

					$total_data = array(
						'totals' => &$totals,
						'taxes'  => &$taxes,
						'total'  => &$total
					);

					$sort_order = array();

					$results = $this->model_setting_extension->getExtensions('total');

					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
					}

					array_multisort($sort_order, SORT_ASC, $results);

					foreach ($results as $result) {
						if ($this->config->get('total_' . $result['code'] . '_status')) {
							$this->load->model('extension/total/' . $result['code']);

							$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
						}
					}
					
					$method_data = array();

					$results = $this->model_setting_extension->getExtensions('payment');
					
					$recurring = $this->cart->hasRecurringProducts();

					foreach ($results as $result) {
						if ($this->config->get('payment_' . $result['code'] . '_status')) {
							$this->load->model('extension/payment/' . $result['code']);

							$method = $this->{'model_extension_payment_' . $result['code']}->getMethod(array('country_id' => $request['country_id'], 'zone_id' => $request['zone_id']), $total);

							if ($method) {
								if ($recurring) {
									if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
										$method_data[$result['code']] = array(
											'code' => $method['code'],
											'title' => $method['title']
										);
									}
								} else {
									$method_data[$result['code']] = array(
										'code' => $method['code'],
										'title' => $method['title']
									);
								}
							}
						}
					}

					$data = array(
						'jsonrpc' => "2.0",
						'result' => array(
							'payment_methods' => $method_data
						)
					);
					
					$chat_action_data = array(
						'chat_id' => $chat['chat_id'],
						'tool_code' => 'checkout',
						'action_code' => 'getPaymentMethods',
						'action_message' => 'Get information about payment methods.'
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
		
		$this->model_extension_module_stable->log($request, $data, 'getPaymentMethods');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCountries() {
		$this->load->model('extension/module/stable');
		$this->load->model('extension/stable/frontend');
		
		$request = $this->getRequestData();
				
		if (empty($request['chat_id'])) {
			$this->errors[] = 'Chat ID required!';
		}
						
		if (!$this->errors) {				
			$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
			
			if ($chat) {
				$this->model_extension_stable_frontend->refreshStartup($chat);
						
				$countries = $this->model_extension_stable_frontend->getCountries();
			
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
		$this->load->model('extension/stable/frontend');
		
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
				$this->model_extension_stable_frontend->refreshStartup($chat);

				$zones = $this->model_extension_stable_frontend->getZonesByCountryId($request['country_id']);
				
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
				
		if (!empty($setting['side']['frontend']['tool'][$tool_code]['status'])) {
			$permission = true;
		}
							
		return $permission;
	}
}