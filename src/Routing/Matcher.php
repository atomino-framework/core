<?php namespace Atomino\Routing;

use Symfony\Component\HttpFoundation\ParameterBag;

class Matcher{
	public function __invoke(?string $pattern, ?string $subject, $separator, ParameterBag $params): bool{
		$pattern = trim($pattern, $separator);
		$subject = trim($subject, $separator);
		$segments = explode($separator, $pattern);

		if (!str_ends_with($pattern, '**') && !str_contains($pattern, $separator.':?') && count($segments) !== count(explode($separator, $subject))) return false;

		$segments = array_map(function ($segment) use($separator){
			if ($segment === '**') return "?(?<__REST>(".preg_quote($separator).".*|.{0}))";
			if ($segment === '*') return '.+?';
			if (preg_match('/^:(?<optional>\??)(?<name>(.*?))(\((?<pattern>.*?)\))?$/', $segment, $matches)){
				$pattern = ( array_key_exists('pattern', $matches) && strlen($matches['pattern']) ) ? $matches['pattern'] : '.+?';
				$pattern = ( array_key_exists('name', $matches) && strlen($matches['name']) ) ? "(?'" . $matches['name'] . "'" . $pattern . ")" : $pattern;
				if ($matches['optional']) $pattern = '?('.preg_quote($separator). $pattern . '|.{0})';
				return $pattern;
			}
			return $segment;
		}, $segments);

		$pattern = '%^' . join(preg_quote($separator), $segments) . "(?'_ERROR_'/.*?)?" . '$%';

		if (preg_match($pattern, $subject, $result)){
			if (array_key_exists('_ERROR_', $result)) return false;
			$result = array_filter($result, function ($key){ return !is_numeric($key); }, ARRAY_FILTER_USE_KEY);
			$result = array_map(function ($value){ return urldecode($value); }, $result);
			$params->replace($result);
			return true;
		}else{
			return false;
		}
	}
}