<?php

	namespace MehrItLaraTokenBucketTest\Cases;

	use Carbon\Carbon;
	use Illuminate\Cache\CacheManager;
	use MehrIt\LaraTokenBucket\Facades\TokenBucket;
	use MehrIt\LaraTokenBucket\Provider\TokenBucketServiceProvider;
	use MehrIt\LaraTokenBucket\TokenBucketManager;

	class TestCase extends \Orchestra\Testbench\TestCase
	{
		protected function getPackageProviders($app) {

			return [
				TokenBucketServiceProvider::class,
			];

		}

		/**
		 * @inheritDoc
		 */
		protected function getPackageAliases($app) {

			return [
				'TokenBucket' => TokenBucket::class,
			];
		}

		/**
		 * @param $abstract
		 * @param null $class
		 * @return \PHPUnit\Framework\MockObject\MockObject
		 */
		protected function mockAppSingleton($abstract, $class = null) {

			if ($class === null)
				$class = $abstract;

			$mock = $this->getMockBuilder($class)->disableOriginalConstructor()->getMock();

			app()->singleton($abstract, function () use ($mock) {
				return $mock;
			});

			return $mock;
		}

		protected function setUp(): void {

			parent::setUp();

			Carbon::setTestNow();

			/** @var CacheManager $cacheManager */
			$cacheManager = app('cache');

			$cacheManager->clear();
		}
	}