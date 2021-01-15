<?php namespace Atomino\Responder;

use Atomino\Routing\Interfaces\ResponderInterface;
use Atomino\Routing\Pipeline\BreakPipelineException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ServerBag;


abstract class Responder implements ResponderInterface{

	#[Pure] static protected final function args($args){return(['class' =>get_called_class(), 'args' =>$args]);}

	private Request $request;
	private ParameterBag $argsBag;
	private ParameterBag $hostBag;
	private ParameterBag $pathBag;
	private ParameterBag $dataBag;

	protected function getRequest(): Request{ return $this->request; }
	protected function getPathBag(): ParameterBag{ return $this->pathBag; }
	protected function getHostBag(): ParameterBag{ return $this->hostBag; }
	protected function getArgsBag(): ParameterBag{ return $this->argsBag; }
	protected function getAttributesBag(): ParameterBag{ return $this->request->attributes; }
	protected function getFilesBag(): FileBag{ return $this->request->files; }
	protected function getRequestBag(): InputBag{ return $this->request->request; }
	protected function getQueryBag(): InputBag{ return $this->request->query; }
	protected function getCookiesBag(): InputBag{ return $this->request->cookies; }
	protected function getHeaderBag(): HeaderBag{ return $this->request->headers; }
	protected function getServerBag(): ServerBag{ return $this->request->server; }
	protected function getRequestContent(): string{ return $this->request->getContent(); }
	protected function getRequestData(): array{ return $this->request->toArray(); }
	protected function getDataBag(): ParameterBag{ return $this->dataBag; }

	public function exec(Response $response, Request $request, ParameterBag $hostBag, ParameterBag $pathBag, ParameterBag $argsBag):Response{
		$this->request = $request;
		$this->pathBag = $pathBag;
		$this->hostBag = $hostBag;
		$this->argsBag = $argsBag;
		try{
			$data =  $this->request->toArray();
		}catch (\Exception $e){
			$data = [];
		}

		$this->dataBag = new ParameterBag($data);
		return $this->respond($response);
	}

	public function break(){throw new BreakPipelineException();}

	abstract protected function respond(Response $response):Response;

	protected final function redirect(Response $response, $url = '/', $statusCode=302, $immediate = true):Response{
		$response->headers->set('Location', $url);
		$response->setStatusCode($statusCode);
		if($immediate){
			$response->send();
			die();
		}else{
			return $response;
		}
	}

}