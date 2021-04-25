<?php namespace Atomino\RequestPipeline\Responder\Api\Attributes;

use Atomino\Neutrons\Attr;
use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route extends Attr {
	const Barefoot = false;
	const Method = true;
	#[Pure] public function __construct(public string|array $verb = [], private string|bool $url = self::Method) {
		if (!is_array($this->verb)) $this->verb = [$this->verb];
	}
	public function getPattern(\ReflectionMethod $method): string {
		if (is_string($this->url)) return trim($this->url, '/');
		$url = [];
		foreach ($method->getParameters() as $parameter) $url[] = ':' . ($parameter->isOptional() ? '?' : '') . $parameter->name;
		return ($this->url === self::Method ? $method->name . '/' : '') . join('/', $url);
	}
}