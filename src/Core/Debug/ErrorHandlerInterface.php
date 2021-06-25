<?php namespace Atomino\Core\Debug;

use function Atomino\Core\Debug;

interface ErrorHandlerInterface {

	const ERROR = 'ERROR';
	const EXCEPTION = 'EXCEPTION';
	const TRACE = 'TRACE';

	public function register();

}