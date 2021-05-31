<?php namespace Atomino\Debug;

use function Atomino\debug;

interface ErrorHandlerInterface {

	const DEBUG_ERROR = 'ERROR';
	const DEBUG_EXCEPTION = 'EXCEPTION';

	public function register();

}