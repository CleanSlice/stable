<?php 
$_['stable_setting'] = [
	'extension' => [
		'version' => '1.0.0'
	],
	'debug_status' => false,
	'side' => [
		'frontend' => [
			'code' => 'frontend',
			'api_key' => '',
			'agent_id' => '',
			'status' => false,
			'chat_session_duration' => '1',
			'store_id' => [],
			'tool' => [
				'product' => [
					'code' => 'product',
					'status' => true
				],
				'customer' => [
					'code' => 'customer',
					'status' => true
				],
				'cart' => [
					'code' => 'cart',
					'status' => true
				],
				'checkout' => [
					'code' => 'checkout',
					'status' => false
				]
			]
		],
		'backend' => [
			'code' => 'backend',
			'api_key' => '',
			'agent_id' => '',
			'status' => false,
			'chat_session_duration' => '3',
			'tool' => [
				'product' => [
					'code' => 'product',
					'status' => true
				],
				'customer' => [
					'code' => 'customer',
					'status' => true
				],
				'order' => [
					'code' => 'order',
					'status' => true
				]
			]
		]
	],
	'chat_session_duration' => ['1', '2', '3', '4', '5', '6', '7'],
	'payment_method' => [
		'cod' => [
			'code' => 'cod',
			'flow' => 'confirm',
			'field' => []
		],
		'bank_transfer' => [
			'code' => 'bank_transfer',
			'flow' => 'confirm',
			'field' => []
		],
		'cheque' => [
			'code' => 'cheque',
			'flow' => 'confirm',
			'field' => []
		],
		'free_checkout' => [
			'code' => 'free_checkout',
			'flow' => 'confirm',
			'field' => []
		],
		// Card-taking methods. Field descriptions are written into createOrder's
		// inputSchema keyed by field code, so a field shared between methods keeps
		// only the LAST description declared here — they must stay identical and must
		// never name one method. Which method actually requires which field is
		// expressed by the generated allOf/if/then branches, not by the prose.
		'authorizenet_aim' => [
			'code' => 'authorizenet_aim',
			'flow' => 'send',
			'field' => [
				'cc_owner' => [
					'code' => 'cc_owner',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Owner',
					'description' => 'Card Owner, as printed on the card. Required only by the payment methods that list it in required_fields.'
				],
				'cc_number' => [
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				],
				'cc_expire_date_month' => [
					'code' => 'cc_expire_date_month',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Month',
					'description' => 'Card Expiry Month, 2 digits as a string ("01".."12"). Required only by the payment methods that list it in required_fields.'
				],
				'cc_expire_date_year' => [
					'code' => 'cc_expire_date_year',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Year',
					'description' => 'Card Expiry Year, 4 digits as a string ("2029"). Required only by the payment methods that list it in required_fields.'
				],
				'cc_cvv2' => [
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				]
			]
		],
		'sagepay_us' => [
			'code' => 'sagepay_us',
			'flow' => 'send',
			'field' => [
				'cc_owner' => [
					'code' => 'cc_owner',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Owner',
					'description' => 'Card Owner, as printed on the card. Required only by the payment methods that list it in required_fields.'
				],
				'cc_number' => [
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				],
				'cc_expire_date_month' => [
					'code' => 'cc_expire_date_month',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Month',
					'description' => 'Card Expiry Month, 2 digits as a string ("01".."12"). Required only by the payment methods that list it in required_fields.'
				],
				'cc_expire_date_year' => [
					'code' => 'cc_expire_date_year',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Year',
					'description' => 'Card Expiry Year, 4 digits as a string ("2029"). Required only by the payment methods that list it in required_fields.'
				],
				'cc_cvv2' => [
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				]
			]
		],
		// No cc_owner: web_payment_software's send() never reads it.
		'web_payment_software' => [
			'code' => 'web_payment_software',
			'flow' => 'send',
			'field' => [
				'cc_number' => [
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				],
				'cc_expire_date_month' => [
					'code' => 'cc_expire_date_month',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Month',
					'description' => 'Card Expiry Month, 2 digits as a string ("01".."12"). Required only by the payment methods that list it in required_fields.'
				],
				'cc_expire_date_year' => [
					'code' => 'cc_expire_date_year',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Year',
					'description' => 'Card Expiry Year, 4 digits as a string ("2029"). Required only by the payment methods that list it in required_fields.'
				],
				'cc_cvv2' => [
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				]
			]
		],
		// Start-date fields are optional: only some cards (legacy UK Maestro/Switch)
		// carry one, and the extension reads them without an isset() guard. Untested
		// against a live gateway — the first real order through it needs watching.
		'perpetual_payments' => [
			'code' => 'perpetual_payments',
			'flow' => 'send',
			'field' => [
				'cc_number' => [
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				],
				'cc_expire_date_month' => [
					'code' => 'cc_expire_date_month',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Month',
					'description' => 'Card Expiry Month, 2 digits as a string ("01".."12"). Required only by the payment methods that list it in required_fields.'
				],
				'cc_expire_date_year' => [
					'code' => 'cc_expire_date_year',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Expiry Date Year',
					'description' => 'Card Expiry Year, 4 digits as a string ("2029"). Required only by the payment methods that list it in required_fields.'
				],
				'cc_cvv2' => [
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				],
				'cc_start_date_month' => [
					'code' => 'cc_start_date_month',
					'type' => 'string',
					'required' => false,
					'name' => 'Card Start Date Month',
					'description' => 'Card Start Month, 2 digits as a string ("01".."12"). Only a few card types carry a start date — ask the customer, and omit the field when their card has none. Never invent one.'
				],
				'cc_start_date_year' => [
					'code' => 'cc_start_date_year',
					'type' => 'string',
					'required' => false,
					'name' => 'Card Start Date Year',
					'description' => 'Card Start Year, 4 digits as a string ("2024"). Only a few card types carry a start date — ask the customer, and omit the field when their card has none. Never invent one.'
				]
			]
		],
		// Uses cc_name rather than cc_owner, and its send() does not read the expiry
		// date at all. cc_choice picks a stored card and defaults to "new" when absent.
		// Untested against a live gateway.
		'firstdata_remote' => [
			'code' => 'firstdata_remote',
			'flow' => 'send',
			'field' => [
				'cc_name' => [
					'code' => 'cc_name',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Name',
					'description' => 'Name printed on the card, as a string. Required only by the payment methods that list it in required_fields.'
				],
				'cc_number' => [
					'code' => 'cc_number',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Number',
					'description' => 'Card Number, digits only, no spaces or dashes. Send as a string. Required only by the payment methods that list it in required_fields.'
				],
				'cc_cvv2' => [
					'code' => 'cc_cvv2',
					'type' => 'string',
					'required' => true,
					'name' => 'Card Security Code (CVV2)',
					'description' => 'Card Security Code (CVV2), 3-4 digits as a string so leading zeros survive. Required only by the payment methods that list it in required_fields.'
				],
				'cc_choice' => [
					'code' => 'cc_choice',
					'type' => 'string',
					'required' => false,
					'name' => 'Card Choice',
					'description' => 'Which stored card to charge. Omit it to pay with the card details supplied in this call — that is the default.'
				]
			]
		]
	]
];
?>