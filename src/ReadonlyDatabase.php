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

    function set($path,$value){
        //Disabled
    }

    function update($path,$value){
        //Disabled
    }

    function delete($path){
        //Disabled
    }

    function __call($name, $arguments)
    {
        return call_user_func_array([$this->database,$name],$arguments);
    }
}