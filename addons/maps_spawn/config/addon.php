<?php
return array(
	'MenuItems' => array(
		'DatabaseLabel' => array(
			'Maps Database' => array('module' => 'map'),
			'NPCs Database' => array('module' => 'npcs'),
			'Mob Database (new)' => array('module' => 'monster_new'),
		),
		'Misc. Stuff' => array(
			'Map Database Edit' => array('module' => 'admin_spawn'),
		)
	),
	'SubMenuItems'	=> array(
		'map' => array(
			'index' => 'Map List'
		),
		'npcs' => array(
			'index' => 'NPC List'
		)
	)
);