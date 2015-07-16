<?php
//...
return array(
    'web_dir' => '/admin',
    'core_debug' => true,
    'core_debug_info' => false,
	'db_config' => array(
		'client' => 'mysqli',
		'server' => 'localhost',
		'username' => 'root',
		'password' => '...',
		'port' => 3306,
		'database' => 'web',
	),
	'url_router' => true,
    'router_rules' => include_once APP_DIR.'/config/router.php',
	
);