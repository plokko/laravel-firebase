<?php
namespace Plokko\LaravelFirebase;

use Plokko\Firebase\IO\Database;

/**
 * Wrapper of Plokko\Firebase\IO\Database that disabes write operations
 * @package Plokko\LaravelFirebase
 */
class ReadonlyDatabase
{
    private
        /**@var \Plokko\Firebase\IO\Database **/
        $database;
    function __construct(Database $db)
    {
        $this->database = $db;
    }

    function __call($name, $arguments)
    {
        switch($name){
            case 'set':
            case 'update':
            case 'delete':
                return false;
            default:
                return call_user_func_array([$this->database,$name],$arguments);
        }
    }
}