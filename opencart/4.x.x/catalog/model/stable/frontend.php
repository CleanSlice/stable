<?php
namespace Opencart\Catalog\Model\Extension\Stable\Stable;
class Frontend extends \Opencart\System\Engine\Model {
	private $product_statement = [];
	private $product_price_expression = null;
	
	public function refreshStartup($chat) {
		if ($this->config->get('config_session_expire')) {
			$this->config->set('session_expire', $this->config->get('config_session_expire'));
		}

		$this->config->set('session_samesite', $this->config->get('config_session_samesite'));
		
		$this->session->start($chat['session_id']);

		$option = [
			'expires'  => $this->config->get('config_session_expire') ? time() + (int)$this->config->get('config_session_expire') : 0,
			'path'     => $this->config->get('session_path'),
			'secure'   => $this->request->server['HTTPS'],
			'httponly' => false,
			'SameSite' => $this->config->get('session_samesite')
		];
		
		setcookie($this->config->get('session_name'), $this->session->getId(), $option);
		
		if (isset($this->session->data['language_catalog'])) {
			$language_code = $this->session->data['language_catalog'];
		} else {
			if (version_compare((string)VERSION, '4.1.0.0', '>=')) {
				$language_code = $this->config->get('config_language_catalog');
			} else {
				$language_code = $this->config->get('config_language');
			}
		}
		
		$this->load->model('localisation/language');

		$language_info = $this->model_localisation_language->getLanguageByCode($language_code);

		if ($language_info) {
			$this->config->set('config_language_id', $language_info['language_id']);
			$this->config->set('config_language', $language_info['code']);
		
			if (version_compare((string)VERSION, '4.0.2.0', '>=')) {			
				if ($language_info['extension']) {
					$this->language->addPath('extension/' . $language_info['extension'], DIR_EXTENSION . $language_info['extension'] . '/catalog/language/');
				}
		
				$this->load->language('default');	
			} else{						
				$language = new \Opencart\System\Library\Language($language_info['code']);
				
				if (!empty($language_info['extension'])) {
					$language->addPath(DIR_EXTENSION . $language_info['extension'] . '/catalog/language/');
				} else {
					$language->addPath(DIR_LANGUAGE);
				}
				
				$language->load($language_info['code']);
		
				$this->registry->set('language', $language);				
			}
		}
		
						
		$this->registry->set('customer', new \Opencart\System\Library\Cart\Customer($this->registry));

		if (isset($this->session->data['customer'])) {
			$this->config->set('config_customer_group_id', $this->session->data['customer']['customer_group_id']);
		} elseif ($this->customer->isLogged()) {
			$this->config->set('config_customer_group_id', $this->customer->getGroupId());
		}

		$code = '';

		$this->load->model('localisation/currency');

		$currencies = $this->model_localisation_currency->getCurrencies();

		if (isset($this->session->data['currency'])) {
			$code = $this->session->data['currency'];
		}

		if (isset($this->request->cookie['currency']) && !array_key_exists($code, $currencies)) {
			$code = $this->request->cookie['currency'];
		}

		if (!array_key_exists($code, $currencies)) {
			$code = $this->config->get('config_currency');
		}

		if (!isset($this->session->data['currency']) || $this->session->data['currency'] != $code) {
			$this->session->data['currency'] = $code;
		}

		if (!isset($this->request->cookie['currency']) || $this->request->cookie['currency'] != $code) {
			$option = [
				'expires'  => time() + 60 * 60 * 24 * 30,
				'path'     => '/',
				'SameSite' => 'Lax'
			];

			setcookie('currency', $code, $option);
		}

		$this->registry->set('currency', new \Opencart\System\Library\Cart\Currency($this->registry));
		
		$this->registry->set('tax', new \Opencart\System\Library\Cart\Tax($this->registry));

		if (isset($this->session->data['shipping_address'])) {
			$this->tax->setShippingAddress((int)$this->session->data['shipping_address']['country_id'], (int)$this->session->data['shipping_address']['zone_id']);
		} elseif ($this->config->get('config_tax_default') == 'shipping') {
			$this->tax->setShippingAddress((int)$this->config->get('config_country_id'), (int)$this->config->get('config_zone_id'));
		}

		if (isset($this->session->data['payment_address'])) {
			$this->tax->setPaymentAddress((int)$this->session->data['payment_address']['country_id'], (int)$this->session->data['payment_address']['zone_id']);
		} elseif ($this->config->get('config_tax_default') == 'payment') {
			$this->tax->setPaymentAddress((int)$this->config->get('config_country_id'), (int)$this->config->get('config_zone_id'));
		}

		$this->tax->setStoreAddress((int)$this->config->get('config_country_id'), (int)$this->config->get('config_zone_id'));
				
		$this->registry->set('weight', new \Opencart\System\Library\Cart\Weight($this->registry));
		
		$this->registry->set('length', new \Opencart\System\Library\Cart\Length($this->registry));
		
		$this->registry->set('cart', new \Opencart\System\Library\Cart\Cart($this->registry));
	}
	
	public function getCategory(int $category_id): array|bool {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		if ($query->num_rows) {
			$description = html_entity_decode($query->row['description'], ENT_QUOTES, 'UTF-8');
			
			$this->load->model('tool/image');
			
			if (is_file(DIR_IMAGE . $query->row['image'])) {
				$image = $this->model_tool_image->resize($query->row['image'], 40, 40);
			} else {
				$image = '';
			}
				
			return [
				'category_id' 		 => $query->row['category_id'],
				'name'        		 => $query->row['name'],
				'description'      	 => $description,
				'meta_title'       	 => $query->row['meta_title'],
				'meta_description' 	 => $query->row['meta_description'],
				'meta_keyword'     	 => $query->row['meta_keyword'],
				'image'				 => $image,
				'parent_category_id' => $query->row['parent_id'],
				'sort_order'  		 => $query->row['sort_order'],
				'date_added'         => $query->row['date_added'],
				'date_modified'      => $query->row['date_modified']
			];
		} else {
			return false;
		}
	}
		
	public function getCategories(array $data = []): array {
		$sql = "SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'";
		
		$implode = [];
		
		if (!empty($data['filter_parent_category_id'])) {
			$implode[] = "c.parent_id = '" . (int)$data['filter_parent_category_id'] . "'";
		}
		
		if (!empty($data['filter_name'])) {
			$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

			foreach ($words as $word) {
				$implode[] = "cd.name LIKE '%" . $this->db->escape($word) . "%'";
			}
		}
						
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}
		
		$sort_data = [
			'name' => 'cd.name',
			'sort_order' => 'c.sort_order',
		];

		if (isset($data['sort']) && isset($sort_data[$data['sort']])) {
			if ($sort_data[$data['sort']] == 'cd.name') {
				$sql .= " ORDER BY LCASE(" . $sort_data[$data['sort']] . ")";
			} else {
				$sql .= " ORDER BY " . $sort_data[$data['sort']];
			}
		} else {
			$sql .= " ORDER BY c.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(cd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(cd.name) ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	
				
		$category_data = array();
		
		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) {
			$category_data[] = $this->getCategory($result['category_id']);
		}

		return $category_data;
	}
	
	public function getTotalCategories(array $data = []): int {
		$sql = "SELECT COUNT(DISTINCT c.category_id) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'";

		$implode = [];
		
		if (!empty($data['filter_parent_category_id'])) {
			$implode[] = "c.parent_id = '" . (int)$data['filter_parent_category_id'] . "'";
		}
		
		if (!empty($data['filter_name'])) {
			$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

			foreach ($words as $word) {
				$implode[] = "cd.name LIKE '%" . $this->db->escape($word) . "%'";
			}
		}
				
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}
	
	public function getManufacturer(int $manufacturer_id): array|bool {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			$this->load->model('tool/image');
			
			if (is_file(DIR_IMAGE . $query->row['image'])) {
				$image = $this->model_tool_image->resize($query->row['image'], 40, 40);
			} else {
				$image = '';
			}
				
			return [
				'manufacturer_id' 	=> $query->row['manufacturer_id'],
				'name'        		=> $query->row['name'],
				'image'				=> $image,
				'sort_order'  		=> $query->row['sort_order']
			];
		} else {
			return false;
		}
	}
	
	public function getManufacturers(array $data = []): array {
		$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		$implode = [];
				
		if (!empty($data['filter_name'])) {
			$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

			foreach ($words as $word) {
				$implode[] = "m.name LIKE '%" . $this->db->escape($word) . "%'";
			}
		}
						
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}
		
		$sort_data = [
			'name' => 'm.name',
			'sort_order' => 'm.sort_order'
		];
		
		if (isset($data['sort']) && isset($sort_data[$data['sort']])) {
			if ($sort_data[$data['sort']] == 'm.name') {
				$sql .= " ORDER BY LCASE(" . $sort_data[$data['sort']] . ")";
			} else {
				$sql .= " ORDER BY " . $sort_data[$data['sort']];
			}
		} else {
			$sql .= " ORDER BY LCASE(m.name)";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(m.name) DESC";
		} else {
			$sql .= " ASC, LCASE(m.name) ASC";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$manufacturer_data = array();
		
		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) {
			$manufacturer_data[] = $this->getManufacturer($result['manufacturer_id']);
		}

		return $manufacturer_data;
	}
	
	public function getTotalManufacturers(array $data = []): int {
		$sql = "SELECT COUNT(DISTINCT m.manufacturer_id) AS total FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		$implode = [];
				
		if (!empty($data['filter_name'])) {
			$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

			foreach ($words as $word) {
				$implode[] = "m.name LIKE '%" . $this->db->escape($word) . "%'";
			}
		}
				
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}
		
	public function getProduct(int $product_id): array|bool {
		$product_statement = $this->getProductStatement();
		
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, GROUP_CONCAT(DISTINCT(p2c.category_id) ORDER BY p2c.category_id ASC SEPARATOR ',') AS categories_id, " . $product_statement['discount'] . ", " . $product_statement['special'] . ", " . $product_statement['reward'] . ", " . $product_statement['review'] . ", " . $product_statement['weight_class'] . ",  " . $product_statement['length_class'] . ", " . $product_statement['tax_class'] . ", " . $product_statement['rating'] . ", p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) JOIN " . DB_PREFIX . "product_to_category AS p2c ON (p.product_id = p2c.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		
		if (!empty($query->row['product_id'])) {
			$description = html_entity_decode($query->row['description'], ENT_QUOTES, 'UTF-8');
			
			$this->load->model('tool/image');
			
			if (is_file(DIR_IMAGE . $query->row['image'])) {
				$image = $this->model_tool_image->resize($query->row['image'], 40, 40);
			} else {
				$image = '';
			}
			
			if ($query->row['discount']) {
				$price = $this->currency->format($this->tax->calculate($query->row['discount'], $query->row['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = $this->currency->format($this->tax->calculate($query->row['price'], $query->row['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			}
			
			$special = $this->currency->format($this->tax->calculate($query->row['special'], $query->row['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			
			return [
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $description,
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'image'            => $image,
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'categories_id'	   => $query->row['categories_id'],
				'options'		   => $this->getProductOptions($query->row['product_id'], $query->row['tax_class_id']),
				'subscriptions'	   => $this->getProductSubscriptions($query->row['product_id']),
				'price'            => $price,
				'special'          => $special,
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'tax_class'        => $query->row['tax_class'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'weight_class'     => $query->row['weight_class'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'length_class'     => $query->row['length_class'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'href'        	   => $this->url->link('product/product', 'product_id=' . $query->row['product_id'])
			];
		} else {
			return false;
		}
	}
	
	public function getProductOptions(int $product_id, int $tax_class_id): array {
		$product_option_data = [];

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = [];

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$price = $this->currency->format($this->tax->calculate($product_option_value['price'], $tax_class_id, $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
				
				$product_option_value_data[] = [
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $price,
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				];
			}

			$product_option_data[] = [
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			];
		}

		return $product_option_data;
	}
	
	public function getProductSubscriptions(int $product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_subscription` `ps` LEFT JOIN `" . DB_PREFIX . "subscription_plan` `sp` ON (`ps`.`subscription_plan_id` = `sp`.`subscription_plan_id`) LEFT JOIN `" . DB_PREFIX . "subscription_plan_description` `spd` ON (`sp`.`subscription_plan_id` = `spd`.`subscription_plan_id`) WHERE `ps`.`product_id` = '" . (int)$product_id . "' AND `ps`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND `spd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `sp`.`status` = '1' ORDER BY `sp`.`sort_order` ASC");

		return $query->rows;
	}
	
	public function getProducts(array $data = []): array {
		$product_statement = $this->getProductStatement();
		
		$sql = "SELECT p.product_id, m.name AS manufacturer, " . $product_statement['discount'] . ", " . $product_statement['special'] . ", " . $product_statement['rating'];

		if (!empty($data['filter_category_id'])) {
			$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		$implode = [];
		
		if (!empty($data['filter_category_id'])) {
			$implode[] = "cp.path_id = '" . (int)$data['filter_category_id'] . "'";
		}
				
		if (!empty($data['filter_name'])) {
			$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

			foreach ($words as $word) {
				$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
			}
		}
		
		if (!empty($data['filter_model'])) {
			$implode[] = "LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_model'])) . "'";
		}
				
		if (isset($data['filter_price_min']) && $data['filter_price_min'] !== '') {
			$implode[] = $this->getProductPriceExpression() . " >= " . (float)$data['filter_price_min'];
		}

		if (isset($data['filter_price_max']) && $data['filter_price_max'] !== '') {
			$implode[] = $this->getProductPriceExpression() . " <= " . (float)$data['filter_price_max'];
		}

		if (isset($data['filter_quantity_min']) && $data['filter_quantity_min'] !== '') {
			$implode[] = "p.quantity >= '" . (int)$data['filter_quantity_min'] . "'";
		}
		
		if (isset($data['filter_quantity_max']) && $data['filter_quantity_max'] !== '') {
			$implode[] = "p.quantity <= '" . (int)$data['filter_quantity_max'] . "'";
		}
		
		if (!empty($data['filter_manufacturer_id'])) {
			$implode[] = "p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		
		if (!empty($data['filter_date_added_from'])) {
			$implode[] = "DATE(p.date_added) >= DATE('" . $this->db->escape($data['filter_date_added_from']) . "')";
		}

		if (!empty($data['filter_date_added_to'])) {
			$implode[] = "DATE(p.date_added) <= DATE('" . $this->db->escape($data['filter_date_added_to']) . "')";
		}
		
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}
		
		$sql .= " GROUP BY p.product_id";
		
		$sort_data = [
			'name' => 'pd.name',
			'model' => 'p.model',
			'price' => 'p.price',
			'quantity' => 'p.quantity',
			'sort_order' => 'p.sort_order',
			'date_added' => 'p.date_added',
			'manufacturer' => 'manufacturer',
			'rating' => 'rating'
		];
		
		if (isset($data['sort']) && isset($sort_data[$data['sort']])) {
			if ($sort_data[$data['sort']] == 'pd.name' || $sort_data[$data['sort']] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $sort_data[$data['sort']] . ")";
			} elseif ($sort_data[$data['sort']] == 'p.price') {
				$sql .= " ORDER BY " . $this->getProductPriceExpression();
			} else {
				$sql .= " ORDER BY " . $sort_data[$data['sort']];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}
				
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) {
			$product_data[] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}
	
	public function getTotalProducts(array $data = []): int {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['filter_category_id'])) {
			$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		$implode = [];
		
		if (!empty($data['filter_category_id'])) {
			$implode[] = "cp.path_id = '" . (int)$data['filter_category_id'] . "'";
		}
				
		if (!empty($data['filter_name'])) {
			$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

			foreach ($words as $word) {
				$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
			}
		}
		
		if (!empty($data['filter_model'])) {
			$implode[] = "LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_model'])) . "'";
		}
		
		if (!empty($data['filter_manufacturer_id'])) {
			$implode[] = "p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		
		if (isset($data['filter_price_min']) && $data['filter_price_min'] !== '') {
			$implode[] = $this->getProductPriceExpression() . " >= " . (float)$data['filter_price_min'];
		}

		if (isset($data['filter_price_max']) && $data['filter_price_max'] !== '') {
			$implode[] = $this->getProductPriceExpression() . " <= " . (float)$data['filter_price_max'];
		}
		
		if (isset($data['filter_quantity_min']) && $data['filter_quantity_min'] !== '') {
			$implode[] = "p.quantity >= '" . (int)$data['filter_quantity_min'] . "'";
		}
		
		if (isset($data['filter_quantity_max']) && $data['filter_quantity_max'] !== '') {
			$implode[] = "p.quantity <= '" . (int)$data['filter_quantity_max'] . "'";
		}
		
		if (!empty($data['filter_date_added_from'])) {
			$implode[] = "DATE(p.date_added) >= DATE('" . $this->db->escape($data['filter_date_added_from']) . "')";
		}

		if (!empty($data['filter_date_added_to'])) {
			$implode[] = "DATE(p.date_added) <= DATE('" . $this->db->escape($data['filter_date_added_to']) . "')";
		}
		
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}
	
	public function getCustomer(int $customer_id): array|bool {
		$customer_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
	
		if ($customer_query->num_rows) {
			return [
				'customer_id'             => $customer_query->row['customer_id'],
				'customer_group_id'       => $customer_query->row['customer_group_id'],
				'firstname'               => $customer_query->row['firstname'],
				'lastname'                => $customer_query->row['lastname'],
				'email'                   => $customer_query->row['email'],
				'telephone'               => $customer_query->row['telephone'],
				'custom_field'            => json_decode($customer_query->row['custom_field'], true),
				'address'				  => $this->getAddress($customer_query->row['address_id']),
				'newsletter'       		  => $customer_query->row['newsletter'],
				'status'            	  => $customer_query->row['status'],
				'safe'            		  => $customer_query->row['safe'],
				'date_added'              => $customer_query->row['date_added']
			];
		} else {
			return false;
		}
	}
				
	public function getAddress(int $address_id): array|bool {
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$address_data = [
				'address_id'     => $address_query->row['address_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				'custom_field'   => json_decode($address_query->row['custom_field'], true)
			];

			return $address_data;
		} else {
			return false;
		}
	}
	
	public function getCustomerOrder(int $customer_id, int $order_id): array|bool {
		$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "' AND o.customer_id = '" . (int)$customer_id . "' AND o.customer_id != '0' AND o.order_status_id > '0'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}
		
			return [
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer'                => $order_query->row['customer'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'custom_field'            => json_decode($order_query->row['custom_field'], true),
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => json_decode($order_query->row['payment_custom_field'], true),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => json_decode($order_query->row['shipping_custom_field'], true),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'products'		   		  => $this->getOrderProducts($order_query->row['order_id']),
				'totals'	   			  => $this->getOrderTotals($order_query->row['order_id']),
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'order_status'            => $order_query->row['order_status'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified']
			];
		} else {
			return false;
		}
	}
	
	public function getCustomerOrders(int $customer_id, int $start = 0, int $limit = 20): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}
		
		$query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname, o.order_status_id, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o WHERE o.customer_id = '" . (int)$customer_id . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);
		
		return $query->rows;
	}
	
	public function getTotalCustomerOrders(int $customer_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE customer_id = '" . (int)$customer_id . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		return (int)$query->row['total'];
	}
	
	public function getOrder(int $order_id): array|bool {
		$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			return [
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer'                => $order_query->row['customer'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'custom_field'            => json_decode($order_query->row['custom_field'], true),
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => json_decode($order_query->row['payment_custom_field'], true),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => json_decode($order_query->row['shipping_custom_field'], true),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'products'		   		  => $this->getOrderProducts($order_query->row['order_id']),
				'totals'	   			  => $this->getOrderTotals($order_query->row['order_id']),
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'order_status'            => $order_query->row['order_status'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified']
			];
		} else {
			return false;
		}
	}
	
	public function getOrderProducts(int $order_id): array {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->rows;
	}
	
	public function getOrderTotals(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
		
		return $query->rows;
	}
	
	public function getCountry(int $country_id): array {
		if (version_compare((string)VERSION, '4.1.0.1', '>=')) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` `c` LEFT JOIN `" . DB_PREFIX . "country_description` `cd` ON (`c`.`country_id` = `cd`.`country_id`) WHERE `c`.`country_id` = '" . (int)$country_id . "' AND `cd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `c`.`status` = '1'");
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");
		}
		
		return $query->row;
	}
			
	public function getCountries(): array {
		if (version_compare((string)VERSION, '4.1.0.1', '>=')) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "country` `c` LEFT JOIN `" . DB_PREFIX . "country_description` `cd` ON (`c`.`country_id` = `cd`.`country_id`) WHERE `c`.`status` = '1' AND `cd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `cd`.`name` ASC";
		} else {
			$sql = "SELECT * FROM `" . DB_PREFIX . "country` WHERE `status` = '1' ORDER BY `name` ASC";
		}
		
		$key = md5($sql);

		$country_data = $this->cache->get('country.' . $key);

		if (!$country_data) {
			$query = $this->db->query($sql);

			$country_data = $query->rows;

			$this->cache->set('country.' . $key, $country_data);
		}
		
		return $country_data;
	}
	
	public function getZone(int $zone_id): array {
		if (version_compare((string)VERSION, '4.1.0.1', '>=')) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` `z` LEFT JOIN `" . DB_PREFIX . "zone_description` `zd` ON (`z`.`zone_id` = `zd`.`zone_id`) WHERE `z`.`zone_id` = '" . (int)$zone_id . "' AND `zd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `z`.`status` = '1'");
		} else {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE `zone_id` = '" . (int)$zone_id . "' AND `status` = '1'");
		}
		
		return $query->row;
	}
	
	public function getZonesByCountryId(int $country_id): array {
		if (version_compare((string)VERSION, '4.1.0.1', '>=')) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "zone` `z` LEFT JOIN `" . DB_PREFIX . "zone_description` `zd` ON (`z`.`zone_id` = `zd`.`zone_id`) WHERE `z`.`country_id` = '" . (int)$country_id . "' AND `zd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `z`.`status` = '1' ORDER BY `zd`.`name`";
		} else {
			$sql = "SELECT * FROM `" . DB_PREFIX . "zone` WHERE `country_id` = '" . (int)$country_id . "' AND `status` = '1' ORDER BY `name`";
		}
		
		$key = md5($sql);

		$zone_data = $this->cache->get('zone.' . $key);

		if (!$zone_data) {
			$query = $this->db->query($sql);

			$zone_data = $query->rows;

			$this->cache->set('zone.' . $key, $zone_data);
		}
		
		return $zone_data;
	}
	
	private function getProductStatement(): array {
		if ($this->product_statement) {
			return $this->product_statement;
		}
				
		if (version_compare((string)VERSION, '4.1.0.0', '>=')) {
			$this->product_statement['discount'] = "(SELECT (CASE WHEN `pd2`.`type` = 'P' THEN (`p`.`price` - (`p`.`price` * (`pd2`.`price` / 100))) WHEN `pd2`.`type` = 'S' THEN (`p`.`price` - `pd2`.`price`) ELSE `pd2`.`price` END) FROM `" . DB_PREFIX . "product_discount` `pd2` WHERE `pd2`.`product_id` = `p`.`product_id` AND `pd2`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND `pd2`.`quantity` = '1' AND `pd2`.`special` = '0' AND ((`pd2`.`date_start` = '0000-00-00' OR `pd2`.`date_start` < NOW()) AND (`pd2`.`date_end` = '0000-00-00' OR `pd2`.`date_end` > NOW())) ORDER BY `pd2`.`priority` ASC, `pd2`.`price` ASC LIMIT 1) AS `discount`";
			$this->product_statement['special'] = "(SELECT (CASE WHEN `ps`.`type` = 'P' THEN (`p`.`price` - (`p`.`price` * (`ps`.`price` / 100))) WHEN `ps`.`type` = 'S' THEN (`p`.`price` - `ps`.`price`) ELSE `ps`.`price` END) FROM `" . DB_PREFIX . "product_discount` `ps` WHERE `ps`.`product_id` = `p`.`product_id` AND `ps`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND `ps`.`quantity` = '1' AND `ps`.`special` = '1' AND ((`ps`.`date_start` = '0000-00-00' OR `ps`.`date_start` < NOW()) AND (`ps`.`date_end` = '0000-00-00' OR `ps`.`date_end` > NOW())) ORDER BY `ps`.`priority` ASC, `ps`.`price` ASC LIMIT 1) AS `special`";
		} else {
			$this->product_statement['discount'] = "(SELECT `pd2`.`price` FROM `" . DB_PREFIX . "product_discount` `pd2` WHERE `pd2`.`product_id` = `p`.`product_id` AND `pd2`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "'AND `pd2`.`quantity` = '1' AND ((`pd2`.`date_start` = '0000-00-00' OR `pd2`.`date_start` < NOW()) AND (`pd2`.`date_end` = '0000-00-00' OR `pd2`.`date_end` > NOW())) ORDER BY `pd2`.`priority` ASC, `pd2`.`price` ASC LIMIT 1) AS `discount`";
			$this->product_statement['special'] = "(SELECT `ps`.`price` FROM `" . DB_PREFIX . "product_special` `ps` WHERE `ps`.`product_id` = `p`.`product_id` AND `ps`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((`ps`.`date_start` = '0000-00-00' OR `ps`.`date_start` < NOW()) AND (`ps`.`date_end` = '0000-00-00' OR `ps`.`date_end` > NOW())) ORDER BY `ps`.`priority` ASC, `ps`.`price` ASC LIMIT 1) AS `special`";	
		}
		
		$this->product_statement['reward'] = "(SELECT `pr`.`points` FROM `" . DB_PREFIX . "product_reward` `pr` WHERE `pr`.`product_id` = `p`.`product_id` AND `pr`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "') AS `reward`";
		$this->product_statement['review'] = "(SELECT COUNT(*) FROM `" . DB_PREFIX . "review` `r` WHERE `r`.`product_id` = `p`.`product_id` AND `r`.`status` = '1' GROUP BY `r`.`product_id`) AS `reviews`";
		$this->product_statement['weight_class'] = "(SELECT `wcd`.`unit` FROM `" . DB_PREFIX . "weight_class_description` `wcd` WHERE `p`.`weight_class_id` = `wcd`.`weight_class_id` AND `wcd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "') AS `weight_class`";
		$this->product_statement['length_class'] = "(SELECT `lcd`.`unit` FROM `" . DB_PREFIX . "length_class_description` `lcd` WHERE `p`.`length_class_id` = `lcd`.`length_class_id` AND `lcd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "') AS length_class";
		$this->product_statement['tax_class'] = "(SELECT `tc`.`title` FROM `" . DB_PREFIX . "tax_class` `tc` WHERE `p`.`tax_class_id` = `tc`.`tax_class_id`) AS tax_class";
		
		if (version_compare((string)VERSION, '4.0.2.0', '>=')) {
			$this->product_statement['rating'] = "p.rating";
		} else {
			$this->product_statement['rating'] = "(SELECT AVG(rating) AS `total` FROM `" . DB_PREFIX . "review` `r2` WHERE `r2`.`product_id` = `p`.`product_id` AND `r2`.`status` = '1' GROUP BY `r2`.`product_id`) AS rating";
		}
			
		return $this->product_statement;
	}
	
	private function getProductPriceStatement(): array {		
		if (version_compare((string)VERSION, '4.1.0.0', '>=')) {
			$special = "(SELECT (CASE WHEN `ps`.`type` = 'P' THEN (`p`.`price` - (`p`.`price` * (`ps`.`price` / 100))) WHEN `ps`.`type` = 'S' THEN (`p`.`price` - `ps`.`price`) ELSE `ps`.`price` END) FROM `" . DB_PREFIX . "product_discount` `ps` WHERE `ps`.`product_id` = `p`.`product_id` AND `ps`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND `ps`.`quantity` = '1' AND `ps`.`special` = '1' AND ((`ps`.`date_start` = '0000-00-00' OR `ps`.`date_start` < NOW()) AND (`ps`.`date_end` = '0000-00-00' OR `ps`.`date_end` > NOW())) ORDER BY `ps`.`priority` ASC, `ps`.`price` ASC LIMIT 1)";
			$discount = "(SELECT (CASE WHEN `pd2`.`type` = 'P' THEN (`p`.`price` - (`p`.`price` * (`pd2`.`price` / 100))) WHEN `pd2`.`type` = 'S' THEN (`p`.`price` - `pd2`.`price`) ELSE `pd2`.`price` END) FROM `" . DB_PREFIX . "product_discount` `pd2` WHERE `pd2`.`product_id` = `p`.`product_id` AND `pd2`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND `pd2`.`quantity` = '1' AND `pd2`.`special` = '0' AND ((`pd2`.`date_start` = '0000-00-00' OR `pd2`.`date_start` < NOW()) AND (`pd2`.`date_end` = '0000-00-00' OR `pd2`.`date_end` > NOW())) ORDER BY `pd2`.`priority` ASC, `pd2`.`price` ASC LIMIT 1)";
		} else {
			$special = "(SELECT `ps`.`price` FROM `" . DB_PREFIX . "product_special` `ps` WHERE `ps`.`product_id` = `p`.`product_id` AND `ps`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((`ps`.`date_start` = '0000-00-00' OR `ps`.`date_start` < NOW()) AND (`ps`.`date_end` = '0000-00-00' OR `ps`.`date_end` > NOW())) ORDER BY `ps`.`priority` ASC, `ps`.`price` ASC LIMIT 1)";
			$discount = "(SELECT `pd2`.`price` FROM `" . DB_PREFIX . "product_discount` `pd2` WHERE `pd2`.`product_id` = `p`.`product_id` AND `pd2`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "'AND `pd2`.`quantity` = '1' AND ((`pd2`.`date_start` = '0000-00-00' OR `pd2`.`date_start` < NOW()) AND (`pd2`.`date_end` = '0000-00-00' OR `pd2`.`date_end` > NOW())) ORDER BY `pd2`.`priority` ASC, `pd2`.`price` ASC LIMIT 1)";
		}

		return "COALESCE(" . $special . ", " . $discount . ", p.price)";
	}

	private function getTaxCoefficients(): array {
		$coefficients = [];

		$query = $this->db->query("SELECT tax_class_id FROM " . DB_PREFIX . "tax_class");

		foreach ($query->rows as $row) {
			$tax_class_id = (int)$row['tax_class_id'];

			$c = (float)$this->tax->calculate(0, $tax_class_id, $this->config->get('config_tax'));
			$k = ((float)$this->tax->calculate(100, $tax_class_id, $this->config->get('config_tax')) - $c) / 100;

			if ($k != 1 || $c != 0) {
				$coefficients[$tax_class_id] = array('k' => $k, 'c' => $c);
			}
		}

		return $coefficients;
	}

	private function getProductPriceExpression(): float {
		if ($this->product_price_expression !== null) {
			return $this->product_price_expression;
		}

		$price = $this->getProductPriceStatement();

		$coefficients = $this->getTaxCoefficients();

		if ($coefficients) {
			$k_case = "CASE p.tax_class_id";
			$c_case = "CASE p.tax_class_id";

			foreach ($coefficients as $tax_class_id => $coefficient) {
				$k_case .= " WHEN '" . $tax_class_id . "' THEN " . sprintf('%.6F', $coefficient['k']);
				$c_case .= " WHEN '" . $tax_class_id . "' THEN " . sprintf('%.6F', $coefficient['c']);
			}

			$price = "(" . $price . " * (" . $k_case . " ELSE 1 END) + (" . $c_case . " ELSE 0 END))";
		}

		$currency_value = (float)$this->currency->getValue($this->session->data['currency']);

		if ($currency_value && $currency_value != 1) {
			$price = "(" . $price . " * " . sprintf('%.6F', $currency_value) . ")";
		}

		$this->product_price_expression = $price;

		return $price;
	}
}