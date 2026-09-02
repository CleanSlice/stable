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
	'chat_session_duration' => array('1', '2', '3', '4', '5', '6', '7'),
	'payment_method' => array(
		'cod' => array(
			'code' => 'cod',
			'flow' => 'confirm',
			'field' => array()
		),
		'bank_transfer' => array(
			'code' => 'bank_transfer',
			'flow' => 'confirm',
			'field' => array()
		),
		'cheque' => array(
			'code' => 'cheque',
			'flow' => 'confirm',
			'field' => array()
		),
		'free_checkout' => array(
			'code' => 'free_checkout',
			'flow' => 'confirm',
			'field' => array()
		),
		// Card-taking methods. Field descriptions are written into createOrder's
		// inputSchema keyed by field code, so a field shared between methods keeps
		// only the LAST description declared here — they must stay identical and must
		// never name one method. Which method actually requires which field is
		// expressed by the generated allOf/if/then branches, not by the prose.
		'authorizenet_aim' => array(
			'code' => 'authorizenet_aim',
			'flow' => 'send',
			'field' => array(
				'cc_owner' => array(
					'code' => 'cc_owner',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Owner',
					'description' => 'Card Owner, as printed on the card. Required only by the payment methods that list it in required_fields.'
				),
				'cc_number' => array(
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				),
				'cc_expire_date_month' => array(
					'code' => 'cc_expire_date_month',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Month',
					'description' => 'Card Expiry Month, 2 digits as a string ("01".."12"). Required only by the payment methods that list it in required_fields.'
				),
				'cc_expire_date_year' => array(
					'code' => 'cc_expire_date_year',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Year',
					'description' => 'Card Expiry Year, 4 digits as a string ("2029"). Required only by the payment methods that list it in required_fields.'
				),
				'cc_cvv2' => array(
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				)
			)
		),
		'sagepay_us' => array(
			'code' => 'sagepay_us',
			'flow' => 'send',
			'field' => array(
				'cc_owner' => array(
					'code' => 'cc_owner',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Owner',
					'description' => 'Card Owner, as printed on the card. Required only by the payment methods that list it in required_fields.'
				),
				'cc_number' => array(
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				),
				'cc_expire_date_month' => array(
					'code' => 'cc_expire_date_month',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Month',
					'description' => 'Card Expiry Month, 2 digits as a string ("01".."12"). Required only by the payment methods that list it in required_fields.'
				),
				'cc_expire_date_year' => array(
					'code' => 'cc_expire_date_year',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Year',
					'description' => 'Card Expiry Year, 4 digits as a string ("2029"). Required only by the payment methods that list it in required_fields.'
				),
				'cc_cvv2' => array(
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				)
			)
		),
		// No cc_owner: web_payment_software's send() never reads it.
		'web_payment_software' => array(
			'code' => 'web_payment_software',
			'flow' => 'send',
			'field' => array(
				'cc_number' => array(
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				),
				'cc_expire_date_month' => array(
					'code' => 'cc_expire_date_month',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Month',
					'description' => 'Card Expiry Month, 2 digits as a string ("01".."12"). Required only by the payment methods that list it in required_fields.'
				),
				'cc_expire_date_year' => array(
					'code' => 'cc_expire_date_year',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Year',
					'description' => 'Card Expiry Year, 4 digits as a string ("2029"). Required only by the payment methods that list it in required_fields.'
				),
				'cc_cvv2' => array(
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				)
			)
		),
		// Start-date fields are optional: only some cards (legacy UK Maestro/Switch)
		// carry one, and the extension reads them without an isset() guard. Untested
		// against a live gateway — the first real order through it needs watching.
		'perpetual_payments' => array(
			'code' => 'perpetual_payments',
			'flow' => 'send',
			'field' => array(
				'cc_number' => array(
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				),
				'cc_expire_date_month' => array(
					'code' => 'cc_expire_date_month',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Month',
					'description' => 'Card Expiry Month, 2 digits as a string ("01".."12"). Required only by the payment methods that list it in required_fields.'
				),
				'cc_expire_date_year' => array(
					'code' => 'cc_expire_date_year',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Year',
					'description' => 'Card Expiry Year, 4 digits as a string ("2029"). Required only by the payment methods that list it in required_fields.'
				),
				'cc_cvv2' => array(
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				),
				'cc_start_date_month' => array(
					'code' => 'cc_start_date_month',
					'type' => 'string',
					'required' => false,
					'name' => 'Card Start Date Month',
					'description' => 'Card Start Month, 2 digits as a string ("01".."12"). Only a few card types carry a start date — ask the customer, and omit the field when their card has none. Never invent one.'
				),
				'cc_start_date_year' => array(
					'code' => 'cc_start_date_year',
					'type' => 'string',
					'required' => false,
					'name' => 'Card Start Date Year',
					'description' => 'Card Start Year, 4 digits as a string ("2024"). Only a few card types carry a start date — ask the customer, and omit the field when their card has none. Never invent one.'
				)
			)
		),
		// Uses cc_name rather than cc_owner, and its send() does not read the expiry
		// date at all. cc_choice picks a stored card and defaults to "new" when absent.
		// Untested against a live gateway.
		'firstdata_remote' => array(
			'code' => 'firstdata_remote',
			'flow' => 'send',
			'field' => array(
				'cc_name' => array(
					'code' => 'cc_name',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Name',
					'description' => 'Name printed on the card, as a string. Required only by the payment methods that list it in required_fields.'
				),
				'cc_number' => array(
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				),
				'cc_cvv2' => array(
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				),
				'cc_choice' => array(
					'code' => 'cc_choice',
					'type' => 'string',
					'required' => false,
					'name' => 'Card Choice',
					'description' => 'Which stored card to charge. Omit it to pay with the card details supplied in this call — that is the default.'
				)
			)
		),
	)
);
?>