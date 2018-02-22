<?php
namespace Plokko\LaravelFirebase\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Plokko\LaravelFirebase\Collections\SyncsWithFirebaseCollection;

class SyncWithFirebaseBelongsToMany extends BelongsToMany
{
    private
        /**
         * @var bool
         * @internal internal use only
         */
        $_syncWithFirebase = true;

    public function attach($id, array $attributes = [], $touch = true)
    {
        parent::attach($id,$attributes,$touch);

        $this->syncParentWithFirebase();

        $this->related->newQuery()->where($this->parentKey,$id)->first()->syncWithFirebase();
    }

    function detach($ids = null, $touch = true)
    {
        parent::detach($ids,$touch);
        $this->syncParentWithFirebase();

        $items = $this->related->newQuery()->whereIn($this->relatedKey,is_array($ids)?$ids:[$ids])->get();
        /**@var SyncsWithFirebaseCollection $items*/
        $items->syncWithFirebase();

    }


    public function sync($ids, $detaching = true)
    {
        // Temporary disable Firebase synching to avoid
        // triggering multiple attach/detach syncs
        $this->_syncWithFirebase = false;
        parent::sync($ids,$detaching);
        $this->_syncWithFirebase = true;

        //Sync with firebase
        $this->syncParentWithFirebase();

    }

    public function updateExistingPivot($id, array $attributes, $touch = true)
    {
        parent::updateExistingPivot($id,$attributes,$touch);
        $this->syncParentWithFirebase();
    }

    protected function syncParentWithFirebase(){
        if($this->_syncWithFirebase){
            //TODO:should only update touched relation
            $this->parent->syncWithFirebase();
        }
    }
}