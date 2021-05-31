<?php namespace Atomino\Debug;

use Atomino\Neutrons\Attr;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class DebugHandler extends Attr {
	public $channels;
	public function __construct(string ...$channels) { $this->channels = $channels; }
}