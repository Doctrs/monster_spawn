<?php

return array(
	'modules' => array(
		'admin_spawn' => array(
			'index' => AccountLevel::ADMIN,
			'get' => AccountLevel::ADMIN,
		),
		'map' => array(
			'index' => AccountLevel::ANYONE,
			'view' => AccountLevel::ANYONE,
		),
		'monster_new' => array(
			'index' => AccountLevel::ANYONE,
			'view' => AccountLevel::ANYONE
		),
	),
);