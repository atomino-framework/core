<?php namespace Atomino\Dump;

use Atomino\Core\Application;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class RlogtailSockHandler extends SocketHandler{

	public function __construct(string $connectionString, $level = Logger::DEBUG, bool $bubble = true){
		parent::__construct($connectionString, $level, $bubble);
		$this->setFormatter(new RlogtailFormatter());
	}

}

class RlogtailFormatter implements FormatterInterface{

	private string $id;
	private $method;
	private string $host;
	private string $path;
	public function __construct(){
		$this->id = uniqid();
		$this->method = Application::DIC()->get(Request::class)->getMethod();
		$this->host = Application::DIC()->get(Request::class)->getHost();
		$this->path = Application::DIC()->get(Request::class)->getPathInfo();
	}

	public function format(array $record){
		$data = [
			'request' => [
				'id'     => $this->id,
				'method' => $this->method,
				'host'   => $this->host,
				'path'   => $this->path,
			],
			'type'    => $record['message'],
			'message' => $record['context'],
		];
		return json_encode($data)."\n";
	}
	public function formatBatch(array $records){}

}