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

    /**
     * Specifies the relations that needs to be automatically synched with firebase
     * @return array relations to be synched with firebase, default []
     */
    protected function getRelationsToSyncWithFirebase(){
        return [];
    }

    /**
     * Manually syncs all related models with Firebase
     * Note: only relations returned by getRelationsToSyncWithFirebase() will be synchronized
     * @param string $only only sync specified relation (if in getRelationsToSyncWithFirebase() array)
     */
    final public function syncRelatedWithFirebase($only=null){
        $related = $this->getRelationsToSyncWithFirebase();
        if($only){
            if(!in_array($only,$related))
                return;//Not synched
            $related = [$only];
        }
        foreach($related AS $k){
            $this->$k->syncWithFirebase();
        }
    }

    /**
     * Overrides the BelongsToMany default relationship.
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