<?php

	namespace MehrIt\LaraTokenBucket;
	

	class TokenBucketManager
	{

		/**
		 * @var TokenBucket[] 
		 */
		protected $buckets = [];


		/**
		 * Creates a new token bucket instance. The instance is not registered at the manager
		 * @param string $name The bucket name
		 * @param float $rate The number of tokens per second that are added to the bucket
		 * @param int $burst The maximum number of tokens the bucket can hold
		 * @param int $initialTokenCount The initial number of tokens when the bucket is created/reset
		 * @param string|null $cache The cache to use
		 * @return TokenBucket
		 */
		public function bucket(string $name, float $rate, int $burst, int $initialTokenCount = 0, string $cache = null): TokenBucket {
			
			return new TokenBucket(
				\Cache::store($cache),
				$name,
				$rate, 
				$burst, 
				$initialTokenCount,
			);
				
			
		}

		/**
		 * Resolves a registered bucket
		 * @param string $name The bucket name
		 * @return TokenBucket|null The token bucket or null if not registered
		 */
		public function resolveBucket(string $name): ?TokenBucket {
			return $this->buckets[$name] ?? null;
		}

		/**
		 * Creates a new token bucket instance and registers it for later resolving by name.
		 * @param string $name The bucket name
		 * @param float $rate The number of tokens per second that are added to the bucket
		 * @param int $burst The maximum number of tokens the bucket can hold
		 * @param int $initialTokenCount The initial number of tokens when the bucket is created/reset
		 * @param string|null $cache The cache to use
		 * @return TokenBucket The bucket instance
		 */
		public function registerBucket(string $name, float $rate, int $burst, int $initialTokenCount = 0, string $cache = null): TokenBucket {
			
			$bucket = $this->bucket($name, $rate, $burst, $initialTokenCount, $cache);
			
			$this->buckets[$name] = $bucket;
			
			return $bucket;
		}
	}