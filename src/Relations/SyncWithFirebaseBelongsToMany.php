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
        $_isSynching = true;

    public function attach($id, array $attributes = [], $touch = true)
    {
        parent::attach($id,$attributes,$touch);

        // Sync parent model
        $this->syncParentWithFirebase();

        // Sync attached entity
        $this->related->newQuery()
                ->where($this->parentKey,$id)->first()
                ->syncWithFirebase();
    }

    function detach($ids = null, $touch = true)
    {
        parent::detach($ids,$touch);

        // Sync parent model
        $this->syncParentWithFirebase();

        // Gets detached entities
        $items = $this->related->newQuery()->whereIn($this->relatedKey,is_array($ids)?$ids:[$ids])->get();
        /**@var SyncsWithFirebaseCollection $items*/
        // Sync detached entities
        $items->syncWithFirebase();

    }


    public function sync($ids, $detaching = true)
    {
        // Temporary disable Firebase synching to avoid
        // triggering multiple attach/detach syncs on parent
        $this->_isSynching = false;
        parent::sync($ids,$detaching);
        $this->_isSynching = true;

        // Sync parent with firebase
        $this->syncParentWithFirebase();

        // No need to sync related model,
        // they have been sync in attach/detach
    }

    public function updateExistingPivot($id, array $attributes, $touch = true)
    {
        parent::updateExistingPivot($id,$attributes,$touch);
        $this->syncParentWithFirebase();

        // Sync updated entity
        $this->related->newQuery()
            ->where($this->parentKey,$id)->first()
            ->syncWithFirebase();
    }

    protected function syncParentWithFirebase(){
        if($this->_isSynching){
            $this->parent->syncWithFirebase();
            $this->parent->syncRelatedWithFirebase();
        }
    }
}
