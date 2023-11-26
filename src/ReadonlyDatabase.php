<?php
namespace Plokko\LaravelFirebase;

use Plokko\Firebase\IO\Reference;

/**
 * Wrapper of Plokko\Firebase\IO\Database that disabes write operations
 * @package Plokko\LaravelFirebase
 */
class ReadonlyDatabase extends RealtimeDb
{

    // Disable write functions
    function set($path, $value){}
    function update($path, $value){}
    function delete($path){}

    function getReference($path)
    {
        return new Reference($this,$path);
    }
}
