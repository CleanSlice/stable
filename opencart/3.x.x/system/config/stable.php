<?php 
$_['stable_setting'] = array(
	'extension' => array(
		'version' => '1.0.0'
	),
	'debug_status' => false,
	'side' => array(
		'frontend' => array(
			'code' => 'frontend',
			'status' => false,
			'api_key' => '',
			'agent_id' => '',
			'tool' => array(
				'product' => array(
					'code' => 'product',
					'status' => true
				),
				'customer' => array(
					'code' => 'customer',
					'status' => true
				),
				'cart' => array(
					'code' => 'cart',
					'status' => true
				),
				'checkout' => array(
					'code' => 'checkout',
					'status' => false
				)
			)
		),
		'backend' => array(
			'code' => 'backend',
			'status' => false,
			'api_key' => '',
			'agent_id' => '',
			'tool' => array(
				'product' => array(
					'code' => 'product',
					'status' => true
				),
				'customer' => array(
					'code' => 'customer',
					'status' => true
				),
				'order' => array(
					'code' => 'order',
					'status' => true
				)
			)
		)
	)
);
?>