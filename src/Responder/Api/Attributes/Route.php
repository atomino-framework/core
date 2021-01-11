<?php namespace Atomino\Responder\Api\Attributes;

use Attribute;


#[Attribute( Attribute::TARGET_METHOD)]
class Route{
	const Barefoot = false;
	const Method = true;
	public function __construct(public string|array $verb = [], private string|bool $url=self::Method){
		if(!is_array($this->verb)) $this->verb = [$this->verb];
	}
	public function getPattern(\ReflectionMethod $method, string $apiBase):string{
		$apiBase = trim($apiBase, '/').'/';
		if(is_string($this->url)) return $apiBase.trim($this->url, '/');
		$url = [];
		foreach ($method->getParameters() as $parameter){
			$url[] = ':'.($parameter->isOptional() ? '?': '').$parameter->name;
		}
		return $apiBase.($this->url === true ? $method->name.'/' : '').join('/',$url);
	}
}