<?php

	namespace MehrItLaraTokenBucketTest\Cases\Unit;
	
	use Carbon\Carbon;
	use Illuminate\Cache\ArrayStore;
	use Illuminate\Cache\CacheManager;
	use Illuminate\Cache\RetrievesMultipleKeys;
	use Illuminate\Cache\TaggableStore;
	use Illuminate\Contracts\Cache\LockProvider;
	use Illuminate\Contracts\Cache\Repository;
	use Illuminate\Support\InteractsWithTime;
	use InvalidArgumentException;
	use MehrIt\LaraTokenBucket\TokenBucket;
	use MehrItLaraTokenBucketTest\Cases\TestCase;

	class TokenBucketTest extends TestCase
	{
		/**
		 * @var Repository
		 */
		protected $cache;

		protected function setUp(): void {
			
			parent::setUp(); 
			
			/** @var CacheManager $cacheManager */
			$cacheManager = app('cache');
			
			$this->cache = $cacheManager->store();
		}


		public function testTakeEstimate_1token_rate1_burst4() {

			$bucket = new TokenBucket($this->cache, 'b1', 1, 4, 4);

			Carbon::setTestNow(Carbon::now());


			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSecond());

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSeconds(2));

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSeconds(10));

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

		}

		public function testTakeEstimate_1token_rate2_burst5() {

			$bucket = new TokenBucket($this->cache,'b1', 2, 5, 4);

			Carbon::setTestNow(Carbon::now());


			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSecond());

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSeconds(2));

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSeconds(10));

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

		}

		public function testTakeEstimate_1token_rate05_burst2() {

			$bucket = new TokenBucket($this->cache,'b1', 0.5, 2, 4);

			Carbon::setTestNow(Carbon::now());


			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSecond());

			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSecond());

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSeconds(2));

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability());

			Carbon::setTestNow(Carbon::now()->addSeconds(10));

			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability());
			
			$this->assertSame(true, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability());
			
			$this->assertSame(false, $bucket->tryTake(1, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability());
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability());

		}

		public function testTakeEstimate_2tokens_rate1_burst4() {

			$bucket = new TokenBucket($this->cache, 'b1', 1, 4, 4);

			Carbon::setTestNow(Carbon::now());


			$this->assertSame(true, $bucket->tryTake(2, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability(2));
			
			$this->assertSame(true, $bucket->tryTake(2, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability(2));
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability(2));
			
			$this->assertSame(false, $bucket->tryTake(), $nextAvail);
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability(2));
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability(2));

			
			Carbon::setTestNow(Carbon::now()->addSecond());
			
			$this->assertSame(false, $bucket->tryTake(2, $nextAvail));
			$this->assertGreaterThan(0, $nextAvail);
			$this->assertGreaterThan(0, $bucket->estimateAvailability(2));
			$this->assertLessThanOrEqual(1, $nextAvail);
			$this->assertLessThanOrEqual(1, $bucket->estimateAvailability(2));

			Carbon::setTestNow(Carbon::now()->addSecond());

			$this->assertSame(true, $bucket->tryTake(2, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability(2));
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability(2));
			
			$this->assertSame(false, $bucket->tryTake());
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability(2));
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability(2));

			Carbon::setTestNow(Carbon::now()->addSeconds(10));

			$this->assertSame(true, $bucket->tryTake(2, $nextAvail));
			$this->assertSame(0.0, $nextAvail);
			$this->assertSame(0.0, $bucket->estimateAvailability(2));
			
			$this->assertSame(true, $bucket->tryTake(2, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability(2));
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability(2));
			
			$this->assertSame(false, $bucket->tryTake(2, $nextAvail));
			$this->assertGreaterThan(1, $nextAvail);
			$this->assertGreaterThan(1, $bucket->estimateAvailability(2));
			$this->assertLessThanOrEqual(2, $nextAvail);
			$this->assertLessThanOrEqual(2, $bucket->estimateAvailability(2));

		}

		public function testTryTake_tokensGreaterThanBurst() {
			
			$bucket = new TokenBucket($this->cache, 'b1', 1, 4);
			
			$this->expectException(InvalidArgumentException::class);
			$bucket->tryTake(5);
			
		}
		
		public function testTryTake_differentNamesUseIndependentBuckets() {
			
			$bucketA = new TokenBucket($this->cache, 'b1', 1, 4, 1);
			$bucketB = new TokenBucket($this->cache, 'b2', 1, 4, 1);
			
			$this->assertSame(true, $bucketA->tryTake());
			$this->assertSame(true, $bucketB->tryTake());
		}
		
		public function testTryTake_sameNamesUseSameBucket() {
			
			$bucketA = new TokenBucket($this->cache, 'b1', 1, 4, 1);
			$bucketB = new TokenBucket($this->cache, 'b1', 1, 4, 1);
			
			$this->assertSame(true, $bucketA->tryTake());
			$this->assertSame(false, $bucketB->tryTake());
		}

		public function testTryTake_withCacheSupportingLocking() {

			$this->cache = new \Illuminate\Cache\Repository(new ArrayStore());
			
			if (! ($this->cache->getStore() instanceof LockProvider))
				$this->markTestSkipped('Precondition failed: The chosen cache must support locking.');

			$bucket = new TokenBucket($this->cache, 'b1', 1, 4, 1);

			$this->assertSame(true, $bucket->tryTake());
		}
		
		public function testTryTake_withNotCacheSupportingLocking() {
			
			$this->cache = new \Illuminate\Cache\Repository(new TokenBucketTestCacheStoreWithoutLocking());
			
			if ($this->cache->getStore() instanceof LockProvider)
				$this->markTestSkipped('Precondition failed: The chosen cache must not support locking.');

			$bucket = new TokenBucket($this->cache, 'b1', 1, 4, 1);

			$this->assertSame(true, $bucket->tryTake());
		}
		
		public function testEstimateAvailability_tokensGreaterThanBurst() {
			
			$bucket = new TokenBucket($this->cache, 'b1', 1, 4);
			
			$this->expectException(InvalidArgumentException::class);
			$bucket->estimateAvailability(5);
			
		}

		public function testResetBucket() {

			$bucket = new TokenBucket($this->cache,'b1', 1, 4, 4);

			Carbon::setTestNow(Carbon::now());

			$this->assertSame(true, $bucket->tryTake());
			$this->assertSame(true, $bucket->tryTake());

			Carbon::setTestNow(Carbon::now()->setMilli(0));

			$bucket->resetBucket();

			$this->assertSame(true, $bucket->tryTake());
			$this->assertSame(true, $bucket->tryTake());
			$this->assertSame(true, $bucket->tryTake());
			$this->assertSame(true, $bucket->tryTake());
			$this->assertSame(false, $bucket->tryTake());

		}

		public function testResetBucket_withTokenCountSpecified() {

			$bucket = new TokenBucket($this->cache,'b1', 1, 4, 4);

			Carbon::setTestNow(Carbon::now());

			$this->assertSame(true, $bucket->tryTake());
			$this->assertSame(true, $bucket->tryTake());

			Carbon::setTestNow(Carbon::now()->setMilli(0));

			$bucket->resetBucket(2);

			$this->assertSame(true, $bucket->tryTake());
			$this->assertSame(true, $bucket->tryTake());
			$this->assertSame(false, $bucket->tryTake());

		}
	}
	
	class TokenBucketTestCacheStoreWithoutLocking extends TaggableStore
	{
		use InteractsWithTime, RetrievesMultipleKeys;

		/**
		 * The array of stored values.
		 *
		 * @var array
		 */
		protected $storage = [];

		/**
		 * The array of locks.
		 *
		 * @var array
		 */
		public $locks = [];

		/**
		 * Indicates if values are serialized within the store.
		 *
		 * @var bool
		 */
		protected $serializesValues;

		/**
		 * Create a new Array store.
		 *
		 * @param bool $serializesValues
		 * @return void
		 */
		public function __construct($serializesValues = false) {
			$this->serializesValues = $serializesValues;
		}

		/**
		 * Retrieve an item from the cache by key.
		 *
		 * @param string|array $key
		 * @return mixed
		 */
		public function get($key) {
			if (!isset($this->storage[$key])) {
				return null;
			}

			$item = $this->storage[$key];

			$expiresAt = $item['expiresAt'] ?? 0;

			if ($expiresAt !== 0 && $this->currentTime() > $expiresAt) {
				$this->forget($key);

				return null;
			}

			return $this->serializesValues ? unserialize($item['value']) : $item['value'];
		}

		/**
		 * Store an item in the cache for a given number of seconds.
		 *
		 * @param string $key
		 * @param mixed $value
		 * @param int $seconds
		 * @return bool
		 */
		public function put($key, $value, $seconds) {
			$this->storage[$key] = [
				'value'     => $this->serializesValues ? serialize($value) : $value,
				'expiresAt' => $this->calculateExpiration($seconds),
			];

			return true;
		}

		/**
		 * Increment the value of an item in the cache.
		 *
		 * @param string $key
		 * @param mixed $value
		 * @return int
		 */
		public function increment($key, $value = 1) {
			if (!is_null($existing = $this->get($key))) {
				return tap(((int)$existing) + $value, function ($incremented) use ($key) {
					$value = $this->serializesValues ? serialize($incremented) : $incremented;

					$this->storage[$key]['value'] = $value;
				});
			}

			$this->forever($key, $value);

			return $value;
		}

		/**
		 * Decrement the value of an item in the cache.
		 *
		 * @param string $key
		 * @param mixed $value
		 * @return int
		 */
		public function decrement($key, $value = 1) {
			return $this->increment($key, $value * -1);
		}

		/**
		 * Store an item in the cache indefinitely.
		 *
		 * @param string $key
		 * @param mixed $value
		 * @return bool
		 */
		public function forever($key, $value) {
			return $this->put($key, $value, 0);
		}

		/**
		 * Remove an item from the cache.
		 *
		 * @param string $key
		 * @return bool
		 */
		public function forget($key) {
			if (array_key_exists($key, $this->storage)) {
				unset($this->storage[$key]);

				return true;
			}

			return false;
		}

		/**
		 * Remove all items from the cache.
		 *
		 * @return bool
		 */
		public function flush() {
			$this->storage = [];

			return true;
		}

		/**
		 * Get the cache key prefix.
		 *
		 * @return string
		 */
		public function getPrefix() {
			return '';
		}

		/**
		 * Get the expiration time of the key.
		 *
		 * @param int $seconds
		 * @return int
		 */
		protected function calculateExpiration($seconds) {
			return $this->toTimestamp($seconds);
		}

		/**
		 * Get the UNIX timestamp for the given number of seconds.
		 *
		 * @param int $seconds
		 * @return int
		 */
		protected function toTimestamp($seconds) {
			return $seconds > 0 ? $this->availableAt($seconds) : 0;
		}
	}