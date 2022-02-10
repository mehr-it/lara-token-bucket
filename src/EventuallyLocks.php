<?php

	namespace MehrIt\LaraTokenBucket;

	use Closure;
	use Illuminate\Contracts\Cache\LockProvider;
	use Illuminate\Contracts\Cache\Repository;

	trait EventuallyLocks
	{
		/**
		 * Executes the given callback while locking using the given store
		 * @param Repository $cache The store
		 * @param string $lock The lock name
		 * @param int $seconds The number of seconds to acquire the lock
		 * @param Closure $callback The callback
		 * @return mixed The callback return
		 */
		protected function withEventualLock(Repository $cache, string $lock, int $seconds, Closure $callback) {

			if ($this->supportsLocking($cache)) {
				
				/** @var LockProvider $lockProvider */
				$lockProvider = $cache->getStore();
				
				// invoke locked
				return $lockProvider->lock($lock, $seconds)->get($callback);
			}
			else {

				// emit warning in production
				if (app()->environment('production'))
					logger()->warning("Trying to acquire lock \"{$lock}\" but the given cache store does not support locking. Executing callback without locking.");

				// invoke without locking
				return $callback();
			}

		}

		/**
		 * Returns if the given cache supports locking
		 * @param Repository $cache The cache
		 * @return bool
		 */
		protected function supportsLocking(Repository $cache): bool {
			return $cache->getStore() instanceof LockProvider;
		}
	}