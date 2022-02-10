<?php

	namespace MehrItLaraTokenBucketTest\Cases\Unit;

	use MehrIt\LaraTokenBucket\TokenBucketManager;
	use MehrItLaraTokenBucketTest\Cases\TestCase;

	class TokenBucketManagerTest extends TestCase
	{
		/**
		 * Define environment setup.
		 *
		 * @param \Illuminate\Foundation\Application $app
		 * @return void
		 */
		protected function getEnvironmentSetUp($app) {
			// for backwards compatibility with testbench 3.8
			$this->defineEnvironment($app);
		}
		
		protected function defineEnvironment($app) {
			
			$app['config']->set('cache.stores.alt', [
				'driver'   => 'array',
			]);
			
		}


		public function testBucket() {
			
			$m = new TokenBucketManager();
			
			
			$ret = $m->bucket('b1', 1.0, 4);
			
			
			$this->assertSame('b1', $ret->getName());
			$this->assertSame(1.0, $ret->getRate());
			$this->assertSame(4, $ret->getBurst());
			$this->assertSame(0, $ret->getInitialTokenCount());
			$this->assertSame(\Cache::store(), $ret->getCache());
			
			$this->assertSame(null, $m->resolveBucket('b1'));
		}
		
		public function testBucket_altCache() {
			
			$m = new TokenBucketManager();
			
			
			$ret = $m->bucket('b1', 1.0, 4, 1, 'alt');
			
			
			$this->assertSame('b1', $ret->getName());
			$this->assertSame(1.0, $ret->getRate());
			$this->assertSame(4, $ret->getBurst());
			$this->assertSame(1, $ret->getInitialTokenCount());
			$this->assertSame(\Cache::store('alt'), $ret->getCache());
			
			$this->assertSame(null, $m->resolveBucket('b1'));
		}
		
		public function testRegisterResolveBucket() {
			
			$m = new TokenBucketManager();
			
			
			$ret = $m->registerBucket('b1', 1.0, 4);
			
			
			$this->assertSame('b1', $ret->getName());
			$this->assertSame(1.0, $ret->getRate());
			$this->assertSame(4, $ret->getBurst());
			$this->assertSame(0, $ret->getInitialTokenCount());
			$this->assertSame(\Cache::store(), $ret->getCache());
			
			$this->assertSame($ret, $m->resolveBucket('b1'));
		}
		
		public function testRegisterResolveBucket_altCache() {
			
			$m = new TokenBucketManager();
			
			
			$ret = $m->registerBucket('b1', 1.0, 4, 1, 'alt');
			
			
			$this->assertSame('b1', $ret->getName());
			$this->assertSame(1.0, $ret->getRate());
			$this->assertSame(4, $ret->getBurst());
			$this->assertSame(1, $ret->getInitialTokenCount());
			$this->assertSame(\Cache::store('alt'), $ret->getCache());
			
			$this->assertSame($ret, $m->resolveBucket('b1'));
		}
		
		
	}