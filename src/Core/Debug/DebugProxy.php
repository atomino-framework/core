<?php namespace Atomino\Core\Debug;

class DebugProxy {
	static DebugHandlerInterface|null $debugHandler = null;
	public static function setDebugHandler(DebugHandlerInterface $handler) { static::$debugHandler = $handler; }
	public static function getHandler(): DebugHandlerInterface|null { return static::$debugHandler; }
}