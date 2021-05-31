<?php namespace Atomino\Debug;

use function Atomino\debug;

class ErrorHandler implements ErrorHandlerInterface {


	public function fatalError() {
		if (!is_null($error = error_get_last())) {
			$this->error($error['type'], $error['message'], $error['file'], $error['line']);
			exit;
		}
	}
	public function error(int $errno, string $errstr, string $errfile, int $errline) {
		debug([
			'errorlevel' => $this->errorType($errno),
			'message'    => $errstr,
			'file'       => $errfile,
			'line'       => $errline,
		], self::DEBUG_ERROR);
	}
	public function exception(\Throwable $exception) {
		$line = $exception->getLine();
		$file = $exception->getFile();
		$message = $exception->getMessage() . ' (' . $exception->getCode() . ')';
		$trace = $exception->getTrace();
		$type = get_class($exception);
		if ($exception instanceof \ErrorException) {
			$ftrace = $trace[0];
			array_shift($trace);
			debug([
				'errorlevel' => $this->errorType(array_key_exists('args', $ftrace) ? $ftrace['args'][0] : E_ERROR),
				'message'    => $message,
				'file'       => $file,
				'line'       => $line,
				'trace'      => $trace,
			], self::DEBUG_ERROR);
		} else {
			debug([
				'type'    => $type,
				'message' => $message,
				'file'    => $file,
				'line'    => $line,
				'trace'   => $trace,
			], self::DEBUG_EXCEPTION);
		}
	}

	public function register() {
		register_shutdown_function(fn() => $this->fatalError());
		set_exception_handler(fn(\Throwable $exception) => $this->exception($exception));
		set_error_handler(fn(int $errno, string $errstr, string $errfile, int $errline) => $this->error($errno, $errstr, $errfile, $errline), E_ALL);
	}

	protected function errorType($type) {
		return match ($type) {
			E_ERROR => 'ERROR',
			E_WARNING => 'WARNING',
			E_PARSE => 'PARSE',
			E_NOTICE => 'NOTICE',
			E_CORE_ERROR => 'CORE_ERROR',
			E_CORE_WARNING => 'CORE_WARNING',
			E_COMPILE_ERROR => 'COMPILE_ERROR',
			E_COMPILE_WARNING => 'COMPILE_WARNING',
			E_USER_ERROR => 'USER_ERROR',
			E_USER_WARNING => 'USER_WARNING',
			E_USER_NOTICE => 'USER_NOTICE',
			E_STRICT => 'STRICT',
			E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
			E_DEPRECATED => 'DEPRECATED',
			E_USER_DEPRECATED => 'USER_DEPRECATED',
		};
	}

}