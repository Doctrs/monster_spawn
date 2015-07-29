<?php
if (!defined('FLUX_ROOT')) exit;
error_reporting(0);


$title = 'Spawn Monsters';

if($params->get('act')){
    switch($params->get('act')){
        case 'truncate':
            try {
                $sth = $server->connection->getStatement('truncate table `mob_spawns`; truncate table `map_index`;');
                $sth->execute();
            } catch(Exception $e){}

            $successMessage = 'Database successfully clean';
            break;
        case 'create':
            $sth = $server->connection->getStatement('
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
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
');
            $sth->execute();
            $successMessage = 'Database successfully create';
            break;
        case 'delete':
            try {
                $sth = $server->connection->getStatement('drop table `mob_spawns`; drop table `map_index`;');
                $sth->execute();
            } catch(Exception $e){}
            $successMessage = 'Database successfully delete';
            break;
    }
}

if($files->get('map_index')) {
    $tmp = $files->get('map_index')->get('tmp_name');
    $maps = explode("\n", file_get_contents($tmp));
    $sql  = 'insert into map_index (`name`)values';
    $insert = array();
    $data = array();
    foreach($maps as $map){
        $map = trim($map);
        if(!$map){
            continue;
        }
        if(substr($map, 0, 2) == '//'){
            continue;
        }
        $insert[] = '(?)';
        $data[] = $map;
    }
    if(sizeof($insert)) {
        try {
            $sql .= join(',', $insert);
            $sth = $server->connection->getStatement($sql);
            $sth->execute($data);
            $successMessage = 'Maps successfully added to database. Total maps - ' . sizeof($data);
        } catch(Exception $e){
            $errorMessage = $e->getMessage();
        }
    }
}

if($files->get('mobs_zip')) {
    $dirExtract = FLUX_ROOT . DIRECTORY_SEPARATOR . 'mobs_spawn';
    $zip = new ZipArchive;
    if ($zip->open($files->get('mobs_zip')->get('tmp_name')) === true) {
        $zip->extractTo($dirExtract);
        $zip->close();
        $file = scanDirs($dirExtract);
        foreach($file as &$f){
            $f = str_replace('\\', '/', $f);
        }unset($f);
    } else {
        $file = array();
        $errorMessage = 'file must be ZIP ARCHIVE';
    }
    if(sizeof($file) == 0){
        $errorMessage = 'files in the archive not found';
    }
} else {
    $file = array();
}



try {
    $sth = $server->connection->getStatement('select count(*) as count from `mob_spawns`');
    $sth->execute();
    $MobSpawnBase = $sth->fetch()->count;
    if($MobSpawnBase === false || $MobSpawnBase === null){
        throw new Flux_Error('db not found');
    }
} catch(Exception $e){
    $MobSpawnBase = false;
}
try {
    $sth = $server->connection->getStatement('select count(*) as count from `map_index`');
    $sth->execute();
    $mapIndexBase = $sth->fetch()->count;
    if($mapIndexBase === false || $mapIndexBase === null){
        throw new Flux_Error('db not found');
    }
} catch(Exception $e){
    $mapIndexBase = false;
}





function scanDirs($path, $array = array()){
    $dir = array_diff(scandir($path), array('.', '..'));
    foreach($dir as $item){
        $innerDir = $path . DIRECTORY_SEPARATOR . $item;
        if(is_dir($innerDir)){
            $array = array_merge(scanDirs($innerDir, $array), $array);
        } else {
            $array[$innerDir] = $innerDir;
        }
    }
    return $array;
}
