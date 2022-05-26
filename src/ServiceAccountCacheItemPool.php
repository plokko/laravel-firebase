<?php
namespace Plokko\LaravelFirebase;

use Cache;
use Google\Auth\Cache\Item;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\InvalidArgumentException;

class ServiceAccountCacheItemPool implements CacheItemPoolInterface
{
    const
        CACHE_PREFIX='firebase.servicecache.';
    protected
        /**
         * @var \Illuminate\Cache\TaggedCache
         */
        $cache,
        $ttl;

    private
        $deferred=[];

    function __construct(array $config=null)
    {
        $this->ttl = $config && isset($config['ttl'])?$config['ttl']:null;

        $driver = $config && !empty($config['cache_driver'])?$config['cache_driver']:Cache::getDefaultDriver();
        $this->cache = Cache::store($driver);
        //Add tagging if possible
        if($driver !=='file'){
            try{
                $this->cache = $this->cache->tags(['firebase','token']);
            }catch(\BadMethodCallException $e){}
        }
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem(string $key): CacheItemInterface
    {
        return $this->cache->get(self::CACHE_PREFIX.$key)?:new Item($key);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *   An indexed array of keys of items to retrieve.
     *
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return array|\Traversable
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = array()): iterable
    {
        $items = [];
        foreach($keys AS $k){
            $items[] = $this->getItem($k);
        }
        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *   The key for which to check existence.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key): bool
    {
        return $this->cache->has(self::CACHE_PREFIX.$key);
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear(): bool
    {
        $this->cache->flush();
        return true;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key to delete.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key): bool
    {
        return $this->cache->delete(self::CACHE_PREFIX.$key);
    }
    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *   An array of keys that should be removed from the pool.
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys): bool
    {
        foreach($keys AS $key){
            $this->deleteItem($key);
        }
        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item): bool
    {
        try {
            $this->cache->set(self::CACHE_PREFIX.$item->getKey(), $item,$this->ttl);
        } catch (InvalidArgumentException $e) {
            return false;
        }
        return true;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[] = $item;
        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit(): bool
    {
        foreach($this->deferred AS $item){
            if(!$this->save($item))
                return false;
        }
        return true;
    }
}
