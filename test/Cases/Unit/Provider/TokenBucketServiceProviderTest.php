<?php

	namespace MehrItLaraTokenBucketTest\Cases\Unit\Provider;

	use MehrIt\LaraTokenBucket\TokenBucketManager;
	use MehrItLaraTokenBucketTest\Cases\TestCase;

	class TokenBucketServiceProviderTest extends TestCase
	{

		public function testTokenBucketManagerRegistration() {
			
			$instance = app(TokenBucketManager::class);
			
			$this->assertInstanceOf(TokenBucketManager::class, $instance);
			$this->assertSame($instance, app(TokenBucketManager::class));
			
			
		}
		
	}