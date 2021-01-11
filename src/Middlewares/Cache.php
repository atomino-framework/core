<?php namespace Atomino\Middlewares;

use Atomino\Core\Application;
use Atomino\Middlewares\Cache\CacheInterface;
use Atomino\Middlewares\Cache\Event;
use Atomino\Responder\Middleware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\ItemInterface;

class_alias(\Symfony\Contracts\Cache\CacheInterface::class, CacheInterface::class);

class Cache extends Middleware{

	private int $cacheRequest = -1;

	public function __construct(private EventDispatcher $eventDispatcher, private CacheInterface $storage){ }

	static function Request(int $interval){ Application::DIC()->get(EventDispatcher::class)->dispatch(new Event($interval), Event::request); }

	public function respond(Response $response): Response{
		$this->eventDispatcher->addListener(Event::request, function (Event $event){ $this->cacheRequest = $event->interval; });
		return $this->storage->get(
			crc32($this->getRequest()->getRequestUri()),
			function (ItemInterface $item) use ($response): Response{
				$response = $this->next($response);
				$item->expiresAfter($this->cacheRequest);
				return $response;
			}
		);
	}

}