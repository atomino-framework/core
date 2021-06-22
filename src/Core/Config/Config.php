<?php namespace Atomino\Core\Config;

class Config implements ConfigInterface, \ArrayAccess {

	public function __construct(private array $values) { }

	public function __invoke(string $key): mixed {
		$value = $this->values;
		foreach (explode('.', $key) as $index) $value = $value[$index];
		return $value;
	}

	public function all(): array { return $this->values; }

	public function offsetExists(mixed $offset): bool {
		$value = $this->values;
		foreach (explode('.', $key) as $index) {
			if (array_key_exists($index, $value)) return false;
			$value = $value[$index];
		}
		return true;
	}
	public function offsetGet(mixed $offset): mixed { return $this($offset); }
	public function offsetSet(mixed $offset, mixed $value): void { }
	public function offsetUnset(mixed $offset): void { }
}