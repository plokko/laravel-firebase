<?php
namespace Plokko\LaravelFirebase;

use App;
use Plokko\Firebase\IO\Database;

/**
 * Extends base class to allow database selction
 */
class RealtimeDb extends Database
{
    /**
     * @param string $dbName Database name to use
     */
    public function use ($dbName): Database
    {
        return App::makeWith(Database::class, ['db' => $dbName]);
    }
}
