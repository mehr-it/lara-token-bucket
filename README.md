# Token bucket algorithm for laravel  
Implements the ["Token bucket" algorithm]([https://en.wikipedia.org/wiki/Token_bucket) using laravel's cache
repositories.

**Note**: This implementation uses a clock resolution of one second.
This means, new tokens are added every second at most.

## Install

	composer require mehr-it/lara-token-bucket

This package uses Laravel's package auto-discovery, so the service provider will be loaded automatically.


## Usage

Use the `TokenBucket` facade to access a token bucket:

```php
// Create a token bucket with rate of 5 tokens per second
// and burst size of 20 tokens.
$bucket = \TokenBucket::bucket('myBucket', 5.0, 20);

// try to take 2 tokens from the bucket
$success = $bucket->tryTake(2);
```

All token bucket instances with the same name use the same token
store, as long as the use the same underlying cache.


### Estimate time until tokens are available

Sometimes, it might be helpful to estimate the duration until new
tokens become available. The `tryTake()` method returns the 
estimated time to the second parameter if given. If you just want
to check the estimated availability without taking tokens out,
the `estimateAvailability()` method is what you are looking for.

```php
// $secUntilAvailableNext is filled with the duration until 
// another 2 tokens are available after an eventually
// successful taking
$bucket->tryTake(2, $secUntilAvailableNext);

// returns the duration until 2 tokens are available
$secUntilAvailableNext = $bucket->estimateAvailability(3);
```

### Putting tokens back
Sometimes, you might have taken a token which you didn't need. You
can give tokens back to buckets without affecting the time-based filling:

```php
// give back 2 tokens
$bucket->putTokens(2);
```

### Predefined buckets

If you want to predefine token buckets and use them later 
by resolving the bucket name, the `registerBucket()` method is
what you need:

```php
// register
\TokenBucket::registerBucket('myBucket', 5.0, 20);

// resolve
$bucket = \TokenBucket::resolveBucket('myBucket');
```

## Edge cases

By default, buckets are empty when no data exists. This happens
the first time a bucket is requested or after a cache flush.

You may define the initial number of tokens a bucket holds, when
creating a bucket instance:

```php
// create a token bucket with initial token number of 3
$bucket = \TokenBucket::bucket('myBucket', 5.0, 20, 3);
```