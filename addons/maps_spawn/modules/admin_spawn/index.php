<?php
if (!defined('FLUX_ROOT')) exit;
require __DIR__ . '/parse.php';
error_reporting(0);

$title = 'Spawn Monsters';

if($params->get('act')){
    switch($params->get('act')){
        case 'truncate':
            try {
                $sth = $server->connection->getStatement('
                truncate table `mob_spawns`;
                truncate table `map_index`;
                truncate table `warps`;
                truncate table `npcs`;
                truncate table `shops_sells`;
                ');
                $sth->execute();
            } catch(Exception $e){}

            $successMessage = 'Database successfully clean';
            break;
        case 'create':
            $sth = $server->connection->getStatement('
CREATE TABLE IF NOT EXISTS `warps` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `map` varchar(20) NOT NULL,
  `x` smallint(4) NOT NULL,
  `y` smallint(4) NOT NULL,
  `to` varchar(20) NOT NULL,
  `tx` smallint(4) NOT NULL,
  `ty` smallint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shops_sells` (
  `id_shop` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `shops_sells`
 ADD KEY `id_shop` (`id_shop`);

CREATE TABLE IF NOT EXISTS `npcs` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `map` varchar(20) NOT NULL,
  `x` smallint(4) NOT NULL,
  `y` smallint(4) NOT NULL,
  `name` varchar(30) NOT NULL,
  `sprite` smallint(4) NOT NULL,
  `is_shop` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `mob_spawns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map` varchar(20) NOT NULL,
  `x` smallint(4) NOT NULL,
  `y` smallint(4) NOT NULL,
  `range_x` smallint(4) NOT NULL,
  `range_y` smallint(4) NOT NULL,
  `mob_id` smallint(5) NOT NULL,
  `count` smallint(4) NOT NULL,
  `name` varchar(40) NOT NULL,
  `time_to` int(11) NOT NULL,
  `time_from` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map` (`map`),
  KEY `mob_id` (`mob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `map_index` (
  `name` varchar(20) NOT NULL,
  `x` smallint(4) NOT NULL,
  `y` smallint(4) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
');
            $sth->execute();
            $successMessage = 'Database successfully create';
            break;
        case 'delete':
            try {
                $sth = $server->connection->getStatement('
                drop table if exists `mob_spawns`;
                drop table if exists `map_index`;
                drop table if exists `warps`;
                drop table if exists `npcs`;
                drop table if exists `shops_sells`;
                ');
                $sth->execute();
            } catch(Exception $e){}
            $successMessage = 'Database successfully delete';
            break;
    }
}

if($files->get('map_index')) {
    $tmp = $files->get('map_index')->get('tmp_name');
    $array_insert = array();

    $data = file_get_contents($tmp);
    $array = array(
        array('A12', 12),
        array('S', 2),
        array('S', 2),
        array('L', 4),
    );

    $count = 0;
    $i = 8;
    while($i < strlen($data)){
        $byte = '';
        for($k = $i ; $k < $i + $array[$count][1] ; $k++){
            $byte .= $data[$k];
        }
        $datas = unpack($array[$count][0], $byte);
        if($count != 3) {
            $array_insert[] = trim($datas[1]);
        }
        $i += $array[$count][1];
        $count ++;
        if(!isset($array[$count])) {
            $count = 0;
            $i += $datas[1];
        }
    }

    if(sizeof($array_insert) % 3 == 0) {
        $rows = sizeof($array_insert) / 3;
        $sql = 'insert into map_index (`name`, `x`, `y`)values';
        $insert = array();
        for ($i = 0; $i < $rows; $i++) {
            $insert[] = '(?, ?, ?)';
        }

        try {
            $sql .= join(',', $insert);
            $sth = $server->connection->getStatement($sql);
            $sth->execute($array_insert);
            $successMessage = 'Maps successfully added to database. Total maps - ' . ($rows);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    } else {
        $errorMessage = 'File map_cache.dat not validate';
    }
}

if($files->get('npc_zip')) {
    if($files->get('npc_zip')->get('error')){
        $errorMessage = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
    } else {
        $dirExtract = FLUX_ROOT . '/upload_npc';
        $zip = new ZipArchive;
        if ($zip->open($files->get('npc_zip')->get('tmp_name')) === true) {
            $zip->extractTo($dirExtract);
            $zip->close();
            $parse = new parse($server);
            $file = $parse->getFiles();
        } else {
            $errorMessage = 'file must be ZIP ARCHIVE';
        }
        if (sizeof($file) == 0) {
            $errorMessage = 'files in the archive not found';
        }
    }
}

$tables = array(
    'mob_spawns' => 'MobSpawnBase',
    'map_index' => 'mapIndexBase',
    'warps' => 'warpsBase'
);

try {
    $sth = $server->connection->getStatement('select count(*) as count from `npcs` where is_shop = 0');
    $sth->execute();
    $npcsBase = $sth->fetch()->count;
    if ($npcsBase === false || $npcsBase === null) {
        throw new Flux_Error('db not found');
    }
} catch (Exception $e) {
    $npcsBase = false;
}
try {
    $sth = $server->connection->getStatement('select count(*) as count from `npcs` where is_shop = 1');
    $sth->execute();
    $shopsBase = $sth->fetch()->count;
    if ($shopsBase === false || $shopsBase === null) {
        throw new Flux_Error('db not found');
    }
} catch (Exception $e) {
    $shopsBase = false;
}

foreach($tables as $table => $var) {
    try {
        $sth = $server->connection->getStatement('select count(*) as count from `' . $table . '`');
        $sth->execute();
        $$var = $sth->fetch()->count;
        if ($$var === false || $$var === null) {
            throw new Flux_Error('db not found');
        }
    } catch (Exception $e) {
        $$var = false;
    }
}