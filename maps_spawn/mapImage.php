<?php
function mapImage($mapName, $small = false)
{
    $link = FLUX_DATA_DIR . '/maps/map' . ($small ? '_sm' : '') . '/' . $mapName . '.png';
    $path = FLUX_ROOT . '/' . $link;
    return file_exists($path) ? $link : false;
}