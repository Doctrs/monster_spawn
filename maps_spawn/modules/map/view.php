<?php
if (!defined('FLUX_ROOT')) exit;
require __DIR__ . '/../../mapImage.php';
error_reporting(0);

$title = 'Map Database';


if($params->get('image') && $params->get('mn')){
    list($type, $data) = explode(';', $params->get('image'));
    list(, $data)      = explode(',', $data);
    $data = base64_decode($data);
    $put = file_put_contents(FLUX_ROOT . '/data/maps/map/' . $params->get('mn') . '.png', $data);
    img_resize(FLUX_ROOT . '/data/maps/map/' . $params->get('mn') . '.png', FLUX_ROOT . '/data/maps/map_sm/' . $params->get('mn') . '.png', 100, 100);
    print_r(1111);
    die();
}
function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
    if (!file_exists($src)) return false;

    $size = getimagesize($src);

    if ($size === false) return false;

    // Определяем исходный формат по MIME-информации, предоставленной
    // функцией getimagesize, и выбираем соответствующую формату
    // imagecreatefrom-функцию.
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio       = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
    $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
    $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);

    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
        $new_width, $new_height, $size[0], $size[1]);

    imagejpeg($idest, $dest, $quality);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;

}

try {
    $sth = $server->connection->getStatement('select * from `map_index` where name = ?');
    $sth->execute(array($params->get('map')));
    if((int)$sth->stmt->errorCode()){
        throw new Flux_Error('db not found');
    }
    $map = $sth->fetchAll();
    $map = $map[0];
    $map->cell_data = gzuncompress($map->cell_data);
    $str = '';
    for($u = 0 ; $u < strlen($map->cell_data); $u ++) {
        $cells = (unpack('C', $map->cell_data[$u]));
        $str .= $cells[1];
    }
    $map->cell_data = $str;
} catch(Exception $e){
    $map = false;
}
if($map){
    try {
        $sql = 'select * from `mob_spawns` where map = ?';
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($map->name));
        if((int)$sth->stmt->errorCode()){
            throw new Flux_Error('db not found');
        }
        $mobs = $sth->fetchAll();
    } catch(Exception $e){
        $mobs = array();
    }
}

function conv($point, $size){
    if($point == 0){
        return 0;
    }
    return 512 / ($size / $point);
}