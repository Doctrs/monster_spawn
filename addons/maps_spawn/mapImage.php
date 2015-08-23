<?php
function mapImage($mapName, $small = false)
{
    $link = FLUX_DATA_DIR . '/maps/map' . ($small ? '_sm' : '') . '/' . $mapName . '.png';
    $path = FLUX_ROOT . '/' . $link;
    return file_exists($path) ? $link : false;
}
function npcImage($id)
{
    $link = FLUX_DATA_DIR . '/NPCs/' . $id . '.gif';
    $path = FLUX_ROOT . '/' . $link;
    return file_exists($path) ? $link : false;
}
function conv($point, $size, $map = false, $map_image = 512){
    if($map) {
        $max = max($map->x, $map->y);
        if($size != $max){
            $point += ($max - $size) / 2;
        }
    } else {
        $max = $size;
    }
    return $map_image / ($max / $point);
}