<?php

/**
 * database baseclass
 *
 */

namespace EatWhat\Storage;

class StorageClient 
{

    /**
     * storage config 
     *
     */
    private static $config = [];

        /**
     * private instance 
     * 
     */
    private static $instance = [
        "mysql" => null,
        "mongoDB" => null,
        "redis" => null,
    ];

    /**
     * 
     * forbidden constructor
     */
    private function __construct()
    {
        throw new EatWhatException("This Class Can Not Be Constructed!");
    }

    /**
     * forbidden __clone
     * 
     */
    private function __clone()
    {
        throw new EatWhatException("This Class Can Not Be Cloned!");
    }

    /**
     * forbidden __clone
     * 
     */
    private function __sleep(){
        throw new EatWhatException("This Class Can Not Be Serialized!");
    }

    /**
     * generate single obj
     * 
     */
    public static function generate($storageType)
    {
        if( !self::$instance[strtolower($storageType)] ) {
            $storageHandlerName = "EatWhat\\Storage\\".ucfirst($storageType)."StorageClient";
            self::$instance[strtolower($storageType)] = $storageHandlerName::getClient();
        } 

        return self::$instance;
    }

    /**
     * static classname
     * 
     */
    public static function className($withoutNamespace = false)
    {
        $classname = get_called_class();
        $withoutNamespace && ($classname = substr($classname, (strrpos($classname, "\\") + 1)));
        return $classname;
    }
}