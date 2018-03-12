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

You can customize what will be synched with Firebase via the `toFirebase` method:
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
