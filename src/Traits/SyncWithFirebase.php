<?php
namespace Plokko\LaravelFirebase\Traits;

use Plokko\LaravelFirebase\Collections\SyncsWithFirebaseCollection;
use Plokko\LaravelFirebase\ModelSynchronizer;

/**
 * Trait FirebaseSynchronizable
 * apply this trait to an eloquent Model to add Firebase real-time database synchronization
 * @mixin \Illuminate\Database\Eloquent\Model
 * @package Plokko\FirebaseSync\Traits
 */
trait SyncWithFirebase
{
    /*
     protected
         $firebaseReference
     */

    public static function bootSyncWithFirebase(){
        static::created(function ($model) {
            $sync = new ModelSynchronizer($model);
            $sync->withRelated()->create();
        });

        static::updated(function ($model) {
            $sync = new ModelSynchronizer($model);
            $sync->withRelated()->update();
        });

        static::deleted(function ($model) {
            $sync = new ModelSynchronizer($model);
            $sync->withRelated()->delete();
        });
        if(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(self::class))){
            static::restored(function ($model) {
                $sync = new ModelSynchronizer($model);
                $sync->withRelated()->create();
            });
        }
    }

    public function syncWithFirebase($withRelated=false){
        $sync = new ModelSynchronizer($this);
        $sync->withRelated($withRelated)->create();
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
    public function toFirebase()
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