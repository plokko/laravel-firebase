<?php
namespace Plokko\LaravelFirebase\Collections;

use Illuminate\Database\Eloquent\Collection;

class SyncsWithFirebaseCollection extends Collection
{
    public function syncWithFirebase($withRelated=false){
        foreach($this AS $e){
            $e->syncWithFirebase($withRelated);
        }
    }
}