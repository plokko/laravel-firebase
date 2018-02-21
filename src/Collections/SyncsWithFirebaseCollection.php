<?php
namespace Plokko\LaravelFirebase\Collections;

use Illuminate\Database\Eloquent\Collection;

class SyncsWithFirebaseCollection extends Collection
{
    public function syncWithFirebase(){
        foreach($this AS $e){
            $e->syncWithFirebase();
        }
    }
}