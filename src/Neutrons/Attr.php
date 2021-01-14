<?php namespace Atomino\Neutrons;

abstract class Attr{
	public static function get(\ReflectionClass|\ReflectionMethod $reflection):static|null{
		$attributes = $reflection->getAttributes(static::class);
		if(count($attributes) === 0) return null;
		/** @var static $instance */
		$instance = $attributes[0]->newInstance();
		return $instance;
	}

	/** @return static[] */
	public static function all(\ReflectionClass|\ReflectionMethod ...$reflections):array{
		/** @var static[] $attributes */
		$attributes = [];
		foreach ($reflections as $reflection) array_push($attributes, ... $reflection->getAttributes(static::class));
		return array_map(function (\ReflectionAttribute $attribute):static{return $attribute->newInstance();}, $attributes);
	}
}