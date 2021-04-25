<?php namespace Atomino\RequestPipeline\Responder\Smart\Cache\Middleware;

use Atomino\Core\Application;
use Atomino\Molecules\Cache\CacheInterface;
use Atomino\RequestPipeline\Responder\Smart\Cache\Event;
use Atomino\RequestPipeline\Pipeline\Handler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\ItemInterface;

class_alias(\Symfony\Contracts\Cache\CacheInterface::class, CacheInterface::class);

class Cache extends Handler{

	private int $cacheRequest = -1;

	public function __construct(private EventDispatcher $eventDispatcher, private CacheInterface $storage){ }

	static function SetCache(int $interval){ Application::DIC()->get(EventDispatcher::class)->dispatch(new Event($interval), Event::request); }

	public function handle(Request $request): Response{
		$this->eventDispatcher->addListener(Event::request, function (Event $event){ $this->cacheRequest = $event->interval; });
		return $this->storage->get(
			crc32($this->getRequest()->getRequestUri()),
			function (ItemInterface $item): Response{
				$response = $this->next($response);
				$item->expiresAfter($this->cacheRequest);
				return $response;
			}
		);
	}

}