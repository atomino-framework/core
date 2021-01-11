<?php namespace Atomino\Dump;

use Atomino\Core\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class_alias(\Monolog\Logger::class, Logger::class);

class Debug{

	private static ?Debug $instance = null;
	private ?LoggerInterface $logger = null;

	private function __construct(){
		try{
			$this->logger = Application::DIC()->get(Logger::class);
		}catch (\Exception $e){
			$this->logger = null;
		}
	}

	private static function instance(){ return is_null(static::$instance) ? ( static::$instance = new static() ) : static::$instance; }

	private function send($type, array $message){
		if (!is_null($this->logger)){
			$this->logger->debug($type, $message);
		}
	}

	static public function sql($message){
		static::instance()->send('sql', ['message'=>$message]);
	}

	static public function info($message){
		$trace = debug_backtrace();
		$message = ([
			'file'=> $trace[0]['file'],
			'line'=> $trace[0]['line'],
			'message'=>$message
		]);
		static::instance()->send('info', $message);
	}
}

