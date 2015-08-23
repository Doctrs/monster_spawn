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
		'npcs' => array(
			'index' => AccountLevel::ANYONE,
			'view' => AccountLevel::ANYONE,
		),
		'monster_new' => array(
			'index' => AccountLevel::ANYONE,
			'view' => AccountLevel::ANYONE
		),
		'item_new' => array(
			'index' => AccountLevel::ANYONE,
			'view' => AccountLevel::ANYONE,
			'add' => AccountLevel::ADMIN,
			'edit' => AccountLevel::ADMIN,
			'copy' => AccountLevel::ADMIN
		),
	),
);