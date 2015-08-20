<?php
if (!defined('FLUX_ROOT')) exit;


class parse{

    private $pref = null;
    private $server = null;

    function __construct($server){
        $this->pref = $server->isRenewal ? 're' : 'pre-re';
        $this->server = $server;
    }

    function getFiles($path = false){
        $files = array();
        if(!$path) {
            $path = FLUX_ROOT . '/upload_npc/npc/' . $this->pref . '/scripts_main.conf';
            if(!file_exists($path)){
                throw new Flux_Error('file scripts_main.conf not found');
            }
        }
        if(!file_exists($path)){
            return array();
        }
        $data = file_get_contents($path);
        preg_match_all('/(.*)(npc|import): (.*)/', $data, $match);
        foreach($match[3] as $key => $item){
            if(trim($match[1][$key]) == '//'){
                continue;
            }
            switch(trim($match[2][$key])){
                case 'npc':
                    $files[] = FLUX_ROOT . '/upload_npc/' . $item;
                    break;
                case 'import':
                    $files = array_merge($files, $this->getFiles(FLUX_ROOT . '/upload_npc/' . $item));
                    break;
            }
        }
        return $files;
    }

    function loadFiles(array $files){
        $array = array(
            'mobs' => 0,
            'npsc' => 0,
            'warps' => 0,
            'shops' => 0,
        );
        foreach($files as $file){
            $npcs = $this->getNpc($file);
            if(is_array($npcs) && sizeof($npcs)){
                $array['npsc'] += $this->loadNpc($npcs);
            }
            $warps = $this->getWarps($file);
            if(is_array($warps) && sizeof($warps)){
                $array['warps'] += $this->loadWarps($warps);
            }
            $monsters = $this->getMonsters($file);
            if(is_array($monsters) && sizeof($monsters)){
                $array['mobs'] += $this->loadMonsters($monsters);
            }
            $shops = $this->getShops($file);
            if(is_array($shops) && sizeof($shops)){
                $array['shops'] += $this->loadShops($shops);
            }
        }
        return $array;
    }

    private function loadShops(array $data){
        $sql = 'insert into shops (`map`, `x`, `y`, `name`, `sprite`)values';
        $array = array();
        $insert = array();
        foreach($data as $item){
            $import = explode(',', $item);
            if(sizeof($import) != 5){
                continue;
            }
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?)';
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
        return sizeof($insert);
    }

    private function getShops($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\t(shop|duplicate\(([^\)]+)\))\t(.*?)\t([0-9]+),?([0-9]+,)?([0-9]+,)?(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            preg_match("/\tduplicate\(([^\)]+)\)\t/", $item, $match);
            $duplicate = $match[1];
            if($duplicate && !preg_match("/\tshop\t" . $duplicate . "\t/", $text)){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/,([0-9]+)\t(shop|duplicate\(([^\)]+)\))\t(.*?)\t([0-9]+),?([0-9]+,)?([0-9]+,)?(.*)/", ',$4,$5', $item);
            $item = explode(',', $item);
            $item[3] = explode('#', $item[3]);
            $item[3] = $item[3][0] ? $item[3][0] : 'No Name';
            $item = join(',', $item);
            $item = explode('::', $item);
            $item = $item[0];
        }unset($item);
        return $data;
    }

    private function getNpc($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\t(script|duplicate\(([^\)]+)\))\t(.*?)\t([0-9]+),?([0-9]+,)?([0-9]+,)?(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            preg_match("/\tduplicate\(([^\)]+)\)\t/", $item, $match);
            $duplicate = $match[1];
            if($duplicate && !preg_match("/\tscript\t" . $duplicate . "\t/", $text)){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/,([0-9]+)\t(script|duplicate\(([^\)]+)\))\t(.*?)\t([0-9]+),?([0-9]+,)?([0-9]+,)?(.*)/", ',$4,$5', $item);
            $item = explode(',', $item);
            $item[3] = explode('#', $item[3]);
            $item[3] = $item[3][0] ? $item[3][0] : 'No Name';
            $item = join(',', $item);
            $item = explode('::', $item);
            $item = $item[0];
        }unset($item);
        return $data;
    }

    private function loadNpc(array $data){
        $sql = 'insert into npsc (`map`, `x`, `y`, `name`, `sprite`)values';
        $array = array();
        $insert = array();
        foreach($data as $item){
            $import = explode(',', $item);
            if(sizeof($import) != 5){
                continue;
            }
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?)';
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
        return sizeof($insert);
    }

    private function getWarps($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\twarp\t(.*?)\t([0-9]+),([0-9]+),(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/,([0-9]+)\twarp\t(.*?)\t([0-9]+),([0-9]+),/", ',', $item);
        }unset($item);
        return $data;
    }

    private function loadWarps(array $data){
        $sql = 'insert into warps (`map`, `x`, `y`, `to`, `tx`, `ty`)values';
        $array = array();
        $insert = array();
        foreach($data as $item){
            $import = explode(',', $item);
            if(sizeof($import) != 6){
                continue;
            }
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?, ?)';
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
        return sizeof($insert);
    }

    private function getMonsters($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*)\t(boss_)?monster\t(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/\t(boss_)?monster\t(.*?)\t/", ',$2,', $item);
        }unset($item);
        return $data;
    }

    private function loadMonsters(array $data){
        $sql = 'insert into mob_spawns (`map`, `x`, `y`, `range_x`, `range_y`, `name`, `mob_id`, `count`, `time_to`, `time_from`)values';
        $array = array();
        $insert = array();
        foreach($data as $item){
            $import = explode(',', $item);
            if(sizeof($import) > 10){
                $import = array_slice($import, 0, 10);
            }
            if(sizeof($import) < 9){
                for($i = sizeof($import) ; $i < 10 ; $i ++){
                    $import[$i] = 0;
                }
            }
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
        return sizeof($insert);
    }




}