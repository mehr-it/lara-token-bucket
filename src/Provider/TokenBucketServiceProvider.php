<?php

	namespace MehrIt\LaraTokenBucket\Provider;

	use Illuminate\Contracts\Support\DeferrableProvider;
	use Illuminate\Support\ServiceProvider;
	use MehrIt\LaraTokenBucket\TokenBucketManager;

	class TokenBucketServiceProvider extends ServiceProvider implements DeferrableProvider
	{
		
		public function register() {
			
			$this->app->singleton(TokenBucketManager::class, function() {
				return new TokenBucketManager();
			});
			
		}
		
		public function provides() {
			return [
				TokenBucketManager::class,
			];
		}


	}