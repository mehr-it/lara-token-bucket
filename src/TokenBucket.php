<?php

	namespace MehrIt\LaraTokenBucket;

	use Carbon\Carbon;
	use Illuminate\Contracts\Cache\Repository;
	use InvalidArgumentException;

	class TokenBucket
	{
		use EventuallyLocks;

		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @var float 
		 */
		protected $rate;

		/**
		 * @var int 
		 */
		protected $burst;

		/**
		 * @var int 
		 */
		protected $initialTokenCount;

		/**
		 * @var Repository
		 */
		protected $cache;

		/**
		 * The bucket name
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * Gets the number of tokens per second that are added to the bucket
		 * @return float
		 */
		public function getRate(): float {
			return $this->rate;
		}

		/**
		 * Gets the maximum number of tokens the bucket can hold
		 * @return int
		 */
		public function getBurst(): int {
			return $this->burst;
		}

		/**
		 * Gets the initial number of tokens when the bucket is created/reset
		 * @return int
		 */
		public function getInitialTokenCount(): int {
			return $this->initialTokenCount;
		}

		/**
		 * Gets the used cache repository
		 * @return Repository
		 */
		public function getCache(): Repository {
			return $this->cache;
		}
		
		
		

		/**
		 * Creates a new instance.
		 * @param Repository $cache The cache repository to use
		 * @param string $name The bucket name
		 * @param float $rate The number of tokens per second that are added to the bucket
		 * @param int $burst The maximum number of tokens the bucket can hold
		 * @param int $initialTokenCount The initial number of tokens when the bucket is created/reset
		 */
		public function __construct(Repository $cache, string $name, float $rate, int $burst, int $initialTokenCount = 0) {

			if ($rate <= 0)
				throw new InvalidArgumentException('Rate must be greater than 0');
			if ($burst <= 0)
				throw new InvalidArgumentException('Burst must be greater than 0');

			$this->name              = $name;
			$this->rate              = $rate;
			$this->burst             = $burst;
			$this->initialTokenCount = $initialTokenCount;
			$this->cache             = $cache;
		}

		/**
		 * Tries to take the given number of tokens out of the bucket. Returns true on success. Else false.
		 * @param int $tokens The number of tokens to acquire
		 * @param float $estimatedNextAvailableIn Returns the estimated minimum duration in seconds until the requested number of tokens become available again. Time resolution is milliseconds.
		 * @return bool True if tokens were taken. Else false.
		 */
		public function tryTake(int $tokens = 1, &$estimatedNextAvailableIn = 0): bool {
			
			if ($tokens > $this->burst)
				throw new InvalidArgumentException("Trying to take {$tokens} from token bucket \"{$this->name}\" with burst size {$this->burst}. This would never succeed.");

			return $this->withEventualLock($this->cache, $this->getLockKey(), 3, function () use ($tokens, &$estimatedNextAvailableIn) {

				$state = $this->readState($this->cache);

				$now = Carbon::now();
				
				// fill the bucket
				$this->fillBucket($state, $now);

				// stop if there are not enough tokens
				if ($state['tokens'] < $tokens) {

					// calculate the duration until available
					$estimatedNextAvailableIn = $this->durationUntilTokensAvailable($state, $tokens, $now);

					
					return false;
				}


				// decrement token count and save state
				$state['tokens'] -= $tokens;
				$this->writeState($this->cache, $state);

				// calculate the duration until available next time
				$estimatedNextAvailableIn = $this->durationUntilTokensAvailable($state, $tokens, $now);


				return true;

			});
		}

		/**
		 * Puts the given number of tokens into the bucket. The bucket is filled up to burst size without affecting the rate-timing
		 * @param int $tokens The number of tokens to put.
		 * @return $this
		 */
		public function putTokens(int $tokens = 1) : TokenBucket {
			
			$this->withEventualLock($this->cache, $this->getLockKey(), 3, function () use ($tokens) {

				$state = $this->readState($this->cache);
				
				// add tokens
				$state['tokens'] = min($state['tokens'] + $tokens, $this->burst);
				
				$this->writeState($this->cache, $state);
				
			});
			
			return $this;
		}

		/**
		 * Resets the bucket
		 * @param int|null $tokens The number of tokens the bucket holds. If null, the initialTokenCount will be used.
		 * @return TokenBucket
		 */
		public function resetBucket(int $tokens = null): TokenBucket {
			
			$this->withEventualLock($this->cache, $this->getLockKey(), 3, function () use ($tokens) {

				$this->resetBucketState($this->cache, $tokens);

			});

			return $this;
		}

		/**
		 * Estimates the minimum duration until the given number of tokens become available. This is lightweight, 
		 * non-locking operation.
		 * @param int $tokens The requested number of tokens 
		 * @return float The minimum duration (in seconds) until the given number of tokens become available 
		 */
		public function estimateAvailability(int $tokens = 1): float {

			if ($tokens > $this->burst)
				throw new InvalidArgumentException("Trying to take {$tokens} from token bucket \"{$this->name}\" with burst size {$this->burst} would never succeed.");

			$now = Carbon::now();

			$state = $this->readState($this->cache);

			// simulate fill
			$this->fillBucket($state, $now);
			
			
			return $this->durationUntilTokensAvailable($state, $tokens, $now);
		}

		/**
		 * Calculates the duration until the given number of tokens become available.
		 * @param array $state The state
		 * @param int $tokenCount The token count
		 * @param Carbon $now The current point in time
		 * @return float The duration 
		 */
		protected function durationUntilTokensAvailable(array $state, int $tokenCount, Carbon $now): float {
			
			// calculate the time since the last fill
			$elapsedSinceLastFill = max(($now->getTimestamp() + $now->milli / 1000) - $state['fillTs'], 0);
			
			// Calculate the ETA of the requested tokens (starting at last fill time).
			// We use ceil because the fill-up will only happen at full seconds 
			$etaRelativeToLastFill = ceil(($tokenCount - $state['tokens']) / $this->rate);
			
			return max(0.0, $etaRelativeToLastFill - $elapsedSinceLastFill);
		}


		/**
		 * Resets the bucket to the given state
		 * @param Repository $store The cache store
		 * @param int|null $tokens The number of tokens the bucket holds. If null, the initialTokenCount will be used.
		 * @return array The bucket state
		 */
		protected function resetBucketState(Repository $store, int $tokens = null): array {

			if ($tokens === null)
				$tokens = $this->initialTokenCount;

			$state = [
				'tokens' => $tokens,
				'fillTs' => Carbon::now()->getTimestamp(),
			];

			$this->writeState($store, $state);

			return $state;
		}

		/**
		 * Reads the current state from cache
		 * @param Repository $store The cache store
		 * @return array The state
		 */
		protected function readState(Repository $store): array {
			$state = $store->get($this->getThrottleKey());

			// reset if no state available or broken
			if (!is_array($state) || !is_int($state['tokens'] ?? null) || !is_int($state['fillTs'])) {

				$state = $this->resetBucketState($store);
			}

			return $state;
		}

		/**
		 * Writes the given state to the cache
		 * @param Repository $store The cache store
		 * @param array $state The state
		 */
		protected function writeState(Repository $store, array $state) {
			$store->forever($this->getThrottleKey(), $state);
		}

		/**
		 * Fills the bucket with as many tokens as can be assigned for the elapsed time
		 * @param array $state The bucket state
		 * @param Carbon $now The current point in time
		 */
		protected function fillBucket(array &$state, Carbon $now) {

			$nowTs = $now->getTimestamp();

			$elapsedTime = $nowTs - $state['fillTs'];
			if ($elapsedTime > 0) {

				$currentTokenCount = min((int)($state['tokens'] + $elapsedTime * $this->rate), $this->burst);

				if ($currentTokenCount > $state['tokens']) {
					$state['tokens'] = $currentTokenCount;
					$state['fillTs'] = $nowTs;
				}
			}
		}


		/**
		 * Gets the lock key
		 * @return string The lock key
		 */
		protected function getLockKey(): string {
			return "tokenBucketL{$this->name}";
		}

		/**
		 * Gets the throttle key
		 * @return string The throttle key
		 */
		protected function getThrottleKey(): string {
			return "tokenBucketT{$this->name}";
		}
	}