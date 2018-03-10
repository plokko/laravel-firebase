<?php
namespace Plokko\LaravelFirebase;

use Illuminate\Database\Eloquent\Model;
use Plokko\Firebase\IO\Reference;
use Plokko\LaravelFirebase\Facades\FirebaseDb;
use Plokko\LaravelFirebase\Traits\SyncRelatedWithFirebase;

class ModelSynchronizer
{

    private
        /**@var Model */
        $model,
        /** @var bool */
        $syncRelated = false;

    function __construct(Model $model){
        $this->model        = $model;
    }

    /**
     * Enables/disables synching with related models (if supported)
     * @param bool $withRelated
     * @return $this
     */
    public function withRelated($withRelated=true){
        $this->syncRelated  = $withRelated && in_array(SyncRelatedWithFirebase::class,class_uses($this->model));
        return $this;
    }

    private function getData($onlyChanged=false){
        $data = $this->model->toFirebase();
        if($onlyChanged){
            $this->model->getDirty();
        }
        return $data;
    }

    /**
     * @param mixed|null id primary key of the model
     * @return Reference
     */
    private function getReference($id=null){
        return FirebaseDb::getReference($this->model->getFirebaseReferenceName().'/'.($id?:$this->model->getKey()));
    }

    /**
     * @return Reference
     */
    private function getBaseReference(){
        return FirebaseDb::getReference($this->model->getFirebaseReferenceName());
    }

    /**
     *
     */
    public function create(){
        $this->getReference()->set($this->getData());
        if($this->syncRelated){
            $this->model->syncRelatedWithFirebase();
        }
    }


    public function delete(){
        $this->getReference()->delete();
        if($this->syncRelated){
            $this->model->syncRelatedWithFirebase();
        }
    }

    /**
     * @param Model $model model to be deleted
    static function deletes(Model $model,array $ids){
        $ref = self::getBaseReference($model);
        foreach($ids AS $id){
            $ref->delete($id);
        }
    }
     */

    /**
     * @param array|null $updatedFields if present only the keys in array will be updated
     */
    public function update(array $updatedFields=null){
        $data = $this->getData();
        if($updatedFields){
            $data = array_intersect_key($data, array_flip($updatedFields));
        }

        $this->getReference()->update('',$data);

        //TODO: just sync changed relations?
        if($this->syncRelated){
            $this->model->syncRelatedWithFirebase();
        }
    }


}