<?php


class Core_Console{
    public static function show($message){
        if(Henry::isCommanLineMode()){
            echo $message."\n";
        }
    }
    public static function showInLine($message){
        if(Henry::isCommanLineMode()){
            echo $message;
        }
    }
}