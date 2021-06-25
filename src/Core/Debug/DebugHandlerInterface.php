<?php namespace Atomino\Core\Debug;

interface DebugHandlerInterface {

	const DEBUG_CHANNEL_USER = 'USER';

	public const DEBUG = 100;
	public const INFO = 200;
	public const NOTICE = 250;
	public const WARNING = 300;
	public const ERROR = 400;
	public const CRITICAL = 500;
	public const ALERT = 550;
	public const EMERGENCY = 600;


	public function handle(mixed $data, string $channel, int $level);
}