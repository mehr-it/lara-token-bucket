<?php

	namespace MehrIt\LaraTokenBucket\Facades;

	use Illuminate\Support\Facades\Facade;
	use MehrIt\LaraTokenBucket\TokenBucketManager;

	class TokenBucket extends Facade
	{

		/**
		 * Get the registered name of the component.
		 *
		 * @return string
		 */
		protected static function getFacadeAccessor() {
			return TokenBucketManager::class;
		}
		
	}