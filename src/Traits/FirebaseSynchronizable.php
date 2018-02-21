<?php
namespace Plokko\LaravelFirebase\Traits;

use Plokko\LaravelFirebase\Collections\SyncsWithFirebaseCollection;

/**
 * Trait FirebaseSynchronizable
 * apply this trait to an eloquent Model to add Firebase real-time database synchronization
 * @mixin \Illuminate\Database\Eloquent\Model
 * @package Plokko\FirebaseSync\Traits
 * @property string|null $firebaseReference optional name to use in Firebase, if not specified table name will be used
 */
trait FirebaseSynchronizable
{
    //private $firebaseReference

    public static function bootSyncWithFirebase(){

        static::created(function ($model) {
            $model->syncToFirebase('set');
        });
        static::updated(function ($model) {
            $model->syncToFirebase('update');
        });
        static::deleted(function ($model) {
            $model->syncToFirebase('delete');
        });
        if(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(self::class))){
            static::restored(function ($model) {
                $model->syncToFirebase('set');
            });
        }
    }

    public function syncWithFirebase(){
        $this->syncToFirebase('update');
    }

    /**
     * Automatically casts Collection to SyncsWithFirebaseCollection
     * to allow bulk syncWithFirebase
     * @param array $models
     * @return SyncsWithFirebaseCollection
     */
    public function newCollection(array $models = [])
    {
        return new SyncsWithFirebaseCollection($models);
    }

    protected function getFirebaseReference(){

    }

    /**
     * @param $mode
     */
    protected function syncToFirebase($mode)
    {
        /*

        $path = (!empty($this->firebaseReference)?$this->_firebaseReference:$this->getTable()) . '/' . $this->getKey();

        if ($mode === 'set') {
            $this->firebaseClient->set($path, $this->getFirebaseSyncData());
        } elseif ($mode === 'update') {
            $this->firebaseClient->update($path, $this->getFirebaseSyncData());
        } elseif ($mode === 'delete') {
            $this->firebaseClient->delete($path);
        }
        */
    }
}