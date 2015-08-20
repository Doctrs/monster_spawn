<?php
if (!defined('FLUX_ROOT')) exit;
require __DIR__ . '/../../mapImage.php';
error_reporting(0);

$title = 'Map Database';

try {
    $sth = $server->connection->getStatement('select * from `map_index` where name = ?');
    $sth->execute(array($params->get('map')));
    if((int)$sth->stmt->errorCode()){
        throw new Flux_Error('db not found');
    }
    $map = $sth->fetchAll();
    $map = $map[0];
} catch(Exception $e){
    $map = false;
}
if($map){
    $tables = array(
        'mob_spawns' => 'mobs',
        'warps' => 'warps',
        'npsc' => 'npsc',
        'shops' => 'shops'
    );
    foreach($tables as $table => $var) {
        try {
            $sql = 'select * from `' . $table . '` where map = ?';
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($map->name));
            if ((int)$sth->stmt->errorCode()) {
                throw new Flux_Error('db not found');
            }
            $$var = $sth->fetchAll();
        } catch (Exception $e) {
            $$var = array();
        }
    }
}

function conv($point, $size, $map = false){
    if($map) {
        $max = max($map->x, $map->y);
        if($size != $max){
            $point += ($max - $size) / 2;
        }
    } else {
        $max = $size;
    }
    return 512 / ($max / $point);
}