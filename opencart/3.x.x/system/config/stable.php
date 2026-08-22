<?php 
$_['stable_setting'] = array(
	'extension' => array(
		'version' => '1.0.0'
	),
	'debug_status' => false,
	'side' => array(
		'frontend' => array(
			'code' => 'frontend',
			'api_key' => '',
			'agent_id' => '',
			'status' => false,
			'chat_session_duration' => '1',
			'store_id' => array(),
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
			'api_key' => '',
			'agent_id' => '',
			'status' => false,
			'chat_session_duration' => '3',
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
	),
	'chat_session_duration' => array('1', '2', '3', '4', '5', '6', '7')
);
?>