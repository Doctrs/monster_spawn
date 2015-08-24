<?php

return array(
	'MenuItems' => array(
		'DatabaseLabel' => array(
			'Maps Database' => array('module' => 'map'),
			'NPCs Database' => array('module' => 'npcs'),
			'Mob Database (new)' => array('module' => 'monster_new'),
			'Item Database (new)' => array('module' => 'item_new'),
		),
		'Misc. Stuff' => array(
			'Map Database Edit' => array('module' => 'admin_spawn'),
		)
	),
	'SubMenuItems'	=> array(
		'map' => array(
			'index' => 'Map List',
			'view' => 'View Map',
		),
		'npcs' => array(
			'index' => 'NPC List',
			'view' => 'View NPC',
		),
		'item_new' => array(
			'index' => 'List Items',
			'view' => 'View Items',
		),
	)
);