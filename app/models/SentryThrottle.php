<?php
use Cartalyst\Sentry\Throttling\Eloquent\Throttle as SentryThrottleModel;

class SentryThrottle extends SentryThrottleModel {
	protected $table = 'sentryThrottle';
}