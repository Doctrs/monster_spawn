<?php
if (!defined('FLUX_ROOT')) exit;
require __DIR__ . '/../../mapImage.php';
error_reporting(0);

$title = 'Map Database';

require __DIR__ . '/../../config.php';

$RENEVAL = ($MAIN_CONFIG['renewal'] == 'REN');
$mobdb = 'mob_db' . ($RENEVAL ? '_re' : '');
$mobdb2 = 'mob_db2' . ($RENEVAL ? '_re' : '');


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
        $sql = 'select ms.*, SUM(ms.count) as count, m.kName as mob_name, m2.kName as mob_name_2 ';
        $sql .= 'from `mob_spawns` ms left join `' . $mobdb . '` m on ms.mob_id = m.ID ';
        $sql .= 'left join `' . $mobdb2 . '` m2 on ms.mob_id = m2.ID ';
        $sql .= 'where ms.map = ? group by mob_id';
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