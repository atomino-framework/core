<?php namespace Atomino\RequestPipeline\Responder\Smart\Attributes;

use Atomino\RequestPipeline\Responder\Smart\Cache\Middleware\Cache as CacheMiddleware;
use Atomino\Neutrons\Attr;
use Attribute;

#[Attribute( Attribute::TARGET_CLASS )]
class Cache extends Attr{
	public function __construct(int $interval){ CacheMiddleware::SetCache($interval); }
}
