<?php
namespace Plokko\LaravelFirebase\Traits;

use Plokko\Firebase\IO\Reference;
use Plokko\LaravelFirebase\Collections\SyncsWithFirebaseCollection;
use Plokko\LaravelFirebase\Facades\FirebaseDb;
use Plokko\LaravelFirebase\ModelSynchronizer;

/**
 * Trait FirebaseSynchronizable
 * apply this trait to an eloquent Model to add Firebase real-time database synchronization
 * @mixin \Illuminate\Database\Eloquent\Model
 * @package Plokko\FirebaseSync\Traits
 */
trait FirebaseSynchronizable
{
    /*
     protected
         $firebaseReference
     */

    public static function bootFirebaseSynchronizable(){
        static::created(function ($model) {
            ModelSynchronizer::create($model);
        });

        static::updated(function ($model) {
            ModelSynchronizer::update($model);
        });

        static::deleted(function ($model) {
            /**@var $model $this**/
            ModelSynchronizer::delete($model);
        });
        if(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(self::class))){
            static::restored(function ($model) {
                ModelSynchronizer::create($model);
            });
        }
    }

    public function syncWithFirebase(){
        ModelSynchronizer::create($this);
    }

    /**
     * Automatically casts Collection to SyncsWithFirebaseCollection
     * to allow bulk syncWithFirebase
     * @param array $models
     * @return SyncsWithFirebaseCollection
     * @internal
     */
    public function newCollection(array $models = [])
    {
        return new SyncsWithFirebaseCollection($models);
    }

    /**
     * Data to be synchronized with Firebase
     * @return array
     */
    public function getFirebaseSyncData()
    {
        if ($fresh = $this->fresh()) {
            return $fresh->toArray();
        }
        return [];
    }

    /**
     * @return string reference name
     */
    public function getFirebaseReferenceName(){
        return $this->getTable();
    }


}