<?php namespace Atomino\RequestPipeline\Router\Matcher;

abstract class Matcher {

	protected string $regex;
	protected bool|string $rest = false;
	protected array $attributes = [];
	protected bool $matches = false;
	public function isMatches(): bool { return $this->matches; }

	public function parse(?string $pattern, string $separator): void {

		$segments = explode($separator, trim($pattern, $separator));
		if ($rest = end($segments) == '**') array_pop($segments);

		if (count($segments) === 0 && $rest) {
			$this->regex = "%^(?'__REST__'.*)$%";
		} else {
			$segments = array_map(
				function ($segment) use ($separator) {
					if ($segment === '*') return '[^/]+';
					elseif (preg_match('/^:(?<optional>\??)(?<name>(.*?))(\((?<pattern>.*?)\))?$/', $segment, $matches)) {
						$pattern = (array_key_exists('pattern', $matches) && strlen($matches['pattern'])) ? $matches['pattern'] : '[^/]+';
						if (array_key_exists('name', $matches) && strlen($matches['name'])) $pattern = "(?'" . $matches['name'] . "'" . $pattern . ")";
						if ($matches['optional']) $pattern = '?(' . preg_quote($separator) . $pattern . '|.{0})';
						return $pattern;
					} else return $segment;
				},
				$segments
			);
			$this->regex = '%^' . join(preg_quote($separator), $segments) . ($rest ? "?(?'__REST__'" . preg_quote($separator) . ".*|.{0})" : "/{0}") . '$%';
		}

		$this->rest = $rest;
	}


	protected function match(string $subject) {
		if (preg_match($this->regex, $subject, $result)) {
			$result = array_filter($result, function ($key) { return !is_numeric($key); }, ARRAY_FILTER_USE_KEY);
			$result = array_map(function ($value) { return urldecode($value); }, $result);
			if ($this->rest) {
				if (array_key_exists('__REST__', $result)) {
					$this->rest = $result['__REST__'];
					unset($result['__REST__']);
				} else {
					$this->rest = '';
				}
			}
			$this->attributes = $result;
			$this->matches = true;
		}
	}
}