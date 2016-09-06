<?php

return array
(
	'display_name' => 'Payment',
	'description' => 'Handles payment routines',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'gateways' => array(
								'display_name'	=> 'Payment Gateway Control Panel',
								'script'		=> 'gateways.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'configure_gateway' => array(
								'display_name'	=> 'Payment Gateway Control Panel',
								'script'		=> 'configure_gateway.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'get_product_price' => array(
			'display_name'	=> 'Get product price',
			'script'		=> 'get_product_price.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output'	=> false,
		),
		'manage_invoices' =>  array(
			'display_name'	=> 'Manage Invoices',
			'script'		=> 'manage_invoices.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_invoice' =>  array(
			'display_name'	=> 'Edit Invoice',
			'script'		=> 'edit_invoice.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

//
//               USER SCRIPTS
//


		'payment_page' => array(
								'display_name'	=> 'Payment',
								'script'		=> 'payment_page.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'paypal_pro_fill_payment_card' => array(
								'display_name'	=> 'PayPal Payments Pro',
								'script'		=> 'paypal_pro_fill_payment_card.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
		),
		'callback' => array(
								'display_name'	=> 'Payment',
								'script'		=> 'callback_payment_page.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
        'notifications' => array(
								'display_name'	=> 'Payment notifications',
								'script'		=> 'notifications_payment_page.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'service_completed' => array(
								'display_name'	=> 'Payment complited',
								'script'		=> 'service_completed.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'products' => array(
								'display_name'	=> 'Products',
								'script'		=> 'products.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'add_product' => array(
								'display_name'	=> 'Add Product',
								'script'		=> 'add_product.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'edit_product' => array(
								'display_name'	=> 'Edit Product',
								'script'		=> 'edit_product.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'user_products' => array(
								'display_name'	=> 'Products',
								'script'		=> 'products.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								'params'		=> array ('action', 'userGroupID')
								),
		'shopping_cart' => array(
								'display_name'	=> 'Shopping Cart',
								'script'		=> 'shopping_cart.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'user_product' => array(
								'display_name'	=> 'User Product',
								'script'		=> 'user_product.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								'params'		=> array ('action')
								),
		'show_shopping_cart' =>  array(
								'display_name'	=> 'Show Shopping Cart',
								'script'		=> 'show_shopping_cart.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'manage_promotions' =>  array(
								'display_name'	=> 'Manage Discounts',
								'script'		=> 'manage_promotions.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								'params'		=> array ('action')
								),
		'promotions_log' =>  array(
								'display_name'	=> 'Discounts Log',
								'script'		=> 'promotions_log.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'my_invoices' =>  array(
			'display_name'	=> 'My Invoices',
			'script'		=> 'my_invoices.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'	=> false,
		),
	),
);
