<?php
if (!defined('FLUX_ROOT')) exit;
require __DIR__ . '/../../mapImage.php';
error_reporting(0);

$title = 'Map Database';

try {
    $sth = $server->connection->getStatement('select name from `map_index` where name = ?');
    $sth->execute(array($params->get('map')));
    if((int)$sth->stmt->errorCode()){
        throw new Flux_Error('db not found');
    }
    $map = $sth->fetch();
} catch(Exception $e){
    $map = false;
}
if($map){
    $map = $map->name;
    try {
        $sql = 'select *, SUM(count) as count from `mob_spawns` where map = ? group by mob_id';
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($params->get('map')));
        if((int)$sth->stmt->errorCode()){
            throw new Flux_Error('db not found');
        }
        $mobs = $sth->fetchAll();
    } catch(Exception $e){
        $mobs = array();
    }
}