<?php
class ControllerExtensionModuleStable extends Controller {
	private $error = array();
							
	public function index() {
		$_config = new Config();
		$_config->load('stable');
				
		$config_setting = $_config->get('stable_setting');
				
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_SERVER;
		} else {
			$server = HTTP_SERVER;
		}
		
		$mcp_url = $server . 'index.php?route=extension/module/stable';
				
		$data = array(
			'jsonrpc' => "2.0",
            'result' => array(
				'tools' => array(),
				'serverInfo' => array(
                    'name' => 'php-mcp-server',
                    'version' => '1.0.0'
                )
			)
		);
		
		if (!empty($this->request->get['side'])) {
			$side = $this->request->get['side'];
			
			if (!empty($setting['side'][$side]['tool']['product']['status'])) {
				$data['result']['tools']['getCategory'] = array(
					'name' => 'getCategory',
					'description' => 'Get information about the product category',
					'endpoint' => $mcp_url . '/getCategory',
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
					'endpoint' => $mcp_url . '/getCategories',
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
					'endpoint' => $mcp_url . '/getProduct',
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
					'endpoint' => $mcp_url . '/getProducts',
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
			}
			
			if (!empty($setting['side'][$side]['tool']['customer']['status'])) {
				$data['result']['tools']['getCustomer'] = array(
					'name' => 'getCustomer',
					'description' => 'Get information about the customer',
					'endpoint' => $mcp_url . '/getCustomer',
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
					'endpoint' => $mcp_url . '/getCustomers',
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
					'endpoint' => $mcp_url . '/getCustomerGroups',
					'requestMethod' => 'GET',
					'properties' => array(
						'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
					),
					'required' => array('chat_id')
				);
			}
			
			if (!empty($setting['side'][$side]['tool']['order']['status'])) {
				$data['result']['tools']['getOrder'] = array(
					'name' => 'getOrder',
					'description' => 'Get information about the order',
					'endpoint' => $mcp_url . '/getOrder',
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
					'endpoint' => $mcp_url . '/getOrders',
					'requestMethod' => 'GET',
					'properties' => array(
						'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
						'customer_name' => array('type' => 'string', 'description' => 'Search orders by customer name', 'default' => ''),
						'order_status_id' => array('type' => 'number', 'description' => 'Search orders by order status ID', 'default' => ''),
						'date_added_from' => array('type' => 'string', 'format' => 'date', 'description' => 'Search orders by date added, starting from this date (Format: YYYY-MM-DD)', 'default' => ''),
						'date_added_to' => array('type' => 'string', 'format' => 'date', 'description' => 'Search orders by date added, ending with this date (Format: YYYY-MM-DD)', 'default' => ''),
						'page' => array('type' => 'number', 'description' => 'Page number in the customer search results', 'default' => '1')
					),
					'required' => array('chat_id')
				);
				
				$data['result']['tools']['getOrderStatuses'] = array(
					'name' => 'getOrderStatuses',
					'description' => 'Get information about order statuses',
					'endpoint' => $mcp_url . '/getOrderStatuses',
					'requestMethod' => 'GET',
					'properties' => array(
						'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
					),
					'required' => array('chat_id')
				);
			}
						
			if (!empty($setting['side'][$side]['tool']['cart']['status'])) {
				$data['result']['tools']['addProductInCart'] = array(
					'name' => 'addProductInCart',
					'description' => 'Add product to cart',
					'endpoint' => $mcp_url . '/addProductInCart',
					'requestMethod' => 'POST',
					'inputSchema' => array(
						'type' => 'object',
						'properties' => array(
							'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
							'product_id' => array('type' => 'number', 'description' => 'Product ID'),
							'quantity' => array('type' => 'number', 'description' => 'Quantity', 'default' => '1'),
							'option' => array(
								'type' => 'object', 
								'description' => 'Product options where KEY is product_option_id (stringified number) and VALUE is product_option_value_id (number) or text value.',
								'additionalProperties' => array(
									'type' => array('number', 'string', 'array'),
									'description' => 'The value of the product option (product_option_value_id, text, or array of IDs for checkboxes)'
								)
							),						
							'recurring_id' => array('type' => 'number', 'description' => 'Recurring ID', 'default' => '0'),
						),
						'required' => array('chat_id', 'product_id')
					)
				);
				
				$data['result']['tools']['updateProductInCart'] = array(
					'name' => 'updateProductInCart',
					'description' => 'Update product in the cart',
					'endpoint' => $mcp_url . '/updateProductInCart',
					'requestMethod' => 'PATCH',
					'inputSchema' => array(
						'type' => 'object',
						'properties' => array(
							'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
							'cart_id' => array('type' => 'number', 'description' => 'Cart ID'),
							'quantity' => array('type' => 'number', 'description' => 'Quantity')
						),
						'required' => array('chat_id', 'product_id', 'quantity')
					)
				);
				
				$data['result']['tools']['deleteProductInCart'] = array(
					'name' => 'deleteProductInCart',
					'description' => 'Delete product from the cart',
					'endpoint' => $mcp_url . '/deleteProductInCart',
					'requestMethod' => 'DELETE',
					'properties' => array(
						'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
						'cart_id' => array('type' => 'number', 'description' => 'Cart ID')
					),
					'required' => array('chat_id', 'cart_id')
				);
				
				$data['result']['tools']['getProductsInCart'] = array(
					'name' => 'getProductsInCart',
					'description' => 'Get information about products in the cart',
					'endpoint' => $mcp_url . '/getProductsInCart',
					'requestMethod' => 'GET',
					'properties' => array(
						'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
						'cart_id' => array('type' => 'number', 'description' => 'Cart ID')
					),
					'required' => array('chat_id', 'cart_id')
				);
			}
			
			if (!empty($setting['side'][$side]['tool']['checkout']['status'])) {				
				$data['result']['tools']['createOrder'] = array(
					'name' => 'createOrder',
					'description' => 'Create Order',
					'endpoint' => $mcp_url . '/createOrder',
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
							'shipping_method_code' => array('type' => 'string', 'description' => 'Shipping Method Code'),
							'payment_method_code' => array('type' => 'string', 'description' => 'Payment Method Code'),
							'cc_owner' => array('type' => 'string', 'description' => 'Card Owner'),
							'cc_number' => array('type' => 'number', 'description' => 'Card Number'),
							'cc_expire_date_month' => array('type' => 'number', 'description' => 'Card Expiry Date Month'),
							'cc_expire_date_year' => array('type' => 'number', 'description' => 'Card Expiry Date Month Year'),
							'cc_cvv2' => array('type' => 'number', 'description' => 'Card Security Code (CVV2)')
						),
						'required' => array('chat_id', 'shipping_method_code', 'payment_method_code')
					)
				);
			}
			
			$data['result']['tools']['getCountries'] = array(
				'name' => 'getCountries',
				'description' => 'Get information about countries',
				'endpoint' => $mcp_url . '/getCountries',
				'requestMethod' => 'GET',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID')
				),
				'required' => array('chat_id')
			);
			
			$data['result']['tools']['getZonesByCountryId'] = array(
				'name' => 'getZonesByCountryId',
				'description' => 'Get information about zones for this country ID',
				'endpoint' => $mcp_url . '/getCountries',
				'requestMethod' => 'GET',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'country_id' => array('type' => 'number', 'description' => 'Country ID')
				),
				'required' => array('chat_id', 'country_id')
			);
			
			$data['result']['tools']['getShippingMethods'] = array(
				'name' => 'getShippingMethods',
				'description' => 'Get information about shipping methods',
				'endpoint' => $mcp_url . '/getShippingMethods',
				'requestMethod' => 'GET',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'country_id' => array('type' => 'number', 'description' => 'Country ID'),
					'zone_id' => array('type' => 'number', 'description' => 'Zone ID')
				),
				'required' => array('chat_id', 'country_id', 'zone_id')
			);
			
			$data['result']['tools']['getPaymentMethods'] = array(
				'name' => 'getPaymentMethods',
				'description' => 'Get information about payment methods',
				'endpoint' => $mcp_url . '/getPaymentMethods',
				'requestMethod' => 'GET',
				'properties' => array(
					'chat_id' => array('type' => 'string', 'description' => 'Chat ID'),
					'country_id' => array('type' => 'number', 'description' => 'Country ID'),
					'zone_id' => array('type' => 'number', 'description' => 'Zone ID')
				),
				'required' => array('chat_id', 'country_id', 'zone_id')
			);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCategory() {
		$this->load->model('extension/module/stable');
		
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
					if ($this->validateToolPermission($chat, 'product')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
									
						$category = $this->model_extension_module_stable->getCategory($this->request->get['category_id']);
					
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
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
										
			if (!$error) {				
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'product')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
					
						if (!empty($this->request->get['parent_id'])) {
							$parent_id = $this->request->get['parent_id'];
						} else {
							$parent_id = 0;
						}
						
						$categories = $this->model_extension_module_stable->getCategories($parent_id);
							
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
					if ($this->validateToolPermission($chat, 'product')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
								
						$product = $this->model_extension_module_stable->getProduct($this->request->get['product_id']);
					
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
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {						
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (!$error) {				
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'product')) {
						$this->model_extension_module_stable->refreshChatSession($chat);

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
																
						$product_total = $this->model_extension_module_stable->getTotalProducts($filter_data);
							
						$products = $this->model_extension_module_stable->getProducts($filter_data);
							
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
					if ($this->validateToolPermission($chat, 'customer')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
								
						$customer = $this->model_extension_module_stable->getCustomer($this->request->get['customer_id']);
					
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
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {			
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'customer')) {
						$this->model_extension_module_stable->refreshChatSession($chat);

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
																
						$customer_total = $this->model_extension_module_stable->getTotalCustomers($filter_data);
							
						$customers = $this->model_extension_module_stable->getCustomers($filter_data);
							
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
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'customer')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
								
						$customer_groups = $this->model_extension_module_stable->getCustomerGroups();
					
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
					if ($this->validateToolPermission($chat, 'order')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
								
						$order = $this->model_extension_module_stable->getOrder($this->request->get['order_id']);
					
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
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {						
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
						
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'order')) {
						$this->model_extension_module_stable->refreshChatSession($chat);

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
																
						$order_total = $this->model_extension_module_stable->getTotalOrders($filter_data);
							
						$orders = $this->model_extension_module_stable->getOrders($filter_data);
							
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
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'order')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
								
						$order_statuses = $this->model_extension_module_stable->getOrderStatuses();
					
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
			
	public function addProductInCart() {
		$this->load->model('extension/module/stable');
		
		$input = file_get_contents('php://input');
		$request = json_decode($input, true);
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'POST') {						
			if (empty($request['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (empty($request['product_id'])) {
				$error = 'Product ID required!';
			}
											
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'cart')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
					
						$product = $this->model_extension_module_stable->getProduct($request['product_id']);
					
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
													
							$product_options = $this->model_extension_module_stable->getProductOptions($product['product_id']);

							foreach ($product_options as $product_option) {
								if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
									$error = sprintf('%s required!', $product_option['name']);
								}
							}
							
							if (!empty($request['recurring_id'])) {
								$recurring_id = $request['recurring_id'];
							} else {
								$recurring_id = 0;
							}

							$recurrings = $this->model_extension_module_stable->getProductRecurrings($product['product_id']);

							if ($recurrings) {
								$recurring_ids = array();

								foreach ($recurrings as $recurring) {
									$recurring_ids[] = $recurring['recurring_id'];
								}

								if (!in_array($recurring_id, $recurring_ids)) {
									$error = 'Please select a payment recurring!';
								}
							}
						
							if (!$error) {
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
									'action_code' => 'addProductInCart',
									'action_message' => sprintf('Add product with ID %s to cart.', $product['product_id'])
								);
							
								$this->model_extension_module_stable->addChatAction($chat_action_data);
							}
						} else {
							$data = array(
								'jsonrpc' => "2.0",
								'error' => 'Product not found!'
							);
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
		
		$this->model_extension_module_stable->log($request, $data, 'addProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function updateProductInCart() {
		$this->load->model('extension/module/stable');
		
		$input = file_get_contents('php://input');
		$request = json_decode($input, true);
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'PATCH') {			
			if (empty($request['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (empty($request['cart_id'])) {
				$error = 'Cart ID required!';
			}
			
			if (empty($request['quantity'])) {
				$error = 'Quantity required!';
			}
											
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($request['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'cart')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
				
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
							'action_code' => 'updateProductInCart',
							'action_message' => 'Update product in the cart.'
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
		
		$this->model_extension_module_stable->log($request, $data, 'updateProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function deleteProductInCart() {
		$this->load->model('extension/module/stable');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'DELETE') {
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (empty($this->request->get['cart_id'])) {
				$error = 'Cart ID required!';
			}
											
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'cart')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
				
						$this->cart->remove($this->request->get['cart_id']);
							
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
							'action_code' => 'deleteProductInCart',
							'action_message' => 'Delete product from the cart.'
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
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'deleteProductInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function getProductsInCart() {
		$this->load->model('extension/module/stable');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {			
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
										
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'cart')) {						
						$this->model_extension_module_stable->refreshChatSession($chat);
		
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
							'action_code' => 'getProductsInCart',
							'action_message' => 'Get information about products in the cart.'
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
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getProductsInCart');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));		
	}
	
	public function createOrder() {
		$this->load->model('extension/module/stable');
		
		$input = file_get_contents('php://input');
		$request = json_decode($input, true);
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'POST') {			
			if (empty($request['chat_id'])) {
				$error = 'Chat ID required!';
			}
																
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					if ($this->validateToolPermission($chat, 'checkout')) {
						$this->model_extension_module_stable->refreshChatSession($chat);
						
						if ($this->customer->isLogged()) {
							$customer_info = $this->model_extension_module_stable->getCustomer($this->customer->getId());
							$address_info = $this->model_extension_module_stable->getAddress($this->customer->getAddressId());
						}
						
						if (empty($request['firstname'])) {
							if (!empty($customer_info['firstname'])) {
								$request['firstname'] = $customer_info['firstname'];
							} else {
								$request['firstname'] = '';
							}
						}
												
						if (empty($request['lastname'])) {
							if (!empty($customer_info['lastname'])) {
								$request['lastname'] = $customer_info['lastname'];
							} else {
								$request['lastname'] = '';
							}
						}
						
						if (empty($request['email'])) {
							if (!empty($customer_info['email'])) {
								$request['email'] = $customer_info['email'];
							} else {
								$request['email'] = '';
							}
						}
						
						if (empty($request['telephone'])) {
							if (!empty($customer_info['telephone'])) {
								$request['telephone'] = $customer_info['telephone'];
							} else {
								$request['telephone'] = '';
							}
						}
						
						if (empty($request['company'])) {
							if (!empty($address_info['company'])) {
								$request['company'] = $address_info['company'];
							} else {
								$request['company'] = '';
							}
						}
						
						if (empty($request['address_1'])) {
							if (!empty($address_info['address_1'])) {
								$request['address_1'] = $address_info['address_1'];
							} else {
								$request['address_1'] = '';
							}
						}
						
						if (empty($request['address_2'])) {
							if (!empty($address_info['address_2'])) {
								$request['address_2'] = $address_info['address_2'];
							} else {
								$request['address_2'] = '';
							}
						}
						
						if (empty($request['city'])) {
							if (!empty($address_info['city'])) {
								$request['city'] = $address_info['city'];
							} else {
								$request['city'] = '';
							}
						}
						
						if (empty($request['postcode'])) {
							if (!empty($address_info['postcode'])) {
								$request['postcode'] = $address_info['postcode'];
							} else {
								$request['postcode'] = '';
							}
						}
						
						if (empty($request['country_id'])) {
							if (!empty($address_info['country_id'])) {
								$request['country_id'] = $address_info['country_id'];
							} else {
								$request['country_id'] = '';
							}
						}
						
						if (empty($request['zone_id'])) {
							if (!empty($address_info['zone_id'])) {
								$request['zone_id'] = $address_info['zone_id'];
							} else {
								$request['zone_id'] = '';
							}
						}	
			
						if (empty($request['firstname'])) {
							$error = 'First Name required!';
						} elseif ((utf8_strlen($request['firstname']) < 1) || (utf8_strlen($request['firstname']) > 32)) {
							$error = 'First Name must be between 1 and 32 characters!';
						} 
							
						if (empty($request['lastname'])) {
							$error = 'Last Name required!';
						} elseif ((utf8_strlen($request['lastname']) < 1) || (utf8_strlen($request['lastname']) > 32)) {
							$error = 'Last Name must be between 1 and 32 characters!';
						}
							
						if (empty($request['email'])) {
							$error = 'E-Mail required!';
						} elseif ((utf8_strlen($request['email']) > 96) || !filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
							$error = 'E-Mail address does not appear to be valid!';
						}
						
						if (!empty($request['telephone']) && (utf8_strlen($request['telephone']) < 3) || (utf8_strlen($request['telephone']) > 32)) {
							$error = 'Telephone does not appear to be valid!';
						}
							
						if (empty($request['address_1'])) {
							$error = 'Address 1 required!';
						} elseif ((utf8_strlen($request['address_1']) < 3) || (utf8_strlen($request['address_1']) > 128)) {
							$error = 'Address 1 must be between 3 and 128 characters!';
						} 
						
						if (empty($request['city'])) {
							$error = 'City required!';
						} elseif ((utf8_strlen($request['city']) < 3) || (utf8_strlen($request['city']) > 128)) {
							$error = 'City must be between 2 and 128 characters!';
						}
							
						if (empty($request['postcode'])) {
							$error = 'Postcode required!';
						} elseif ((utf8_strlen($request['postcode']) < 3) || (utf8_strlen($request['postcode']) > 10)) {
							$error = 'Postcode must be between 2 and 10 characters!';
						}
										
						if (empty($request['country_id'])) {
							$error = 'Country ID required!';
						}
										
						if (empty($request['zone_id'])) {
							$error = 'Zone ID required!';
						}
									
						if (empty($request['payment_method_code'])) {
							$error = 'Payment method required!';
						}
					
						if (!$this->cart->hasProducts()) {
							$error = 'Your shopping cart is empty!';
						}
						
						if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
							$error = 'Products in cart are not in stock!';
						}
						
						if ($this->cart->hasShipping() && empty($request['shipping_method_code'])) {
							$error = 'Shipping method required!';
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
								$error = 'Products in cart are not available in the required quantity!';

								break;
							}
						}
						
						if ($request['payment_method_code'] == 'authorizenet_aim') {
							if (empty($request['cc_owner'])) {
								$error = 'Card Owner required for this payment method!';
							}
							
							if (empty($request['cc_number'])) {
								$error = 'Card Number required for this payment method!';
							}
							
							if (empty($request['cc_expire_date_month'])) {
								$error = 'Card Expiry Date Month required for this payment method!';
							}
							
							if (empty($request['cc_expire_date_year'])) {
								$error = 'Card Expiry Date Year required for this payment method!';
							}
							
							if (empty($request['cc_cvv2'])) {
								$error = 'Card Security Code (CVV2) required for this payment method!';
							}
						}
					} else {
						$error = 'You do not have permission to use this tool!';
					}
				} else {
					$error = 'Chat not found!';
				}	
										
				if (!$error) {
					$this->request->post = $request;
					
					$this->session->data['account'] = 'guest';
					$this->session->data['guest']['customer_id'] = $this->customer->getId();
					$this->session->data['guest']['customer_group_id'] = $this->customer->getGroupId();
					$this->session->data['guest']['firstname'] = trim($request['firstname']);
					$this->session->data['guest']['lastname'] = trim($request['lastname']);
					$this->session->data['guest']['email'] = trim($request['email']);
					$this->session->data['guest']['telephone'] = trim($request['telephone']);
					$this->session->data['guest']['custom_field'] = array();
										
					$this->session->data['payment_address']['firstname'] = trim($request['firstname']);
					$this->session->data['payment_address']['lastname'] = trim($request['lastname']);
					$this->session->data['payment_address']['company'] = trim($request['company']);
					$this->session->data['payment_address']['address_1'] = trim($request['address_1']);
					$this->session->data['payment_address']['address_2'] = trim($request['address_2']);
					$this->session->data['payment_address']['city'] = trim($request['city']);
					$this->session->data['payment_address']['postcode'] = trim($request['postcode']);
					$this->session->data['payment_address']['country_id'] = $request['country_id'];
					$this->session->data['payment_address']['zone_id'] = $request['zone_id'];
					$this->session->data['payment_address']['custom_field'] = array();
					
					$this->session->data['shipping_address']['firstname'] = trim($request['firstname']);
					$this->session->data['shipping_address']['lastname'] = trim($request['lastname']);
					$this->session->data['shipping_address']['company'] = trim($request['company']);
					$this->session->data['shipping_address']['address_1'] = trim($request['address_1']);
					$this->session->data['shipping_address']['address_2'] = trim($request['address_2']);
					$this->session->data['shipping_address']['city'] = trim($request['city']);
					$this->session->data['shipping_address']['postcode'] = trim($request['postcode']);
					$this->session->data['shipping_address']['country_id'] = $request['country_id'];
					$this->session->data['shipping_address']['zone_id'] = $request['zone_id'];
					$this->session->data['shipping_address']['custom_field'] = array();
															
					$this->load->model('localisation/country');
		
					$country_info = $this->model_localisation_country->getCountry($request['country_id']);

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
					
					$this->load->model('localisation/zone');
		
					$zone_info = $this->model_localisation_zone->getZone($request['zone_id']);

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
				
					if (!empty($request['shipping_method_code'])) {
						$shipping = explode('.', $request['shipping_method_code']);

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
											
					if (!empty($request['payment_method_code'])) {
						if (!empty($this->session->data['payment_methods'][$request['payment_method_code']])) {
							$this->session->data['payment_method'] = $this->session->data['payment_methods'][$request['payment_method_code']];
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
									
					if ($request['payment_method_code'] == 'authorizenet_aim') {
						$this->load->controller('extension/payment/' . $this->session->data['payment_method']['code'] . '/send');
					} else {
						$this->load->controller('extension/payment/' . $this->session->data['payment_method']['code'] . '/confirm');
					}
					
					$output = $this->response->getOutput();
					
					$json = json_decode($output, true);
					
					if (!empty($json['redirect'])) {
						$order = $this->model_extension_module_stable->getOrder($this->session->data['order_id']);
					
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
		} else {
			$error = 'Method Not Allowed!';
		}
		
		if ($error) {
			$data = array(
				'jsonrpc' => "2.0",
				'error' => $error
			);
		}
		
		$this->model_extension_module_stable->log($request, $data, 'createOrder');
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
	public function getCountries() {
		$this->load->model('extension/module/stable');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {			
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
							
			if (!$error) {				
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					$this->model_extension_module_stable->refreshChatSession($chat);
							
					$countries = $this->model_extension_module_stable->getCountries();
				
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
					$this->model_extension_module_stable->refreshChatSession($chat);

					$zones = $this->model_extension_module_stable->getZonesByCountryId($this->request->get['country_id']);
					
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

	public function getShippingMethods() {
		$this->load->model('extension/module/stable');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {			
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
				
			if (empty($this->request->get['country_id'])) {
				$error = 'Country ID required!';
			}
							
			if (empty($this->request->get['zone_id'])) {
				$error = 'Zone ID required!';
			}
				
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					$this->model_extension_module_stable->refreshChatSession($chat);
				
					$this->load->model('setting/extension');
						
					$method_data = array();

					$results = $this->model_setting_extension->getExtensions('shipping');

					foreach ($results as $result) {
						if ($this->config->get('shipping_' . $result['code'] . '_status')) {
							$this->load->model('extension/shipping/' . $result['code']);

							$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote(array('country_id' => $this->request->get['country_id'], 'zone_id' => $this->request->get['zone_id']));

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
						'tool_code' => '',
						'action_code' => 'getShippingMethods',
						'action_message' => 'Get information about shipping methods.'
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
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getShippingMethods');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}	
	
	public function getPaymentMethods() {
		$this->load->model('extension/module/stable');
		
		$error = '';
		
		if ($this->request->server['REQUEST_METHOD'] === 'GET') {			
			if (empty($this->request->get['chat_id'])) {
				$error = 'Chat ID required!';
			}
				
			if (empty($this->request->get['country_id'])) {
				$error = 'Country ID required!';
			}
							
			if (empty($this->request->get['zone_id'])) {
				$error = 'Zone ID required!';
			}
				
			if (!$error) {
				$chat = $this->model_extension_module_stable->getChat($this->request->get['chat_id']);
				
				if ($chat) {
					$this->model_extension_module_stable->refreshChatSession($chat);
					
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

							$method = $this->{'model_extension_payment_' . $result['code']}->getMethod(array('country_id' => $this->request->get['country_id'], 'zone_id' => $this->request->get['zone_id']), $total);

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
						'tool_code' => '',
						'action_code' => 'getPaymentMethods',
						'action_message' => 'Get information about payment methods.'
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
		
		$this->model_extension_module_stable->log($this->request->get, $data, 'getPaymentMethods');
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));	
	}
	
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
				if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
					$server = HTTPS_SERVER;
				} else {
					$server = HTTP_SERVER;
				}
			
				$mcp_url = $server . 'index.php?route=extension/module/stable&side=frontend';
		
				setcookie('stable_chat_id', $chat_id, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_agent_id', $setting['side']['frontend']['agent_id'], time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_token', $ranch_token, time() + 60, '/', $this->request->server['HTTP_HOST']);
				setcookie('stable_mcp_url', $mcp_url, time() + 60, '/', $this->request->server['HTTP_HOST']);
						
				$this->document->addScript('catalog/view/javascript/stable/stable.js');
			}
		}
	}
	
	private function validateToolPermission($chat, $tool_code) {
		$_config = new Config();
		$_config->load('stable');
				
		$config_setting = $_config->get('stable_setting');
				
		$setting = array_replace_recursive((array)$config_setting, (array)$this->config->get('module_stable_setting'));
		
		$permission = false;
				
		if ($chat['customer_id']) {
			if (!empty($setting['side']['frontend']['tool'][$tool_code]['status'])) {
				$permission = true;
			}
		}
			
		if ($chat['user_id']) {
			if (!empty($setting['side']['backend']['tool'][$tool_code]['status'])) {
				$permission = true;
			}
		}
				
		return $permission;
	}
}