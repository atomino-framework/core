<?php namespace Atomino\Core\Debug;

abstract class DebugHandler {

	const DEBUG_DUMP = 'DEBUG_DUMP';
	const DEBUG_ALERT = 'DEBUG_ALERT';

	private array $handlers = [];

	public function __construct() {
		foreach ((new \ReflectionClass($this))->getMethods() as $method) {
			foreach (Channel::get($method)?->channels ?? [] as $channel) {
				if (!array_key_exists($channel, $this->handlers)) $this->handlers[$channel] = [];
				$this->handlers[$channel][] = $method->getShortName();
			}
		}
	}

	public final function handle($data, $channel) {
		if (array_key_exists($channel, $this->handlers)) {
			foreach ($this->handlers[$channel] as $handler) {
				$this->$handler($data, $channel);
			}
		}
	}
}