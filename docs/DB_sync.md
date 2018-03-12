# Firebase realtime database synching
This module can automatically sync Eloquent models to Firebase Realtime database.

To enable synching use the `SyncWithFirebase` trait in your Model 
```php
use Plokko\LaravelFirebase\Traits\SyncWithFirebase;

class MyModel extends Model
{
    use SyncWithFirebase;
    //...
}
```
This model will add the `syncWithFirebase` method that manually syncs the model to Firebase and will be triggered at each model modification done throught eloquent(save,update,delete,restore).

You can customize what will be synched with Firebase via the `toFirebase` method otherwise the output of toArray will be used instead
```php
use Plokko\LaravelFirebase\Traits\SyncWithFirebase;

class MyModel extends Model
{
    use SyncWithFirebase;
    //...
    
    /**
    * Returns the data that will be synched with firebase
    * @return array
    **/
    function toFirebase(){
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'custom'=> 'value',
            //....
        ];
    }
    
}
```
### Changing reference name
By default the model will be synchromized to Firebase using the table name (ex. User model using _users_ table will be sync to _/users_);
you can change this behaivour by extending the `getFirebaseReferenceName` method and returning a custom reference name
```php

    /**
     * @return string reference name
     */
    public function getFirebaseReferenceName(){
        return 'my_custom_reference';// default : $this->getTable();
    }
```

## Sync related models
Sometimes you want to serialize to firebase data from othe related models but the changes in the related model will not be automatically updated on the base model and vice-versa.
You can extend the model synchronization to related models using the `SyncRelatedWithFirebase` trait in your Model and extend the `getRelationsToSyncWithFirebase()` function to return an array of relations you want to keep in sync
```php
use Plokko\LaravelFirebase\Traits\SyncWithFirebase;

class MyModel extends Model
{
    use SyncWithFirebase,
        SyncRelatedWithFirebase;

    
    /**
     * Specifies the relations that needs to be automatically synched with firebase
     * @return array relations to be synched with firebase, default []
     */
    protected function getRelationsToSyncWithFirebase(){
        return [
          'myRelation',
          'myOtherRelation'
        ];
    }
    public function myRelation(){
      return $this->belongsToMany(OtherModel::class);
    }
}
```
This trait will automatically sync related model every time a m-n relation is changed or the model is saved.
