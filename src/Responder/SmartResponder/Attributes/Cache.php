<?php namespace Atomino\Responder\SmartResponder\Attributes;

use Attribute;
use Twig\Loader\FilesystemLoader;


#[Attribute(Attribute::TARGET_CLASS)]
class Cache{
	public function __construct(int $interval){
		\Atomino\Middlewares\Cache::Request($interval);
	}
}
