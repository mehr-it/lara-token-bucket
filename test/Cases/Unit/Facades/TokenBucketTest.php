<?php

	namespace MehrItLaraTokenBucketTest\Cases\Unit\Facades;

	use MehrIt\LaraTokenBucket\TokenBucketManager;
	use MehrItLaraTokenBucketTest\Cases\TestCase;
	use TokenBucket;

	class TokenBucketTest extends TestCase
	{
		public function testAncestorCall() {
			// mock ancestor
			$mock = $this->mockAppSingleton(TokenBucketManager::class, TokenBucketManager::class);
			$mock->expects($this->once())
				->method('resolveBucket')
				->with('bucketA');

			TokenBucket::resolveBucket('bucketA');
		}
	}