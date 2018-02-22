<?php
namespace Plokko\LaravelFirebase\Traits;

use Illuminate\Database\Eloquent\{
    Model,Builder
};
use Plokko\LaravelFirebase\Relations\SyncWithFirebaseBelongsToMany;

/**
 * Trait SyncRelatedWithFirebase
 * @package Plokko\LaravelFirebase\Traits
 * @mixin Model
 */
trait SyncRelatedWithFirebase
{
    /*
    protected
        /**
         * Set what relations to sync with firebase
         * The related model MUST use SyncWithFirebase trait
         * @var array array of the relations to sync with firebase
         * /
        $syncRelationWithFirebase=[];
    */

    /**
     * Returns all the relation that needs to be synched with firebase
     * @return array
     */
    protected function getRelationsToSyncWithFirebase(){
        return [];
    }

    /**
     * Instantiate a new BelongsToMany relationship.
     *
     * @param  Builder  $query
     * @param  Model  $parent
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relationName
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    protected function newBelongsToMany(Builder $query, Model $parent, $table, $foreignPivotKey, $relatedPivotKey,
                                        $parentKey, $relatedKey, $relationName = null)
    {
        if(in_array($relationName,$this->getRelationsToSyncWithFirebase())){
            return new SyncWithFirebaseBelongsToMany( $query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
        }
        return parent::newBelongsToMany( $query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }
}