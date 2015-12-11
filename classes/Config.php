<?php

class Config{
    public static function get($path){
        $config = $GLOBALS['config'];
        $path = explode('/', $path);
        foreach($path as $v){
            if(isset($config[$v])){
                $config = $config[$v];
            }
        }
        return $config;
    }
}