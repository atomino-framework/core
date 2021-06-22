<?php


namespace Atomino\Core\Debug;

class DebugProxy {

	static DebugHandler|null $debugHandler = null;

	public static function setDebugHandler(DebugHandler $handler){
		static::$debugHandler = $handler;
	}

	public static function debug(mixed $message, string $channel){
		static::$debugHandler?->handle($message, $channel);
	}
}