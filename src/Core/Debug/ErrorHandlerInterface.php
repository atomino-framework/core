<?php namespace Atomino\Core\Debug;

use function Atomino\Core\Debug;

interface ErrorHandlerInterface {

	const DEBUG_ERROR = 'ERROR';
	const DEBUG_EXCEPTION = 'EXCEPTION';

	public function register();

}